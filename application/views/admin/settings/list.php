<div class="container">
	<table class="table table-striped table-bordered">
		<tr>
			<th>ID</th>
			<th>Код настройки</th>
			<th>Наименование</th>
			<th>Значение</th>
			<th></th>
		</tr>
		<?php foreach($settings as $setting) : ?>
		<tr>
			<td><?=$setting->id?></td>
			<td><?=$setting->code_name?></td>
			<td><?=$setting->title?></td>
			<td><?=$setting->value?></td>
			<td>
				<a class="btn btn-mini" href="<?=URL::site('admin/settings/edit/'.$setting->id);?>"><i class="icon-edit"></i> Редактировать</a>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>

	<h3>Категории затрат</h3>
	<button type="button" class="btn btn-success" data-toggle="modal" data-target="#create_costs_type">
		Создать
	</button>

	<div class="modal fade" id="create_costs_type" tabindex="-1" role="dialog" aria-labelledby="create_costs_typeLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<form method="POST">
					<div class="modal-header">
						<h4 class="modal-title" id="create_costs_typeLabel">Создать категорию затрат</h4>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<label for="costs_type">Название</label>
							<input type="text" class="form-control" id="costs_type" name="costs_type" required>
							<label>
								<input type="checkbox" name="costs_subtract" value="1"> Списывать с заказов
							</label>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
						<button type="submit" class="btn btn-primary">Сохранить</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<table class="table table-striped table-bordered">
		<tr>
			<th>Название</th>
			<th>Списывать</th>
		</tr>
		<?php foreach ($costs_type as $one) : ?>
			<tr>
				<td><?= $one['type'] ?></td>
				<td><?= $one['subtract'] ? 'Да' : 'Нет' ?></td>
				<td>
					<button class="btn btn-mini" data-toggle="modal" data-target="#edit_costs_type_<?= $one['id'] ?>"><i
							class="icon-edit"></i> Редактировать
					</button>
					<br>
					<br>
					<a class="btn btn-mini" href="<?=URL::site('admin/settings/delete/'.$one["id"]);?>"><i class="icon-edit"></i> Скрыть</a>
				</td>
			</tr>
			<div class="modal fade" id="edit_costs_type_<?= $one['id'] ?>" tabindex="-1" role="dialog"
				 aria-labelledby="edit_costs_typeLabel">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<form method="POST">
							<div class="modal-header">
								<h4 class="modal-title" id="create_costs_typeLabel">Изменить категорию затрат</h4>
							</div>
							<div class="modal-body">
								<div class="form-group">
									<label for="costs_type">Название</label>
									<input type="text" class="form-control" id="costs_type" name="costs_type"
										   value="<?= $one['type'] ?>" required>
									<label>
										<input type="checkbox" name="costs_subtract"
											   value="1" <?= $one['subtract'] ? 'checked' : '' ?>> Списывать с заказов
									</label>
									<input type="hidden" name="id" value="<?= $one['id'] ?>"/>
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
								<button type="submit" class="btn btn-primary">Сохранить</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</table>
</div>