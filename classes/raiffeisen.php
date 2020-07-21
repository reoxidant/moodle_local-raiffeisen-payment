<?php

namespace classes;

use stdClass;

class raiffeisen
{

    private function recordNewPay($summ, $goods_type): void
    {
        global $DB;

        try {
            $sqlObjParam = $this -> createRecordStdClass($summ, $goods_type);
            $DB -> insert_record('student_pays', $sqlObjParam);
        } catch (\dml_exception $e) {
        }
    }

    private function createRecordStdClass($summ, $goods_type, $status = 0)
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

    private function validateFormData($summ, $goods_type, $pay_type): bool
    {
        if ($this -> validateNumber($summ) && $this -> validateGoodType($goods_type) && $this -> validatePayType($pay_type))
            return true;
        else
            return false;
    }

    private function validateNumber($num): bool
    {
        return preg_match('/\d++/m', $num);
    }

    private function validateGoodType($str): bool
    {
        return preg_match('/\^type\d{1,2}$/g', $str);
    }

    private function validatePayType($str): bool
    {
        return preg_match('/^type\d{1,2}$/g', $str);
    }

    public function createPay($summ, $goods_type, $pay_type): void
    {
        if ($this -> validateFormData($summ, $goods_type, $pay_type)) {
            $this -> recordNewPay($summ, $goods_type);
        }
    }
}