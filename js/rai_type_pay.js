/*
 * Description actions
 * @author vshapovalov
 * @date 30/7/2020
 * @copyright 2020 Moscow Witte University. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package moodle
 */


export const createNewInputRaiPay = () => {
    let pay_selector = document.createElement('div');
    pay_selector.innerHTML =
        `<div id="fitem_rai_type_pay" class="form-group row fitem femptylabel">
            <div class="col-md-3">
                <span class="float-sm-right text-nowrap"></span>
            </div>
            <div class="col-md-9 form-inline felement" data-fieldtype="select">
                <select class="custom-select" name="raiffeisen_type_pay" id="rai_type_pay">
                    <option value="type1" selected="">Система быстрых платежей по QR коду</option>
                    <option value="type2">Оплата по банковской карте</option>
                </select>
            </div>
        </div>`;

    pay_selector.setAttribute('id', 'pay_selector');

    return pay_selector;
}

export default createNewInputRaiPay();


