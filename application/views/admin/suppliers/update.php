<div class="container">
    <div class="row">
		<div class="span6 offset3">
			<p><b>Загружать нужно файл в формате CSV. В первой строке должны быть наименования колонок!!!</b></p>
			<?= Form::open(URL::site('admin/suppliers/update_step2'), array('class' => 'form-horizontal', 'id' => 'validate_form', 'enctype' => 'multipart/form-data')); ?>			
			<div class="control-group">
				<?= Form::label('supplier_id', 'Поставщик', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::select('supplier_id', $suppliers, Arr::get($data, 'supplier_id'), array('validate' => 'required')); ?>
				</div>
			</div>
			<div class="control-group">
				<?= Form::label('filename', 'Прайс', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::file('filename', array('validate' => 'required')); ?>
				</div>
			</div>
			<div class="control-group">
				<?= Form::label('file_type', 'Формат файла', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::select('file_type', array('csv' => 'CSV', 'xls' => 'EXCEL'), Arr::get($data, 'file_type'), array('validate' => 'required')); ?>
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