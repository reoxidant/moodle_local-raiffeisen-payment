<?php
defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    /** @noinspection PhpIncludeInspection */
    require_once($CFG -> dirroot . '/local/student_pay/locallib.php');

    $settings = new admin_settingpage('local_student_pay', get_string('pluginname', 'local_student_pay'));
    /** @noinspection PhpUndefinedVariableInspection */
    $ADMIN -> add('localplugins', $settings);

    $name = 'local_student_pay/sber_url';
    $title = get_string('sber_url', 'local_student_pay');
    $default = 'https://securepayments.sberbank.ru/payment/rest/';
    $setting = new admin_setting_configtext($name, $title, null, $default);
    $settings -> add($setting);

    $name = 'local_student_pay/sber_user';
    $title = get_string('sber_user', 'local_student_pay');
    $setting = new admin_setting_configtext($name, $title, null, null);
    $settings -> add($setting);

    $name = 'local_student_pay/sber_pass';
    $title = get_string('sber_pass', 'local_student_pay');
    $setting = new admin_setting_configpasswordunmask($name, $title, null, null);
    $settings -> add($setting);

    $status_types = student_pay ::get_status_types(true);
    $default = 1;
    $settings -> add(new admin_setting_configselect('local_student_pay/status_new', get_string('status_default', 'local_student_pay'), null, $default, $status_types));

    $name = 'local_student_pay/sber_returnpage';
    $title = get_string('sber_returnpage', 'local_student_pay');
    $default = '/local/student_pay/result.php?ok';
    $setting = new admin_setting_configtext($name, $title, null, $default);
    $settings -> add($setting);

    $name = 'local_student_pay/sber_failpage';
    $title = get_string('sber_failpage', 'local_student_pay');
    $default = '/local/student_pay/result.php?fail';
    $setting = new admin_setting_configtext($name, $title, null, $default);
    $settings -> add($setting);

    $sber_codes = student_pay ::get_sber_codes();

    $default = array(0, 1, 5);
    $settings -> add(new admin_setting_configmultiselect('local_student_pay/sber_codes_normal', get_string('sber_codes_normal', 'local_student_pay'), null, $default, $sber_codes));

    $default = array(3, 4, 6);
    $settings -> add(new admin_setting_configmultiselect('local_student_pay/sber_codes_fail', get_string('sber_codes_fail', 'local_student_pay'), null, $default, $sber_codes));

    $name = 'local_student_pay/sber_registrurl';
    $title = get_string('sber_registrurl', 'local_student_pay');
    $default = 'register.do';
    $setting = new admin_setting_configtext($name, $title, null, $default);
    $settings -> add($setting);

    $name = 'local_student_pay/sber_statusurl';
    $title = get_string('sber_statusurl', 'local_student_pay');
    $default = 'getOrderStatusExtended.do';
    $setting = new admin_setting_configtext($name, $title, null, $default);
    $settings -> add($setting);

    $name = 'local_student_pay/ws_user';
    $title = get_string('ws_user', 'local_student_pay');
    $setting = new admin_setting_configtext($name, $title, null, null);
    $settings -> add($setting);

    $name = 'local_student_pay/ws_pass';
    $title = get_string('ws_pass', 'local_student_pay');
    $setting = new admin_setting_configpasswordunmask($name, $title, null, null);
    $settings -> add($setting);

    $name = 'local_student_pay/ws_timeout';
    $title = get_string('ws_timeout', 'local_student_pay');
    $default = '1';
    $setting = new admin_setting_configtext($name, $title, null, $default, PARAM_INT);
    $settings -> add($setting);


    // LifeAPI
    $settings -> add(new admin_setting_heading('life_pay',
        get_string('life_pay_heading', 'local_student_pay'), ''));

    $name = 'local_student_pay/life_pay_url';
    $title = get_string('life_pay_url', 'local_student_pay');
    $default = 'https://sapi.life-pay.ru/cloud-print/create-receipt';
    $setting = new admin_setting_configtext($name, $title, null, $default);
    $settings -> add($setting);

    $name = 'local_student_pay/life_pay_apikey';
    $title = get_string('life_pay_apikey', 'local_student_pay');
    $setting = new admin_setting_configpasswordunmask($name, $title, null, null);
    $settings -> add($setting);

    $name = 'local_student_pay/life_pay_login';
    $title = get_string('life_pay_login', 'local_student_pay');
    $setting = new admin_setting_configtext($name, $title, null, null);
    $settings -> add($setting);

    $name = 'local_student_pay/life_pay_cashier_name';
    $title = get_string('life_pay_cashier_name', 'local_student_pay');
    $default = get_string('life_pay_cashier_name_default', 'local_student_pay');
    $setting = new admin_setting_configtext($name, $title, null, $default);
    $settings -> add($setting);

    $name = 'local_student_pay/life_pay_target_serial';
    $title = get_string('life_pay_target_serial', 'local_student_pay');
    $setting = new admin_setting_configpasswordunmask($name, $title, null, null);
    $settings -> add($setting);

    $name = 'local_student_pay/life_pay_product_name';
    $title = get_string('life_pay_product_name', 'local_student_pay');
    $default = get_string('goods_name', 'local_student_pay');
    $setting = new admin_setting_configtext($name, $title, null, $default);
    $settings -> add($setting);

    $name = 'local_student_pay/rai_api_secret_key_ecom';
    $title = get_string('rai_api_secret_key_ecom', 'local_student_pay');
    $setting = new admin_setting_configtext($name, $title, null, null);
    $settings -> add($setting);

    $name = 'local_student_pay/rai_api_secret_key_sbp';
    $title = get_string('rai_api_secret_key_sbr', 'local_student_pay');
    $setting = new admin_setting_configtext($name, $title, null, null);
    $settings -> add($setting);

    $name = 'local_student_pay/rai_sbp_merchant_id';
    $title = get_string('rai_sbp_merchant_id', 'local_student_pay');
    $setting = new admin_setting_configtext($name, $title, null, null);
    $settings -> add($setting);

    $name = 'local_student_pay/rai_api_url';
    $title = get_string('rai_api_url', 'local_student_pay');
    $default = 'https://test.ecom.raiffeisen.ru';
    $setting = new admin_setting_configtext($name, $title, null, $default);
    $settings -> add($setting);
}
