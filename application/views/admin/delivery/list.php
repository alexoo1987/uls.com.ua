<div class="container">
	<a class="btn btn-mini" href="<?=URL::site('admin/delivery/add');?>"><i class="icon-plus"></i> Добавить</a><br /><br />
	<table class="table table-striped table-bordered">
		<tr>
			<th>ID</th>
			<th>Название</th>
			<th>Стоимость</th>
			<th></th>
		</tr>
		<?php foreach($delivery_methods as $delivery_method) : ?>
		<tr>
			<td><?=$delivery_method->id?></td>
			<td><?=$delivery_method->name?></td>
			<td><?=$delivery_method->price?>.00 грн.</td>
			<td>
				<a class="btn btn-mini" href="<?=URL::site('admin/delivery/edit/'.$delivery_method->id);?>"><i class="icon-edit"></i> Редактировать</a>
				<a class="btn btn-mini btn-danger delete_row" href="<?=URL::site('admin/delivery/delete/'.$delivery_method->id);?>"><i class="icon-remove"></i> Удалить</a>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
</div>