/*
 * Description actions
 * @author vshapovalov
 * @date 31/7/2020
 * @copyright 2020 Moscow Witte University. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package moodle
 */

const getPopupNodeHtml = () => {
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

const addGeneratedQrCodeToThePopup = (popup) => {
    const qrCodeElement = document.createElement('div');
    qrCodeElement.innerHTML = `
            <a href="#">
                <img alt="QR_SBP.png" src="#"
                     style="margin-left: 8px; width: 258px; box-shadow: 0 0 10px rgba(0,0,0,0.5);"
                     title="Оплатить по QR-коду">
            </a>`;

    qrCodeElement.setAttribute('id', 'qr-code');

    popup.appendChild(qrCodeElement);
}

export {getPopupNodeHtml, insertPopupToPageContainer, addPopupEventOnCloseWindow, addGeneratedQrCodeToThePopup};