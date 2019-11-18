<div class="container">
	<h1>Вход</h1>
	<?php if ($message) { ?>
		<div class="alert alert-info">
			<?= $message; ?>
		</div>
	<?php } ?>

		<?php if ($errors) { ?>
			<div class="alert alert-warning">
				<?= $errors; ?>
			</div>
		<?php } ?>


		<?php if (isset($_GET['message'])) {?>
			<div class="alert alert-info">
				Ваш номер телефона уже зарегистрирован в системе. Войдите или восстановите пароль
			</div>
		<?php }?>

		<form action="<?= URL::site("authorization/login"); ?>" method="POST" id="loginForm" autocomplete="off">
			<div class="form-group">
				<label>Номер телефона</label>
				<input class="form-control phone" type="text" name="phone" required/>
			</div>
			<div class="form-group">
				<label>Пароль</label>
				<input class="form-control" type="password" name="password" required/>
			</div>
			<div class="checkbox">
				<label>
					<input class="i-check" type="checkbox"/>Запомнить</label>
			</div>
			<input class="btn btn-primary" type="submit" value="Войти"/>

			<div class="gap gap-small"></div>
			<ul class="list-inline">
				<li><a href="<?= URL::site("authorization/registration"); ?>">Зарегестрироваться</a>
				</li>
				<li><a href="<?= URL::site("/authorization/password_reset"); ?>">Забыли пароль?</a>
				</li>
			</ul>
		</form>

</div>