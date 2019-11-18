<div class="container">
    <div class="row">
		<div class="span6 offset3">
			<? if ($message) : ?>
				<h3 class="alert alert-info">
					<?= $message; ?>
				</h3>
			<? endif; ?>

			<?= Form::open('', array('class' => 'form-horizontal')); ?>
			<div class="control-group">
				<?= Form::label('name', '1', array('class' => 'control-label')); ?><?= Form::input('name', HTML::chars(Arr::get($data, 'name'))); ?>
				<div class="controls">
					<? if ($err = Arr::get($errors, 'name')) : ?>
					<div class="alert alert-error">
						<?= $err; ?>
					</div>
					<? endif; ?>
				</div>
			</div>

			<div class="control-group">
				<?= Form::label('ratio', '=', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('ratio', HTML::chars(Arr::get($data, 'ratio'))); ?>
					<span class="add-on"> грн.</span>
					<? if ($err = Arr::get($errors, 'ratio')) : ?>
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