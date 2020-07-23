const ready = () => {
    const pay_form = document.querySelector('.mform');
    const selector = document.querySelector('#id_pay_type');

    pay_form.addEventListener('submit', function (e) {
        if (selector.options[selector.selectedIndex].value === 'type2') {
            e.preventDefault();
            //const payment = new PaymentPageSdk('000001780357001-80357001'); //publicId
            // noinspection JSUnresolvedFunction
            const payment = new PaymentPageSdk('000001780049001-80049001');

            const amount = document.querySelector('#id_summ').value;
            const orderId = 1;

            // noinspection JSValidateTypes
            require(['core/notification'], function (Notification) {
                // noinspection JSUnresolvedFunction
                payment.openPopup({
                    orderId: orderId,
                    amount: amount
                }).then(function () {
                    let formData = new FormData(pay_form);
                    Notification.addNotification({
                        message: "Оплата совершена успешно!",
                        type: "success"
                    });
                    xhrSender(formData);
                }).catch(function () {
                    let formData = new FormData(pay_form);
                    Notification.addNotification({
                        message: "Оплата не совершена, попробуйте еще раз!",
                        type: "error"
                    });
                    xhrSender(formData);
                });
            });
        }
    });
};

const xhrSender = (form_data) => {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '/local/student_pay/lib/raiffeisen_record.php', true);
    xhr.send(form_data);
    if (xhr.status !== 200) {
        // обработать ошибку
        console.log("Error " + xhr.status + ': ' + xhr.statusText);
    }
}

document.addEventListener("DOMContentLoaded", ready);