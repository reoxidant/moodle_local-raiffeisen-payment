const ready = () => {
    const pay_form = document.querySelector('.pay_form > form');
    const selector = document.querySelector('#id_pay_type');

    pay_form.addEventListener('submit', function (e) {
        e.preventDefault();

        if (selector.options[selector.selectedIndex].value == 'type2') {
            //const payment = new PaymentPageSdk('000001780357001-80357001'); //publicId
            const payment = new PaymentPageSdk('000001780049001-80049001');

            const amount = 100;
            const orderId = 1;

            require(['core/notification'], function (Notification) {
                payment.openPopup({
                    orderId: orderId,
                    amount: amount
                }).then(function () {
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
        }
    });
};

document.addEventListener("DOMContentLoaded", ready);