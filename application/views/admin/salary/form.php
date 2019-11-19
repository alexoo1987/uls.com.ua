<div class="container">
    <div class="row">
		<div class="span6 offset3">
			<? if ($message) : ?>
				<h3 class="alert alert-info">
					<?= $message; ?>
				</h3>
			<? endif; ?>

			<?= Form::open('', array('class' => 'form-horizontal', 'id' => 'validate_form')); ?>
			<div class="control-group">
				<?= Form::label('from_id', 'Чей заказ', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::select('from_id', $managers, Arr::get($data, 'from_id'), array('validate' => 'required')); ?>
				</div>
			</div>
			
			<div class="control-group">
				<?= Form::label('to_id', 'Кто получает', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::select('to_id', $managers, Arr::get($data, 'to_id'), array('validate' => 'required')); ?>
				</div>
			</div>

			<div class="control-group">
				<?= Form::label('percentage', 'Процент', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('percentage', HTML::chars(Arr::get($data, 'percentage')), array('validate' => 'required|float|min,0|max,100')); ?>
					<span class="add-on">%</span>
					<? if ($err = Arr::get($errors, 'percentage')) : ?>
					<div class="alert alert-error">
						<?= $err; ?>
					</div>
					<? endif; ?>
				</div>
			</div>
			
			<div class="control-group">
				<div class="controls">
					<?= Form::submit('create', 'Сохранить', array('class' => 'btn btn-primary')); ?>
				</div>
			</div>
			<?= Form::close(); ?>
		</div>
    </div>
</div>