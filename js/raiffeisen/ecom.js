/*
 * Description actions
 * @author vshapovalov
 * @date 30/7/2020
 * @copyright 2020 Moscow Witte University. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package moodle
 */

// noinspection JSUnresolvedFunction
import {promiseSendFormData} from "./promise_handler.js";

const ecom = (orderId, pay_form, amount) => {
    // noinspection JSUnresolvedFunction
    const payment = new PaymentPageSdk('000001780357001-80357001', {url: 'https://test.ecom.raiffeisen.ru/pay'});

// noinspection JSValidateTypes
    require(['core/notification'], function (Notification) {
        // noinspection JSUnresolvedFunction
        payment.openWindow({
            orderId: orderId,
            amount: amount
        }).then(function () {
            let formData = new FormData(pay_form);
            formData.append('orderId', `${orderId}`);
            Notification.addNotification({
                message: "Оплата совершена успешно!",
                type: "success"
            });
            promiseSendFormData(formData).catch(error => {
                throw error;
            })
        }).catch(function () {
            Notification.addNotification({
                message: "Оплата не совершена, попробуйте еще раз!",
                type: "error"
            });
        });
    });
}

export default ecom;


