<div class="container">
    <div class="row">
		<div class="span6 offset3">
			<? if ($message) : ?>
				<h3 class="alert alert-info">
					<?= $message; ?>
				</h3>
			<? endif; ?>

			<?= Form::open('', array('class' => 'form-horizontal', 'id' => 'validate_form')); ?>
			
			<? if(ORM::factory('Permission')->checkPermission('orders_edit_manager')) { ?>
			<div class="control-group">
				<?= Form::label('manager_id', 'Менеджер', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::select('manager_id', $managers, Arr::get($data, 'manager_id')); ?>
				</div>
			</div>
			<?php } ?>
			
			<div class="control-group">
				<?= Form::label('delivery_method_id', 'Метод доставки', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::select('delivery_method_id', $delivery_methods, Arr::get($data, 'delivery_method_id'), array('validate' => 'required', 'id' => 'delivery_method')); ?>
				</div>
			</div>

			<div class="control-group">
				<?= Form::label('delivery_address', 'Адрес доставки', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::textarea('delivery_address', HTML::chars(Arr::get($data, 'delivery_address')), array('rows' => '4', 'id' => 'delivery_address', 'class' => ($data['delivery_method_id'] != 3) ? '' : 'none')); ?>
				</div>
			</div>

			<div class="control-group">
				<?= Form::label('np_area_id', 'Область', array('class' => 'control-label none')); ?>
				<div class="controls">
					<?= Form::select('np_area_id', $final_area, Arr::get($data, 'np_area_id'), array('validate' => 'required', 'id' => 'region', 'class' => $data['delivery_method_id'] == 3 ? '' : 'none')); ?>
				</div>
			</div>

			<div class="control-group">
				<?= Form::label('np_city_id', 'Город', array('class' => 'control-label none')); ?>
				<div class="controls">
					<?= Form::select('np_city_id', $final_city, Arr::get($data, 'np_city_id'), array('validate' => 'required', 'id' => 'city', 'class' => $data['delivery_method_id'] == 3 ? '' : 'none')); ?>
				</div>
			</div>

			<div class="control-group">
				<?= Form::label('np_warehouse_id', 'Отделение', array('class' => 'control-label none')); ?>
				<div class="controls">
					<?= Form::select('np_warehouse_id', $final_warehous, Arr::get($data, 'np_warehouse_id'), array('validate' => 'required', 'id' => 'warehous', 'class' => $data['delivery_method_id'] == 3 ? '' : 'none')); ?>
				</div>
			</div>
			
			<div class="control-group">
				<?= Form::label('ttn', 'ТТН', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::textarea('ttn', Arr::get($data, 'ttn'), array('rows' => '2')); ?>
				</div>
			</div>
			
			<div class="control-group">
				<?= Form::label('manager_comment', 'Комментарий менеджера', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::textarea('manager_comment', HTML::chars(Arr::get($data, 'manager_comment')), array('rows' => '4')); ?>
				</div>
			</div>
			
			<div class="control-group">
				<?= Form::label('client_comment', 'Комментарий клиента', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::textarea('client_comment', HTML::chars(Arr::get($data, 'client_comment')), array('rows' => '4')); ?>
				</div>
			</div>
			
			<? if(ORM::factory('Permission')->checkPermission('orders_edit_archive')) { ?>
			<div class="control-group">
				<?= Form::label('archive', 'Архив', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::checkbox('archive', 1, (Arr::get($data, 'archive') == 1)); ?>
				</div>
			</div>
			<?php } ?>
			
			<div class="control-group">
				<div class="controls">
					<?= Form::submit('create', 'Сохранить', array('class' => 'btn btn-primary')); ?>
				</div>
			</div>
			<?= Form::close(); ?>
		</div>
    </div>
</div>