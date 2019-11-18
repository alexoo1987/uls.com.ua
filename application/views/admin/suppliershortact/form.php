<div class="container">
    <div class="row">
		<div class="span6 offset3">
			<? if ($message) : ?>
				<h3 class="alert alert-info">
					<?= $message; ?>
				</h3>
			<? endif; ?>

			<?= Form::open(URL::site('admin/suppliershortact/get_act'), array('class' => 'form-horizontal', 'id' => 'validate_form')); ?>
			
			<div class="control-group">
				<?= Form::label('date_from', 'Дата от', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('date_from', '08.08.2016', array('class' => 'datepicker')); ?>
				</div>
			</div>
			
			<div class="control-group">
				<?= Form::label('date_to', 'Дата до', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('date_to', HTML::chars(Arr::get($data, 'date_to')), array('class' => 'datepicker')); ?>
				</div>
			</div>
			
			<div class="control-group">
				<div class="controls">
					<?= Form::submit('create', 'Получить', array('class' => 'btn btn-primary')); ?>
				</div>
			</div>
			<?= Form::close(); ?>
		</div>
    </div>
</div>