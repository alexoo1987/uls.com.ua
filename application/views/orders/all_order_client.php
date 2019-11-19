<link rel="stylesheet" href="/media/css/dist/bootstrap-datetimepicker.min.css" />
<link rel="stylesheet" href="/media/css/dist/bootstrap-datetimepicker.css">

<div class="container">
	<header class="page-header">
		<h1 class="page-title">Личный кабинет</h1>
	</header>
	<div class="gap gap-small"></div>
	<div class="row row-sm-gap" data-gutter="10">
		<div class="col-md-12">
			<div class="clearfix">
				<span class="block widget-title-lg"><?=$title; ?></span>
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
					<div style="clear: both">
						<div class="col-xs-3" style="padding-top: 6px; width: auto; padding-left: 11px; padding-right: 0px;"><a class="btn btn-primary" onclick="$(this).closest('form').submit()"><i class="fa fa-filter"></i>Применить фильтр</a><p></p></div>
						<div class="col-xs-3" style="padding-top: 6px; width: auto; padding-left: 11px; padding-right: 0px;"><a class="btn btn-primary" href="<?=URL::site('orders/all_balance');?>"><i class="fa fa-repeat"></i>Сброс</a><p></p></div>
						<div class="col-xs-3 col-xs-offset-3"><a class="btn btn-primary" href="<?=URL::site('orders?archive=all');?>"><i class="icon-white icon-refresh"></i>Назад</a><p></p></div>
						<p></p>
					</div>
				</form>
				<div style="clear: both"></div>
				<div style="clear: both"></div>

				<table class="table1-order">
					<thead class="thead1">
					<tr class="tr1">
						<th>№&nbspзаказа</th>
						<th>Дата</th>
						<th>Состояние</th>
						<th>Доп. данные</th>
						<th>Сумма</th>
					</tr>
					</thead>
					<tbody>
					<?php foreach($orderitems as $orderitem) : ?>
						<tr class="tr1">
							<td class="td2"><?=$orderitem->order->get_order_number()?></td>
							<td class="td2"><?php $d = new DateTime($orderitem->order->date_time); ?><?=$d->format('d.m.Y H:i:s')?></td>
							<td class="td2">
								<?php if(!in_array($orderitem->state_id, $disallowed_states)):?>
									<img src="<?=URL::base()?>media/img/states/<?=$orderitem->state->img?>" title="<?=$orderitem->state->description?>" /> <?=$orderitem->state->name?>
								<?php else:?>
									<img src="<?=URL::base()?>media/img/states/17.png" title="Возвращено" /> возвращено
								<?php endif ?>
							</td>
							<td class="td2">
								<span  class="bold">Артикул:</span> <?=$orderitem->article?><br />
								<span  class="bold">Производитель:</span> <?=$orderitem->brand?><br />
								<span  class="bold">Наименование:</span> <?=$orderitem->name?>
							</td>
							<td class="td2"><?=$orderitem->val?></td>
						</tr>
					<?php endforeach; ?>
					<tr>
						<?php if($key_bottom==0):?>
							<td><span  class="bold">Всего заказано позиций на сумму:</span></td>
							<td></td>
							<td></td>
							<td></td>
							<td><?=$total['orderitems']+$total['return']?> грн.</td>
						<?php elseif ($key_bottom==1): ?>
							<td><span  class="bold">Всего выполненно заказов на сумму:</span></td>
							<td></td>
							<td></td>
							<td></td>
							<td><?=$total['orderitems']?> грн.</td>
						<?php elseif ($key_bottom==2): ?>
							<td><span  class="bold">Всего невыполненных позиций <br>заказов на сумму:</span></td>
							<td></td>
							<td></td>
							<td></td>
							<td><?=$total['return']?> грн.</td>
						<?php endif ?>
					</tr>
					</tbody>
				</table>
				<?=$oi_pagination?>
				<!--				--><?//= $oi_pagination ?>
			</div>
		</div>
	</div>

	<div class="gap"></div>
</div>