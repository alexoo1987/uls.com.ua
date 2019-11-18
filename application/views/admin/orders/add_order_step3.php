<div class="container">
    <div class="row">
		<div class="span6 offset3">
			<? if ($message) : ?>
				<h3 class="alert alert-info">
					<?= $message; ?>
				</h3>
			<? endif; ?>
			
			<?= Form::open('', array('class' => 'form-horizontal', 'id' => 'validate_form')); ?>
			<?= Form::hidden('client_id', HTML::chars(Arr::get($data, 'client_id'))); ?>
			<?= Form::hidden('manager_id', HTML::chars(Arr::get($data, 'manager_id'))); ?>
			
			
			<div class="control-group">
				<?= Form::label('delivery_method_id', 'Метод доставки', array('class' => 'control-label')); ?>
				<div class="controls">
					<select name="delivery_method_id">
						<option value="0">---</option>
						<?php foreach ($delivery_methods AS $method){?>
						<option value="<?=$method->id?>" data-order-state="<?=$method->order_state?>"><?=$method->name?></option>
						<?php }?>
					</select>
				</div>
			</div>
			
			<div class="control-group">
				<?= Form::label('delivery_address', 'Адрес доставки (по Киеву)', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::textarea('delivery_address', HTML::chars(Arr::get($data, 'delivery_address')), array('rows' => '4')); ?>
				</div>
			</div>

			<div class="control-group">
				<?= Form::label('partial_payment', 'Частичная оплата', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('partial_payment'); ?>
				</div>
			</div>

			<div class="control-group">
				<?= Form::label('np_area_id', 'Область', array('class' => 'control-label none')); ?>
				<div class="controls">
					<?= Form::select('np_area_id', $area, 1, array('validate' => 'required', 'id' => 'region', 'class' => 'none')); ?>
				</div>
			</div>

			<div class="control-group">
				<?= Form::label('np_city_id', 'Город', array('class' => 'control-label none')); ?>
				<div class="controls">
					<?= Form::select('np_city_id', null, 1, array('validate' => 'required', 'id' => 'city', 'class' => 'none')); ?>
				</div>
			</div>

			<div class="control-group">
				<?= Form::label('np_warehouse_id', 'Отделение', array('class' => 'control-label none')); ?>
				<div class="controls">
					<?= Form::select('np_warehouse_id', null, 1, array('validate' => 'required', 'id' => 'warehous', 'class' => 'none')); ?>
				</div>
			</div>

			<div class="control-group">
				<?= Form::label('state', 'Тип заказа', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::select('state', $orders_states, null, array('disabled' => 'disabled')); ?>
					<?= Form::hidden('state'); ?>
				</div>
			</div>
			
			<div class="control-group">
				<?= Form::label('manager_comment', 'Комментарий менеджера', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::textarea('manager_comment', HTML::chars(Arr::get($data, 'manager_comment')), array('rows' => '4')); ?>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="delivery_to">Ожидается</label>
				<div class="controls">
					<p class="delivery_to" data-date="<?=$delivery_to->format('Y-m-d')?>"><?=$delivery_to->format('d.m.Y')?></p>
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