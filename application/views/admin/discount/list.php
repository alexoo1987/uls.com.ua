<div class="container">
	<a class="btn btn-mini" href="<?=URL::site('admin/discount/add');?>"><i class="icon-plus"></i> Добавить</a><br /><br />
	<table class="table table-striped table-bordered">
		<tr>
			<th>ID</th>
			<th>Название</th>
			<th>Стандарт</th>
			<th>По-умолчанию в админке</th>
			<th>Накрутки</th>
			<th></th>
		</tr>
		<?php foreach($discounts as $discount) : ?>
		<tr>
			<td><?=$discount->id?></td>
			<td><?=$discount->name?></td>
			<td><?=($discount->standart == 1 ? "Да" : "Нет")?></td>
			<td><?=($discount->admin_default == 1 ? "Да" : "Нет")?></td>
			<td>
			<?php foreach($discount->discount_limits->find_all()->as_array() as $dl) {
				echo $dl->from." грн. ... ".($dl->to > 0 ? $dl->to." грн." : "&infin;").": ".$dl->percentage."%<br />";
			} ?>
			</td>
			<td>
				<a class="btn btn-mini" href="<?=URL::site('admin/discount/edit/'.$discount->id);?>"><i class="icon-edit"></i> Редактировать</a>
				<?php if($discount->id > 1) { ?><a class="btn btn-mini btn-danger delete_row" href="<?=URL::site('admin/discount/delete/'.$discount->id);?>"><i class="icon-remove"></i> Удалить</a><?php } ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
</div>