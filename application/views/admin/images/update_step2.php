<div class="container">
    <div class="row">
		<div class="span6 offset3">

			<?= Form::open(URL::site('admin/images/update_step3'), array('class' => '', 'id' => 'validate_form', 'enctype' => 'multipart/form-data')); ?>
			<?= Form::hidden('filepath', $filepath); ?>
			<?= Form::hidden('date_time', $date_time); ?>

			<div class="control-group">
				<?= Form::label('article', 'Артикул', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::select('article', $columns, '', array('validate' => 'required')); ?>
				</div>
			</div>
			<div class="control-group">
				<?= Form::label('brand', 'Бренд', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::select('brand', $columns, ''); ?> или <?= Form::input('brand_text', ''); ?>
				</div>
			</div>
			<div class="control-group">
				<?= Form::label('image_path', 'Путь к картинке', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::select('image_path', $columns, ''); ?>
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