<div class="container">
    <div class="row">
		<div class="span6 offset1">
			<? if ($message) : ?>
				<span class="block alert alert-info">
					<?= $message; ?>
				</span>
			<? endif; ?>
			<?= Form::open('', array('class' => 'form-horizontal', 'id' => 'validate_form', 'autocomplete' => 'off')); ?>
			<?php /*<div class="control-group">
				<?= Form::label('manufacturer', 'Марка автомобиля*', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('manufacturer', HTML::chars(Arr::get($data, 'manufacturer')), array('validate' => 'required')); ?>
				</div>
			</div>
			<div class="control-group">
				<?= Form::label('model', 'Модель*', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('model', HTML::chars(Arr::get($data, 'model')), array('validate' => 'required')); ?>
				</div>
			</div>
			<div class="control-group">
				<?= Form::label('modification', 'Модификация', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('modification', HTML::chars(Arr::get($data, 'modification'))); ?>
				</div>
			</div>
			<div class="control-group">
				<?= Form::label('year', 'Год выпуска*', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('year', HTML::chars(Arr::get($data, 'year')), array('validate' => 'required')); ?>
				</div>
			</div>
			<div class="control-group">
				<?= Form::label('volume', 'Объем', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('volume', HTML::chars(Arr::get($data, 'volume'))); ?>
				</div>
			</div>
			<div class="control-group">
				<?= Form::label('vin', 'VIN код*', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('vin', HTML::chars(Arr::get($data, 'vin')), array('validate' => 'required')); ?>
				</div>
			</div>
			<div class="control-group">
				<?= Form::label('city', 'Город*', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('city', HTML::chars(Arr::get($data, 'city')), array('validate' => 'required')); ?>
				</div>
			</div>*/?>
			<div class="control-group">
				<?= Form::label('name', 'Имя*', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('name', HTML::chars(Arr::get($data, 'name')), array('validate' => 'required')); ?>
				</div>
			</div>
			<div class="control-group">
				<?= Form::label('phone', 'Телефон*', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('phone', HTML::chars(Arr::get($data, 'phone')), array('class' => 'bfh-phone', 'data-format' => '(ddd)ddd-dd-dd', 'data-number' => preg_replace('/[^0-9]/', '', HTML::chars(Arr::get($data, 'phone'))), 'validate' => 'required|phone')); ?>
				</div>
			</div>
			<div class="control-group">
				<?= Form::label('description', 'Описание запчасти*', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::textarea('description', HTML::chars(Arr::get($data, 'description')), array('rows' => '4', 'validate' => 'required')); ?>
				</div>
			</div>
			
			<div class="control-group">
				<div class="controls">
					<?= Form::submit('submit', 'Отправить', array('class' => 'btn btn-success')); ?>
				</div>
			</div>
			<?= Form::close(); ?>
		</div>
    </div>
</div>