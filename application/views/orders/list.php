<link rel="stylesheet" href="/media/css/dist/bootstrap-datetimepicker.min.css" />
<link rel="stylesheet" href="/media/css/dist/bootstrap-datetimepicker.css">

<div class="container">
	<header class="page-header">
		<h1 class="page-title">Личный кабинет</h1>
	</header>
	<div class="gap gap-small"></div>
	<div class="row row-sm-gap" data-gutter="10">
		<div class="col-md-4">
			<div class="clearfix">
<!--				<h3 class="widget-title-lg">Баланс</h3>-->
				<div class="box">
					<table class="table">
						<thead>
						<tr>
							<th><span class="block">Ваш уровень цен: </span></th>
							<th><span class="block" style="color: #429063;"><?=$client->discount->name?></span></th>
						</tr>
						</thead>
						<tbody>
						<tr>
							<td><a href="<?=URL::site('orders/all_balance/'.$client);?>">Всего заказов на сумму: </a> </td>
							<td><?=$order_details['all_sale']+$order_details['return']?> грн.</td>
						</tr>
						<tr>
							<td><a href="<?=URL::site('orders/return_balance/'.$client);?>">Невыполнненых заказов на сумму:</a> </td>
							<td><?=$order_details['return']?> грн.</td>
						</tr>
						<tr>
							<td><a href="<?=URL::site('orders/real_balance/'.$client);?>">Активных заказов на сумму:</a> </td>
							<td><?=$order_details['all_sale']?> грн.</td>
						</tr>
						<tr>
							<td><a href="<?=URL::site('orders/all_pay/'.$client);?>">Всего оплат внесено:</a></td>
							<td><?=$order_details['all_in']?> грн.</td>
						</tr>


<!--						<tr>-->
<!--							<td><a href="--><?//=URL::site('orders/all_balance');?><!--?client_id=--><?//=$client?><!--">Подробный баланс</a> </td>-->
<!--						</tr>-->
<!--						<tr>-->
<!--							<td>Активных заказов на сумму: </td>-->
<!--							<td>--><?//=$order_details['active_sale']?><!-- грн.</td>-->
<!--						</tr>-->
<!--						<tr>-->
<!--							<td>Баланс без активных заказов: </td>-->
<!--							<td>--><?//=$order_details['balance']?><!-- грн.</td>-->
<!--						</tr>-->
<!--						<tr>-->
<!--							<td>Баланс учитывая активные заказы: </td>-->
<!--							<td>--><?//=$order_details['active_balance']?><!-- грн.</td>-->
<!--						</tr>-->
						<tr>
							<td><span class="block">Баланс: </span></td>
							<?php if($order_details['debt']<0):?>
								<td><span class="block" style="color: red;"><?=$order_details['debt']?> грн.</span></td>
							<?php else: ?>
								<td><span class="block" style="color: green;"><?=$order_details['debt']?> грн.</span></td>
							<? endif;?>
						</tr>
						</tbody>
					</table>
					<div style="text-align: center;"><a href="https://www.privat24.ua/#login"><img src="<?=URL::base()?>media/img/dist/pb24.png " alt="Image Alternative text" title="Image Title" style="padding: 10px;"></a></div><br>
					<a class="btn btn-primary" style="width: 100%;" href="<?=URL::site('clients/index/'.$client);?>"><i class="fa fa-cogs"></i>Редактировать информацию профиля</a><br><br>
<!--
			        // TODO: Uncomment when approve LiqPay

					<a class="btn btn-primary" style="width: 100%;" href="<?=URL::site('liqpay/prepare');?>">
						<i class="fa fa-cogs"></i>Пополнить баланс
					</a>
					<br /><br />
-->
					<a class="btn btn-primary" style="width: 100%;" href="<?=URL::site('authorization/logout');?>"><i class="fa fa-sign-in"></i>Выйти</a>
				</div>
			</div>
		</div>
		<div class="col-md-8">
			<div class="clearfix">
				<span class="block widget-title-lg">Заказы</span>
				<form method="get">
				<div class="col-xs-5" style="width: auto; padding-left: 0px; padding-right: 0px;">
					<div class="col-xs-1" style="padding-top: 6px; width: 71px;">Дата&nbspот</div>
					<div class="col-xs-3" style="width: 214px;">
						<div class="form-group">
							<div class="input-group date" id="datetimepicker8">
								<input name="date_from" type="text" class="form-control" value="<?= isset($_GET['date_from']) ? $_GET['date_from'] : ''?>" />
								<span class="input-group-addon">
									<span class="glyphicon glyphicon-calendar"></span>
								</span>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xs-5" style="width: auto; padding-left: 0px; padding-right: 0px;">
					<div class="col-xs-1" style="padding-top: 6px; width: auto; padding-left: 11px; padding-right: 11px;">до</div>
					<div class="col-xs-3" style="width: 214px;">
						<div class="form-group">
							<div class="input-group date" id="datetimepicker9">
								<input name="date_to" type="text" class="form-control"  value="<?= isset($_GET['date_to']) ? $_GET['date_to'] : ''?>"/>
								<span class="input-group-addon">
									<span class="glyphicon glyphicon-calendar"></span>
								</span>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xs-5" style="width: auto;">
					<div class="col-xs-1" style="padding-top: 6px; width: auto; padding-left: 11px; padding-right: 0px;">Архив: </div>
					<div class="col-xs-1" style=" width: auto; padding-left: 11px; padding-right: 11px;">
						<select class="select" name="archive">
							<option value="all">Все</option>
							<option value="0">Нет</option>
							<option value="1">Да</option>
						</select>
					</div>
				</div>
				<div style="clear: both">
					<div class="col-xs-3" style="padding-top: 6px; width: auto; padding-left: 11px; padding-right: 0px;"><a class="btn btn-primary" onclick="$(this).closest('form').submit()"><i class="fa fa-filter"></i>Применить фильтр</a><p></p></div>
					<div class="col-xs-3" style="padding-top: 6px; width: auto; padding-left: 11px; padding-right: 0px;"><a class="btn btn-primary" href="<?=URL::site('orders/');?>"><i class="fa fa-repeat"></i>Сброс</a><p></p></div>
<!--					<div class="col-xs-3 col-xs-offset-3" style="padding-top: 6px; width: auto; padding-left: 11px; padding-right: 0px;"><a class="btn btn-primary" href="--><?//=URL::site('orders/all_balance/'.$client);?><!--"><i class="icon-white icon-refresh"></i>Подробный баланс</a><p></p></div>-->
					<p></p>
				</div>
				</form>
				<div style="clear: both"></div>
				<div style="clear: both"></div>

				<table class="table1">
					<thead class="thead1">
					<tr class="tr1">
						<th>№&nbspзаказа</th>
						<th>Дата</th>
						<th>Состояние</th>
						<th>Сумма</th>
						<th></th>
						<th></th>
					</tr>
					</thead>
					<tbody>
					<?php foreach($orders as $order) : ?>
						<tr class="tr1" onclick="document.location = '<?=URL::site('orders/items/'.$order->id);?>'">
							<td class="td2"><a href="<?=URL::site('orders/items/'.$order->id);?>"><?=$order->get_order_number()?></a></td>
							<td class="td2"><?php $d = new DateTime($order->date_time); ?><?=$d->format('d.m.Y H:i:s')?></td>
							<td class="td2">
								<? foreach($order->orderitems->find_all()->as_array() as $orderitem) { ?>
									<img src="<?=URL::base()?>media/img/states/<?=$orderitem->state->img?>" title="<?=$orderitem->state->name?>" />
								<? } ?>
							</td>
							<td class="td2">
								<?
								$sale = 0;
								foreach($order->orderitems->find_all()->as_array() as $orderitem) {
									$sale += $orderitem->sale_per_unit * $orderitem->amount;
								} ?>
								<?=$sale?> грн.
							</td>
							<td class="td2">
								<a href="<?=URL::site('orders/print/'.$order->id);?>"><i class="fa fa-print" style="font-size: 22px;"></i></a>
							</td>
							<td class="td2">
								<a href="<?= Helper_Url::createUrl('/liqpay/order_pay/' . $order->id) ?>">Оплатить</a>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
<!--				--><?//= $oi_pagination ?>
			</div>
		</div>
	</div>

	<div class="gap"></div>

	<span class="block widget-title-lg">Ваши автомобили&nbsp<a type="button" href="<?=URL::site('cars/add');?>" class="btn btn-primary"><span class="fa fa-plus"></span> Добавить авто</a></span>
	<?php $cars = $client->cars->find_all()->as_array(); ?>
	<?php if(count($cars) > 0): ?>
		<table class="table1">
			<thead class="thead1">
			<tr class="tr1">
				<th>Авто</th>
				<th>VIN код</th>
				<th>Двигатель</th>
				<th>Год выпуска</th>
				<th></th>
			</tr>
			</thead>
			<tbody>
			<?php foreach($cars as $car) : ?>
				<tr class="tr1">
					<td class="td3"><?=$car->brand?> <?=$car->model?></td>
					<td class="td3"><?=$car->vin?></td>
					<td class="td3"><?=$car->engine ? $car->engine : "-----"?></td>
					<td class="td3"><?=$car->year ? $car->year : "-----"?></td>
					<td class="td3"><a href="<?=URL::site('cars/edit/'.$car->id);?>" style="display:inline-block;"><i class="fa fa-pencil-square-o table-shopping-remove" style="font-size: 22px;"></i></a>&nbsp&nbsp
						<a href="<?=URL::site('cars/delete/'.$car->id);?>" class="delete_row_cars" style="display:inline-block;"><i class="fa fa-close table-shopping-remove" style="font-size: 22px;"></i></a></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	<?php else: ?>
		<p>Вы еще не добавили ни одного автомобиля.</p>
	<?php endif; ?>
</div>