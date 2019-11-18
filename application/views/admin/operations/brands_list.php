<div class="container">
	<?= Form::open('', array('class' => 'form-horizontal', 'method' => 'get')); ?>
		Брэнд <?= Form::input('brand', HTML::chars(Arr::get($filters, 'brand'))); ?><br />
		
		<?= Form::submit('', 'Применить фильтр', array('class' => 'btn btn-primary')); ?>
	<?= Form::close(); ?>


	<?= Form::open('', array('method' => 'post')); ?>
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th class="filter-false">ID</th>
				<th>Брэнд</th>
				<th>Запчасти</th>
				<th>Предложения</th>
				<th>Заменить на</th>
				<th>Не загружать</th>
				<th>TecDoc</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach($brands as $brand) : ?>
		<tr>
			<td><?=$brand->id?></td>
			<td><?=$brand->brand_long?></td>
			<td><?=ORM::factory('Part')->where('brand', '=', $brand->brand)->count_all()?>
				<a href="<?=URL::site('admin/operations/parts');?>?brand=<?=$brand->brand?>"> Найти</a>
			</td>
			<td>
				<div class="accordion-heading">
					<a class="accordion-toggle" data-toggle="collapse" href="#collapse<?=$brand->brand?>">
						<?=ORM::factory('Priceitem')->with('part')->where('brand', '=', $brand->brand)->count_all()?>
					</a>
				</div>
				<?php $result = DB::select(array(DB::expr('COUNT(*)'), 'c'), array('suppliers.name', 'supplier'))
					->from('priceitems')->group_by('supplier_id')
					->join('parts', 'left')
					->on('priceitems.part_id', '=', 'parts.id')
					->join('suppliers', 'left')
					->on('priceitems.supplier_id', '=', 'suppliers.id')
					->where('parts.brand', '=', $brand->brand)
					->order_by('c', 'DESC')->execute()
					->as_array();?>

						<div id="collapse<?=$brand->brand?>" class="accordion-body collapse">
							<div class="accordion-inner">
								<?php
								foreach ($result AS $row) {
									echo "<p>" . $row['supplier'] . " (" . $row['c'] . ")</p>";
								}
								?>
							</div>
						</div>
			</td>
			<td><?= Form::input('change_to['.$brand->id.']', $brand->change_to); ?></td>
			<td><?= Form::checkbox('dont_upload['.$brand->id.']', 1, ($brand->dont_upload == 1)); ?></td>
			<td><?= (!empty($brand->tecdoc_id) ? "Да" : "Нет"); ?></td>
			<td><a class="btn btn-mini" href="<?=URL::site('admin/brandrules/list/'.$brand->id);?>"><i class="icon-edit"></i> Правила(<?=$brand->brandrules->count_all()?>)</a></td>
		</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
		<?= Form::submit('', 'Сохранить', array('class' => 'btn btn-primary')); ?>
	<?= Form::close(); ?>
	<?=$pagination?>
</div>