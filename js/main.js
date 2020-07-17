require(['jquery'], function ($) {
    let summEl = $('#id_summ'),
        pay_submitEl = $('#id_submitbutton');
    $('#pay_container form').submit(function () {
        if (summEl.val() != '')
            pay_submitEl.attr('disabled', 'disabled');
    });
    summEl.keyup(function () {
        this.value = this.value.replace(/[^\d\s]+/, "")
    });
});