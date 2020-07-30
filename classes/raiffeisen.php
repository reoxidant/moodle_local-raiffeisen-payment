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
// --Commented out by Inspection START (30.07.2020 17:25):
//    /**
//     * @var null
//     */
//    private static $instance = null;
// --Commented out by Inspection STOP (30.07.2020 17:25)


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
     * @param $order_id
     * @param $qrId
     */
    private function recordNewPay($summ, $goods_type, $order_id, $qrId): void
    {
        student_pay ::createNewOrder($summ, $goods_type, 1, 'raiff', $order_id, $qrId);
    }

    /**
     * @param $summ
     * @param $goods_type
     * @param $pay_type
     * @param $order_id
     * @return bool
     */
    private function validateFormData($summ, $goods_type, $pay_type, $order_id): bool
    {
        if (
            $this -> validateNumber($summ) &&
            $this -> validateNumber($order_id) &&
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

    public function generateQrCode(): string
    {
        //TODO: write curl generator qr code from api
    }

    /**
     * @param $summ
     * @param $goods_type
     * @param $pay_type
     * @param $order_id
     * @param null $qrId
     */
    public function createPay($summ, $goods_type, $pay_type, $order_id, $qrId): void
    {
        if ($this -> validateFormData($summ, $goods_type, $pay_type, $order_id)) {
            $this -> recordNewPay($summ, $goods_type, $order_id, $qrId);
        }
    }
}