<div class="container">
    <div class="row">
		<?= Form::open('', array('class' => 'form-horizontal', 'id' => 'validate_form'));?>
		<div class="span6 offset3">
			<? if ($message) : ?>
				<h3 class="alert alert-info">
					<?= $message; ?>
				</h3>
			<? endif; ?>

<!--			Название раздела-->
			<div class="control-group">
				<?= Form::label('section_titles', 'Названия раздела', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('section_titles', HTML::chars(Arr::get($data, 'section_titles')), array('validate' => 'required')); ?>
					<? if ($err = Arr::get($errors, 'section_titles')) : ?>
					<div class="alert alert-error">
						<?= $err; ?>
					</div>
					<? endif; ?>
				</div>
			</div>
<!--            Заголовок-->
            <div class="control-group">
                <?= Form::label('h1', 'H1', array('class' => 'control-label')); ?>
                <div class="controls">
                    <?= Form::input('h1', HTML::chars(Arr::get($data, 'h1')), array('validate' => 'required')); ?>
                    <? if ($err = Arr::get($errors, 'h1')) : ?>
                        <div class="alert alert-error">
                            <?= $err; ?>
                        </div>
                    <? endif; ?>
                </div>
            </div>
<!--			TITLE-->
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
<!--            URL-->
			<div class="control-group">
				<?= Form::label('seo_identifier', 'URL', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('seo_identifier', HTML::chars(Arr::get($data, 'seo_identifier'))); ?>
					<? if ($err = Arr::get($errors, 'seo_identifier')) : ?>
					<div class="alert alert-error">
						<?= $err; ?>
					</div>
					<? endif; ?>
				</div>
			</div>
<!--            канонический адрес-->
            <div class="control-group">
                <?= Form::label('canonical_address', 'Канонический адрес', array('class' => 'control-label')); ?>
                <div class="controls">
                    <?= Form::input('canonical_address', HTML::chars(Arr::get($data, 'canonical_address'))); ?>
                    <? if ($err = Arr::get($errors, 'canonical_address')) : ?>
                        <div class="alert alert-error">
                            <?= $err; ?>
                        </div>
                    <? endif; ?>
                </div>
            </div>
			
<!--			<div class="control-group">-->
<!--				--><?//= Form::label('keywords', 'keywords', array('class' => 'control-label')); ?>
<!--				<div class="controls">-->
<!--					--><?//= Form::textarea('keywords', HTML::chars(Arr::get($data, 'keywords')), array('validate' => '')); ?>
<!--					--><?// if ($err = Arr::get($errors, 'keywords')) : ?>
<!--					<div class="alert alert-error">-->
<!--						--><?//= $err; ?>
<!--					</div>-->
<!--					--><?// endif; ?>
<!--				</div>-->
<!--			</div>-->



<!--			описание-->
			<div class="control-group">
				<?= Form::label('description', 'description', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::textarea('description', HTML::chars(Arr::get($data, 'description')), array('validate' => '')); ?>
					<? if ($err = Arr::get($errors, 'description')) : ?>
					<div class="alert alert-error">
						<?= $err; ?>
					</div>
					<? endif; ?>
				</div>
			</div>
<!--			контент-->
            <div class="control-group">
			
					<?= Form::label('content', 'Содержимое', array('class' => 'control-label')); ?>
				<div class="controls">

				<?= Form::textarea('content', Arr::get($data, 'content'), array('id' => 'content-area')); ?>
					<? if ($err = Arr::get($errors, 'content')) : ?>
					<div class="alert alert-error">
						<?= $err; ?>
					</div>
					<? endif; ?>
			
				</div>
			</div>
<!--            индекс-->
			<div class="control-group">

			<?= Form::label('noindex', 'Noindex', array('class' => 'control-label')); ?>
			<div class="controls">
                <?= Form::checkbox('noindex', 1, (Arr::get($data, 'noindex') == 1)); ?>
			<? if ($err = Arr::get($errors, 'content')) : ?>
				<div class="alert alert-error">
					<?= $err; ?>
				</div>
			<? endif; ?>
			</div>
        <!--копка сохранить-->
			</div>
			<div class="control-group">
				<div class="controls">
					<?= Form::submit('submit', 'Сохранить', array('class' => 'btn btn-primary')); ?>
				</div>
			</div>
		<?= Form::close(); ?>
    </div>
</div>