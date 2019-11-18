<div class="container">
    <div class="row">
		<div class="span6 offset3">
			<? if ($message) : ?>
				<h3 class="alert alert-info">
					<?= $message; ?>
				</h3>
			<? endif; ?>

			<?= Form::open('', array('class' => 'form-horizontal', 'id' => 'validate_form'));?>
			<div class="control-group">
				<?= Form::label('name', 'Название', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('name', HTML::chars(Arr::get($data, 'name')), array('validate' => 'required')); ?>
					<? if ($err = Arr::get($errors, 'name')) : ?>
					<div class="alert alert-error">
						<?= $err; ?>
					</div>
					<? endif; ?>
				</div>
			</div>

			<div class="control-group">
				<?= Form::label('identifier', 'Идентификатор', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('identifier', HTML::chars(Arr::get($data, 'identifier')), array('validate' => 'required')); ?>
					<? if ($err = Arr::get($errors, 'identifier')) : ?>
					<div class="alert alert-error">
						<?= $err; ?>
					</div>
					<? endif; ?>
				</div>
			</div>

			<div class="control-group">
				<?= Form::label('max_levels', 'Максимальное число уровней', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('max_levels', HTML::chars(Arr::get($data, 'max_levels')), array('validate' => 'required|number|max,3')); ?>
					<? if ($err = Arr::get($errors, 'max_levels')) : ?>
					<div class="alert alert-error">
						<?= $err; ?>
					</div>
					<? endif; ?>
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