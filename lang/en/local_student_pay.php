<?php
defined('MOODLE_INTERNAL') || die();

$string['student_pay:viewandpay'] = 'Видеть страницу оплаты и производить оплату';

$string['pluginname'] = 'Оплата обучения';
$string['summ_error'] = 'Пожалуйста, заполните сумму!';
$string['pay_summ_text'] = 'Сумма к оплате (руб.)';
$string['submit_button_text'] = 'Оплатить';
$string['submit_button_text_raiffeisen'] = 'Оплата через raiffeisen';

$string['sber_user'] = 'Пользователь подключения к Сбербанк';
$string['sber_pass'] = 'Пароль подключения к Сбербанк';
$string['sber_url'] = 'Ссылка подключения к Сбербанк';

$string['ws_user'] = 'Пользователь подключения к 1С';
$string['ws_pass'] = 'Пароль подключения к 1С';
$string['ws_timeout'] = 'Таймаут подключения к 1С (в секундах)';

$string['status_default'] = 'Статус по умолчанию для нового платежа';

$string['status_new'] = 'Новый';
$string['status_sended1c'] = 'Отправлен в 1С';
$string['status_paid'] = 'Оплачен';
$string['status_canceled'] = 'Отменён';
$string['status_error'] = 'Ошибка';

$string['sber_returnpage'] = 'Страница для удачной оплаты';
$string['sber_failpage'] = 'Страницы для ошибки оплаты';

$string['sber_codes_normal'] = 'Коды оплаты находящейся в процессе, из Сбербанка';
$string['sber_codes_fail'] = 'Коды ошибок или отмен оплаты, из Сбербанка';

$string['sber_registrurl'] = 'Страница регистрации оплаты';
$string['sber_statusurl'] = 'Страница получения статуса оплаты';

$string['sber_measure'] = 'Штука';

$string['access_deny'] = 'Доступ запрещен';

$string['criticalerror'] = 'Возникла ошибка. Пожалуйста, попробуйте позже или обратитесь в поддержку';
$string['payok'] = 'Операция выполнена';
$string['payerror'] = 'Операция не выполнена';

$string['invoice'] = 'Счёт ';

$string['goods_name'] = 'Образовательные услуги';
$string['view_page'] = 'Смотрит страницу оплаты';
$string['do_pay'] = 'Производит оплату';


$string['goods_name_type1'] = 'Оплата за обучение';
$string['goods_name_type2'] = 'Оплата за доп. услугу';

//LifePay
$string['life_pay_heading'] = 'LifePay настройки';
$string['life_pay_url'] = 'LifePay URL';
$string['life_pay_apikey'] = 'LifePay apikey';
$string['life_pay_login'] = 'LifePay login';
$string['life_pay_cashier_name'] = 'Имя кассира';
$string['life_pay_cashier_name_default'] = 'Кассир';
$string['life_pay_target_serial'] = 'Серийный номер принтера';
$string['life_pay_product_name'] = 'Наименование товарной позиции'; // по умолчанию $string['goods_name']

//Raiffeisen
$string['rai_pay_success'] = "Оплата совершена успешно!";
$string['rai_pay_error'] = "Оплата не совершена, попробуйте еще раз!";
$string['pay_type_rai'] = "Оплатить через Райффайзенбанк";
$string['pay_type_sber'] = "Оплатить через Сбербанк";