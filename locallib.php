<?php
defined('MOODLE_INTERNAL') || die;

require_once($CFG -> libdir . "/formslib.php");
$config_values = student_pay ::set_config_vals(get_config('local_student_pay'));

$STATUS_TYPES = student_pay ::get_status_types();

class pay_form extends moodleform
{
    //Add elements to form
    public function definition()
    {
        global $CFG;

        $mform = $this -> _form; // Don't forget the underscore!

        $attributes = array('maxlength' => '10', 'min' => '1', 'autocomplete' => 'off', 'autofocus', 'required');

        $mform -> addElement('text', 'summ', get_string('pay_summ_text', 'local_student_pay'), $attributes); // Add elements to your form
        $mform -> setType('summ', PARAM_TEXT); //Set type of element

        $options = array(
            'type1' => get_string('goods_name_type1', 'local_student_pay'),
            'type2' => get_string('goods_name_type2', 'local_student_pay')
        );
        $select = $mform -> addElement('select', 'goods_type', '', $options);
        // This will select the colour blue.
        $select -> setSelected('type1');

        $options_pay = array(
            'type1' => get_string('pay_type_sber', 'local_student_pay'),
            'type2' => get_string('pay_type_rai', 'local_student_pay')
        );

        $select_pay = $mform -> addElement('select', 'pay_type', '', $options_pay);

        $select_pay -> setSelected('type1');

        $this -> add_action_buttons(false, get_string('submit_button_text', 'local_student_pay')); // false - без cancel
    }

    public function validation($data, $files)
    {
        $errors = parent ::validation($data, $files);

        // если вдруг неверный тип указан
        if (empty($data['goods_type'])) {
            $errors['goods_type'] = get_string('criticalerror', 'local_student_pay');
        }

        if (isset($data['summ'])) {
            $summ = str_replace(' ', '', $data['summ']);
            if (!(!empty($summ) && is_numeric($summ) && $summ > 0 && strlen($summ) <= 10 && floatval($summ) > 0 && preg_match('/^\d+$/', $summ)))
                $errors['summ'] = get_string('summ_error', 'local_student_pay');
        }

        return $errors;
    }
}

class student_pay
{
    private static $config; // = get_config('local_student_pay')

    public static function set_config_vals($config)
    {
        self ::$config = $config;
    }

    public static function get_status_types($bycode = false)
    {
        $statuses = array(
            'new' => array("code" => 1, "name" => get_string('status_new', 'local_student_pay')), // Новый
            'sended1c' => array("code" => 2, "name" => get_string('status_sended1c', 'local_student_pay')), // Отправлен в 1С
            'paid' => array("code" => 3, "name" => get_string('status_paid', 'local_student_pay')), // Оплачен
            'canceled' => array("code" => 4, "name" => get_string('status_canceled', 'local_student_pay')), // Отменён
            'error' => array("code" => 5, "name" => get_string('status_error', 'local_student_pay')), // Ошибка
        );

        $return_arr = array();
        foreach ($statuses as $key => $val) {
            if ($bycode) {
                $return_arr[$val['code']] = $val['name'];
            } else {
                $return_arr[$key] = $val['code'];
            }
        }

        return $return_arr;
    }

    public static function get_sber_codes()
    {
        $arr_codes = array();
        for ($i = 0; $i <= 10; $i++)
            $arr_codes[$i] = $i;
        return $arr_codes;
    }

    public static function getOrderByID($id)
    {
        global $DB;

        try {
            $res = $DB -> get_record("student_pays", array('id' => $id));
            return $res;
        } catch (Exception $e) {
        }

        return null;
    }

    public static function getOrdersByStatus($statuses)
    {
        global $DB;

        try {
            list($itemsql, $itemlist) = $DB -> get_in_or_equal($statuses);

            $res = $DB -> get_records_sql("SELECT * FROM {student_pays} WHERE status $itemsql", $itemlist);
            return $res;
        } catch (Exception $e) {
        }

        return array();
    }

    // возращает необработанные записи по фискализации
    public static function getNonFiscalPays()
    {
        global $DB;

        return $DB -> get_records("student_pays", array('fiscal_uid' => '', 'status' => 3));
    }

    // основные функции
    public static function createNewOrder($summ, $goods_type)
    {
        global $USER, $DB;

        $new_status_id = self ::$config -> status_new;

        $timenow = time();

        $orderid = false;

        try {
            $record = new stdClass();
            $record -> userid = $USER -> id;
            $record -> timecreate = $timenow;
            $record -> timemodified = $timenow;
            $record -> amount = $summ;
            $record -> goods_type = $goods_type;
            $record -> status = $new_status_id;
            $orderid = $DB -> insert_record('student_pays', $record);
        } catch (Exception $e) {
        }

        return array('orderid' => $orderid, 'timecreate' => $timenow);
    }

    public static function updateOrder($values)
    {
        global $DB;

        if (empty($values -> id))
            return false;

        $values -> timemodified = time();

        $updated = false;
        try {
            $updated = $DB -> update_record('student_pays', $values);
        } catch (Exception $e) {
        }

        return $updated;
    }

    public static function updateOrderStatus($orderid, $statusid)
    {
        $values = new stdClass;
        $values -> id = $orderid;
        $values -> status = $statusid;

        return self ::updateOrder($values);
    }

    public static function sendRequest($params, $url)
    {
        $opts = array(
            'http' => array(
                'method' => 'POST',
                'header' => "Content-type: application/x-www-form-urlencoded",
                'content' => http_build_query($params, "", "&"),
                'timeout' => 20
            ),
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            )
        );

        // И отправляем эти данные на сервер сбербанка
        $context = stream_context_create($opts);

        return file_get_contents($url, false, $context);
    }

    public static function do_pay($summ, $goods_type)
    {
        global $USER, $CFG, $STATUS_TYPES;

        /*echo '<pre>';
        print_r($pay_type);
        echo '</pre>';
        die('Дебаг');*/

        if (!preg_match('/^\d+$/', $USER -> username))
            return "username not int";

        if (!empty($summ))
            $summ = str_replace(' ', '', $summ);
        else
            return "summ empty";

        // создаём новую запись
        $new_order = self ::createNewOrder($summ, $goods_type);
        if (!$ORDER_ID = $new_order['orderid'])
            return "new order create DB error";

        $summ = $summ * 100; // сумма в копейках

        $config = self ::$config;

        // корзина
        $quantity = new StdClass;
        $quantity -> value = 1;
        $quantity -> measure = get_string('sber_measure', 'local_student_pay');

        $itemObj = new StdClass;
        $itemObj -> positionId = "1";
        $itemObj -> name = get_string("goods_name_$goods_type", 'local_student_pay');
        $itemObj -> quantity = $quantity;
        $itemObj -> itemAmount = $summ;
        $itemObj -> itemCode = (string)$ORDER_ID;

        $items = new StdClass;
        $items -> items = array($itemObj);

        $customerDetails = new StdClass;
        $customerDetails -> email = $USER -> email;
        $customerDetails -> contact = fullname($USER);

        $orderBundle = new StdClass;
        $orderBundle -> orderCreationDate = number_format($new_order['timecreate'], 3, '', ''); // добавим три нуля в конец для Сбербанка
        $orderBundle -> customerDetails = $customerDetails;
        $orderBundle -> cartItems = $items;

        $PARAMS = array(
            'userName' => $config -> sber_user,            // А тут его логин
            'password' => $config -> sber_pass,            // Здесь пароль от вашего API юзера в Сбербанке
            'orderNumber' => $ORDER_ID,            // Внутренний ID заказа
            'amount' => $summ,               // Сумма пополнения
            'returnUrl' => $CFG -> wwwroot . $config -> sber_returnpage,     // URL куда вернуть пользователя после перечисления средств
            'failUrl' => $CFG -> wwwroot . $config -> sber_failpage,     // URL куда вернуть пользователя после ошибки
            'description' => $USER -> username, // описание
            'orderBundle' => json_encode($orderBundle),
        );
        $REGISTER_URL = $config -> sber_url . $config -> sber_registrurl;
        $result = self ::sendRequest($PARAMS, $REGISTER_URL);

        @$result_obj = json_decode($result);

        // проверка ответа и запись ошибки, при наличии
        if (!$result || empty($result) || empty($result_obj -> formUrl) || empty($result_obj -> orderId)) {
            if (isset($result_obj -> errorCode) && $result_obj -> errorCode !== 0) {
                $update_values = new stdClass;
                $update_values -> id = $ORDER_ID;
                $update_values -> error = $result_obj -> errorCode . ' - ' . $result_obj -> errorMessage;

                self ::updateOrder($update_values);
            }
            self ::updateOrderStatus($ORDER_ID, $STATUS_TYPES['canceled']);
            return "send to sber error";
        }

        // запись orderId, formUrl и перевод статуса в "в процессе"
        $update_values = new stdClass;
        $update_values -> id = $ORDER_ID;
        $update_values -> external_order_id = $result_obj -> orderId;
        $update_values -> pay_url = $result_obj -> formUrl;
        self ::updateOrder($update_values);
        //self::updateOrderStatus($ORDER_ID, $STATUS_TYPES['do']);

        $event = \local_student_pay\event\student_pay_do ::create(array(
            'objectid' => null,
            'context' => context_system ::instance(),
        ));
        $event -> trigger();

        // перенаправляем на оплату и останавливаем дальнейшую работу
        redirect($result_obj -> formUrl);
        die;
    }

    // отправка записи в кассовый аппарат
    // $record - запись об оплате
    // $istest - для проверки - 0 или 1 (1 - проверка)
    public static function sendToFiscal_LifePay(&$record, $istest = 0)
    {
        global $DB;

        // обновим из базы
        $record = self ::getOrderByID($record -> id);

        if ($record -> status != 3)
            return 'Для pay recordid ' . $record -> id . ' status не равен 3 (' . get_string('status_paid', 'local_student_pay') . ') и не может быть отправлен в кассу';

        if (!$user = $DB -> get_record('user', array('id' => (int)$record -> userid)))
            return 'Bad userid: ' . $record -> userid;

        if (empty($record -> goods_type))
            return 'Для pay recordid ' . $record -> id . ' не указан "goods_type"';

        $plug_config = self ::$config;

        $data = [];
        $data['apikey'] = $plug_config -> life_pay_apikey; // заполнить. АПИ-ключ компании в системе Lifepay.
        $data['login'] = $plug_config -> life_pay_login; // заполнить. Логин администратора компании или торговой точки в системе Lifepay.

        // товары корзины
        $data_products = new stdClass;
        $data_products -> name = get_string("goods_name_{$record->goods_type}", 'local_student_pay') . '. ' . fullname($user); // заполнить. Наименование позиции.
        $data_products -> price = (string)$record -> amount; // заполнить. Цена за единицу.
        $data_products -> quantity = 1;
        $data_products -> tax = 'none';

        $data['purchase'] = array('products' => array($data_products));
        $data['type'] = 'payment';
        $data['test'] = ($istest === 0 ? 0 : 1);
        $data['mode'] = 'email'; // напечатать чек и отправить квитанцию по email
        $data['customer_email'] = $user -> email; // заполнить. Электронный адрес клиента для отправки чека. Максимум 100 символов.
        $data['card_amount'] = '#'; // # - подсчёт по сумме товаров в корзине
        $data['cashier_name'] = $plug_config -> life_pay_cashier_name; // заполнить. Имя кассира. По умолчанию - “Кассир”.

        $life_pay_target_serial = $plug_config -> life_pay_target_serial;
        if (!empty($life_pay_target_serial)) {
            $data['target_serial'] = $life_pay_target_serial; // заполнить. Серийный номер принтера, на котором необходимо фискализировать данные.
        }

        $data['ext_id'] = (string)$record -> id; // заполнить. Идентификатор внутренний.

        $request = json_encode($data);

        $url = $plug_config -> life_pay_url; // заполнить. URL для отправки данных

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $request);

        $result = curl_exec($curl);

        if ($result === false) {
            $curl_error = curl_error($curl);
            curl_close($curl);

            if (isset($curl_error))
                $return = 'Ошибка curl: ' . $curl_error;
            else
                $return = 'Неизвестная ошибка подключения';

            return $return;
        }

        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($http_code != 200) {
            curl_close($curl);
            return 'Неожиданный код ответа (' . $http_code . ') HTTP от LifePay: ' . $plug_config -> life_pay_url;
        }

        curl_close($curl);

        $resultJson = @json_decode($result);

        if (empty($resultJson) || !isset($resultJson -> code) || $resultJson -> code !== 0 || empty($resultJson -> data -> uuid)) {
            return 'Получен неверный ответ (или ошибка) от LivePay: ' . (empty($result) ? 'пустой ответ' : print_r($result, true));
        }

        // если всё удачно, помечаем отправленным
        $record -> fiscal_uid = $resultJson -> data -> uuid;
        if (!self ::updateOrder($record)) {
            return "Ошибка обновления записи в БД recordid - {$record->id}";
        }

        return true;
    }
}

?>