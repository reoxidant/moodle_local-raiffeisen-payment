/*
 * JS Library of Raiffeisen Bank
 * @author vshapovalov
 * @date 23/7/2020
 * @copyright 2020 Moscow Witte University. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package moodle
 */

const ready = () => {
    const pay_form = document.querySelector('.mform');
    const selector = document.querySelector('#id_pay_type');

    pay_form.addEventListener('submit', function (e) {
        if (selector.options[selector.selectedIndex].value === 'type2') {
            e.preventDefault();

            const orderId = getOrderId('new');

            if (typeof orderId !== "number" && !orderId) {
                throw new Error("Ошибка выполнения запроса!");
            }

            // noinspection JSUnresolvedFunction
            const payment = new PaymentPageSdk('000001780357001-80357001', {url: 'https://test.ecom.raiffeisen.ru/pay'});

            const amount = document.querySelector('#id_summ').value;

            // noinspection JSValidateTypes
            require(['core/notification'], function (Notification) {
                // noinspection JSUnresolvedFunction
                payment.openPopup({
                    amount: amount
                }).then(function () {
                    let formData = new FormData(pay_form);
                    Notification.addNotification({
                        message: "Оплата совершена успешно!",
                        type: "success"
                    });
                    xhrSender(formData);
                }).catch(function () {
                    Notification.addNotification({
                        message: "Оплата не совершена, попробуйте еще раз!",
                        type: "error"
                    });
                });
            });
        }
    });
};

const getOrderId = (keyName) => {
    if (keyName !== null) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '/local/student_pay/lib/raiffeisen_order.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.send('key=' + keyName);

        // responseType должно быть пустой строкой, либо "text"
        xhr.responseType = 'text';

        xhr.onload = function () {
            if (xhr.readyState === xhr.DONE) {
                if (xhr.status === 200) {
                    return xhr.response;
                }
            }
        }
    }
}

const xhrSender = (form_data) => {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '/local/student_pay/lib/raiffeisen_record.php', true);
    xhr.send(form_data);
    if (xhr.status !== 200) {
        console.log("Error " + xhr.status + ': ' + xhr.statusText);
    }
}

document.addEventListener("DOMContentLoaded", ready);