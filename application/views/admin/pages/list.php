<div class="container">
	<a class="btn btn-mini" href="<?=URL::site('admin/pages/add');?>"><i class="icon-plus"></i> Добавить</a><br /><br />
	<table class="table table-striped table-bordered">
		<tr>
			<th>ID</th>
			<th>H1 title</th>
			<th>Title</th>
			<th>URL</th>
			<th>Активна</th>
			<th></th>
		</tr>
		<?php foreach($pages as $page) : ?>
		<tr>
			<td><?=$page->id?></td>
			<td><?=$page->h1_title?></td>
			<td><?=$page->title?></td>
			<td><?=$page->syn?></td>
			<td><?=$page->active ? "Да" : "Нет"?></td>
			<td>
				<a class="btn btn-mini" href="<?=URL::site('admin/pages/edit/'.$page->id);?>"><i class="icon-edit"></i> Редактировать</a>
				<a class="btn btn-mini btn-danger delete_row" href="<?=URL::site('admin/pages/delete/'.$page->id);?>"><i class="icon-remove icon-white"></i> Удалить</a>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
</div>