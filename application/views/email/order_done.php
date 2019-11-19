<html>
<head>
  <title>Заказ оформлен.</title>
</head>
<body>
Заказ <span  class="bold">№<?=$order->get_order_number()?></span> успешно оформлен.<br>
С Вами свяжется менеджер в ближайшее время.<br><br>
<span  class="bold">Позиции в заказе:</span><br>
<?php foreach($order->orderitems->find_all()->as_array() as $orderitem): ?>
	<?=$orderitem->brand?> <?=$orderitem->article?> <?=$orderitem->name?> <?=$orderitem->amount?>шт. на сумму <?=$orderitem->sale_per_unit*$orderitem->amount?> грн.<br>
<?php endforeach; ?><br>
Спасибо за заказ!<br><br>
Сайт: <a href="http://ulc.com.ua">http://ulc.com.ua</a><br>
Эл. почта: <a href="mailto:office@eparts.kiev.ua">office@eparts.kiev.ua</a><br>
(044) 361-96-64<br>                                                                   
(067) 291-18-25<br>
(095) 053-00-35<br>
(063) 631-84-39

</body>
</html>