<div class="container">
    <div class="row">
		<div class="span6 offset3">
			<? if ($message) : ?>
				<h3 class="alert alert-info">
					<?= $message; ?>
				</h3>
			<? endif; ?>
			<?php $group = false; ?>

			<?= Form::open('', array('class' => 'form-horizontal', 'id' => 'validate_form')); ?>
			<div class="control-group">
				<?= Form::label('article', 'Артикул', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('article', HTML::chars(Arr::get($data, 'article')), array('validate' => 'required')); ?>
				</div>
			</div>
			
			<div class="control-group">
				<?= Form::label('brand', 'Производитель', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('brand', HTML::chars(Arr::get($data, 'brand')), array('validate' => 'required')); ?>
				</div>
			</div>
			
			<div class="control-group">
				<?= Form::label('name', 'Наименование', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('name', HTML::chars(Arr::get($data, 'name'))); ?>
				</div>
			</div>
			
			
<!--			--><?// if(ORM::factory('Permission')->checkPermission('orders_edit_state')) { ?>
<!--			<div class="control-group">-->
<!--				--><?//= Form::label('state_id', 'Состояние', array('class' => 'control-label')); ?>
<!--				<div class="controls">-->
<!--					--><?//= Form::select('state_id', $states, Arr::get($data, 'state_id'), array('validate' => 'required')); ?>
<!--				</div>-->
<!--			</div>-->
<!--			--><?php //} ?>

			<?php if (ORM::factory('Permission')->checkRole('Бухгалтер') OR ORM::factory('Permission')->checkRole('Руководитель закупки') OR  ORM::factory('Permission')->checkRole('Владелец') OR ORM::factory('Permission')->checkRole('Програмист') OR ORM::factory('Permission')->checkPermission('orders_edit_purchase'))
			{
				$group = true;
			}
			?>
			<?= Form::hidden('currency_id_value', HTML::chars(Arr::get($data, 'currency_id_value')), array('validate' => 'required')); ?>
			<? if(ORM::factory('Permission')->checkPermission('orders_edit_supplier')) { ?>
				<div class="control-group">
					<?= Form::label('supplier_id', 'Поставщик', array('class' => 'control-label')); ?>
					<div class="controls">
						<?= Form::select('supplier_id', $suppliers, Arr::get($data, 'supplier_id'), array('validate' => 'required', 'id' => 'change_supplier')); ?>
					</div>
				</div>

			<div class="control-group">
				<?= Form::label('currency_id', 'Валюта закупки', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::select('currency_id', $currencies, Arr::get($data, 'currency_id'), array('validate' => 'required', 'disabled' => 'false', 'class' => 'currency')); ?>
					<?= Form::hidden('currency_id', HTML::chars(Arr::get($data, 'currency_id')), array('validate' => 'required')); ?>

				</div>
			</div>
			<?php } ?>
			<? if(ORM::factory('Permission')->checkPermission('orders_edit_purchase')) { ?>
			<div class="control-group">
				<?= Form::label('purchase_per_unit_in_currency', 'Закупка в валюте', array('class' => 'control-label')); ?>
				<div class="controls">
					<?php if($group == true): ?>
						<?= Form::input('purchase_per_unit_in_currency', HTML::chars(Arr::get($data, 'purchase_per_unit_in_currency'))); ?>
					<?php else: ?>
						<?= Form::input('purchase_per_unit_in_currency', HTML::chars(Arr::get($data, 'purchase_per_unit_in_currency')), array('readonly' => 'readonly')); ?>
					<?php endif;?>
				</div>
			</div>
			<?php } ?>
			
			<div class="control-group">
				<?= Form::label('amount', 'Количество', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('amount', HTML::chars(Arr::get($data, 'amount')), array('validate' => 'required|number|min,1')); ?>
				</div>
			</div>
			
			<? if(ORM::factory('Permission')->checkPermission('orders_edit_delivery_days')) { ?>
			<div class="control-group">
				<?= Form::label('delivery_days', 'Ожидание', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('delivery_days', HTML::chars(Arr::get($data, 'delivery_days')), array('validate' => 'required')); ?>
				</div>
			</div>
			<?php } ?>

			<? if(ORM::factory('Permission')->checkPermission('orders_edit_purchase')) { ?>
			<div class="control-group">
				<?= Form::label('purchase_per_unit', 'Закупка в грн', array('class' => 'control-label')); ?>
				<div class="controls">
					<?php if($group == true): ?>
						<?= Form::input('purchase_per_unit', HTML::chars(Arr::get($data, 'purchase_per_unit')), array('validate' => 'required|float')); ?>
					<?php else: ?>
						<?= Form::input('purchase_per_unit', HTML::chars(Arr::get($data, 'purchase_per_unit')), array( 'readonly' => 'readonly', 'validate' => 'required|float')); ?>
					<?php endif;?>
				</div>
			</div>
			<?php } ?>

			<? if(ORM::factory('Permission')->checkPermission('orders_edit_sale')) { ?>
			<div class="control-group">
				<?= Form::label('sale_per_unit', 'Продажа за шт.', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('sale_per_unit', HTML::chars(Arr::get($data, 'sale_per_unit')), array('validate' => 'required|float')); ?>
				</div>
			</div>
			<?php } ?>

			<? if(ORM::factory('Permission')->checkPermission('orders_edit_delivery')) { ?>
			<div class="control-group">
				<?= Form::label('delivery_price', 'Доставка от поставщика', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('delivery_price', HTML::chars(Arr::get($data, 'delivery_price')), array('validate' => 'float')); ?>
				</div>
			</div>
			<?php } ?>
			
			<? if(ORM::factory('Permission')->checkPermission('orders_edit_salary')) { ?>
			<div class="control-group">
				<?= Form::label('salary', 'З/п выплаченна', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::checkbox('salary', 1, (Arr::get($data, 'salary') == 1)); ?>
				</div>
			</div>
			<?php } ?>

			<div class="control-group">
				<?= Form::label('manager_comment', 'Комментарий менеджера', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::textarea('manager_comment', HTML::chars(Arr::get($data, 'manager_comment')), array('rows' => '4')); ?>
				</div>
			</div>
			
			
			<div class="control-group">
				<div class="controls">
					<?= Form::submit('create', 'Сохранить', array('class' => 'btn btn-primary save_item')); ?>
				</div>
			</div>
			<?= Form::close(); ?>
		</div>
    </div>
</div>
