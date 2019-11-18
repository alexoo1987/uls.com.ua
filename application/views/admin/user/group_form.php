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
				<?= Form::label('name', 'Имя', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('name', HTML::chars(Arr::get($data, 'name'))); ?>
					<? if ($err = Arr::get($errors, 'name')) : ?>
					<div class="alert alert-error">
						<?= $err; ?>
					</div>
					<? endif; ?>
				</div>
			</div>

			<div class="control-group">
				<?= Form::label('description', 'Описание', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('description', HTML::chars(Arr::get($data, 'description'))); ?>
					<? if ($err = Arr::get($errors, 'description')) : ?>
					<div class="alert alert-error">
						<?= $err; ?>
					</div>
					<? endif; ?>
				</div>
			</div>
			
			<?php
				foreach($permissions as $permission) {
			?>
			
			<div class="control-group">
				<?= Form::label($permission->name, $permission->description, array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::checkbox($permission->name, 1, (Arr::get($data, $permission->name) == "1" ? TRUE : FALSE)); ?>
				</div>
			</div>
			<?php
				}
			?>

			
			<div class="control-group">
				<div class="controls">
					<?= Form::submit('create', 'Сохранить', array('class' => 'btn btn-primary')); ?>
				</div>
			</div>
			<?= Form::close(); ?>
		</div>
    </div>
</div>