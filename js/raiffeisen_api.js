const ready = () => {
    const pay_form = document.querySelector('.pay_form > form');

    pay_form.addEventListener('submit', function(e) {
        e.preventDefault();

        const payment = new PaymentPageSdk('000001780357001-80357001'); //publicId

        const amount = 100;
        const failUrl = '/local/student_pay/view.php';
        const successUrl = '/local/student_pay/view.php';
        const orderId = 1;

        payment.openPopup({
            orderId: orderId,
            amount: amount,
            successUrl: successUrl+`?orderId=${orderId}`,
            failUrl: failUrl+`?orderId=${orderId}`,
        });
    });
};

document.addEventListener("DOMContentLoaded", ready);