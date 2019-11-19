<div class="container">	
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th class="filter-false">ID</th>
				<th>Имя</th>
				<th>Телефон</th>
				<th>Описание</th>
				<th>Статус</th>
				<th>Дата</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach($carsales as $carsale) : ?>
		<tr>
			<td><?=$carsale->id?></td>
			<td><?=$carsale->name?></td>
			<td><?=$carsale->phone?></td>
			<td><?=$carsale->description?></td>
			<td><?=$statuses[$carsale->status]?></td>
			<td><?php $d = new DateTime($carsale->date_time); ?><?=$d->format('d.m.Y H:i:s')?></td>
			<td>
				<a class="btn btn-mini" href="<?=URL::site('admin/carsales/edit/'.$carsale->id);?>"><i class="icon-edit"></i> Подробнее</a>
			</td>
		</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<?=$pagination?>
</div>