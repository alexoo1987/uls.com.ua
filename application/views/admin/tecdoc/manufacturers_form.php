<div class="container">
    <div class="row">
		<div class="span6 offset3">
			<? if ($message) : ?>
				<h3 class="alert alert-info">
					<?= $message; ?>
				</h3>
			<? endif; ?>

			<?= Form::open('', array('class' => 'form-horizontal', 'id' => 'validate_form', 'enctype' => 'multipart/form-data')); ?>
			<?= Form::hidden('id', HTML::chars(Arr::get($data, 'id')), array('id' => 'id')); ?>
			<div class="control-group">
				<?= Form::label('brand', 'Название*', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('brand', HTML::chars(Arr::get($data, 'brand')), array('validate' => 'required')); ?>
					<? if ($err = Arr::get($errors, 'brand')) : ?>
					<div class="alert alert-error">
						<?= $err; ?>
					</div>
					<? endif; ?>
				</div>
			</div>
			<div class="control-group">
				<?= Form::label('code', 'Код*', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('code', HTML::chars(Arr::get($data, 'code')), array('validate' => 'required')); ?>
					<? if ($err = Arr::get($errors, 'code')) : ?>
					<div class="alert alert-error">
						<?= $err; ?>
					</div>
					<? endif; ?>
				</div>
			</div>
			
			<div class="control-group">
				<?= Form::label('description', 'Описание', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::textarea('description', HTML::chars(Arr::get($data, 'description')), array('rows' => '4')); ?>
				</div>
			</div>
			
			<div class="control-group">
				<?= Form::label('filename', 'Добавить или заменить лого', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::file('filename'); ?>
				</div>
			</div>
			
			<?php if(!empty($data['logo'])): ?>
			<div class="control-group">
				<?= Form::label('delete_logo', 'Удалить лого', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::checkbox('delete_logo', 1, (Arr::get($data, 'delete_logo') == 1)); ?>
				</div>
			</div>
			<?php endif; ?>
			
			
			<div class="control-group">
				<?= Form::label('title', 'Title', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('title', HTML::chars(Arr::get($data, 'title')), array('validate' => 'length_max,255')); ?>
					<? if ($err = Arr::get($errors, 'title')) : ?>
					<div class="alert alert-error">
						<?= $err; ?>
					</div>
					<? endif; ?>
				</div>
			</div>			
			
			<div class="control-group">
				<?= Form::label('meta_keywords', 'meta_keywords', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::textarea('meta_keywords', HTML::chars(Arr::get($data, 'meta_keywords')), array('validate' => 'length_max,300')); ?>
					<? if ($err = Arr::get($errors, 'meta_keywords')) : ?>
					<div class="alert alert-error">
						<?= $err; ?>
					</div>
					<? endif; ?>
				</div>
			</div>
			
			<div class="control-group">
				<?= Form::label('meta_description', 'meta_description', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::textarea('meta_description', HTML::chars(Arr::get($data, 'meta_description')), array('validate' => 'length_max,160')); ?>
					<? if ($err = Arr::get($errors, 'meta_description')) : ?>
					<div class="alert alert-error">
						<?= $err; ?>
					</div>
					<? endif; ?>
				</div>
			</div>
			
			<div class="control-group">
				<?= Form::label('active', 'Показывать на сайте', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::checkbox('active', 1, (Arr::get($data, 'active') == 1)); ?>
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