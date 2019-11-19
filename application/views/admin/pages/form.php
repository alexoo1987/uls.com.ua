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
				<?= Form::label('h1_title', 'H1 title', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('h1_title', HTML::chars(Arr::get($data, 'h1_title')), array('validate' => 'required')); ?>
					<? if ($err = Arr::get($errors, 'h1_title')) : ?>
					<div class="alert alert-error">
						<?= $err; ?>
					</div>
					<? endif; ?>
				</div>
			</div>
			
			<div class="control-group">
				<?= Form::label('title', 'Title', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('title', HTML::chars(Arr::get($data, 'title')), array('validate' => 'required|length_max,255')); ?>
					<? if ($err = Arr::get($errors, 'title')) : ?>
					<div class="alert alert-error">
						<?= $err; ?>
					</div>
					<? endif; ?>
				</div>
			</div>

			<div class="control-group">
				<?= Form::label('syn', 'URL', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('syn', HTML::chars(Arr::get($data, 'syn'))); ?>
					<? if ($err = Arr::get($errors, 'syn')) : ?>
					<div class="alert alert-error">
						<?= $err; ?>
					</div>
					<? endif; ?>
				</div>
			</div>
			
<!--			<div class="control-group">-->
<!--				--><?//= Form::label('meta_keywords', 'meta_keywords', array('class' => 'control-label')); ?>
<!--				<div class="controls">-->
<!--					--><?//= Form::textarea('meta_keywords', HTML::chars(Arr::get($data, 'meta_keywords')), array('validate' => 'length_max,300')); ?>
<!--					--><?// if ($err = Arr::get($errors, 'meta_keywords')) : ?>
<!--					<div class="alert alert-error">-->
<!--						--><?//= $err; ?>
<!--					</div>-->
<!--					--><?// endif; ?>
<!--				</div>-->
<!--			</div>-->
			
			<div class="control-group">
				<?= Form::label('meta_description', 'meta_description', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::textarea('meta_description', HTML::chars(Arr::get($data, 'meta_description')), array('validate' => 'length_max,400')); ?>
					<? if ($err = Arr::get($errors, 'meta_description')) : ?>
					<div class="alert alert-error">
						<?= $err; ?>
					</div>
					<? endif; ?>
				</div>
			</div>
			
			<div class="control-group">
				<?= Form::label('active', 'Активна', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::checkbox('active', 1, (Arr::get($data, 'active') == 1 ? TRUE : FALSE)); ?>
					<? if ($err = Arr::get($errors, 'h1_title')) : ?>
					<div class="alert alert-error">
						<?= $err; ?>
					</div>
					<? endif; ?>
				</div>
			</div>
			
					<?= Form::label('content', 'Содержимое', array('class' => 'control-label')); ?>
					<?= Form::textarea('content', Arr::get($data, 'content'), array('id' => 'content-area')); ?>
					<? if ($err = Arr::get($errors, 'content')) : ?>
					<div class="alert alert-error">
						<?= $err; ?>
					</div>
					<? endif; ?>
			
			<div class="control-group">
				<div class="controls">
					<?= Form::submit('submit', 'Save', array('class' => 'btn btn-primary')); ?>
				</div>
			</div>
			<?= Form::close(); ?>
		</div>
    </div>
</div>