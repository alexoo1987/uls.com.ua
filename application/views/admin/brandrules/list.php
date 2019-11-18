<div class="container">
	<a class="btn btn-mini" href="<?=URL::site('admin/brandrules/add/'.$brand_id);?>"><i class="icon-plus"></i> Добавить</a><br /><br />
	<a class="btn btn-mini" href="<?=URL::site('admin/brandrules/apply/'.$brand_id);?>"><i class="icon-refresh"></i> Применить правила</a><br /><br />
	<table class="table table-striped table-bordered">
		<tr>
			<th>ID</th>
			<th>Брэнд</th>
			<th>Правило</th>
			<th>Значение</th>
			<th></th>
		</tr>
		<?php foreach($brandrules as $brandrule) : ?>
		<tr>
			<td><?=$brandrule->id?></td>
			<td><?=$brandrule->brand->brand_long?></td>
			<td><?=$types[$brandrule->type]?></td>
			<td><?=$brandrule->value?></td>
			<td>
				<a class="btn btn-mini" href="<?=URL::site('admin/brandrules/edit/'.$brandrule->id);?>"><i class="icon-edit"></i> Редактировать</a>
				<a class="btn btn-mini btn-danger delete_row" href="<?=URL::site('admin/brandrules/delete/'.$brandrule->id);?>"><i class="icon-remove"></i> Удалить</a>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
</div>