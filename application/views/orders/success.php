<div class="container">
	<h1><?=$title?></h1>
	<div class="alert alert-success"><?=$message?></div>

	<?php if (!empty($orderId)): ?>
	<div class="row">
		<div class="col-md-offset-5 col-md-2">
			<a class="btn btn-primary" style="width: 100%;" href="<?=Helper_Url::createUrl('liqpay/order_pay/'.$orderId);?>"></i>Оплатить заказ</a><br><br>
		</div>
	</div>
	<?php endif ?>
</div>