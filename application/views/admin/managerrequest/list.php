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
		<?php foreach($managerrequests as $managerrequest) : ?>
		<tr>
			<td><?=$managerrequest->id?></td>
			<td><?=$managerrequest->name?></td>
			<td><?=$managerrequest->phone?></td>
			<td><?=$managerrequest->description?></td>
			<td><?=$statuses[$managerrequest->status]?></td>
			<td><?php $d = new DateTime($managerrequest->date_time); ?><?=$d->format('d.m.Y H:i:s')?></td>
			<td>
				<a class="btn btn-mini" href="<?=URL::site('admin/managerrequest/edit/'.$managerrequest->id);?>"><i class="icon-edit"></i> Подробнее</a>
			</td>
		</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<?=$pagination?>
</div>