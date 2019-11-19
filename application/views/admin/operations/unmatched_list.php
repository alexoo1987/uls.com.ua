<div class="container">
	<?= Form::open('', array('class' => 'form-horizontal', 'method' => 'get')); ?>
		Артикул <?= Form::input('article', HTML::chars(Arr::get($filters, 'article'))); ?><br />
		Брэнд <?= Form::input('brand', HTML::chars(Arr::get($filters, 'brand'))); ?> или <?= Form::select('brand_select', $brands_list, Arr::get($filters, 'brand_select')); ?><br />
		
		<?= Form::submit('', 'Применить фильтр', array('class' => 'btn btn-primary')); ?>
	<?= Form::close(); ?>

	<h3>Добавить все по брэнду</h3>
	<?= Form::open('', array('class' => 'form-horizontal', 'method' => 'post')); ?>
		Брэнд <?= Form::select('brand_add', $brands_list, Arr::get($filters, 'brand_select')); ?><br />
		
		<?= Form::submit('', 'Добавить', array('class' => 'btn btn-primary')); ?>
	<?= Form::close(); ?>
	<?= Form::open('', array('class' => 'form-horizontal', 'method' => 'post')); ?>
	<?= Form::submit('all_brands', 'Добавить все бренды', array('class' => 'btn btn-primary')); ?>
	<?= Form::close(); ?>


	<?= Form::open('', array('method' => 'post')); ?>
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th class="filter-false">ID</th>
				<th>Артикул</th>
				<th>Брэнд</th>
				<th>Название</th>
				<th>Причина</th>
				<th><input type="checkbox" id="select_all" /> Действие</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach($unmatched_list as $unmatched) : ?>
		<tr>
			<td><?=$unmatched->id?></td>
			<td><?=$unmatched->article?></td>
			<td><?=$unmatched->brand?></td>
			<td><?=$unmatched->name?></td>
			<td><?=$unmatched->get_reason()?></td>
			<td><input type="checkbox" name="unmatched_id[]" value="<?=$unmatched->id?>" /> <?=($unmatched->reason == 'bad_brand' ? 'Добавить бренд и пересопоставить артикулы этого бренда' : 'Добавить артикул')?></td>
		</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
		<?= Form::submit('', 'Сохранить', array('class' => 'btn btn-primary')); ?>
	<?= Form::close(); ?>
	<?=$pagination?>
</div>