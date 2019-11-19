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
				<?= Form::label('name', 'Имя*', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('name', HTML::chars(Arr::get($data, 'name')), array('validate' => 'required')); ?>
				</div>
			</div>
			<div class="control-group">
				<?= Form::label('number_order', 'Номер заказа*', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('number_order', HTML::chars(Arr::get($data, 'number_order')), array('class' => 'bfh-phone', 'validate' => 'required')); ?>
				</div>
			</div>
			<div class="control-group">
				<?= Form::label('order_position', 'Что заказывали*', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('order_position', HTML::chars(Arr::get($data, 'order_position')), array('validate' => 'required')); ?>
				</div>
			</div>
			<div class="control-group">
				<?= Form::label('dis_like', 'Не понравилось?*', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::textarea('dis_like', HTML::chars(Arr::get($data, 'dis_like')), array('rows' => '3', 'validate' => 'required')); ?>
				</div>
			</div>
			<div class="control-group">
				<?= Form::label('suggestions', 'Ваши предложения чтобы Вы автозапчасти <br>покупали именно у нас:*', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::textarea('suggestions', HTML::chars(Arr::get($data, 'suggestions')), array('rows' => '3', 'style' => 'text-align: left', 'validate' => 'required')); ?>
				</div>
			</div>
			<div class="control-group">
				<?= Form::label('answer', 'Комментарий к отзыву:', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::textarea('answer', HTML::chars(Arr::get($data, 'answer')), array('rows' => '3', 'style' => 'text-align: left')); ?>
				</div>
			</div>
			<b>Как Вы оцениваете работу Компании Епартс в целом?:</b> <?=Arr::get($data, 'rating')?><br>
			<b>Работа менеджера:</b> <?=Arr::get($data, 'manager_rating')?><br>
			<div class="control-group">
				<?= Form::label('active', 'Показывать на сайте', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::checkbox('active', 1, (Arr::get($data, 'active') == 1)); ?>
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