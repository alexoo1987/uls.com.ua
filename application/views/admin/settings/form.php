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
				<?= Form::label('value', 'Значение', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('value', HTML::chars(Arr::get($data, 'value')), array('validate' => 'required')); ?>
					<? if ($err = Arr::get($errors, 'value')) : ?>
					<div class="alert alert-error">
						<?= $err; ?>
					</div>
					<? endif; ?>
				</div>
			</div>
			
			<div class="control-group">
				<div class="controls">
					<?= Form::submit('submit', 'Save', array('class' => 'btn btn-primary')); ?>
				</div>
			</div>
			<?= Form::close(); ?>
		</div>
    </div>
</div>