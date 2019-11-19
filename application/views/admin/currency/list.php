<div class="container">
	<a class="btn btn-mini" href="<?=URL::site('admin/currency/add');?>"><i class="icon-plus"></i> Добавить</a><br /><br />
	<table class="table table-striped table-bordered">
		<tr>
			<th>ID</th>
			<th>Название</th>
			<th>Коэфициент</th>
			<th></th>
		</tr>
		<?php foreach($currencies as $currency) : ?>
		<tr>
			<td><?=$currency->id?></td>
			<td><?=$currency->name?></td>
			<td><?=$currency->ratio?> грн.</td>
			<td>
				<a class="btn btn-mini" href="<?=URL::site('admin/currency/edit/'.$currency->id);?>"><i class="icon-edit"></i> Редактировать</a>
				<a class="btn btn-mini btn-danger delete_row" href="<?=URL::site('admin/currency/delete/'.$currency->id);?>"><i class="icon-remove"></i> Удалить</a>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
</div>