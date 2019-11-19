<div class="container">
    <div class="row">
		<div class="span6 offset3">
			<? if ($message) : ?>
				<h3 class="alert alert-info">
					<?= $message; ?>
				</h3>
			<? endif; ?>

			<?= Form::open('', array('class' => 'form-horizontal', 'id' => 'validate_form', 'autocomplete' => 'off')); ?>
			
			
			<div class="control-group">
				<?= Form::label('send_all', 'Отправить всем', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::checkbox('send_all', 1, (Arr::get($data, 'send_all') == 1), array('id' => "send_all")); ?>
				</div>
			</div>
			<div class="control-group clients_ids_holder">
				<?= Form::label('client_id[]', 'Клиенты', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::select('client_id[]', $clients, Arr::get($data, 'client_id'), array('multiple' => 'multiple', 'id' => "clients_ids")); ?>
				</div>
			</div>
			
			<div class="control-group">
				<?= Form::label('message', 'Сообщение', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::textarea('message', Arr::get($data, 'message'), array('rows' => '4')); ?>
				</div>
			</div>
			
			<div class="control-group">
				<div class="controls">
					<?= Form::submit('submit', 'Сохранить', array('class' => 'btn btn-primary')); ?>
				</div>
			</div>
			<?= Form::close(); ?>
		</div>
    </div>
</div>