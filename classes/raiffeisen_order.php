<?php
/**
 * Description actions
 * @author vshapovalov
 * @date 29/7/2020
 * @copyright 2020 Moscow Witte University. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package moodle
 */

namespace classes;

defined('MOODLE_INTERNAL') || die;

class raiffeisen_order
{
    // --Commented out by Inspection (30.07.2020 17:25):private static $instance;

    protected function __construct()
    {
    }

    public static function getInstance(): raiffeisen_order
    {
        if (self ::$instance === null) {
            self ::$instance = new self();
        }
        return self ::$instance;
    }

    public function getOrderId(): int
    {
        return intval($this -> databaseResult() -> max);
    }

    private function databaseResult()
    {
        global $DB;
        return $DB -> get_record_sql("SELECT MAX(Id) FROM {student_pays}");
    }
}