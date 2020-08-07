<?php
require_once('../../config.php');

if (!isloggedin() or isguestuser()) {
    require_login();
    die;
}

$context_sys = context_system ::instance();
if (!has_capability('local/student_pay:viewandpay', $context_sys)) {
    redirect($CFG -> wwwroot);
    die;
}

require_once("locallib.php");

$PAGE -> set_url('/local/student_pay/view.php');

$PAGE -> set_context($context_sys);
$PAGE -> set_pagelayout('standard');
$title = get_string('pluginname', 'local_student_pay');
$PAGE -> navbar -> add($title);
$PAGE -> set_heading($title);
$PAGE -> set_title($title);
$PAGE -> set_cacheable(false);
$PAGE -> requires -> css('/local/student_pay/styles.css');
$PAGE -> requires -> css(new moodle_url('https://test.ecom.raiffeisen.ru/pay/sdk/v2/payment.min.css'));
$PAGE -> requires -> css('/local/student_pay/js/raiffeisen/popup/popup.css');

$mform = new pay_form;

$pay_result = null;
if ($fromform = $mform -> get_data())
    if ($fromform -> pay_type == 'type1') {
        $pay_result = student_pay ::do_pay($fromform -> summ, $fromform -> goods_type); // если что-то вернул, значит ошибка
    }

$event = \local_student_pay\event\student_pay_viewed ::create(array(
    'objectid' => null,
    'context' => $context_sys,
));
$event -> trigger();

echo $OUTPUT -> header();


// if(!is_siteadmin() && $USER->id != 14404){
// echo 'Извините, оплата временно недоступна из-за проведения технических работ. Пожалуйста, попробуйте немного позже.';
// echo $OUTPUT->footer();
// die;
// }


if ($pay_result != null)
    \core\notification ::add(get_string('criticalerror', 'local_student_pay'));
elseif (isset($_GET['fail']))
    \core\notification ::warning(get_string('payerror', 'local_student_pay'));
elseif (isset($_GET['ok']))
    \core\notification ::add(get_string('payok', 'local_student_pay'), \core\notification::INFO);

echo html_writer ::start_tag('div', array(
    'id' => 'pay_container'
)),

html_writer ::start_tag('div', array(
    'class' => 'pay_info alert alert-info alert-block fade in'
)),

get_string('invoice', 'local_student_pay'),

html_writer ::start_tag('b'),
fullname($USER),
html_writer ::end_tag('b'),

html_writer ::end_tag('div'),

html_writer ::start_tag('div', array(
    'class' => 'pay_form'
));

$mform -> display();

echo html_writer ::end_tag('div'),

html_writer ::end_tag('div');

require("faq.php");

$PAGE -> requires -> js('/local/student_pay/js/main.js');
$PAGE -> requires -> js('/local/student_pay/js/payment.min.js');

//e-commerce url https://e-commerce.raiffeisen.ru/pay/
echo "<script type='module' src='/local/student_pay/js/raiffeisen/api.js'></script>";

echo $OUTPUT -> footer();

?>