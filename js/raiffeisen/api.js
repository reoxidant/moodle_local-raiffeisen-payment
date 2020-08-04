/*
 * Description actions
 * @author vshapovalov
 * @date 30/7/2020
 * @copyright 2020 Moscow Witte University. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package moodle
 */

import {promiseGetOrderId} from './promise_handler.js';
import {showPopup} from './popup/popup.js';

const ready = () => {
    const pay_form = document.querySelector('.mform');
    const selector = document.querySelector('#id_pay_type');


    pay_form.addEventListener('submit', function (e) {
        if (selector.options[selector.selectedIndex].value === 'type2') {
            e.preventDefault();

            const amount = document.querySelector('#id_summ').value;

            promiseGetOrderId('new').then((orderId) => {

                if (typeof orderId !== "number" || !orderId) {
                    throw new Error("Ошибка выполнения запроса!");
                } else {
                    orderId++;
                }

                showPopup(orderId, pay_form, amount).catch(err => {
                    throw new Error(err);
                });
            });
        }
    });
};

document.addEventListener("DOMContentLoaded", ready);