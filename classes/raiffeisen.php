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

use dml_exception;
use stdClass;

/**
 * Class raiffeisen
 * @package classes
 */
class raiffeisen
{
    /**
     * @var
     */
    private static $instance;

    /**
     * raiffeisen constructor.
     */
    protected function __construct()
    {
    }

    /**
     * null don't use
     * @throws \Exception
     */
    protected function __clone()
    {
        throw new \Exception("Cannot clone a singleton.");
    }

    /**
     * @throws \Exception
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }

    /**
     * @return raiffeisen
     */
    public static function getInstance(): raiffeisen
    {
        if (self ::$instance ?? null) {
            self ::$instance = new self();
        }
        return self ::$instance;
    }

    /**
     * @param $summ
     * @param $goods_type
     */
    private function recordNewPay($summ, $goods_type): void
    {
        global $DB;
        try {
            $sqlObjParam = $this -> createRecordStdClass($summ, $goods_type);
            $DB -> insert_record('student_pays', $sqlObjParam);
        } catch (dml_exception $e) {
        }
    }

    /**
     * @param $summ
     * @param $goods_type
     * @param int $status
     * @return stdClass
     */
    private function createRecordStdClass($summ, $goods_type, $status = 1)
    {
        global $USER;
        $record = new stdClass();
        $record -> userid = $USER -> id;
        $record -> timecreate = time();
        $record -> timemodified = time();
        $record -> amount = $summ;
        $record -> goods_type = $goods_type;
        $record -> status = $status;
        return $record;
    }

    /**
     * @param $summ
     * @param $goods_type
     * @param $pay_type
     * @return bool
     */
    private function validateFormData($summ, $goods_type, $pay_type): bool
    {
        if ($this -> validateNumber($summ) && $this -> validateGoodType($goods_type) && $this -> validatePayType($pay_type)) {
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
        return preg_match('/\d++/s', $num);
    }

    /**
     * @param $str
     * @return bool
     */
    private function validateGoodType($str): bool
    {
        return preg_match('/^type[1-2]$/s', $str);
    }

    /**
     * @param $str
     * @return bool
     */
    private function validatePayType($str): bool
    {
        return preg_match('/^type[1-2]$/s', $str);
    }

    /**
     * @param $summ
     * @param $goods_type
     * @param $pay_type
     */
    public function createPay($summ, $goods_type, $pay_type): void
    {
        if ($this -> validateFormData($summ, $goods_type, $pay_type)) {
            $this -> recordNewPay($summ, $goods_type);
        }
    }
}