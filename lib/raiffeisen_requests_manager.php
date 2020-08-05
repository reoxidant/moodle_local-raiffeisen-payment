<?php
/**
 * Description actions
 * @author vshapovalov
 * @date 29/7/2020
 * @copyright 2020 Moscow Witte University. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package moodle
 */

defined('MOODLE_INTERNAL') || die;
require_once('../../../config.php');
require_once('../classes/raiffeisen.php');
require_once('../classes/raiffeisen_order.php');

use classes\raiffeisen;

if ($_POST ?? null) {
    if ($_POST['key'] ?? null) {
        if ($_POST['key'] === 'new') {
            echo json_encode(student_pay :: createNewOrder($_POST['summ'], $_POST['orderId'], 1, "raif"));
        } else {
            throw new moodle_exception('Ошибка при получении параметра: orderId');
        }
    } else {
        $payment = raiffeisen ::getInstance();

        if ($_POST['payment'] === 'sbp') {
            $result = $payment -> generateQrCode($_POST['summ'], $_POST['orderId']);
            $error = ($result["qrId"] ?? null) ? null : $result['code'];
            echo json_encode($result);
        }

        $payment -> createPay($_POST['summ'], $_POST['goods_type'], $_POST['pay_type'], $_POST['orderId'], $result["qrId"], $error);
    }
}