<?php
/**
 * Create object raiffeisen
 * @author vshapovalov
 * @date 23/7/2020
 * @copyright 2020 Moscow Witte University. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package moodle
 */

defined('MOODLE_INTERNAL') || die;
require_once('../../../config.php');
require_once('../classes/raiffeisen.php');

use classes\raiffeisen;

if ($_POST ?? null) {
    $payment = raiffeisen ::getInstance();
    $payment -> createPay($_POST['summ'], $_POST['goods_type'], $_POST['pay_type']);
}