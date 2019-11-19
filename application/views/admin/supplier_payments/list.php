<div class="container">
	<? if(ORM::factory('Permission')->checkPermission('supplierpayment_manage')) { ?>
	<?= Form::open('', array('class' => 'form-horizontal', 'id' => 'validate_form', 'onsubmit' => 'javascript: this.create.disabled="disabled";')); ?>
	<div class="control-group">
		<?= Form::label('supplier_id', 'Поставщик', array('class' => 'control-label')); ?>
		<div class="controls">
			<?= Form::select('supplier_id', $suppliers, Arr::get($data, 'supplier_id'), array('validate' => 'required', 'class' => 'suppliers')); ?>
		</div>
	</div>

	<div class="control-group">
		<?= Form::label('value', 'Внесено', array('class' => 'control-label')); ?>
		<div class="controls">
			<?= Form::input('value', HTML::chars(Arr::get($data, 'value')), array('validate' => 'required|float', 'type' => 'number', 'step' => '0.01')); ?>
		</div>
	</div>

	<div class="control-group">
		<?= Form::label('currency_id', 'Валюта', array('class' => 'control-label')); ?>
		<div class="controls">
			<?= Form::select('currency_id', $currencies, Arr::get($data, 'currency_id'), array('disabled' => 'disabled')); ?>
		</div>
	</div>

	<?php if(in_array(Auth::instance()->get_user()->id, [2,74])): ?>
		<div class="control-group">
			<?= Form::label('date_time', 'Дата', array('class' => 'control-label')); ?>
			<div class="controls">
				<?= Form::input('date_time', $data->date_time, array('class' => 'datepicker')); ?>
			</div>
		</div>
	<?php endif; ?>

	<div class="control-group">
		<?= Form::label('comment_text', 'Коментарий', array('class' => 'control-label')); ?>
		<div class="controls">
			<?= Form::input('comment_text', HTML::chars(Arr::get($data, 'comment_text'))); ?>
		</div>
	</div>
	
	<div class="control-group">
		<div class="controls">
			<?= Form::submit('create', 'Сохранить', array('class' => 'btn btn-primary'/*, 'onclick'=>'this.disabled=true;'*/)); ?>
		</div>
	</div>
	<?= Form::close(); ?>
	<?php } ?>
	
	<b>Фильтр</b>
	<?= Form::open('', array('class' => 'form-horizontal', 'method' => 'get')); ?>
		Дата от <?= Form::input('date_from', HTML::chars(Arr::get($filters, 'date_from')), array('class' => 'datepicker dateFrom', 'data-from' => '2016-08-08')); ?> до <?= Form::input('date_to', HTML::chars(Arr::get($filters, 'date_to')), array('class' => 'datepicker')); ?><br />
		Поставщик <?= Form::select('supplier_id', $suppliers, Arr::get($filters, 'supplier_id')); ?><br />
	Сотрудник <?= Form::select('user_id', $users, Arr::get($filters, 'user_id')); ?><br/>
		Состояние <?= Form::select('state_id', $states, Arr::get($filters, 'state_id')); ?><br />
		
		<?= Form::submit('', 'Применить фильтр', array('class' => 'btn btn-primary')); ?>
	<?= Form::close(); ?>
	<a href="<?=URL::site('admin/supplierpayment/list');?>" class="btn btn-primary"><i class="icon-white icon-refresh"></i> Сброс</a><br /><br />

	<h3>Платежи</h3>
	<table class="table table-striped table-bordered">
		<tr>
			<th>Поставщик</th>
			<th>Значение</th>
			<th>Курс</th>
			<th>Дата</th>
			<th>Сотрудник</th>
			<th>Коментарий</th>
			<th></th>
		</tr>
		<?php
		foreach($supplier_payments as $sp) : ?>
		<tr>
			<td><?=$sp->supplier->name?></td>
			<td><?=($sp->ratio == 1 ? $sp->value : $sp->value . $sp->supplier->currency->code . ' / ' . round($sp->value*$sp->ratio))?>UAH</td>
			<td><?= $sp->ratio ?></td>
			<td><?php $d = new DateTime($sp->date_time); ?><?= $d->format('d.m.Y H:i:s') ?></td>
			<td><?= $sp->user->surname ?></td>
			<td><?=$sp->comment_text?></td>
			<td>
				<? if(ORM::factory('Permission')->checkPermission('supplierpayment_manage')) { ?>
					<a class="btn btn-mini btn-danger delete_row" href="<?=URL::site('admin/supplierpayment/delete/'.$sp->id);?>"><i class="icon-remove"></i> Удалить</a>
				<?php } ?>
			</td>
		</tr>
		<?php endforeach; ?>
		<tr>
			<td><b>Итого:</b></td>
			<td colspan="5">
				<div class="span3">
					<p><b>Проплаты:</b></p>
					<?php $result = array();?>
					<?php $sum_in_UAH = 0;?>
					<?php foreach ($total['payments_table'] AS $code => $data) { ?>
                        <?php $sum_in_UAH += $code != 'UAH' ? $data['in_UAH'] : $data['amount'];?>
						<p><span class="plus" data-currency="<?=$code?>"><?=$data['amount'] . '</span>' . $code . ($code != 'UAH' ? ' / '.$data['in_UAH']. "UAH" : '')?></p>
					<?php } ?>
                    <p style="border-top: 1px solid;"><?='<b>Сумма: ' . round($sum_in_UAH, 2) . 'UAH</b>'?></p>
				</div>
				<div class="span3">
					<p><b>Возвраты:</b></p>
                    <?php $sum_in_UAH = 0;?>
                    <?php foreach ($total['returns'] AS $code => $data) {?>
                        <?php $sum_in_UAH += $code != 'UAH' ? $data['in_UAH'] : $data['amount'];?>
                        <p><span class="minus" data-currency="<?=$code?>"><?=-$data['amount'] . '</span>' .$code . ($code != 'UAH' ? ' / '.-$data['in_UAH']. "UAH" : '')?></p>
					<?php } ?>
                    <p style="border-top: 1px solid;"><?='<b>Сумма: ' . -round($sum_in_UAH, 2) . 'UAH</b>'?></p>
                </div>
			</td>
		</tr>
	</table>
	<?=$sp_pagination?>


	<h3>Доставки</h3>
	<table class="table table-striped table-bordered">
		<tr>
			<th>Поставщик</th>
			<th>Значение</th>
			<th>Дата</th>
			<th>Сотрудник</th>
			<th>Коментарий</th>
			<th></th>
		</tr>
		<?php
		foreach($delivery_payments as $sp) : ?>
			<tr>
				<td><?=$sp->supplier->name?></td>
				<td><?=$sp->amount?> UAH</td>
				<td><?php $d = new DateTime($sp->date); ?><?= $d->format('d.m.Y H:i:s') ?></td>
				<td><?= $sp->user->surname ?></td>
				<td><?=$sp->comment?></td>
			</tr>
		<?php endforeach; ?>
		<tr>
			<td><b>Итого:</b></td>
			<td colspan="5">
				<div class="span3">
					<p><b>Доставки:</b></p>
					<?php $result_del = array();?>
					<?php $sum_in_UAH_del = 0;?>
					<?php foreach ($total['delivery_table'] AS $code => $data) { ?>
						<?php $sum_in_UAH_del += $code != 'UAH' ? $data['in_UAH'] : $data['amount'];?>
						<p><span><?=$data['amount'] . '</span>' . $code . ($code != 'UAH' ? ' / '.$data['in_UAH']. "UAH" : '')?></p>
					<?php } ?>
					<p style="border-top: 1px solid;"><?='<b>Сумма: ' . round($sum_in_UAH_del, 2) . 'UAH</b>'?></p>
				</div>
			</td>
		</tr>
	</table>
	<?=$del_pagination?>



	<h3>Заказы позиций</h3>
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>№ заказа</th>
				<th>Дата закупки</th>
				<th>История статусов</th>
				<th>Доп. данные</th>
				<th>Поставщик</th>
				<th>Сумма</th>
			</tr>
		</thead>

		<tbody>
			<?php foreach ($orderitems['purchase'] as $state) : ?>
			<?php $orderitem = $state->orderitem; ?>
			<tr>
				<td><a href="<?=URL::site('admin/orders/items/'.$orderitem->order->id);?>"><?=$orderitem->order->get_order_number()?></a></td>
				<td><img src="<?=URL::base()?>media/img/states/<?=$state->state->img?>" title="<?=$state->state->description?>" /> <?php $d = new DateTime($state->date_time); ?><?=$d->format('d.m.Y H:i:s')?></td>
				<td>
					<?php if ($orderitem->logs->find_all()->as_array()) { ?>
						<?php foreach (array_slice($array = $orderitem->logs->order_by('date_time', 'DESC')->find_all()->as_array(),0,7) AS $one) {
							echo '<p><img src="'.URL::base().'media/img/states/'.$one->state->img.'" title="'.$one->state->description.'" /> <span class="orderitem_state" title="'.$one->state->name.'">'. date('d.m.Y H:i', strtotime($one->date_time)).'</span></p>';?>
						<?php } ?>
						<a href="#orderitem_log<?=$orderitem->id?>" role="button" class="btn btn-mini" data-toggle="modal">Подробнее..</a>

						<!-- Modal -->
						<div id="orderitem_log<?=$orderitem->id?>" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
								<h3>История изменения статусов</h3>
							</div>
							<div class="modal-body">
								<table class="table table-striped table-bordered">
									<tr>
										<th>Состояние</th>
										<th>Время</th>
										<th>Пользователь</th>
									</tr>
									<?php foreach ($array = $orderitem->logs->find_all()->as_array() AS $one) { ?>
										<tr <?=($one == end($array) ? 'class="success"' : '')?>>
											<td><img src="<?=URL::base()?>media/img/states/<?=$one->state->img?>" title="<?=$one->state->description?>" /> <?=$one->state->name?></td>
											<td><?=date('d.m.Y H:i:s', strtotime($one->date_time))?></td>
											<td><?=$one->user->surname?></td>
										</tr>
									<?php } ?>

								</table>
							</div>
						</div>
					<?php } else { ?>
						<img src="<?=URL::base()?>media/img/states/<?=$orderitem->state->img?>" title="<?=$orderitem->state->description?>" /> <?=$orderitem->state->name?>
					<?php } ?>
				</td>
				<td>
					<b>Артикул:</b> <?=$orderitem->article?><br />
					<b>Производитель:</b> <?=$orderitem->brand?><br />
					<b>Наименование:</b> <?=$orderitem->name?>
				</td>
				<td><?=$orderitem->supplier->name?></td>
				<td>
					<?php
					echo round(($orderitem->currency->code == 'UAH' ? $orderitem->purchase_per_unit : $orderitem->purchase_per_unit_in_currency) * $orderitem->amount, 2) . " " . $orderitem->currency->code;
					?>
				</td>
			</tr>
			<?php endforeach; ?>
			<tr>
				<td><b>Итого:</b></td>
				<td colspan="5">
					<div class="span3">
					<p><b>Заказано на:</b></p>
					<?php foreach($total['purchase'] as  $curr_val): ?>
						<p><span class="minus" data-currency="<?=$curr_val['currency']->code?>"><?=round($curr_val['val'], 2)?></span> <?=$curr_val['currency']->code?></p>
					<?php endforeach; ?>
					</div>
				</td>
			</tr>
		</tbody>
	</table>

	<h3>Возвраты позиций</h3>
	<table class="table table-striped table-bordered">
		<thead>
		<tr>
			<th>№ заказа</th>
			<th>Дата возврата</th>
			<th>История статусов</th>
			<th>Доп. данные</th>
			<th>Поставщик</th>
			<th>Сумма</th>
		</tr>
		</thead>

		<tbody>
		<?php foreach ($orderitems['return'] as $state) : ?>
			<?php $orderitem = $state->orderitem; ?>
			<tr>
				<td><a href="<?=URL::site('admin/orders/items/'.$orderitem->order->id);?>"><?=$orderitem->order->get_order_number()?></a></td>
				<td><img src="<?=URL::base()?>media/img/states/<?=$state->state->img?>" title="<?=$state->state->description?>" /> <?php $d = new DateTime($state->date_time); ?><?=$d->format('d.m.Y H:i:s')?></td>
				<td>
					<?php if ($orderitem->logs->find_all()->as_array()) { ?>
						<?php foreach (array_slice($array = $orderitem->logs->order_by('date_time', 'DESC')->find_all()->as_array(),0,7) AS $one) {
							echo '<p><img src="'.URL::base().'media/img/states/'.$one->state->img.'" title="'.$one->state->description.'" /> <span class="orderitem_state" title="'.$one->state->name.'">'. date('d.m.Y H:i', strtotime($one->date_time)).'</span></p>';?>
						<?php } ?>
						<a href="#orderitem_log<?=$orderitem->id?>" role="button" class="btn btn-mini" data-toggle="modal">Подробнее..</a>

						<!-- Modal -->
						<div id="orderitem_log<?=$orderitem->id?>" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
								<h3>История изменения статусов</h3>
							</div>
							<div class="modal-body">
								<table class="table table-striped table-bordered">
									<tr>
										<th>Состояние</th>
										<th>Время</th>
										<th>Пользователь</th>
									</tr>
									<?php foreach ($array = $orderitem->logs->find_all()->as_array() AS $one) { ?>
										<tr <?=($one == end($array) ? 'class="success"' : '')?>>
											<td><img src="<?=URL::base()?>media/img/states/<?=$one->state->img?>" title="<?=$one->state->description?>" /> <?=$one->state->name?></td>
											<td><?=date('d.m.Y H:i:s', strtotime($one->date_time))?></td>
											<td><?=$one->user->surname?></td>
										</tr>
									<?php } ?>

								</table>
							</div>
						</div>
					<?php } else { ?>
						<img src="<?=URL::base()?>media/img/states/<?=$orderitem->state->img?>" title="<?=$orderitem->state->description?>" /> <?=$orderitem->state->name?>
					<?php } ?>
				</td>
				<td>
					<b>Артикул:</b> <?=$orderitem->article?><br />
					<b>Производитель:</b> <?=$orderitem->brand?><br />
					<b>Наименование:</b> <?=$orderitem->name?>
				</td>
				<td><?=$orderitem->supplier->name?></td>
				<td>
					<?php
					echo round(($orderitem->currency->code == 'UAH' ? $orderitem->purchase_per_unit : $orderitem->purchase_per_unit_in_currency) * $orderitem->amount, 2) . " " . $orderitem->currency->code;
					?>
				</td>
			</tr>
		<?php endforeach; ?>
		<tr>
			<td><b>Итого:</b></td>
			<td colspan="5">
				<div class="span3">
					<p><b>Возврат поставщику на:</b></p>
					<?php foreach($total['return'] as  $curr_val): ?>
						<p><span class="plus" data-currency="<?=$curr_val['currency']->code?>"><?=round($curr_val['val'], 2)?></span> <?=$curr_val['currency']->code?></p>
					<?php endforeach; ?>
				</div>
			</td>
		</tr>
		</tbody>
	</table>

	<h3>В работе</h3>
	<table class="table table-striped table-bordered">
		<thead>
		<tr>
			<th>№ заказа</th>
			<th>История статусов</th>
			<th>Доп. данные</th>
			<th>Поставщик</th>
			<th>Сумма</th>
		</tr>
		</thead>

		<tbody>
		<?php foreach ($orderitems['in_work'] as $state) : ?>
			<?php $orderitem = $state->orderitem; ?>
			<tr>
				<td><a href="<?=URL::site('admin/orders/items/'.$orderitem->order->id);?>"><?=$orderitem->order->get_order_number()?></a></td>
				<td>
					<?php if ($orderitem->logs->find_all()->as_array()) { ?>
						<?php foreach (array_slice($array = $orderitem->logs->order_by('date_time', 'DESC')->find_all()->as_array(),0,7) AS $one) {
							echo '<p><img src="'.URL::base().'media/img/states/'.$one->state->img.'" title="'.$one->state->description.'" /> <span class="orderitem_state" title="'.$one->state->name.'">'. date('d.m.Y H:i', strtotime($one->date_time)).'</span></p>';?>
						<?php } ?>
						<a href="#orderitem_log<?=$orderitem->id?>" role="button" class="btn btn-mini" data-toggle="modal">Подробнее..</a>

						<!-- Modal -->
						<div id="orderitem_log<?=$orderitem->id?>" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
								<h3>История изменения статусов</h3>
							</div>
							<div class="modal-body">
								<table class="table table-striped table-bordered">
									<tr>
										<th>Состояние</th>
										<th>Время</th>
										<th>Пользователь</th>
									</tr>
									<?php foreach ($array = $orderitem->logs->find_all()->as_array() AS $one) { ?>
										<tr <?=($one == end($array) ? 'class="success"' : '')?>>
											<td><img src="<?=URL::base()?>media/img/states/<?=$one->state->img?>" title="<?=$one->state->description?>" /> <?=$one->state->name?></td>
											<td><?=date('d.m.Y H:i:s', strtotime($one->date_time))?></td>
											<td><?=$one->user->surname?></td>
										</tr>
									<?php } ?>

								</table>
							</div>
						</div>
					<?php } else { ?>
						<img src="<?=URL::base()?>media/img/states/<?=$orderitem->state->img?>" title="<?=$orderitem->state->description?>" /> <?=$orderitem->state->name?>
					<?php } ?>
				</td>
				<td>
					<b>Артикул:</b> <?=$orderitem->article?><br />
					<b>Производитель:</b> <?=$orderitem->brand?><br />
					<b>Наименование:</b> <?=$orderitem->name?>
				</td>
				<td><?=$orderitem->supplier->name?></td>
				<td>
					<?php
					echo round(($orderitem->currency->code == 'UAH' ? $orderitem->purchase_per_unit : $orderitem->purchase_per_unit_in_currency) * $orderitem->amount, 2) . " " . $orderitem->currency->code;
					?>
				</td>
			</tr>
		<?php endforeach; ?>
		<tr>
			<td><b>Итого:</b></td>
			<td colspan="5">
				<div class="span2">
					<p><b>В работе на:</b></p>
					<?php $sum_in_UAH = 0;?>
					<?php foreach($total['in_work'] as  $curr_val): ?>
						<p><span><?=round($curr_val['val'], 2)?></span> <?=$curr_val['currency']->code?></p>
						<?php $sum_in_UAH += round($curr_val['val'], 2) * $curr_val['currency']->ratio;?>
					<?php endforeach; ?>
					<p style="border-top: 1px solid;"><?='<b>Сумма: ≈' . round($sum_in_UAH, 2) . ' UAH</b>'?></p>
				</div>
				<div class="span2">
					<p><b>Проплаты от клиента:</b></p>
					<p><?=round($total['client_payments'], 2)?> UAH</p>
				</div>
				<div class="span2">
					<p><b>Разница:</b></p>
					<p><?=round($sum_in_UAH - $total['client_payments'], 2)?> UAH</p>
				</div>
			</td>
		</tr>
		</tbody>
	</table>
	<?=$oi_pagination?>
	
	<b>Баланс до указанного периода: <?=$total['before']?> грн.</b><br>
	<b>Баланс: <span id="total"></span><br>
<!--	<b>Общий баланс: --><?//=($total['before'] + $total['payments'] - $total['orderitems'])?><!-- грн.</b>-->
</div>