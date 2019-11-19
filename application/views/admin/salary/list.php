<div class="container">
	<a class="btn btn-mini" href="<?=URL::site('admin/salary/add');?>"><i class="icon-plus"></i> Добавить</a><br /><br />
	<table class="table table-striped table-bordered">
		<tr>
			<th>ID</th>
			<th>Чей заказ</th>
			<th>Кто получает</th>
			<th>Процент</th>
			<th></th>
		</tr>
		<?php foreach($salaries as $salary) : ?>
		<tr>
			<td><?=$salary->id?></td>
			<td><?=$salary->user_from->surname?></td>
			<td><?=$salary->user_to->surname?></td>
			<td><?=$salary->percentage?> %</td>
			<td>
				<a class="btn btn-mini" href="<?=URL::site('admin/salary/edit/'.$salary->id);?>"><i class="icon-edit"></i> Редактировать</a>
				<a class="btn btn-mini btn-danger delete_row" href="<?=URL::site('admin/salary/delete/'.$salary->id);?>"><i class="icon-remove"></i> Удалить</a>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
</div>