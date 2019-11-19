<div class="container">
    <div class="row">
		<div class="span6 offset3">

			<?= Form::open(URL::site('admin/crosses/update_step3'), array('class' => '', 'id' => 'validate_form', 'enctype' => 'multipart/form-data')); ?>				
			<?= Form::hidden('filepath', $filepath); ?>
			
			
			<div id="addinput">
				<div class="control-group">
					<?= Form::label('article', 'Артикул', array('class' => 'control-label')); ?>
					<div class="controls">
						<?= Form::select('article[0]', $columns, '', array('validate' => 'required')); ?>
						<?= Form::label('brand', 'Бренд', array('class' => 'control-label')); ?>
						<?= Form::select('brand[0]', $columns, ''); ?> или <?= Form::input('brand_text[0]', ''); ?>
					</div>
				</div>
				<div class="control-group">
					<?= Form::label('article', 'Артикул', array('class' => 'control-label')); ?>
					<div class="controls">
						<?= Form::select('article[1]', $columns, '', array('validate' => 'required')); ?>
						<?= Form::label('brand', 'Бренд', array('class' => 'control-label')); ?>
						<?= Form::select('brand[1]', $columns, ''); ?> или <?= Form::input('brand_text[1]', ''); ?>
					</div>
				</div>
			</div>
			<a class="btn btn-mini" id="addNew" href="#"><i class="icon-plus"></i> Добавить</a>
			<br />
			<br />
			
			<div class="control-group">
				<div class="controls">
					<?= Form::submit('create', 'Сохранить', array('class' => 'btn btn-primary')); ?>
				</div>
			</div>
			<?= Form::close(); ?>
			
			<div id="form-unit-tpl" style="display: none;">
				<div class="control-group">
					<?= Form::label('article', 'Артикул', array('class' => 'control-label')); ?>
					<div class="controls">
						<?= Form::select('article[{i}]', $columns, '', array('validate' => 'required')); ?>
						<?= Form::label('brand', 'Бренд', array('class' => 'control-label')); ?>
						<?= Form::select('brand[{i}]', $columns, ''); ?> или <?= Form::input('brand_text[{i}]', ''); ?>
						<br />
						<a class="btn btn-mini remNew" href="#"><i class="icon-minus"></i> Удалить</a>
					</div>
				</div>
			</div>
		</div>
    </div>
</div>