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
				<?= Form::label('name', 'Название', array('class' => 'control-label')); ?>
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
				<?= Form::label('phone', 'Телефон', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('phone', HTML::chars(Arr::get($data, 'phone'))); ?>
					<? if ($err = Arr::get($errors, 'phone')) : ?>
					<div class="alert alert-error">
						<?= $err; ?>
					</div>
					<? endif; ?>
				</div>
			</div>

			<div class="control-group">
				<?= Form::label('delivery_days', 'Доставка', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('delivery_days', HTML::chars(Arr::get($data, 'delivery_days'))); ?>
					<span class="add-on">дней</span>
					<? if ($err = Arr::get($errors, 'delivery_days')) : ?>
					<div class="alert alert-error">
						<?= $err; ?>
					</div>
					<? endif; ?>
				</div>
			</div>

			<div class="control-group">
				<?= Form::label('сomment_text', 'Коментарий', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::textarea('сomment_text', HTML::chars(Arr::get($data, 'сomment_text'))); ?>
					<? if ($err = Arr::get($errors, 'сomment_text')) : ?>
					<div class="alert alert-error">
						<?= $err; ?>
					</div>
					<? endif; ?>
				</div>
			</div>

			<div class="control-group">
				<?= Form::label('price_source', 'Источник прайса', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::textarea('price_source', HTML::chars(Arr::get($data, 'price_source'))); ?>
					<? if ($err = Arr::get($errors, 'price_source')) : ?>
					<div class="alert alert-error">
						<?= $err; ?>
					</div>
					<? endif; ?>
				</div>
			</div>

			<div class="control-group">
				<?= Form::label('notice', 'Примечания', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::textarea('notice', HTML::chars(Arr::get($data, 'notice'))); ?>
					<? if ($err = Arr::get($errors, 'notice')) : ?>
					<div class="alert alert-error">
						<?= $err; ?>
					</div>
					<? endif; ?>
				</div>
			</div>

			<div class="control-group">
				<?= Form::label('order_to', 'Заказ до', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('order_to', HTML::chars(Arr::get($data, 'order_to'))); ?>
				</div>
			</div>
			
			<div class="control-group">
				<?= Form::label('currency_id', 'Валюта', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::select('currency_id', $currencies, Arr::get($data, 'currency_id')); ?>
				</div>
			</div>
			
			<div class="control-group">
				<?= Form::label('dont_show', 'Не показывать прайсы', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::checkbox('dont_show', 1, (Arr::get($data, 'dont_show') == 1)); ?>
				</div>
			</div>

			<div class="control-group">
				<?= Form::label('our_delivery', 'Наша доставка', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::checkbox('our_delivery', 1, (Arr::get($data, 'our_delivery') == 1)); ?>
				</div>
			</div>

			<div class="control-group">
				<?= Form::label('address', 'Адресс доставки', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::textarea('address', HTML::chars(Arr::get($data, 'address'))); ?>
					<? if ($err = Arr::get($errors, 'address')) : ?>
						<div class="alert alert-error">
							<?= $err; ?>
						</div>
					<? endif; ?>
				</div>
			</div>
			
			<div class="control-group">
				<?= Form::label('status', 'Статус загрузки', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::select('status', ORM::factory('Supplier')->_statuses, Arr::get($data, 'status')); ?>
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