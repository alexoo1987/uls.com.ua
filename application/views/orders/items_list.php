<script>
	var print_url = '<?=URL::site('admin/orders/print_items');?>';
</script>
<div class="container">
	<div class="gap"></div>
	<span class="block">В заказе на сумму: <?=$order_details['debt_in_order']?> грн.</span>

	<table class="table1">
		<thead>
			<tr>
				<th>№ заказа</th>
				<th>Дата</th>
				<th>Состояние</th>
				<th>Доп. данные</th>
				<th>Цена за шт</th>
				<th>Кол-во</th>
				<th>Сумма</th>
				<th></th>
			</tr>
		</thead>
		
		<tbody>
			<?php foreach($orderitems as $orderitem) : ?>
			<tr>
				<td class="td6"><?=$orderitem->order->get_order_number()?></td>
				<td class="td6"><?php $d = new DateTime($orderitem->order->date_time); ?><?=$d->format('d.m.Y H:i:s')?></td>
				<td class="td6">
					<img src="<?=URL::base()?>media/img/states/<?=$orderitem->state->img?>" title="<?=$orderitem->state->description?>" /> <?=$orderitem->state->name?>
				</td>
				<td class="td6">
					<span  class="bold">Артикул:</span> <?=$orderitem->article?><br />
					<span  class="bold">Производитель:</span> <?=$orderitem->brand?><br />
					<span  class="bold">Наименование:</span> <?=$orderitem->name?><br />
					<?/*=(!empty($orderitem->order->manager_comment) ? "<b>Комментарий менеджера:</b> ".$orderitem->order->manager_comment."<br />" : "")?>
					<?=(!empty($orderitem->order->client_comment) ? "<b>Комментарий клиента:</b> ".$orderitem->order->client_comment."<br />" : "")*/?>
				</td>
				<td class="td6"><?=$orderitem->sale_per_unit?> грн.</td>
				<td class="td6"><?=$orderitem->amount?></td>
				<td class="td6"><?=$orderitem->sale_per_unit*$orderitem->amount?> грн.</td>
				<td class="td6">
					<?php if ($orderitem->state_id == 1): ?>
						<a href="<?= Helper_Url::createUrl(null, [
							'delitem' => $orderitem->id
						]) ?>" class="remove-order-item">Удалить</a>
					<?php endif ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>