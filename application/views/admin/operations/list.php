<div class="container">	
	<?= Form::open('', array('class' => 'form-horizontal', 'method' => 'get')); ?>
		Поставщик <?= Form::select('supplier_id', $suppliers, Arr::get($filters, 'supplier_id')); ?><br />
		
		<?= Form::submit('', 'Применить фильтр', array('class' => 'btn btn-primary')); ?>
	<?= Form::close(); ?><br /><br />
	
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th class="filter-false">ID</th>
				<th>Дата</th>
				<th>Брэнды</th>
				<th>Запчасти</th>
				<th>Несопоставленно</th>
				<th>Кроссы</th>
				<th>Позиции прайсов</th>
				<th>Описание</th>
				<th>Поставщик</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach($operations as $operation) : ?>
		<tr>
			<td><?=$operation->id?></td>
			<td><?php $d = new DateTime($operation->date_time); ?><?=$d->format('d.m.Y H:i:s')?></td>
			<td><?=$operation->brands->count_all()?> <a href="<?=URL::site('admin/operations/brands/'.$operation->id);?>"> Просмотр</a></td>
			<td><?=$operation->parts->count_all()?> <a href="<?=URL::site('admin/operations/parts/'.$operation->id);?>"> Просмотр</a></td>
			<td><?=$operation->unmatched->count_all()?> <a href="<?=URL::site('admin/operations/unmatched/'.$operation->id);?>"> Просмотр</a></td>
			<td><?=$operation->crosses->count_all()?></td>
			<td><?=$operation->priceitems->count_all()?></td>
			<td><?=$operation->description?></td>
			<td><?=$operation->supplier_id ? $operation->supplier->name : "---"?></td>
			<td>
				<a class="btn btn-mini btn-danger delete_row" href="<?=URL::site('admin/operations/delete/'.$operation->id);?>"><i class="icon-remove"></i> Удалить</a>
			</td>
		</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<?=$pagination?>
</div>