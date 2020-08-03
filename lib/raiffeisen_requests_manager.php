<?php
/**
 * Description actions
 * @author vshapovalov
 * @date 29/7/2020
 * @copyright 2020 Moscow Witte University. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package moodle
 */

require_once('../../../config.php');
require_once('../classes/raiffeisen.php');
require_once('../classes/raiffeisen_order.php');

use classes\raiffeisen;
use classes\raiffeisen_order;

if ($_POST ?? null) {
    if ($_POST['key'] ?? null) {
        if ($_POST['key'] === 'new') {
            $order = raiffeisen_order :: getInstance();
            echo $order -> getOrderId();
        } else {
            throw new moodle_exception('Ошибка при получении параметра: orderId');
        }
    } else {
        $id_qr_code = null;
        $payment = raiffeisen ::getInstance();
        if ($_POST['raiffeisen_type_pay'] === 'type1') {
            $result = $payment -> generateQrCode($_POST['summ'], $_POST['orderId']);
            $id_qr_code = $result["qrId"];
            $error = ($id_qr_code ?? null) ? null : $result["code"];
            echo json_encode($result);
        }
        $payment -> createPay($_POST['summ'], $_POST['goods_type'], $_POST['pay_type'], $_POST['orderId'], $_POST['rai_type_pay'], $id_qr_code, $error);
    }
}