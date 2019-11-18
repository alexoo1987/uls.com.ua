		<div class="span5">
			<form class="form-search" style="margin-top: 15px;" method="GET" action="<?=URL::site('admin/find/index');?>">
				<input type="text" name="art" class="input-xlarge search-query" placeholder="Артикул" value="<?=(!empty($_GET['art'])) ? $_GET['art'] : ''?>">
				<?php $discount = ORM::factory('Discount')->where('admin_default', '=', '1')->find(); ?>
				<input type="hidden" name="discount_id" value="<?=($discount->id) ? $discount->id : 1?>">
				<button type="submit" class="btn">Найти</button>
			</form>

		</div>
<!--        <div class="span3">-->
<!--            <a class="vin_code_long" target="_blank" href="http://catalog.eparts.kiev.ua/">Поиск по VIN code</a>-->
<!--        </div>-->