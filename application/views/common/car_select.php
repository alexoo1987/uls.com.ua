<!--<div class="car-select" id="fix-car">-->
	<?php $car_mod = Cookie::get('car_modification', NULL); ?>
	<?php if(empty($car_mod)): ?>
		<?php echo View::factory('common/car_select_form')->render(); ?>
	<?php else: ?>
		<?php if (Route::name(Request::current()->route()) == 'cat_modification'){
				echo View::factory('common/car_selected_block')->render();
			} else {
				echo View::factory('common/car_selected')->render();
			} ?>
	<?php endif; ?>
<!--</div>-->

<!--<div class="info_price_new" style="-->
<!--    border: 4px solid #429063;-->
<!--    -webkit-box-shadow: 0 3px 2px rgba(0, 0, 0, .15), 0 0 1px rgba(0, 0, 0, .15);-->
<!--    box-shadow: 0 3px 2px rgba(0, 0, 0, .15), 0 0 1px rgba(0, 0, 0, .15);-->
<!--    -webkit-border-radius: 4px;-->
<!--    border-radius: 4px;-->
<!--    padding: 15px;-->
<!--    -webkit-transition: .3s;-->
<!--    -ms-transition: .3s;-->
<!--    transition: .3s;-->
<!--    background: white;-->
<!--    margin-bottom:  40px;-->
<!--    text-align:  center;-->
<!--    font-size:  16px;-->
<!--    font-weight:  bold;-->
<!--"><p><i class="fa fa-warning green" style="font-size: 22px; color:  red; margin-right: 15px;"></i>Хотите купить автозапчасти в нашем магазине по самой низкой цене?-->
<!--		Сделайте полную оплату заказа. И Вы получете самую низкую цену.</p></div>-->