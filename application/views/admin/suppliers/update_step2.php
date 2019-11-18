<div class="container">
    <div class="row">
		<div class="span6 offset3">
		
			<? if($process): ?>
				<p><b>Прайс уже обрабатывается. Попробуйте позже.</b></p>
			<? else: ?>

			<?= Form::open(URL::site('admin/suppliers/update_step3'), array('id' => 'validate_form', 'enctype' => 'multipart/form-data')); ?>		
			<?= Form::hidden('supplier_id', $supplier_id); ?>			
			<?= Form::hidden('filepath', $filepath); ?>
			
			<div class="control-group">
				<?= Form::label('article_column', 'Артикул', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::select('article_column', $columns, Arr::get($data, 'article_column'), array('validate' => 'required')); ?>
				</div>
			</div>
			<div class="control-group">
				<?= Form::label('remove_first', 'Убрать первые буквы', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::checkbox('remove_first', 1, (Arr::get($data, 'remove_first') == 1)); ?>
				</div>
			</div>
			<div class="control-group">
				<?= Form::label('brand_column', 'Производитель', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::select('brand_column', $columns, Arr::get($data, 'brand_column'), array('validate' => 'required')); ?>
				</div>
			</div>
			<div class="control-group">
				<?= Form::label('price_column', 'Цена', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::select('price_column', $columns, Arr::get($data, 'price_column'), array('validate' => 'required')); ?>
				</div>
			</div>
			<div class="control-group">
				<?= Form::label('name_column', 'Наименование', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::select('name_column', $columns, Arr::get($data, 'name_column')); ?>
				</div>
			</div>
			<hr>
			<div id="different_col">
				<?php if(!isset($data['stores']) or empty($data['stores'])) { ?>
				<div class="control-group">
					<?= Form::label('amount_column', 'Количество', array('class' => 'control-label')); ?>
					<div class="controls">
						<?= Form::select('amount_column[1]', $columns, ''); ?>
						<?= Form::label('delivery_column', 'Срок поставки', array('class' => 'control-label')); ?>
						<?= Form::select('delivery_column[1]', $columns, ''); ?> или <?= Form::input('delivery[1]', Arr::get($data, 'delivery')); ?>
					</div>
				</div>
				<?php } else { ?>
					<?php $i = 1; ?>
					<?php foreach(Arr::get($data, 'stores') as $store): ?>
						<div class="control-group">
							<?= Form::label('amount_column', 'Количество', array('class' => 'control-label')); ?>
							<div class="controls">
								<?= Form::select('amount_column['.$i.']', $columns, Arr::get($store, 'amount_column')); ?>
								<?= Form::label('delivery_column', 'Срок поставки', array('class' => 'control-label')); ?>
								<?= Form::select('delivery_column['.$i.']', $columns, Arr::get($store, 'delivery_column')); ?> или <?= Form::input('delivery['.$i.']', Arr::get($store, 'delivery')); ?>
							</div>
						</div>
						<?php $i++; ?>
					<?php endforeach; ?>
				<?php } ?>
			</div>
			<hr>
			<a class="btn btn-mini" id="addNewDC" href="#"><i class="icon-plus"></i> Добавить</a>
			<br />
			<br />
			
			<div class="control-group">
				<?= Form::label('ratio', 'Коэфициент', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('ratio', Arr::get($data, 'ratio'), array('validate' => 'required|float')); ?>
				</div>
			</div>
			
			<div class="control-group">
				<div class="controls">
					<?= Form::submit('create', 'Сохранить', array('class' => 'btn btn-primary')); ?>
				</div>
			</div>
			<?= Form::close(); ?>
			
			<div id="form-dc-unit-tpl" style="display: none;">
				<div class="control-group">
					<?= Form::label('amount_column', 'Количество', array('class' => 'control-label')); ?>
					<div class="controls">
						<?= Form::select('amount_column[{i}]', $columns, ''); ?>
						<?= Form::label('delivery_column', 'Срок поставки', array('class' => 'control-label')); ?>
						<?= Form::select('delivery_column[{i}]', $columns, ''); ?> или <?= Form::input('delivery[{i}]', Arr::get($data, 'delivery')); ?>
						<br />
						<a class="btn btn-mini remNewDC" href="#"><i class="icon-minus"></i> Удалить</a>
					</div>
				</div>
			</div>
			<?php endif; ?>
		</div>
    </div>
</div>