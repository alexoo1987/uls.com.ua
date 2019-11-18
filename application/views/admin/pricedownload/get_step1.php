<div class="container">
    <div class="row">
		<div class="span6 offset3">
			<?= Form::open(URL::site('admin/pricedownload/get_step2'), array('class' => 'form-horizontal', 'id' => 'validate_form'/* , 'enctype' => 'multipart/form-data' */)); ?>			
			<div class="control-group">
				<?= Form::label('discount_id', 'Наценка', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::select('discount_id', $discounts, Arr::get($data, 'discount_id')/*,  array('validate' => 'required') */); ?>
				</div>
			</div>
			<hr>
			<div id="supplier_div">
				<div class="control-group">
					<?= Form::label('suppliers', 'Поставщик', array('class' => 'control-label')); ?>
					<div class="controls">
						<?= Form::select('suppliers[1]', $suppliers, '0'); ?>
					</div>
				</div>
			</div>
			<hr>
			<a class="btn btn-mini" id="addNewSupplier" href="#"><i class="icon-plus"></i> Добавить</a>
			<br />
			<br />
			
			<div class="control-group">
				<div class="controls">
					<?= Form::submit('create', 'Продолжить', array('class' => 'btn btn-primary')); ?>
				</div>
			</div>
			<?= Form::close(); ?>
			<div id="form-supplier-unit-tpl" style="display: none;">
				<div class="control-group">
					<?= Form::label('suppliers', 'Поставщик', array('class' => 'control-label')); ?>
					<div class="controls">
						<?= Form::select('suppliers[{i}]', $suppliers, '0'); ?>
						<br />
						<a class="btn btn-mini remNewSupplier" href="#"><i class="icon-minus"></i> Удалить</a>
					</div>
				</div>
			</div>
		</div>
    </div>
</div>