<div class="container">
	<a class="btn btn-mini" href="<?=URL::site('admin/suppliers/add');?>"><i class="icon-plus"></i> Добавить</a><br /><br />
	<table class="table table-striped table-bordered">
		<tr>
			<th>№</th>
			<th>Название</th>
			<th>Телефон</th>
			<th>Дней доставки</th>
			<th>Коментарий</th>
			<th>Примечания</th>
			<th>Валюта</th>
			<th>Дата обновления</th>
			<th>Кол-во цен</th>
			<th>Статус загрузки</th>
			<th>Не показывать</th>
			<th></th>
		</tr>
		<?php $d_now = new DateTime(); ?>
		<?php $i = 0; ?>
		<?php foreach($suppliers as $supplier) : ?>
			<?php $d = new DateTime($supplier->update_time); ?>
			<?php $interval = $d->diff($d_now); ?>
			<?php $interval = intval($interval->format('%a')); ?>
		<tr class="<?=($interval > 7 ? 'need_update':'')?>">
			<td><?=++$i?></td>
			<td><?=$supplier->name?></td>
			<td><?=$supplier->phone?></td>
			<td><?=$supplier->delivery_days?></td>
			<td style="max-width: 25%;"><?=$supplier->сomment_text?><br><?=$supplier->price_source?></td>
			<td><?=$supplier->notice?></td>
			<td><?=$supplier->currency->name?></td>
			<td><?=$d->format('d.m.Y H:i:s')?></td>
			<td><?=$supplier->update_count?></td>
			<td><?=$supplier->get_status()?><?=$supplier->status == 'process' && $supplier->total_upload ? " ".round($supplier->total_processed * 100 / $supplier->total_upload)."%" : ""?></td>
			<td><?=$supplier->dont_show == 1 ? "Да" : "Нет"?></td>
			<td>
				<a class="btn btn-mini" href="<?=URL::site('admin/suppliers/edit/'.$supplier->id);?>"><i class="icon-edit"></i> Редактировать</a>
				<?php if($admin==true):?>
				<a class="btn btn-mini btn-danger delete_row" href="<?=URL::site('admin/suppliers/delete/'.$supplier->id);?>"><i class="icon-remove"></i> Удалить</a>
				<?php endif;?>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
</div>