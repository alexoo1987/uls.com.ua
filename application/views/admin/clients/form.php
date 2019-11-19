<div class="container">
    <div class="row">
		<div class="span6 offset3">
			<? if ($message) : ?>
				<h3 class="alert alert-info">
					<?= $message; ?>
				</h3>
			<? endif; ?>
			<? if (!empty($_GET['phone_num'])) : ?>
				<h3 class="alert alert-info">
					Полльзователь с номером телефона <?=$_GET['phone_num']?> отсутствует в базе. Добавьте его.
				</h3>
			<? endif; ?>

			<?= Form::open('', array('class' => 'form-horizontal', 'id' => 'validate_form', 'autocomplete' => 'off')); ?>
			<?= Form::hidden('id', HTML::chars(Arr::get($data, 'id')), array('id' => 'id')); ?>
			<div class="form-group">
				<?= Form::label('client_type', 'Тип клиента*', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::select('client_type', [0 => 'Физ лицо', 1 => 'Юридическое лицо'], Arr::get($data, 'client_type')); ?>
				</div>
<!--				<div class="controls">-->
<!--					<select name="client_type" id="client_type" class="form-control">-->
<!--						<option value="0">Частное лицо</option>-->
<!--						<option value="1">Юридическое лицо</option>-->
<!--					</select>-->
<!---->
<!--				</div>-->
			</div><br>
			<div class="control-group">
				<?= Form::label('name', 'Имя*', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('name', HTML::chars(Arr::get($data, 'name')), array('validate' => 'required')); ?>
					<? if ($err = Arr::get($errors, 'name')) : ?>
					<div class="alert alert-error">
						<?= $err; ?>
					</div>
					<? endif; ?>
				</div>
			</div>
			<div class="control-group">
				<?= Form::label('surname', 'Фамилия*', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('surname', HTML::chars(Arr::get($data, 'surname')), array('validate' => 'required')); ?>
					<? if ($err = Arr::get($errors, 'surname')) : ?>
					<div class="alert alert-error">
						<?= $err; ?>
					</div>
					<? endif; ?>
				</div>
			</div>
			<div class="control-group">
				<?= Form::label('middlename', 'Отчество', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('middlename', HTML::chars(Arr::get($data, 'middlename'))); ?>
					<? if ($err = Arr::get($errors, 'middlename')) : ?>
						<div class="alert alert-error">
							<?= $err; ?>
						</div>
					<? endif; ?>
				</div>
			</div>
			<div class="control-group">
				<?= Form::label('phone', 'Телефон*', array('class' => 'control-label ')); ?>
				<div class="controls">
					<?= Form::input('phone', HTML::chars(Arr::get($data, 'phone')), array('class' => 'bfh-phone', 'data-format' => '+38(ddd)ddd-dd-dd', 'data-number' => preg_replace('/[^0-9]/', '', HTML::chars(Arr::get($data, 'phone'))), 'validate' => 'required|phone|unique_phone')); ?>
					<? if ($err = Arr::get($errors, 'phone')) : ?>
					<div class="alert alert-error">
						<?= $err; ?>
					</div>
					<? endif; ?>
				</div>
			</div>

			<div class="control-group">
				<?= Form::label('password', 'Пароль', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::password('password', '', array('id' => 'password')); ?>
					<? if ($err = Arr::path($errors, '_external.password')) : ?>
					<div class="alert alert-error">
						<?= $err; ?>
					</div>
					<? endif; ?>
				</div>
			</div>

			<div class="control-group">
				<?= Form::label('password_confirm', 'Подверждение пароля', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::password('password_confirm', '', array('id' => 'password_confirm', 'validate' => 'pass_eq')); ?>
					<? if ($err = Arr::path($errors, '_external.password_confirm')) : ?>
					<div class="alert alert-error">
						<?= $err; ?>
					</div>
					<? endif; ?>
				</div>
			</div>
			
			<div class="control-group">
				<?= Form::label('additional_phone', 'Доп. тел', array('class' => 'control-label ')); ?>
				<div class="controls">
					<?= Form::input('additional_phone', HTML::chars(Arr::get($data, 'additional_phone')), array('class' => 'bfh-phone', 'data-format' => '(ddd)ddd-dd-dd', 'data-number' => preg_replace('/[^0-9]/', '', HTML::chars(Arr::get($data, 'additional_phone'))), 'validate' => 'phone')); ?>
					<? if ($err = Arr::get($errors, 'additional_phone')) : ?>
					<div class="alert alert-error">
						<?= $err; ?>
					</div>
					<? endif; ?>
				</div>
			</div>
			
			<div class="control-group">
				<?= Form::label('email', 'Email*', array('class' => 'control-label ')); ?>
				<div class="controls">
					<?= Form::input('email', HTML::chars(Arr::get($data, 'email')), array('id'=>'email','validate' => 'email')); ?>
					<div class="submit-error" style="color: red"></div>
					<? if ($err = Arr::get($errors, 'email')) : ?>
					<div class="alert alert-error">
						<?= $err; ?>
					</div>
					<? endif; ?>
				</div>
			</div>
			
			<div class="control-group">
				<?= Form::label('delivery_method_id', 'Метод доставки', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::select('delivery_method_id', $delivery_methods, Arr::get($data, 'delivery_method_id')); ?>
				</div>
			</div>
			
			<div class="control-group">
				<?= Form::label('delivery_address', 'Адрес доставки', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::textarea('delivery_address', HTML::chars(Arr::get($data, 'delivery_address')), array('rows' => '4')); ?>
				</div>
			</div>
			
			<div class="control-group">
				<?= Form::label('manager_id', 'Менеджер', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::select('manager_id', $managers, Arr::get($data, 'manager_id')); ?>
				</div>
			</div>
			
			<div class="control-group">
				<?= Form::label('discount_id', 'Уровень цен', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::select('discount_id', $discounts, Arr::get($data, 'discount_id')); ?>
				</div>
			</div>
			<div class="control-group">
				<?= Form::label('birth_date', 'Дата рождения', array('class' => 'control-label')); ?>
				<div class="controls">
					<div class="form-group">
						<div class="birthdate-fields">
							<div class="ttt">
								<div>День</div>
								<?= Form::input('birth_day', HTML::chars(Arr::get($data, 'birth_day')),
									array('class'=>'input-width form-control','placeholder'=>'День','data-mask'=>'00')) ?>
							</div>
							<div class="ttt">
								<div>Месяц</div>
								<?= Form::input('birth_month', HTML::chars(Arr::get($data, 'birth_month')),
									array('class'=>'form-control input-width','placeholder'=>'Месяць','data-mask'=>'00')) ?>
							</div>
							<div class="ttt year">
								<div>Год</div>
								<?= Form::input('birth_year', HTML::chars(Arr::get($data, 'birth_year')),
									array('class'=>'form-control input-width','placeholder'=>'Год рождения','data-mask'=>'0000')) ?>
							</div>
							<div class="clearfix"></div>
						</div>
					</div>
				</div>
			</div>


			<div class="control-group">
				<?= Form::label('comment', 'Коментарий', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::textarea('comment', Arr::get($data, 'comment'), array('rows' => '4')); ?>
				</div>
			</div>
			
			<div class="control-group">
				<?= Form::label('active', 'Активный', array('class' => 'control-label')); ?>
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
			<div class="documents">
				<?php foreach ($data['documents'] as $document): ?>
				<div class="item" style="max-width: 100%;">
					<a href="<?= $document->url ?>" target="_blank">
						<img src="<?= $document->url ?>" />
					</a>
				</div>
				<?php endforeach ?>
			</div>
		</div>
    </div>
</div>
<style>
	.submit-error{
		border-color: red;
	}
</style>
