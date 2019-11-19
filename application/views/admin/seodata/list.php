<div class="container">
	<a class="btn btn-mini" href="<?=URL::site('admin/seodata/add');?>"><i class="icon-plus"></i> Добавить</a><br /><br />
	
	<?= Form::open('', array('class' => 'form-horizontal', 'method' => 'get')); ?>
		URL <?= Form::input('seo_identifier', HTML::chars(Arr::get($filters, 'seo_identifier'))); ?><br />
		<?= Form::submit('', 'Применить фильтр', array('class' => 'btn btn-primary')); ?>
	<?= Form::close(); ?>
	<a href="<?=URL::site('admin/clients');?>" class="btn btn-primary"><i class="icon-white icon-refresh"></i> Сброс</a><br /><br />


	<table class="table table-striped table-bordered">
		<tr>
			<th>URL</th>
			<th>H1</th>
			<th>Title</th>
			<th></th>
		</tr>
		<?php foreach($seodata as $sd_item) : ?>
		<tr>
			<td><?=$sd_item->seo_identifier?></td>
			<td><?=$sd_item->h1?></td>
			<td><?=$sd_item->title?></td>
			<td>
				<a class="btn btn-mini" href="<?=URL::site('admin/seodata/edit/'.$sd_item->id);?>"><i class="icon-edit"></i> Редактировать</a>
				<a class="btn btn-mini btn-danger delete_row" href="<?=URL::site('admin/seodata/delete/'.$sd_item->id);?>"><i class="icon-remove icon-white"></i> Удалить</a>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
	<?=$pagination?>
</div>