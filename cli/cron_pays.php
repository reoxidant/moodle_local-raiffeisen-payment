<?php
//define('CLI_SCRIPT', true);
require(__DIR__ . '/../../../config.php');
require_once($CFG -> dirroot . "/local/student_pay/locallib.php");
require_once($CFG -> dirroot . "/local/student_pay/cli/cron_raiffeisen.php");
$err_file = '/var/log/php/stud_pays.log';
$config_data = get_config('local_student_pay');
$ws_timeout = (int)$config_data -> ws_timeout;
$ws_timeout = $ws_timeout > 0 ? $ws_timeout : 1;

ini_set("default_socket_timeout", $ws_timeout);
try {
    $status_arr = array($STATUS_TYPES['new'], $STATUS_TYPES['paid']);
    $results = student_pay ::getOrdersByStatus($status_arr);

    if (count($results)) {
        $WS_params = array('login' => $config_data -> ws_user, 'password' => $config_data -> ws_pass, 'connection_timeout' => $ws_timeout);
        $status_url = $config_data -> sber_url . $config_data -> sber_statusurl;
        $sber_user = $config_data -> sber_user;
        $sber_pass = $config_data -> sber_pass;

        $sber_codes_normal = explode(',', $config_data -> sber_codes_normal);
        $sber_codes_fail = explode(',', $config_data -> sber_codes_fail);
        foreach ($results as $id => $val) {
            $record = $val;
            if (isset($id) && isset($val -> amount) && isset($val -> external_order_id)) {
                $user_res = $DB -> get_record('user', array('id' => $val -> userid), 'username');
                if (isset($user_res -> username) && preg_match('/^\d+$/', $user_res -> username)) {
                    $paid = false;
                    if ($val -> status == $STATUS_TYPES['new']) {
                        $PARAMS = array(
                            'orderId' => $val -> external_order_id, // Внутренний ID заказа
                            'userName' => $sber_user, // А тут его логин
                            'password' => $sber_pass, // Здесь пароль от вашего API юзера в Сбербанке
                        );

                        $SBERresult = student_pay ::sendRequest($PARAMS, $status_url);//file_put_contents($err_file, $result);

                        @$result_obj = json_decode($SBERresult);

                        // проверка ответа и запись ошибки, при налачии
                        if (!$SBERresult || empty($SBERresult) || $result_obj == NULL) {
                            $error = $id . ': не получены данные от Банка';
                            report_error($error); // критическая ошибка
                        }
                        if (isset($result_obj -> errorCode)) {
                            if ($result_obj -> errorCode != '0') {
                                $update_values = new stdClass;
                                $update_values -> id = $id;
                                $update_values -> error = $result_obj -> errorCode . ' - ' . $result_obj -> errorMessage;
                                student_pay ::updateOrder($update_values);

                                if ($result_obj -> errorCode == '2') {
                                    $STATUS = $STATUS_TYPES['canceled'];
                                } else {
                                    continue;
                                    //$STATUS = $STATUS_TYPES['error'];
                                }
                                student_pay ::updateOrderStatus($id, $STATUS);
                                continue;
                            } else if (isset($result_obj -> orderStatus)) {
                                if ($result_obj -> orderStatus !== 2) {
                                    if (in_array($result_obj -> orderStatus, $sber_codes_normal)) {
                                        continue; // находится в обработке, не трогаем
                                    } elseif (in_array($result_obj -> orderStatus, $sber_codes_fail)) {
                                        student_pay ::updateOrderStatus($id, $STATUS_TYPES['canceled']);
                                        continue;
                                    }
                                } else {
                                    if (isset($result_obj -> authRefNum)) {
                                        $update_values = new stdClass;
                                        $update_values -> id = $id;

                                        $xid = (int)$result_obj -> authRefNum;
                                        $update_values -> xid = $xid;

                                        $external_date = intval($result_obj -> date);
                                        $external_date = $external_date > 1000 ? ((int)substr($external_date, 0, -3)) : $val -> timecreate; // сбер возвращает на 3 цифры больше
                                        $update_values -> external_date = $external_date;

                                        student_pay ::updateOrder($update_values);

                                        // для отправки в 1С
                                        $val -> xid = $xid;
                                        $val -> external_date = $external_date;

                                        if ($paid = student_pay ::updateOrderStatus($id, $STATUS_TYPES['paid'])) {
                                            $record -> status = $STATUS_TYPES['paid'];
                                        }
                                    }
                                }
                            }
                        } else {
                            $error = $id . ': критическая ошибка';
                            report_error($error); // критическая ошибка
                        }
                    }
                    if ($paid || $val -> status == $STATUS_TYPES['paid']) {
                        // // отправим на фискализацию, чтобы получить UID, который запишется по ссылке в $record
                        // $fiscal_res = student_pay::sendToFiscal_LifePay($record);
                        // if($fiscal_res !== TRUE || empty($record->fiscal_uid)){
                        // if(!empty($fiscal_res)){
                        // throw new Exception("Не удалось отправить платёж (id {$record->id}) на фискализацию из-за ошибки:\r\n" . (string)$fiscal_res);
                        // }else{
                        // throw new Exception("Не удалось отправить платёж (id {$record->id}) на фискализацию из-за критической ошибки.");
                        // }
                        // }

                        try {
                            $Sclient = new SoapClient("https://siriussrv.muiv.ru/siriuswebsrv/ws/studreport.1cws?wsdl", $WS_params);
                            $params = new stdClass();
                            $params -> StudentID = $user_res -> username;
                            $params -> reporttype = 'acquiring';
                            $params -> Report_id = $id;
                            $params -> OptionalParameters = stripslashes('<SUMM>' . htmlspecialchars($val -> amount) . '</SUMM><SBER_ID>' . htmlspecialchars($val -> xid) . '</SBER_ID><FISCAL_UID>' . htmlspecialchars($record -> fiscal_uid) . '</FISCAL_UID>');
                            $params -> Created = $val -> external_date;
                            $res = $Sclient -> put($params);
                            if (isset($res -> return) && $res -> return == 'true') {
                                student_pay ::updateOrderStatus($id, $STATUS_TYPES['sended1c']);
                            } elseif (is_soap_fault($res)){
                            $error = "Ошибка SOAP: (faultcode: {$res->faultcode}, faultstring: {$res->faultstring})";
                                report_error($error);
                            } elseif (isset($res -> return)) {
                                $error = $res -> return;
                                report_error($error);
                            }
                        } catch (Exception $e) {
                            $error = $e -> getMessage() . "\r\nid:" . $id . "\r\ncreated:" . $val -> timecreate;
                            report_error($error);
                        }
                    }
                }
            }
        }
    }

    // // обработаем записи для фискализации
    // $NonFiscalPays = student_pay::getNonFiscalPays();
    // foreach($NonFiscalPays as $record){
    // student_pay::sendToFiscal_LifePay($record);
    // }
} catch (Exception $e) {
    $error = $e -> getMessage();
    report_error($error);
}
try {
    $raiffeisenFacade = new cron_raiffeisen();
    $raiffeisenFacade -> checkStatusOperations();
} catch (Exception $e) {
    $error = $e -> getMessage();
    report_error($error);
}
function report_error($error)
{
    $error = date('H:i:s d.m.Y', time()) . "\r\n" . $error;
    file_put_contents($err_file, $error . "\r\n\r\n", FILE_APPEND);
    mail('itlog@muiv.ru', 'e.muiv.ru - Stud pays', $error);
    die;
}

?>