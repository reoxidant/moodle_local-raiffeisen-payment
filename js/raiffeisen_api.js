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

            getOrderId('new').then((orderId) => {

                if (typeof orderId !== "number" || !orderId) {
                    throw new Error("Ошибка выполнения запроса!");
                } else {
                    orderId++;
                }

                // noinspection JSUnresolvedFunction
                const payment = new PaymentPageSdk('000001780357001-80357001', {url: 'https://test.ecom.raiffeisen.ru/pay'});

                const amount = document.querySelector('#id_summ').value;

                console.log("orderId: " + orderId)

                // noinspection JSValidateTypes
                require(['core/notification'], function (Notification) {
                    // noinspection JSUnresolvedFunction
                    payment.openPopup({
                        orderId: orderId,
                        amount: amount
                    }).then(function () {
                        let formData = new FormData(pay_form);
                        formData.append('orderId', `${orderId}`);
                        Notification.addNotification({
                            message: "Оплата совершена успешно!",
                            type: "success"
                        });
                        promiseSender(formData).catch(error => {
                            throw error;
                        })
                    }).catch(function () {
                        Notification.addNotification({
                            message: "Оплата не совершена, попробуйте еще раз!",
                            type: "error"
                        });
                    });
                });
            });
        }
    });
};

const getOrderId = async (keyName) => {
    if (keyName !== null && keyName === "new") {
        const requestParam = {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
            },
            body: 'key=' + keyName
        }

        return await fetch('/local/student_pay/lib/raiffeisen_order.php', requestParam)
            .then((response) => response.text())
            .then((responseData) => {
                return parseInt(responseData, 10);
            }).catch((error) => {
                throw error;
            })
    }
}

const promiseSender = async (form_data) => {

    const requestParam = {
        method: 'POST',
        body: form_data
    }

    await fetch('/local/student_pay/lib/raiffeisen_record.php', requestParam);
}

document.addEventListener("DOMContentLoaded", ready);