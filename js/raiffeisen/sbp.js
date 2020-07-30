/*
 * Description actions
 * @author vshapovalov
 * @date 30/7/2020
 * @copyright 2020 Moscow Witte University. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package moodle
 */

import {promiseSendFormData} from "./promise_handler";

const sbp = (pay_form) => {
    // noinspection JSValidateTypes
    require(['core/notification'], function (Notification) {
        const formData = new FormData(pay_form);
        promiseSendFormData(formData).catch(error => {

            const preview = `
                <a href="#">
                <img alt="QR_SBP.png" src="#" 
                     style=" margin-left: 8px; /* width: 258px; */ box-shadow: 0 0 10px rgba(0,0,0,0.5); " 
                     title="Оплатить по QR-коду">
                </a>`;

            document.write(preview);

            if (error) {
                Notification.addNotification({
                    message: "Оплата не совершена, попробуйте еще раз!",
                    type: "error"
                });
            } else {
                Notification.addNotification({
                    message: "Оплата совершена успешно!",
                    type: "success"
                });
            }
        })
    });
}

export default sbp;