<div class="container">
    <div class="row">
		<div class="span6 offset3">
			<? if ($message) : ?>
				<h3 class="alert alert-info">
					<?= $message; ?>
				</h3>
			<? endif; ?>

			<?= Form::open('', array('class' => 'form-horizontal', 'id' => 'validate_form', 'autocomplete' => 'off')); ?>

			<div class="control-group">
				<?= Form::label('url', 'Ссылка', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::textarea('url', HTML::chars(Arr::get($data, 'url')), array('rows' => '4')); ?>
				</div>
			</div>

			<div class="control-group">
				<?= Form::label('site', 'Показывать на сайте', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::checkbox('site', 1, (Arr::get($data, 'site') == 1)); ?>
				</div>
			</div>

			<div class="control-group">
				<?= Form::label('admin', 'Показывать в админке', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::checkbox('admin', 1, (Arr::get($data, 'admin') == 1)); ?>
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