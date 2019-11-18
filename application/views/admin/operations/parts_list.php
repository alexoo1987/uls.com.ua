<div class="container">
	<?= Form::open('', array('class' => 'form-horizontal', 'method' => 'get')); ?>
		Артикул <?= Form::input('article', HTML::chars(Arr::get($filters, 'article'))); ?><br />
		Брэнд <?= Form::input('brand', HTML::chars(Arr::get($filters, 'brand'))); ?><br />
		
		<?= Form::submit('', 'Применить фильтр', array('class' => 'btn btn-primary')); ?>
	<?= Form::close(); ?>


	<?= Form::open('', array('method' => 'post')); ?>
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th class="filter-false">ID</th>
				<th>Артикул</th>
				<th>Брэнд</th>
				<th>Прайсы</th>
				<th>Название</th>
				<th>TecDoc</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach($parts as $part) : ?>
		<tr>
			<td><?=$part->id?></td>
			<td><?=$part->article_long?></td>
			<td><?=$part->brand_long?>
				<a href="<?=URL::site('admin/operations/brands');?>?brand=<?=$part->brand?>"> Перейти</a>
			</td>
			<td><?=$part->priceitems->count_all()?></td>
			<td><?= Form::textarea('name['.$part->id.']', $part->name, array('rows' => '2')); ?></td>
			<td><?= (!empty($part->tecdoc_id) ? "Да" : "Нет"); ?></td>
		</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
		<?= Form::submit('', 'Сохранить', array('class' => 'btn btn-primary')); ?>
	<?= Form::close(); ?>
	<?=$pagination?>
</div>