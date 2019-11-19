<div class="container">
	<a class="btn btn-mini" href="<?=URL::site('admin/menu/add');?>"><i class="icon-plus"></i> Добавить</a><br /><br />
	<table class="table table-striped table-bordered">
		<tr>
			<th>ID</th>
			<th>Название</th>
			<th>Идентификатор</th>
			<th></th>
		</tr>
		<?php foreach($menus as $menu) : ?>
		<tr>
			<td><?=$menu->id?></td>
			<td><?=$menu->name?></td>
			<td><?=$menu->identifier?></td>
			<td>
				<a class="btn btn-mini" href="<?=URL::site('admin/menu/edit/'.$menu->id);?>"><i class="icon-edit"></i> Редактировать</a>
				<a class="btn btn-mini btn-info" href="<?=URL::site('admin/menu/items/'.$menu->id);?>"><i class="icon-list icon-white"></i> Пункты меню</a>
				<a class="btn btn-mini btn-danger delete_row" href="<?=URL::site('admin/menu/delete/'.$menu->id);?>"><i class="icon-remove icon-white"></i> Удалить</a>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
</div>