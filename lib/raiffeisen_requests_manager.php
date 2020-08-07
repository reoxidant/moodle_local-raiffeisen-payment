<?php
/**
 * Description actions
 * @author vshapovalov
 * @date 29/7/2020
 * @copyright 2020 Moscow Witte University. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package moodle
 */

//defined('MOODLE_INTERNAL') || die();
require_once('../../../config.php');
require_once('../classes/raiffeisen.php');

use classes\raiffeisen;

$summ = required_param('summ', PARAM_INT);
$goods_type = required_param('goods_type', PARAM_RAW);
$pay_type = required_param('goods_type', PARAM_RAW);
$orderId = required_param('orderId', PARAM_INT);
$key = required_param('key', PARAM_RAW);
$payService = required_param('payment', PARAM_RAW);

if ($_POST ?? null) {
    $payment = raiffeisen ::getInstance();

    $result = null;

    if ($payService === 'sbp') {
        $result = $payment -> generateQrCode($_POST['summ'], $_POST['orderId']);
        $error = ($result["qrId"] ?? null) ? null : $result['code'];
        echo json_encode($result);
    }

    $payData = [
        'summ' => $summ,
        'goods_type' => $goods_type,
        'pay_type' => $pay_type,
        'orderId' => $orderId,
        'qr_code_id' => $result["qrId"],
        'error_code' => $error,
        'is_new_pay' => $key === "new",
        'is_ecom_pay' => $payService === "ecom"
    ];

    $payment -> createPay($payData);
}