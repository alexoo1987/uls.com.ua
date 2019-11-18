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
				<span class="block widget-title-lg">Ваши платежи</span>
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

				<table class="table1">
					<thead class="thead1">
					<tr class="tr1">
						<th>Клиент</th>
						<th>Значение</th>
						<th>Дата</th>
						<th>Заказ</th>
						<th>Коментарий</th>
					</tr>
					</thead>
					<tbody>
					<?php foreach($client_payments as $cp) : ?>
						<tr class="">
							<td class="td7"><?=$cp->client->name?> <?=$cp->client->surname?></td>
							<td class="td7"><?=$cp->value?> грн.</td>
							<td class="td7"><?php $d = new DateTime($cp->date_time); ?><?= $d->format('d.m.Y H:i:s') ?></td>
							<td class="td7"><? echo ($cp->order_id ? '<a href="'.URL::site('orders/items/'.$cp->order_id).'">'.$cp->order_id.'</a>' : "---"); ?></td>
							<td class="td7"><?=$cp->comment_text?></td>
						</tr>
					<?php endforeach; ?>
					<tr>
						<td class="td5"><span  class="bold">Всего произведено оплат на сумму:</span></td>
						<td class="td5"></td>
						<td class="td5"></td>
						<td class="td5"></td>
						<td class="td5"><?=$total['payments']?> грн.</td>
					</tr>
					</tbody>
				</table>
				<?=$cp_pagination?>
				<!--				--><?//= $oi_pagination ?>
			</div>
		</div>
	</div>

	<div class="gap"></div>
</div>