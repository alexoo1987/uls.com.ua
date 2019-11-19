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
				<?= Form::label('link_title', 'Тайтл ссылки', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('link_title', HTML::chars(Arr::get($data, 'link_title'))); ?>
					<? if ($err = Arr::get($errors, 'link_title')) : ?>
					<div class="alert alert-error">
						<?= $err; ?>
					</div>
					<? endif; ?>
				</div>
			</div>
			
			<div class="control-group">
				<?= Form::label('page_id', 'Страница', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::select('page_id', $pages, Arr::get($data, 'page_id')); ?>
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