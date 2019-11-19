<div class="container">

	<form class="form-signin" action="" method="POST">
		<h2 class="form-signin-heading">Вход</h2>
		<?php if(!empty($message)) { ?><div class="alert alert-error">
			<?=$message?>
		</div><?php } ?>
		<input type="text" class="input-block-level" name="username" placeholder="Имя пользователя">
		<input type="password" class="input-block-level" name="password" placeholder="Пароль">
		<label class="checkbox">
			<input type="checkbox" value="remember-me"> Запомнить
        </label>
		<button class="btn btn-large btn-primary" type="submit">Войти</button>
	</form>

</div>