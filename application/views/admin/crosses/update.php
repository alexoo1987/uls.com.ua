<div class="container">
    <div class="row">
		<div class="span6 offset3">
			Всего кроссов в базе: <?=ORM::factory('Cross')->count_all()?>
		</div>
    </div>
    <div class="row">
		<div class="span6 offset3">
			<p><b>Загружать нужно файл в формате CSV. В первой строке должны быть наименования колонок!!!</b></p>
			<?= Form::open(URL::site('admin/crosses/update_step2'), array('class' => 'form-horizontal', 'id' => 'validate_form', 'enctype' => 'multipart/form-data')); ?>
			<div class="control-group">
				<?= Form::label('filename', 'Кроссы в формате csv', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::file('filename', array('validate' => 'required')); ?>
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