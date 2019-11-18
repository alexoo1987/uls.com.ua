<div class="container">
	<a class="btn btn-mini" href="<?=URL::site('admin/cars/add/'.$client_id);?>"><i class="icon-plus"></i> Добавить</a><br /><br />
	<table class="table table-striped table-bordered">
		<tr>
			<th>Авто</th>
			<th>VIN код</th>
			<th>Двигатель</th>
			<th>Год выпуска</th>
			<th></th>
		</tr>
		<?php foreach($cars as $car) : ?>
		<tr>
			<td><?=$car->brand?> <?=$car->model?></td>
			<td><?=$car->vin?></td>
			<td><?=$car->engine ? $car->engine : "-----"?></td>
			<td><?=$car->year ? $car->year : "-----"?></td>
			<td>
				<a class="btn btn-mini" href="<?=URL::site('admin/cars/edit/'.$car->id);?>"><i class="icon-edit"></i> Редактировать</a>
				<a class="btn btn-mini btn-danger delete_row_cars" href="<?=URL::site('admin/cars/delete/'.$car->id);?>"><i class="icon-remove"></i> Удалить</a>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
</div>