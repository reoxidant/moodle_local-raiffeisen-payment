/*
 * Description actions
 * @author vshapovalov
 * @date 30/7/2020
 * @copyright 2020 Moscow Witte University. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package moodle
 */

import createNewInputRaiPay from './type_pay';
import {promiseGetOrderId} from './promise_handler';
import ecom from "./ecom";
import sbp from "./sbp";

const ready = () => {
    const pay_form = document.querySelector('.mform');
    const selector = document.querySelector('#id_pay_type');
    const button_form = document.querySelector('#fitem_id_submitbutton');
    let pay_selector = createNewInputRaiPay();

    selector.addEventListener('change', (e) => {
        if (e.target[selector.selectedIndex].value === 'type2') {
            pay_form.insertBefore(pay_selector, button_form);
        } else {
            pay_form.removeChild(pay_selector);
        }
    });

    pay_form.addEventListener('submit', function (e) {
        if (selector.options[selector.selectedIndex].value === 'type2') {
            e.preventDefault();

            promiseGetOrderId('new').then((orderId) => {

                if (typeof orderId !== "number" || !orderId) {
                    throw new Error("Ошибка выполнения запроса!");
                } else {
                    orderId++;
                }

                const rai_type_pay = document.querySelector('#rai_type_pay');

                (rai_type_pay.options[rai_type_pay.selectedIndex].value === "type1") ? sbp() : ecom(orderId, pay_form);
            });
        }
    });
};

document.addEventListener("DOMContentLoaded", ready);