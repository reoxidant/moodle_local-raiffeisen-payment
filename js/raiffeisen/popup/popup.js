/*
 * Description actions
 * @author vshapovalov
 * @date 31/7/2020
 * @copyright 2020 Moscow Witte University. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package moodle
 */

import {promiseSendFormData} from "./../promise_handler.js";
import ecom from "./../ecom.js";

const getPopupNodeHtml = (amount) => {
    const popup = document.createElement('div');
    popup.innerHTML =
        `
        <div class="popup_root">
            <div class="popup_cover">
                <div class="popup_wrap-list">
                    <div class="wrap-list_cross">
                        <div class="cross">✕</div>
                    </div>
                    <div class="wrap-list_content">
                        <div class="header-app">
                            <div class="header-info">
                                <div class="header-logo">
                                    <img id="logo" src="https://www.muiv.ru/bitrix/templates/muiv_v3/img/svg/logo_inline.svg" alt="logotype">
                                </div>
                               <span type="span" class="header-payment-val">
                                   <div class="pay-val">${amount} 
                                       <span width="20" height="26" class="pay-name-val">
                                           <img src="images/rai_rub.svg" alt="Rub Raiffeisen">
                                       </span>
                                   </div>
                               </span>         
                            </div>
                            <div class="pay-selector">
                               <input id="qr_pay" type="radio" name="opt_pay" checked>
                               <label for="qr_pay" class="radio-inline">QR-код</label>
                               <input id="card_pay" type="radio" name="opt_pay">
                               <label for="card_pay" class="radio-inline">Карта</label>
                            </div>
                        </div>
                        <div class="content-app"></div>
                        <div class="footer-app">
                              <div class="footer-section">
                                <div class="bottom-icon_section">
                                    <span class="rai-icon_text">
                                      <span width="24" height="24" class="rai-icon">
                                        <img src="images/rai_icon.svg" alt="Иконка Raiffeisen">
                                      </span>
                                      <span class="rai-text">
                                          <span width="84" height="24" class="svg-text">
                                             <img src="images/rai_icon_text.svg" alt="Текст Raiffeisen">
                                          </span>
                                      </span>
                                    </span>
                                    <span>
                                      <img
                                              src="https://e-commerce.raiffeisen.ru/pay/resources/pcidss.png"
                                              alt="pcidss"
                                              height="24"
                                              width="63"
                                      />
                                    </span>
                                    <span>
                                      <img
                                              src="https://e-commerce.raiffeisen.ru/pay/resources/visa.svg"
                                              alt="visa"
                                              height="24"
                                              width="46"
                                      />
                                    </span>
                                    <span>
                                      <img
                                              src="https://e-commerce.raiffeisen.ru/pay/resources/mastercard.svg"
                                              alt="mastercard"
                                              height="24"
                                              width="79"
                                      />
                                    </span>
                                    <span>
                                      <img src="https://e-commerce.raiffeisen.ru/pay/resources/mir.png" height="24" width="50" alt="mit"/>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    popup.setAttribute('id', 'popup-window')

    return popup;
}

const insertPopupToPageContainer = (popup) => {
    const pay_container = document.getElementById('pay_container');
    pay_container.appendChild(popup);
}

const addPopupEventOnCloseWindow = (popup) => {
    popup.addEventListener('click', (e) => {
        if (e.target.className === "cross" || e.target.className === "popup_cover") {
            popup.remove();
        }
    })
}

const addGeneratedQrCodeToThePopup = (popup, {payload, qrUrl}) => {
    const qrCodeElement = document.createElement('div');
    qrCodeElement.innerHTML = `
        <div class="qr-code_image">
            <a href="${payload}"><img alt="QR_SBP.png" src="${qrUrl}" title="Оплатить по QR-коду"></a>
        </div>
        <dvi class="qr-code_desc">
            <div class="desc-header"><span>Оплата через Систему Быстрых Платежей</span></div>
                <div class="desc-content">
                    <ol>
                        <li>Откройте приложение вашего банка.</li>
                        <li>Выберите &laquo;Оплата &nbsp по QR-коду&raquo;.</li>
                        <li>Отсканируйте QR и подтвердите оплату.</li>
                    </ol>
                </div>   
            </div>
        </dvi>`;


    qrCodeElement.setAttribute('id', 'qr-code');
    popup.getElementsByClassName("content-app")[0].appendChild(qrCodeElement);
}

const addEcomEventOnClick = (popup, formData, orderId, amount) => {
    popup.querySelector("#card_pay").nextElementSibling.addEventListener('click', () => {
        formData.delete('payment');
        ecom(formData, orderId, amount);
    });
}

const closePopup = (popup) => popup.remove();

const showPopup = async (orderId, formData, amount) => {
    const popup = getPopupNodeHtml(amount);

    insertPopupToPageContainer(popup);
    addPopupEventOnCloseWindow(popup);

    formData.append('orderId', `${orderId}`);
    formData.append('payment', 'sbp');

    addEcomEventOnClick(popup, formData, orderId, amount);

    await promiseSendFormData(formData)
        .then((response) => response.json())
        .then(data => {
            require(['core/notification'], function (Notification) {
                let {code, message} = data;

                if (code === "SUCCESS") {
                    addGeneratedQrCodeToThePopup(popup, {...data})
                } else {
                    closePopup(popup);
                    Notification.addNotification({
                        message: message,
                        type: "error"
                    })
                }
            });
        })
        .catch(error => {
            if (error) {
                throw error;
            }
        })
}

export {
    getPopupNodeHtml,
    insertPopupToPageContainer,
    addPopupEventOnCloseWindow,
    addGeneratedQrCodeToThePopup,
    showPopup
};