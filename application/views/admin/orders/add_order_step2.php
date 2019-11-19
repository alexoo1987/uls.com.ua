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
				<?= Form::label('phone', 'Телефон клиента', array('class' => 'control-label ')); ?>
				<div class="controls">
					<?= Form::input('phone', HTML::chars(Arr::get($data, 'phone')), array('class' => 'bfh-phone', 'data-format' => '+38(ddd)ddd-dd-dd' , 'placeholder' => '+38', 'validate' => 'required|phone')); ?>
				</div>
			</div>
			
			<div class="control-group">
				<div class="controls">
					<?= Form::submit('create', 'Дальше', array('class' => 'btn btn-primary')); ?>
				</div>
			</div>
			<?= Form::close(); ?>
		</div>
    </div>
</div>