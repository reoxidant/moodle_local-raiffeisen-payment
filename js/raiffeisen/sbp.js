/*
 * Description actions
 * @author vshapovalov
 * @date 30/7/2020
 * @copyright 2020 Moscow Witte University. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package moodle
 */

import {promiseSendFormData} from "./promise_handler.js";
import {
    addGeneratedQrCodeToThePopup,
    addPopupEventOnCloseWindow,
    getPopupNodeHtml,
    insertPopupToPageContainer
} from "./popup/popup.js";

const sbp = async (orderId, pay_form) => {

    const popup = getPopupNodeHtml();

    insertPopupToPageContainer(popup);
    addPopupEventOnCloseWindow(popup);

    const formData = new FormData(pay_form);
    formData.append('orderId', `${orderId}`);

    //TODO: append data to popup
    await promiseSendFormData(formData)
        .then(response => response.json())
        .then(data => {
            console.log(data);
            addGeneratedQrCodeToThePopup(popup, data)
        })
        .catch(error => {
            if (error) {
                throw error;
            }
        })
}

export default sbp;