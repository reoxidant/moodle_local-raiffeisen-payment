<?php
defined('MOODLE_INTERNAL') || die();

$capabilities = array(

    'local/student_pay:viewandpay' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => array(
            'manager' => CAP_ALLOW
        ),
    ),
);
