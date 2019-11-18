<div class="container">
    <div class="row">
		<div class="span6 offset3">
			<? if ($message) : ?>
				<h3 class="alert alert-info">
					<?= $message; ?>
				</h3>
			<? endif; ?>

			<?= Form::open('', array('class' => 'form-horizontal')); ?>
			<div class="control-group">
				<?= Form::label('name', 'Название', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('name', HTML::chars(Arr::get($data, 'name'))); ?>
					<? if ($err = Arr::get($errors, 'name')) : ?>
					<div class="alert alert-error">
						<?= $err; ?>
					</div>
					<? endif; ?>
				</div>
			</div>
			<div class="control-group">
				<?= Form::label('standart', 'Стандарт', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::checkbox('standart', 1, (Arr::get($data, 'standart') == 1)); ?>
				</div>
			</div>
			<div class="control-group">
				<?= Form::label('admin_default', 'По-умолчанию в админке', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::checkbox('admin_default', 1, (Arr::get($data, 'admin_default') == 1)); ?>
				</div>
			</div>
			
			<?php 
				$from = Arr::get($data, 'from');
				$to = Arr::get($data, 'to');
				$percentage = Arr::get($data, 'percentage');
			?>
			<div id="addinput">
			<?php if(is_array($from) && count($from) > 0) {
				foreach($from as $key=>$val) { ?>
				<div class="control-group">
					<?= Form::label('from', 'От', array('class' => 'control-label')); ?>
					<div class="controls">
						<?= Form::input('from['.$key.']', $from[$key]); ?>
						<span class="add-on"> грн.</span>
						<?= Form::label('to', 'до'); ?>
						<?= Form::input('to['.$key.']', $to[$key]); ?>
						<span class="add-on"> грн.</span>
						<?= Form::input('percentage['.$key.']', $percentage[$key]); ?>
						<span class="add-on">%</span>
						<?php if($key == 0) { ?>
						<a class="btn btn-mini" id="addNew" href="#"><i class="icon-plus"></i> Добавить</a>
						<?php } else { ?>
						<a class="btn btn-mini remNew" href="#"><i class="icon-minus"></i> Удалить</a>
						<?php } ?>
					</div>
				</div>
			<?php }
			} else { ?>
				<div class="control-group">
					<?= Form::label('from', 'От', array('class' => 'control-label')); ?>
					<div class="controls">
						<?= Form::input('from[0]', ''); ?>
						<span class="add-on"> грн.</span>
						<?= Form::label('to', 'до'); ?>
						<?= Form::input('to[0]', ''); ?>
						<span class="add-on"> грн.</span>
						<?= Form::input('percentage[0]', ''); ?>
						<span class="add-on">%</span>
						<a class="btn btn-mini" id="addNew" href="#"><i class="icon-plus"></i> Добавить</a>
					</div>
				</div>
			<?php } ?>
			
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