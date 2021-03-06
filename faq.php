<?php
defined('MOODLE_INTERNAL') || die();
?>
<p style="text-align: center">После оплаты электронная копия чека будет отправлена на почтовый адрес
    <b><?php echo $USER -> email ?></b></p>
<h3>1. Процесс платежа</h3>
<p>Оплата происходит через авторизационный сервер Процессингового центра Банка с использованием Банковских кредитных
    карт следующих платежных систем:</p>
<ul>
    <li>VISA International (<img src="images/visa_m.png"/> VISA International)</li>
    <li>MasterCard World Wide (<img src="images/mastercard_m.png"/> MasterCard World Wide)</li>
</ul>
<h3>2. Процесс передачи данных</h3>
<p>Для оплаты покупки Вы будете перенаправлены на платежный шлюз ОАО "Сбербанк России" для ввода реквизитов Вашей карты.
    Пожалуйста, приготовьте Вашу пластиковую карту заранее. Соединение с платежным шлюзом и передача информации
    осуществляется в защищенном режиме с использованием протокола шифрования SSL.</p>
<p>В случае если Ваш банк поддерживает технологию безопасного проведения интернет-платежей Verified By Visa или
    MasterCard Secure Code для проведения платежа также может потребоваться ввод специального пароля. Способы и
    возможность получения паролей для совершения интернет-платежей Вы можете уточнить в банке, выпустившем карту.</p>
<p>Настоящий сайт поддерживает 256-битное шифрование. Конфиденциальность сообщаемой персональной информации
    обеспечивается ОАО "Сбербанк России". Введенная информация не будет предоставлена третьим лицам за исключением
    случаев, предусмотренных законодательством РФ. Проведение платежей по банковским картам осуществляется в строгом
    соответствии с требованиями платежных систем Visa Int. и MasterCard Europe Sprl.</p>

<h3>Оплата по банковским картам VISA</h3>

<p>К оплате принимаются все виды платежных карточек VISA, за исключением Visa Electron. В большинстве случаев карта Visa
    Electron не применима для оплаты через интернет, за исключением карт, выпущенных отдельными банками. О возможность
    оплаты картой Visa Electron Вам нужно выяснять у банка-эмитента Вашей карты.</p>
<img src="images/visa.png"/>
<h3>Оплата по кредитным картам MasterCard</h3>

<p>На сайте к оплате принимаются все виды MasterCard, за исключением Maestro.</p>
<img src="images/mastercard.png"/>
<p><b>Что нужно знать:</b></p>
<ul>
    <li>номер Вашей кредитной карты;</li>
    <li>cрок окончания действия Вашей кредитной карты, месяц/год;</li>
    <li>CVV код для карт Visa / CVC код для Master Card:</li>
</ul>
<p>3 последние цифры на полосе для подписи на обороте карты.</p>
<img src="images/cvc.png"/>
<p>Если на Вашей карте код CVC / CVV отсутствует, то, возможно, карта не пригодна для CNP транзакций (т.е. таких
    транзакций, при которых сама карта не присутствует, а используются её реквизиты), и Вам следует обратиться в банк
    для получения подробной информации.</p>

