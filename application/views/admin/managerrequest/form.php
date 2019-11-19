<div class="container">
    <div class="row">
		<div class="span6 offset3">
			<? if ($message) : ?>
				<h3 class="alert alert-info">
					<?= $message; ?>
				</h3>
			<? endif; ?>
			
			<div>
				<?php /*<b>Марка авто:</b> <?=Arr::get($data, 'manufacturer')?><br>
				<b>Модель:</b> <?=Arr::get($data, 'model')?><br>
				<b>Модификация:</b> <?=Arr::get($data, 'modification')?><br>
				<b>Год выпуска:</b> <?=Arr::get($data, 'year')?><br>
				<b>Объем:</b> <?=Arr::get($data, 'volume')?><br>
				<b>VIN:</b> <?=Arr::get($data, 'vin')?><br>
				<b>Город:</b> <?=Arr::get($data, 'city')?><br> */?>
				<b>Имя:</b> <?=Arr::get($data, 'name')?><br>
				<b>Телефон:</b> <?=Arr::get($data, 'phone')?><br>
				<b>Описание запчасти:</b> <?=Arr::get($data, 'description')?><br>
			</div>

			<?= Form::open('', array('class' => 'form-horizontal', 'id' => 'validate_form', 'autocomplete' => 'off')); ?>
			
			<div class="control-group">
				<?= Form::label('status', 'Статус', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::select('status', $statuses, Arr::get($data, 'status')); ?>
				</div>
			</div>
			
			<div class="control-group">
				<div class="controls">
					<?= Form::submit('submit', 'Сохранить', array('class' => 'btn btn-primary')); ?>
				</div>
			</div>
			<?= Form::close(); ?>
		</div>
    </div>
</div>