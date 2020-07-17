<?php

namespace local_student_pay\event;
defined('MOODLE_INTERNAL') || die();

class student_pay_do extends \core\event\base
{

    /**
     * Init method.
     *
     * @return void
     */
    protected function init()
    {
        $this -> data['crud'] = 'r';
        $this -> data['edulevel'] = self::LEVEL_PARTICIPATING;
    }

    public function get_url()
    {
        return new \moodle_url('/local/student_pay/view.php');
    }

    public static function get_name()
    {
        return get_string('do_pay', 'local_student_pay');
    }
}