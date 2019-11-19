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
				<?= Form::label('title', 'Заголовок', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::textarea('title', HTML::chars(Arr::get($data, 'title')), array('rows' => '1')); ?>
				</div>
			</div>
			<div class="control-group">
				<?= Form::label('salary', 'Зароботная плата', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::textarea('salary', HTML::chars(Arr::get($data, 'salary')), array('rows' => '1')); ?>
				</div>
			</div>
			<div class="control-group">
				<?= Form::label('employment', 'Занятость', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::textarea('employment', HTML::chars(Arr::get($data, 'employment')), array('rows' => '1')); ?>
				</div>
			</div>
			<div class="control-group">
				<?= Form::label('experiance', 'Опыт работы', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::textarea('experiance', HTML::chars(Arr::get($data, 'experiance')), array('rows' => '1')); ?>
				</div>
			</div>
<!--			<div class="control-group">-->
<!--				--><?//= Form::label('description', 'Описание', array('class' => 'control-label')); ?>
<!--				<div class="controls">-->
<!--					--><?//= Form::textarea('description', HTML::chars(Arr::get($data, 'description')), array('rows' => '7')); ?>
<!--				</div>-->
<!--			-->
<!--			</div>-->

			<?= Form::label('description', 'Содержимое', array('class' => 'control-label')); ?>
			<?= Form::textarea('description', Arr::get($data, 'description'), array('id' => 'content-area')); ?>
			<? if ($err = Arr::get($errors, 'description')) : ?>
				<div class="alert alert-error">
					<?= $err; ?>
				</div>
			<? endif; ?>


<!--			<div class="control-group">-->
<!--				--><?//= Form::label('vaiting_results', 'Ожидания от соискателя', array('class' => 'control-label')); ?>
<!--				<div class="controls">-->
<!--					--><?//= Form::textarea('vaiting_results', HTML::chars(Arr::get($data, 'vaiting_results')), array('rows' => '5')); ?>
<!--				</div>-->
<!--			</div>-->
			<?= Form::label('vaiting_results', 'Ожидания от соискателя', array('class' => 'control-label')); ?>
			<?= Form::textarea('vaiting_results', Arr::get($data, 'vaiting_results'), array('id' => 'content-area-result')); ?>
			<? if ($err = Arr::get($errors, 'vaiting_results')) : ?>
				<div class="alert alert-error">
					<?= $err; ?>
				</div>
			<? endif; ?>


<!--			<div class="control-group">-->
<!--				--><?//= Form::label('requirements', 'Требования', array('class' => 'control-label')); ?>
<!--				<div class="controls">-->
<!--					--><?//= Form::textarea('requirements', HTML::chars(Arr::get($data, 'requirements')), array('rows' => '5')); ?>
<!--				</div>-->
<!--			</div>-->
			<?= Form::label('requirements', 'Требования', array('class' => 'control-label')); ?>
			<?= Form::textarea('requirements', Arr::get($data, 'requirements'), array('id' => 'content-area-requirements')); ?>
			<? if ($err = Arr::get($errors, 'requirements')) : ?>
				<div class="alert alert-error">
					<?= $err; ?>
				</div>
			<? endif; ?>


<!--			<div class="control-group">-->
<!--				--><?//= Form::label('working_conditions', 'Условия работы', array('class' => 'control-label')); ?>
<!--				<div class="controls">-->
<!--					--><?//= Form::textarea('working_conditions', HTML::chars(Arr::get($data, 'working_conditions')), array('rows' => '5')); ?>
<!--				</div>-->
<!--			</div>-->
			<?= Form::label('working_conditions', 'Условия работы', array('class' => 'control-label')); ?>
			<?= Form::textarea('working_conditions', Arr::get($data, 'working_conditions'), array('id' => 'content-area-conditions')); ?>
			<? if ($err = Arr::get($errors, 'working_conditions')) : ?>
				<div class="alert alert-error">
					<?= $err; ?>
				</div>
			<? endif; ?>


<!--			<div class="control-group">-->
<!--				--><?//= Form::label('probation', 'Испытательный срок', array('class' => 'control-label')); ?>
<!--				<div class="controls">-->
<!--					--><?//= Form::textarea('probation', HTML::chars(Arr::get($data, 'probation')), array('rows' => '5')); ?>
<!--				</div>-->
<!--			</div>-->
			<?= Form::label('probation', 'Испытательный срок', array('class' => 'control-label')); ?>
			<?= Form::textarea('probation', Arr::get($data, 'probation'), array('id' => 'content-area-probation')); ?>
			<? if ($err = Arr::get($errors, 'probation')) : ?>
				<div class="alert alert-error">
					<?= $err; ?>
				</div>
			<? endif; ?>


<!--			<div class="control-group">-->
<!--				--><?//= Form::label('meta_description', 'Дополнительно', array('class' => 'control-label')); ?>
<!--				<div class="controls">-->
<!--					--><?//= Form::textarea('meta_description', HTML::chars(Arr::get($data, 'meta_description')), array('rows' => '5')); ?>
<!--				</div>-->
<!--			</div>-->
			<?= Form::label('meta_description', 'Дополнительно', array('class' => 'control-label')); ?>
			<?= Form::textarea('meta_description', Arr::get($data, 'meta_description'), array('id' => 'content-area-cont')); ?>
			<? if ($err = Arr::get($errors, 'meta_description')) : ?>
				<div class="alert alert-error">
					<?= $err; ?>
				</div>
			<? endif; ?>

			
			<div class="control-group">
				<div class="controls">
					<?= Form::submit('submit', 'Сохранить', array('class' => 'btn btn-primary')); ?>
				</div>
			</div>
			<?= Form::close(); ?>
		</div>
    </div>
</div>