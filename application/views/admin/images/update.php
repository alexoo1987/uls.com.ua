<div class="container">
    <div class="row">
		<div class="span6 offset3">
			<p><b>Загружать нужно файл в формате CSV. В первой строке должны быть наименования колонок!!!</b></p>
			<?= Form::open(URL::site('admin/images/update_step2'), array('class' => 'form-horizontal', 'id' => 'validate_form', 'enctype' => 'multipart/form-data')); ?>
			<div class="control-group">
				<?= Form::label('filename', 'Привязка в формате csv', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::file('filename', array('validate' => 'required')); ?>
				</div>
			</div>

<!--			<div class="control-group">-->
<!--				--><?//= Form::label('photos', 'Zip архив с фото', array('class' => 'control-label')); ?>
<!--				<div class="controls">-->
<!--					--><?//= Form::file('photos', array('validate' => 'required')); ?>
<!--				</div>-->
<!--			</div>-->
			
			<div class="control-group">
				<div class="controls">
					<?= Form::submit('create', 'Сохранить', array('class' => 'btn btn-primary')); ?>
				</div>
			</div>
			<?= Form::close(); ?>
		</div>
    </div>
</div>