<div class="container">	
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>Производитель</th>
				<th>Код</th>
				<th>Описание</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach($manufacturers as $manufacturer) : ?>
		<tr>
			<td><?=$manufacturer['brand']?></td>
			<td><?=$manufacturer['code']?></td>
			<td><?=$manufacturer['description']?></td>
			<td>
				<a class="btn btn-mini" href="<?=URL::site('admin/tecdoc/manufacturers_edit/'.$manufacturer['id']);?>"><i class="icon-edit"></i> Редактировать</a>
			</td>
		</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>