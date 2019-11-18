<html>
<head>
  <title>Изменение статуса.</title>
</head>
<body>
В заказе <span  class="bold">№<?=$orderitem->order->get_order_number()?></span> изменен статус у позиции<br>
<?=$orderitem->brand?> <?=$orderitem->article?> <?=$orderitem->name?> <?=$orderitem->amount?>шт. на сумму <?=$orderitem->sale_per_unit*$orderitem->amount?> грн.<br>
с <span  class="bold">"<?=$oldstatus->name?>"</span> на <span  class="bold">"<?=$newstatus->name?>"</span><br><br>
Сайт: <a href="http://ulc.com.ua">http://ulc.com.ua</a><br>
Эл. почта: <a href="mailto:office@eparts.kiev.ua">office@eparts.kiev.ua</a><br>
(044) 361-96-64<br>                                                                   
(067) 291-18-25<br>
(095) 053-00-35<br>
(063) 631-84-39
</body>
</html>