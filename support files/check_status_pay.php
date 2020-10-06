<?php

require_once('../../../config.php');
require_once("../../../lib/classes/notification.php");

if ($_REQUEST['orderId'] ?? null) {
    try {
        if (validate($_REQUEST['orderId'])) {
            check_status($_REQUEST['orderId']);
        } else {
            \core\notification ::error('Введены неверные данные!');
        }
    } catch (Exception $e) {
        echo 'Запрос не прошел валидацию:', $e -> getMessage(), '\n';
    }
}

/**
 * @param $id
 */
function validate($id)
{
    return preg_match('/\d{0,10}/m', $id);
}

function check_status($id)
{
    $ch = curl_init();
    $fp_err = fopen($_SERVER['DOCUMENT_ROOT'] . '/verbose_file.txt', 'ab+');
    fwrite($fp_err, date('Y-m-d H:i:s') . "\n\n"); //add timestamp to the verbose log
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_FAILONERROR, true);
    curl_setopt($ch, CURLOPT_STDERR, $fp_err);
    curl_setopt($ch, CURLOPT_URL, 'https://test.ecom.raiffeisen.ru/api/payments/v1/orders/' . $id . '/transaction');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


    $headers = array();
    $headers[] = 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwMDAwMDE3ODAzNTcwMDEtODAzNTcwMDEiLCJqdGkiOiIzMGQ2MjM4Yi03MjY3LTRlNWEtOGEwYi04OGY3NTRhNmQ4MTYifQ.Douj-vNUWCS9AA_CfurLqZ2kPwODKIovMPKHzrM3D0A';
    $headers[] = 'Content-Type: application/json';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = json_decode(curl_exec($ch));

    $error = null;

    if (curl_errno($ch)) {
        $error = 'Error:' . curl_error($ch);
    }

    curl_close($ch);

    $status = toString($result -> transaction -> status -> value);

    if ($error ?? null) {
        \core\notification ::error($error);
    } else if ($status === "SUCCESS") {
        \core\notification ::success('Ваш заказ успешно оплачен!');
    } else if ($status === "NOT_FOUND") {
        \core\notification ::warning("Ваша транзакция не найдена, оплатите еще раз!");
    }
}

function toString($value)
{
    try {
        return (string)$value;
    } catch (Exception $e) {
        return $e;
    }
}