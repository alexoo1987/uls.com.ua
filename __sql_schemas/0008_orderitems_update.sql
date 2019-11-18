UPDATE orderitems
LEFT JOIN orders ON orderitems.order_id = orders.id
SET orderitems.delivery_price = 0
WHERE
	DATE(orders.date_time) >= '2016-04-11'