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

const ecom = (formData, orderId, amount) => {
    // noinspection JSUnresolvedFunction
    const payment = new PaymentPageSdk('000001780357001-80357001', {url: 'https://test.ecom.raiffeisen.ru/pay'});

    // noinspection JSValidateTypes
    require(['core/notification'], function (Notification) {
        // noinspection JSUnresolvedFunction
        formData.append('orderId', `${++orderId}`);
        formData.append('payment', 'ecom');
        promiseSendFormData(formData)
            .then(() => {
                payment.replace({
                    amount: amount,
                    orderId: ++orderId,
                    extra: {
                        url: 'https://test.ecom.raiffeisen.ru/pay'
                    },
                    style: {
                        button: {
                            backgroundColor: '#990105',
                            textColor: '#FFFFFF',
                            hoverTextColor: '#FFFFFF',
                            hoverBackgroundColor: '#372C84',
                            borderRadius: '0px'
                        },
                        header: {
                            logo: 'https://www.muiv.ru/bitrix/templates/muiv_v3/img/svg/logo_coub.svg',
                            titlePlace: 'right'
                        }
                    },
                    successUrl: 'http://moodle/local/student_pay/view.php',
                    failUrl: 'http://moodle/local/student_pay/view.php',
                    comment: 'Оплата обучения'
                });
            })
            .catch(() => {
                Notification.addNotification({
                    message: "Оплата не совершена, попробуйте еще раз!",
                    type: "error"
                });
            })
    });
}

export default ecom;


