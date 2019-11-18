<div class="container">
    <div class="row">
		<div class="span6 offset3">
			<? if ($message) : ?>
				<h3 class="alert alert-info">
					<?= $message; ?>
				</h3>
			<? endif; ?>
			
			<div>
				<b>Имя:</b> <?=Arr::get($data, 'name')?><br>
				<b>Телефон:</b> <?=Arr::get($data, 'phone')?><br>
				<b>E-mail:</b> <?=Arr::get($data, 'email')?><br>
				<b>Марка авто:</b> <?=Arr::get($data, 'manufacturer')?><br>
				<b>Модель:</b> <?=Arr::get($data, 'model')?><br>
				<b>Модификация:</b> <?=Arr::get($data, 'modification')?><br>
				<b>Год выпуска:</b> <?=Arr::get($data, 'year')?><br>
				<b>Объем двигателя:</b> <?=Arr::get($data, 'volume')?><br>
				<b>Цвет:</b> <?=Arr::get($data, 'color')?><br>
				<b>Трансмиссия:</b> <?=Arr::get($data, 'transmission')?><br>
				<b>Количество крашенных деталей:</b> <?=Arr::get($data, 'parts_number')?><br>
				<b>Описание авто:</b> <?=Arr::get($data, 'description')?><br>
				<b>Цена:</b> <?=Arr::get($data, 'price')?><br>
				<?php foreach(array('photo1', 'photo2', 'photo3') as $key): ?>
					<?php if(!empty($data[$key])): ?>
						<img src="<?= URL::base(); ?>uploads/carsale/<?=$data[$key]?>" /><br>
					<?php endif; ?>
				<?php endforeach; ?>
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