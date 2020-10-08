<?php
/**
 * Description actions
 * @copyright 2020 vshapovalov
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package PhpStorm
 */

try {
    $config['rai_api_url'] = "https://e-commerce.raiffeisen.ru/";
    $config['rai_api_secret_key_sbr'] = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJNQTAwMDAwNDU2MDQiLCJqdGkiOiJiNDNlYTA2My0xMzViLTQ5YTAtOGVlYy05ODc1ODhlZDg3MjUifQ.2hra9mkvuPGkdCg1NouyJiSXQRDb4V4iQ1Elriuu4H0";
    $config['rai_sbp_merchant_id'] = "MA0000045604";
    $ch = curl_init();
    $params = [
        "amount" => 999,
        "createDate" => date('Y-m-d\TH:i:s.uP'),
        "currency" => "RUB",
        "order" => 99,
        "paymentDetails" => "Оплата за обучение",
        "qrType" => "QRDynamic",
        "qrExpirationDate" => date('Y-m-d\TH:i:s.uP', time() + 900),
        "sbpMerchantId" => $config["rai_sbp_merchant_id"]
    ];

    curl_setopt($ch, CURLOPT_URL, $config['rai_api_url'] . '/api/sbp/v1/qr/register');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));

    $headers = array();
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Authorization: Bearer ' . $config['rai_api_secret_key_sbr'];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = json_decode(curl_exec($ch));

    if ($result === false) {
        $curl_error = curl_error($ch);
        curl_close($ch);

        if (isset($curl_error))
            throw new Exception("Ошибка curl: ", $curl_error);
        else
            throw new Exception("Ошибка при совершении запроса", $curl_error);
    }

    curl_close($ch);

    return get_object_vars($result);

} catch (Exception $error) {
    throw new Exception("Извините, возникла ошибка, попробуйте позже", $error);
}