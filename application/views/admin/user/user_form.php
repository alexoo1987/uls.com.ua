
<div class="container">
	<div class="row">
		<div class="span6 offset3">
			<? if ($message) : ?>
				<h3 class="alert alert-info">
					<?= $message; ?>
				</h3>
			<? endif; ?>

			<?= Form::open('', array('class' => 'form-horizontal','id'=>'user-form')); ?>
			<div class="control-group">
				<?= Form::label('username', 'Имя пользователя', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('username', HTML::chars(Arr::get($data, 'username'))); ?>
					<? if ($err = Arr::get($errors, 'username')) : ?>
						<div class="alert alert-error">
							<?= $err; ?>
						</div>
					<? endif; ?>
				</div>
			</div>

			<div class="control-group">
				<?= Form::label('name', 'Имя', array('class' => 'control-label')); ?>
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
				<?= Form::label('surname', 'Фамилия', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('surname', HTML::chars(Arr::get($data, 'surname'))); ?>
					<? if ($err = Arr::get($errors, 'surname')) : ?>
						<div class="alert alert-error">
							<?= $err; ?>
						</div>
					<? endif; ?>
				</div>
			</div>

			<div class="control-group">
				<?= Form::label('middle_name', 'Отчество', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('middle_name', HTML::chars(Arr::get($data, 'middle_name'))); ?>
					<? if ($err = Arr::get($errors, 'middle_name')) : ?>
						<div class="alert alert-error">
							<?= $err; ?>
						</div>
					<? endif; ?>
				</div>
			</div>

			<div class="control-group">
				<?= Form::label('inside_code', 'Внутренний код', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('inside_code', HTML::chars(Arr::get($data, 'inside_code')),array('id'=>'inside_code','class'=>'form-control')); ?>
					<? if ($err = Arr::get($errors, 'inside_code')) : ?>
						<div class="alert alert-error">
							<?= $err; ?>
						</div>
					<? endif; ?>
				</div>
			</div>



			<div class="control-group">
				<?= Form::label('email', 'Email', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('email', HTML::chars(Arr::get($data, 'email'))); ?>
					<? if ($err = Arr::get($errors, 'email')) : ?>
						<div class="alert alert-error">
							<?= $err; ?>
						</div>
					<? endif; ?>
				</div>
			</div>

			<div class="control-group">
				<?= Form::label('password', 'Пароль', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::password('password'); ?>
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
					<?= Form::password('password_confirm'); ?>
					<? if ($err = Arr::path($errors, '_external.password_confirm')) : ?>
						<div class="alert alert-error">
							<?= $err; ?>
						</div>
					<? endif; ?>
				</div>
			</div>

			<div class="control-group">
				<?= Form::label('phone_number', 'Телефон', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::input('phone_number', HTML::chars(Arr::get($data, 'phone_number')),array('id'=>'phone_number','class'=>'form-control')); ?>
					<? if ($err = Arr::get($errors, 'phone_number')) : ?>
						<div class="alert alert-error">
							<?= $err; ?>
						</div>
					<? endif; ?>
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
				<?= Form::label('place_registration', 'Прописка', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::textarea('place_registration', HTML::chars(Arr::get($data, 'place_registration')), array('rows'=>7 , 'cols'=>80) ); ?>
					<? if ($err = Arr::get($errors, 'place_registration')) : ?>
						<div class="alert alert-error">
							<?= $err; ?>
						</div>
					<? endif; ?>
				</div>
			</div>



			<div class="control-group">
				<?= Form::label('role', 'Группа пользователя', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::select('role', $roles, Arr::get($data, 'role')); ?>
				</div>
			</div>
			<div class="control-group">
				<?= Form::label('dont_show_salary', 'Не показывать з/п этого пользователя', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::checkbox('dont_show_salary', 1, (Arr::get($data, 'dont_show_salary') == 1)); ?>
				</div>
			</div>
			<div class="control-group">
				<?= Form::label('show_salary_only_me', 'Показывать з/п этого пользователя только ему', array('class' => 'control-label')); ?>
				<div class="controls">
					<?= Form::checkbox('show_salary_only_me', 1, (Arr::get($data, 'show_salary_only_me') == 1)); ?>
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