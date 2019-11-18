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
				<?= Form::label('order_id', 'Заказ', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::select('order_id', $orders, Arr::get($data, 'order_id')); ?>
				</div>
			</div>
			
			<div class="control-group">
				<?= Form::label('amount', 'Количество з/ч, добавляемых в заказ', array('class' => 'control-label ')); ?>
				<div class="controls">
					<?= Form::input('amount', HTML::chars(Arr::get($data, 'amount')), array('validate' => 'required|number|min,1')); ?>
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