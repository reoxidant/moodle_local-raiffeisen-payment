<?php
/**
 * Get information about order for pay
 * @author vshapovalov
 * @date 24/7/2020
 * @copyright 2020 Moscow Witte University. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package moodle
 */

//defined('MOODLE_INTERNAL') || die();

class raiffeisen_order
{
    private static $instance;

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
        return $this -> databaseResult();
    }

    private function databaseResult()
    {
        global $DB;
        return $DB -> get_record_sql("SELECT MAX(id) FROM {student_pays}");
    }

}

if ($_POST['key'] ?? null) {
    if ($_POST['key'] === 'new') {
        $order = raiffeisen_order ::getInstance();
        return $order -> getOrderId();
    } else {
        throw new \moodle_exception('Ошибка при получении параметра: orderId');
    }
}