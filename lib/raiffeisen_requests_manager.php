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

use classes\raiffeisen;

if ($_POST ?? null) {
    $payment = raiffeisen ::getInstance();

    $result = null;

    if ($_POST['payment'] === 'sbp') {
        $result = $payment -> generateQrCode($_POST['summ'], $_POST['orderId']);
        $error = ($result["qrId"] ?? null) ? null : $result['code'];
        echo json_encode($result);
    }

    $key = $_POST['key'] === "new";
    $ecom = $_POST['payment'] === "ecom";

    $payment -> createPay($_POST['summ'], $_POST['goods_type'], $_POST['pay_type'], $_POST['orderId'], $result["qrId"], $error, $key, $ecom);
}