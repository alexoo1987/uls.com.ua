
<div class="container">
<!--	--><?php //if(!empty($data['client_id'])): ?>
<!--	-->
<!--	--><?// if(ORM::factory('Permission')->checkPermission('clientpayment_add')) { ?>
<!--	--><?//= Form::open('', array('class' => 'form-horizontal', 'id' => 'validate_form')); ?>
<!--	-->
<!--	--><?//= Form::hidden('client_id', Arr::get($data, 'client_id')); ?>
<!---->
<!--	<div class="control-group">-->
<!--		--><?//= Form::label('value', 'Внесено', array('class' => 'control-label')); ?>
<!--		<div class="controls">-->
<!--			--><?//= Form::input('value', HTML::chars(Arr::get($data, 'value')), array('validate' => 'required|float')); ?>
<!--			<cpan class="add-on">грн.</cpan>-->
<!--		</div>-->
<!--	</div>-->
<!---->
<!--	<div class="control-group">-->
<!--		--><?//= Form::label('date_time', 'Дата', array('class' => 'control-label')); ?>
<!--		<div class="controls">-->
<!--			--><?//= Form::input('date_time', HTML::chars(Arr::get($data, 'date_time')), array('validate' => 'required', 'class' => 'datepicker')); ?>
<!--		</div>-->
<!--	</div>-->
<!---->
<!--	<div class="control-group">-->
<!--		--><?//= Form::label('comment_text', 'Коментарий', array('class' => 'control-label')); ?>
<!--		<div class="controls">-->
<!--			--><?//= Form::input('comment_text', HTML::chars(Arr::get($data, 'comment_text'))); ?>
<!--		</div>-->
<!--	</div>-->
<!---->
<!--	<div class="control-group">-->
<!--		--><?//= Form::label('order_id', 'Заказ', array('class' => 'control-label')); ?>
<!--		<div class="controls">-->
<!--			--><?//= Form::select('order_id', $orders, Arr::get($data, 'order_id')); ?>
<!--		</div>-->
<!--	</div>-->
<!--	-->
<!--	<div class="control-group">-->
<!--		<div class="controls">-->
<!--			--><?//= Form::submit('create', 'Сохранить', array('class' => 'btn btn-primary')); ?>
<!--		</div>-->
<!--	</div>-->
<!--	--><?//= Form::close(); ?>
<!--	--><?php //} ?>
<!--	--><?php //endif; ?>
	
	<b>Фильтр</b>
	<?= Form::open('', array('class' => 'form-horizontal', 'method' => 'get')); ?>
		Дата от <?= Form::input('date_from', HTML::chars(Arr::get($filters, 'date_from')), array('class' => 'datepicker')); ?> до <?= Form::input('date_to', HTML::chars(Arr::get($filters, 'date_to')), array('class' => 'datepicker')); ?><br />
		Менеджер <?= Form::select('manager_id', $managers, Arr::get($filters, 'manager_id')); ?><br />
	Сотрудник <?= Form::select('user_id', $users, Arr::get($filters, 'user_id')); ?><br/>
		<?= Form::hidden('client_id', Arr::get($_GET, 'client_id')); ?>
	<div class="btn-group" data-toggle="buttons">
		<label class="btn btn-success">
			<input type="radio" name="filter_type" value="1" <?=((!isset($_GET['filter_type']) OR $_GET['filter_type'] == 1) ? 'checked' : '')?>>
			Проплаты </label>
		<label class="btn btn-warning">
			<input type="radio" name="filter_type" value="2" <?=((isset($_GET['filter_type']) AND $_GET['filter_type'] == 2) ? 'checked' : '')?>>
			Заказы </label>
	</div>
	<br />
	<br />
		<?= Form::submit('', 'Применить фильтр', array('class' => 'btn btn-primary')); ?>
	<?= Form::close(); ?>
	<a href="<?=URL::site('admin/clientpayment/list');?>" class="btn btn-primary"><i class="icon-white icon-refresh"></i> Сброс</a><br /><br />

	<table class="table table-striped table-bordered">
		<tr>
			<th>Клиент</th>
			<th>Значение</th>
			<th>Дата</th>
			<th>Заказ</th>
			<th>Сотрудник</th>
			<th>Коментарий</th>
			<th></th>
		</tr>
		<?php
		foreach($client_payments as $cp) : ?>
		<tr>
			<td><?=$cp->client->name?> <?=$cp->client->surname?></td>
			<td><?=$cp->value?> грн.</td>
			<td><?php $d = new DateTime($cp->date_time); ?><?= $d->format('d.m.Y H:i:s') ?></td>
			<td><? echo ($cp->order_id ? '<a href="'.URL::site('admin/orders/items/'.$cp->order_id).'">'.$cp->order_id.'</a>' : "---"); ?></td>
			<td><?= $cp->user->surname ?></td>
			<td><?=$cp->comment_text?></td>
			<td>
				<? if(ORM::factory('Permission')->checkPermission('clientpayment_delete')) { ?>
				<a class="btn btn-mini btn-danger delete_row" href="<?=URL::site('admin/clientpayment/delete/'.$cp->id);?>"><i class="icon-remove"></i> Удалить</a>
				<? } ?>
			</td>
		</tr>
		<?php endforeach; ?>
		<tr>
			<td><b>Итого:</b></td>
			<td><?=$total['payments']?> грн.<br>Без "-": <?=$total['payments_table']?> грн.</td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
	</table>
	<?=$cp_pagination?>
	
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>№ заказа</th>
				<th>Дата</th>
				<th>Состояние</th>
				<th>Доп. данные</th>
				<th>Клиент</th>
				<th>Продажа</th>
			</tr>
		</thead>
		
		<tbody>
			<?php
			foreach($orderitems as $orderitem) : ?>
			<tr>
				<td><a href="<?=URL::site('admin/orders/items/'.$orderitem->order->id);?>"><?=$orderitem->order->get_order_number()?></a></td>
				<td><?php $d = new DateTime($orderitem->order->date_time); ?><?=$d->format('d.m.Y H:i:s')?></td>
				<td><img src="<?=URL::base()?>media/img/states/<?=$orderitem->state->img?>" title="<?=$orderitem->state->description?>" /> <?=$orderitem->state->name?></td>
				<td>
					<b>Артикул:</b> <?=$orderitem->article?><br />
					<b>Производитель:</b> <?=$orderitem->brand?><br />
					<b>Наименование:</b> <?=$orderitem->name?>
				</td>
				<td><?=$orderitem->order->client->name?> <?=$orderitem->order->client->surname?></td>
				<td>
					<?=$orderitem->val?>
				</td>
			</tr>
			<?php endforeach; ?>
			<tr>
				<td><b>Итого:</b></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td><?=$total['orderitems']?> грн.</td>
			</tr>
		</tbody>
	</table>
	<?=$oi_pagination?>
	
	<b>Баланс до указанного периода: <?=$total['before']?> грн.</b><br>
	<b>Баланс за указанный период: <?=$total['payments']-$total['orderitems']?> грн.</b><br>
	<b>Общий баланс: <?=($total['before'] + $total['payments'] - $total['orderitems'])?> грн.</b>
</div>