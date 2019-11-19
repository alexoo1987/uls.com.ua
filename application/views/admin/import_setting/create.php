<div class="container">
	<div class="row">
		<?= Form::open(URL::site('admin/importSetting/create/'), array('class' => 'form-horizontal', 'id' => 'validate_form', 'enctype' => 'multipart/form-data')); ?>
		<div class="control-group">
			<div class="controls">
				<?= Form::submit('create', 'Сохранить', array('class' => 'btn btn-primary')); ?>
				<button class="btn btn-success add_variant" type="button">Добавить параметры</button>
			</div>
		</div>
		<div class="span6">

			<div class="control-group">
				<?= Form::label('supplier_id', 'Поставщик', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::select('supplier_id', $suppliers, null, array('required' => 'required')); ?>
				</div>
			</div>
			<div class="control-group">
				<?= Form::label('dayOfWeek', 'Дни недели', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('dayOfWeek', null, array('required' => 'required')); ?>
				</div>
			</div>
			<div class="control-group">
				<?= Form::label('time', 'Часы', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('time', null, array('required' => 'required')); ?>
				</div>
			</div>
			<div class="control-group">
				<?= Form::label('from', 'E-mail отправителя', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('from', null, array('required' => 'required')); ?>
				</div>
			</div>
			<div class="control-group">
				<?= Form::label('subject', 'Тема письма', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('subject', null, array('required' => 'required')); ?>
				</div>
			</div>
			<div class="control-group">
				<?= Form::label('ext', 'Формат файла', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('ext', null, array('required' => 'required')); ?>
				</div>
			</div>
			<div class="control-group">
				<?= Form::label('currency_id', 'Валюта', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::select('currency_id', $currencies, Arr::get($currencies, 'currency_id'), array('required' => 'required')); ?>
				</div>
			</div>
			<div class="control-group">
				<?= Form::label('firstLine', 'Начинать со строки', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('firstLine', null, ['type' => 'number', 'required' => 'required']); ?>
				</div>
			</div>
			<div class="control-group">
				<?= Form::label('encoding', 'Кодировка', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::radio('encoding', 1, null, ['type' => 'number', 'required' => 'required']); ?> UTF8<br />
					<?= Form::radio('encoding', 2, null, ['type' => 'number', 'required' => 'required']); ?> CP1251
				</div>
			</div>
			<div class="control-group">
				<?= Form::label('comment', 'Комментарий', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::textarea('comment'); ?>
				</div>
			</div>
		</div>
		<div class="span6">

			<div class="control-group">
				<h4>Колонки:</h4>
				<?= Form::label('article', 'Артикул', array('class' => 'control-label')); ?>
				<div class="controls"><?= Form::input('article', null, ['type' => 'number', 'required' => 'required']); ?></div>

				<?= Form::label('brand', 'Бренд', array('class' => 'control-label')); ?>
				<div class="controls"><?= Form::input('brand', null, ['type' => 'number', 'required' => 'required']); ?></div>

				<?= Form::label('price', 'Цена', array('class' => 'control-label')); ?>
				<div class="controls"><?= Form::input('price', null, ['type' => 'number', 'required' => 'required']); ?></div>

				<?= Form::label('name', 'Название', array('class' => 'control-label')); ?>
				<div class="controls"><?= Form::input('name', null, ['type' => 'number', 'required' => 'required']); ?></div>

			</div>

		</div>
		<div class="span12" style="clear: both">
			<div class="alert alert-block">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<?= Form::label('count[]', 'Количество', array('class' => 'control-label')); ?>
				<div class="controls"><?= Form::input('count[]', null, ['type' => 'number','placeholder' => '№ столбца']); ?></div>

				<?= Form::label('delivery[]', 'Доставка', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('delivery[]', null, ['type' => 'number','placeholder' => '№ столбца']); ?>
					или <?= Form::input('delivery_const[]', null, ['type' => 'number','placeholder' => 'Значение']); ?>
				</div>
			</div>
		</div>

		<?= Form::close(); ?>

	</div>