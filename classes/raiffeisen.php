<?php
/**
 * Insert form data to db
 * @author vshapovalov
 * @date 23/7/2020
 * @copyright 2020 Moscow Witte University. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package moodle
 */

namespace classes;

defined('MOODLE_INTERNAL') || die;
require_once('../locallib.php');

use Exception;
use student_pay;

/**
 * Class raiffeisen
 * @package classes
 */
class raiffeisen
{
    /**
     * @var null
     */
    private static $instance = null;

    /**
     * raiffeisen constructor.
     */
    protected function __construct()
    {
    }

    /**
     * null don't use
     * @throws Exception
     */
    protected function __clone()
    {
        throw new Exception("Cannot clone a singleton.");
    }

    /**
     * @throws Exception
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize a singleton.");
    }

    /**
     * @return raiffeisen
     */
    public static function getInstance(): raiffeisen
    {
        if (self ::$instance === null) {
            self ::$instance = new self();
        }
        return self ::$instance;
    }

    /**
     * @param $summ
     * @param $goods_type
     * @param $pay_type
     * @param $order_id
     * @return bool
     */
    private function validateFormData($summ, $goods_type, $pay_type, $order_id = null): bool
    {
        if (
            $this -> validateNumber($summ) &&
            ($order_id) ? $this -> validateNumber($order_id) : true  &&
            $this -> validateTypes($pay_type) &&
            $this -> validateTypes($goods_type)
        ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $num
     * @return bool
     */
    private function validateNumber($num): bool
    {
        return preg_match('/^\d++$/s', $num);
    }

    /**
     * @param $str
     * @return bool
     */
    private function validateTypes($str): bool
    {
        return preg_match('/^type[1-2]$/s', $str);
    }

    /**
     * @param null $orderId
     * @param null $id_qr_code
     * @param null $code_error
     * @return \stdClass
     */
    private function createNewClass($orderId = null, $id_qr_code = null, $code_error = null)
    {
        $pay = new \stdClass();
        ($orderId ?? null) ? $pay -> orderId = $orderId : null;
        ($id_qr_code ?? null) ? ($pay -> id_qr_code = $id_qr_code) : null;
        if ($code_error ?? null) {
            $pay -> error = $code_error;
            $pay -> status = 5;
        }
        return $pay;
    }

    /**
     * @param $amount
     * @param $orderId
     * @return array
     * @throws \dml_exception
     */
    public function generateQrCode($amount, $orderId): array
    {
        if ($this -> validateNumber($amount) && $this -> validateNumber($orderId)) {

            $config = get_config('local_student_pay');

            $ch = curl_init();

            $params = [
                "amount" => $amount,
                "createDate" => date('Y-m-d\TH:i:s.uP'),
                "currency" => "RUB",
                "order" => $orderId,
                "paymentDetails" => "Оплата за обучение",
                "qrType" => "QRDynamic",
                "qrExpirationDate" => date('Y-m-d\TH:i:s.uP'),
                "sbpMerchantId" => $config -> rai_sbp_merchant_id
            ];

            curl_setopt($ch, CURLOPT_URL, $config -> rai_api_url . '/api/sbp/v1/qr/register');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));

            $headers = array();
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Authorization: Bearer ' . $config -> rai_api_secret_key_sbr;
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $result = json_decode(curl_exec($ch));

            $error = null;

            if (curl_errno($ch)) {
                $error = 'Error:' . curl_error($ch);
            }
            curl_close($ch);

            if ($error ?? null) {
                throw new Exception("Ошибка при совершении запроса", $error);
            }

            return get_object_vars($result);
        } else {
            throw new Exception("Данные не прошли валидацию");
        }
    }

    /**
     * @param $summ
     * @param $goods_type
     * @param $pay_type
     * @param $order_id
     * @param null $qrId
     * @param bool $key
     * @param bool $ecom
     * @param $error
     */
    public function createPay($summ, $goods_type, $pay_type, $order_id, $qrId, $error, $key = false, $ecom = false): void
    {
        if ($this -> validateFormData($summ, $goods_type, $pay_type, $order_id)) {
            if($ecom or $key){
                echo json_encode(student_pay :: createNewOrder($summ, $goods_type, $order_id, 1, "raif"));
            }else{
                $pay = $this -> createNewClass($order_id, $qrId, $error);
                student_pay ::updateOrder($pay);
            }
        }
    }
}