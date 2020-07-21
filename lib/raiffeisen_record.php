<?php
defined('MOODLE_INTERNAL') || die;

if ($_POST ?? null) {
    $payment = new classes\raiffeisen();
    $payment -> createPay($_POST['summ'], $_POST['goods_type'], $_POST['pay_type']);
}
