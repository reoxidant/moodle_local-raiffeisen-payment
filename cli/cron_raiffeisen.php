<?php /** @noinspection PhpIncludeInspection */

require_once($CFG -> dirroot . "/local/student_pay/locallib.php");

/**
 * Class cron_raiffeisen
 */
class cron_raiffeisen
{
    /**
     * @var Subsystem|null
     */
    protected $subsystem;
    /**
     * @var BankSystem|null
     */
    protected $bank;

    /**
     * cron_raiffeisen constructor.
     * @param Subsystem|null $subsystem
     * @param BankSystem|null $bank
     */
    function __construct(
        Subsystem $subsystem = null,
        Banksystem $bank = null
    )
    {
        $this -> subsystem = $subsystem ?: new Subsystem();
        $this -> bank = $bank ?: new BankSystem();
    }

    /**
     *
     */
    public function checkStatusOperations(): void
    {
        //Facade subsystem initializes
        $this -> subsystem -> setStatusTypes();
        $payments = $this -> subsystem -> getPayments();
        //Facade bank initializes
        $this -> bank -> status_arr;
        $this -> bank -> handlerStatusByPayments($payments);
    }
}

/**
 * Class Subsystem
 */
class Subsystem
{
    /**
     * @var array
     */
    public $status_arr;

    /**
     * @var array
     */
    public function setStatusTypes(): void
    {
        $this -> status_arr = student_pay ::get_status_types();
    }

    /**
     * @return array
     */
    public function getPayments(): array
    {
        $status_arr = array($this -> status_arr['new'], $this -> status_arr['paid']);
        return student_pay ::getOrdersByStatus($status_arr);
    }

    /**
     * @param $id
     * @param $status
     */
    public static function updateStatusToDB($id, $status): void
    {
        if ($status === "SUCCESS") {
            student_pay ::updateOrderStatus($id, student_pay ::get_status_types()['paid']);
        } else if ($status === "NOT_FOUND") {
            student_pay ::updateOrderStatus($id, student_pay ::get_status_types()['error']);
        }
    }
}

/**
 * Class BankSystem
 */
class BankSystem
{
    /**
     * @param $payments
     */
    public function handlerStatusByPayments($payments): void
    {
        foreach ($payments as $id => $payment) {
            $user = $this -> getUserByPayment($payment);
            $this -> validateFields($payment, $id, $user);
            $this -> connect($id);
        }
    }

    /**
     * @param $val_pay
     * @param $id
     * @param $user
     * @return bool
     * @return bool
     */
    private function validateFields($val_pay, $id, $user): bool
    {
        if (($id && $val_pay -> amount && $val_pay -> external_order_id) ?? null) {
            return ($user -> username ?? null && $val_pay -> status == Subsystem ::$status_arr['new']);
        }

        return false;
    }

    /**
     * @param $val
     * @return object
     */
    private function getUserByPayment($val): object
    {
        global $DB;
        $result = null;
        try {
            /** @var object $result */
            $result = $DB -> get_record('user', array('id' => $val -> userid), 'username');
        } catch (dml_exception $e) {
        }
        return $result;
    }

    /**
     * @param $orderId
     */
    public function connect($orderId): void
    {
        $ch = curl_init();
        $fp_err = fopen($_SERVER['DOCUMENT_ROOT'] . '/verbose_file.txt', 'ab+');
        fwrite($fp_err, date('Y-m-d H:i:s') . "\n\n"); //add timestamp to the verbose log
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_STDERR, $fp_err);
        curl_setopt($ch, CURLOPT_URL, 'https://test.ecom.raiffeisen.ru/api/payments/v1/orders/' . $orderId . '/transaction');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

        $headers = array();
        $headers[] = 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwMDAwMDE3ODAzNTcwMDEtODAzNTcwMDEiLCJqdGkiOiIzMGQ2MjM4Yi03MjY3LTRlNWEtOGEwYi04OGY3NTRhNmQ4MTYifQ.Douj-vNUWCS9AA_CfurLqZ2kPwODKIovMPKHzrM3D0A';
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = json_decode(curl_exec($ch));

        $errors = $this -> handlerErrors($ch);

        if ($errors ?? null) {
            $status = (string)($result -> transaction -> status -> value);
            Subsystem ::updateStatusToDB($orderId, $status);
        } else {
            $this -> recordErrorsDB($orderId, $errors);
        }
    }

    /**
     * @param $id
     * @param $msg
     */
    private function recordErrorsDB($id, $msg)
    {
        $error = new stdClass;
        $error -> id = $id;
        $error -> error = $msg;
        student_pay ::updateOrder($error);
    }

    /**
     * @param $ch
     * @param null $error
     * @return string|null
     */
    private function handlerErrors($ch, $error = null): string
    {
        if (curl_errno($ch)) {
            $error = curl_errno($ch) . " - " . curl_error($ch);
        }
        curl_close($ch);
        return $error;
    }
}
