<div class="container">	
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th class="filter-false">ID</th>
				<th>Имя</th>
				<th>Номер заказа</th>
				<th>Оценка в целом</th>
				<th>Оцнка менеджера</th>
				<th>Дата</th>
				<th>Показывать на сайте</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach($comments as $comment) : ?>
		<tr>
			<td><?=$comment->id?></td>
			<td><?=$comment->name?></td>
			<td><?=$comment->number_order?></td>
			<td><?=$comment->rating?></td>
			<td><?=$comment->manager_rating?></td>
			<td><?php $d = new DateTime($comment->date_time); ?><?=$d->format('d.m.Y H:i:s')?></td>
			<td><?=$comment->active == 1 ? "Да" : "Нет" ?></td>
			<td>
				<a class="btn btn-mini" href="<?=URL::site('admin/comments/edit/'.$comment->id);?>"><i class="icon-edit"></i> Подробнее</a>
			</td>
		</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<?=$pagination?>
</div>