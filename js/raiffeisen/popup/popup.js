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
                               <span type="span" class="header-payment-val"><div class="pay-val">${amount} <span width="20" height="26" class="pay-name-val"><svg viewBox="0 0 20 26" width="100%" height="100%"><path fill="#000" fill-rule="evenodd" d="M.828 12.752h2.988V.8h6.696c1.608 0 2.976.222 4.104.666 1.128.444 2.046 1.032 2.754 1.764a6.659 6.659 0 0 1 1.548 2.556c.324.972.486 1.974.486 3.006a9.07 9.07 0 0 1-.486 2.988 6.57 6.57 0 0 1-1.548 2.484c-.708.72-1.626 1.29-2.754 1.71-1.128.42-2.496.63-4.104.63H8.496v2.736h6.336v3.708H8.496V26h-4.68v-2.952H.828V19.34h2.988v-2.736H.828v-3.852z M10.044 12.752H8.496V4.544h1.548c.648 0 1.254.066 1.818.198.564.132 1.062.36 1.494.684.432.324.768.762 1.008 1.314s.36 1.236.36 2.052c0 .816-.12 1.482-.36 1.998s-.576.918-1.008 1.206c-.432.288-.93.486-1.494.594a9.666 9.666 0 0 1-1.818.162z"></path></svg></span></div></span>         
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

const addEcomEventOnClick = (popup) => {
    popup.querySelector("#card_pay").nextElementSibling.addEventListener('click', () => {
        ecom();
    });
}

const closePopup = (popup) => popup.remove();

const showPopup = async (orderId, pay_form, amount) => {
    console.log(amount, 'amount');
    const popup = getPopupNodeHtml(amount);

    insertPopupToPageContainer(popup);
    addPopupEventOnCloseWindow(popup);
    addEcomEventOnClick(popup);

    const formData = new FormData(pay_form);
    formData.append('orderId', `${orderId}`);

    await promiseSendFormData(formData)
        .then((response) => response.json())
        .then(data => {
            require(['core/notification'], function (Notification) {
                console.log(data);

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

export {getPopupNodeHtml, insertPopupToPageContainer, addPopupEventOnCloseWindow, addGeneratedQrCodeToThePopup, showPopup};