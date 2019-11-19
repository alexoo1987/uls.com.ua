<div class="container">
	<a class="btn btn-mini" href="<?=URL::site('admin/videos/create/');?>"><i class="icon-edit"></i> Создать</a>
	<br>

	<table class="table table-striped table-bordered">
		<thead>
		<tr>
			<th class="filter-false">ID</th>
			<th>Сылка</th>
			<th>В админке</th>
			<th>На сайте</th>
			<th></th>
		</tr>
		</thead>
		<tbody>
		<?php foreach($videos as $video) : ?>
			<tr>
				<td><?=$video->id?></td>
				<td><?=$video->url?></td>
				<td><?=$video->admin?></td>
				<td><?=$video->site?></td>

				<td>
					<a class="btn btn-mini" href="<?=URL::site('admin/videos/edit/'.$video->id);?>"><i class="icon-edit"></i> Редактировать</a><br>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>