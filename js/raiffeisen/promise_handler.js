/*
 * Description actions
 * @author vshapovalov
 * @date 30/7/2020
 * @copyright 2020 Moscow Witte University. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package moodle
 */

const promiseGetOrderId = async (formData) => {
    if (formData.get('key') !== null && formData.get('key') === "new") {
        const requestParam = {
            method: 'POST',
            body: formData
        }

        return await fetch('/local/student_pay/lib/raiffeisen_requests_manager.php', requestParam);
    }
}

const promiseSendFormData = async (form_data) => {

    const requestParam = {
        method: 'POST',
        body: form_data
    }

    return await fetch('/local/student_pay/lib/raiffeisen_requests_manager.php', requestParam);
}

export {promiseGetOrderId, promiseSendFormData};
