<div class="container">
    <div class="row">
		<div class="span6 offset3">
			<? if ($message) : ?>
				<h3 class="alert alert-info">
					<?= $message; ?>
				</h3>
			<? endif; ?>

			<?= Form::open('', array('autocomplete' => 'off')); ?>
			<div class="form-group">
				<?= Form::label('brand', 'Производитель*'); ?>
				<div class="controls">
					<?= Form::input('brand', HTML::chars(Arr::get($data, 'brand')), array('required' => 'required','class' => 'form-control')); ?>
				</div>
			</div>
			<div class="form-group">
				<?= Form::label('model', 'Модель*'); ?>
				<div class="controls">
					<?= Form::input('model', HTML::chars(Arr::get($data, 'model')), array('required' => 'required','class' => 'form-control')); ?>
				</div>
			</div>
			<div class="form-group">
				<?= Form::label('vin', 'VIN код*'); ?>
				<div class="controls">
					<?= Form::input('vin', HTML::chars(Arr::get($data, 'vin')), array('required' => 'required','class' => 'form-control')); ?>
				</div>
			</div>
			<div class="form-group">
				<?= Form::label('engine', 'Двигатель'); ?>
				<div class="controls">
					<?= Form::input('engine', HTML::chars(Arr::get($data, 'engine')), array('class' => 'form-control')); ?>
				</div>
			</div>
			<div class="form-group">
				<?= Form::label('year', 'Год выпуска'); ?>
				<div class="controls">
					<?= Form::input('year', HTML::chars(Arr::get($data, 'year'))); ?>
				</div>
			</div>
			
			<div class="form-group">
				<div class="controls">
					<?= Form::submit('submit', 'Сохранить', array('class' => 'btn btn-primary')); ?>
				</div>
			</div>
			<?= Form::close(); ?>
		</div>
    </div>
</div>