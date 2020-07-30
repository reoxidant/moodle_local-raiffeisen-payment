/*
 * Description actions
 * @author vshapovalov
 * @date 30/7/2020
 * @copyright 2020 Moscow Witte University. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package moodle
 */

const promiseGetOrderId = async (keyName) => {
    if (keyName !== null && keyName === "new") {
        const requestParam = {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
            },
            body: 'key=' + keyName
        }

        return await fetch('/local/student_pay/lib/raiffeisen_requests_manager.php', requestParam)
            .then((response) => response.text())
            .then((responseData) => {
                return parseInt(responseData, 10);
            }).catch((error) => {
                throw error;
            })
    }
}

const promiseSendFormData = async (form_data) => {

    const requestParam = {
        method: 'POST',
        body: form_data
    }

    await fetch('/local/student_pay/lib/raiffeisen_requests_manager.php', requestParam);
}

export {promiseGetOrderId, promiseSendFormData};
