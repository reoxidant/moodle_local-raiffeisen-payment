const ready = () => {
    const pay_form = document.querySelector('.pay_form > form');

    pay_form.addEventListener('submit', function (e) {
        e.preventDefault();

        //const payment = new PaymentPageSdk('000001780357001-80357001'); //publicId
        const payment = new PaymentPageSdk('000001780049001-80049001');

        const amount = 100;
        const orderId = 1;

        require(['core/notification'], function (Notification) {
            payment.openPopup({
                orderId: orderId,
                amount: amount
            }).then(function () {
                // noinspection JSCheckFunctionSignatures
                Notification.addNotification({
                    message: "Оплата совершена успешно!",
                    type: "success"
                });
            }).catch(function () {
                Notification.addNotification({
                    message: "Оплата не совершена, попробуйте еще раз!",
                    type: "error"
                });
            });
        });
    });
};

document.addEventListener("DOMContentLoaded", ready);