<?php
require_once('../../../config.php');
require_once('../classes/raiffeisen.php');

use classes\raiffeisen;

if ($_POST ?? null) {
    $payment = new raiffeisen();
    $payment -> createPay($_POST['summ'], $_POST['goods_type'], $_POST['pay_type']);
}
