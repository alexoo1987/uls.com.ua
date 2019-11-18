<div class="container">
	<h1>Восстановление пароля</h1>
	<?php if ($message) { ?>
		<div class="alert alert-info">
			<?= $message; ?>
		</div>
	<?php } else { ?>

	<?php if ($errors) { ?>
		<div class="alert alert-warning">
			<?= $errors; ?>
		</div>
	<?php } ?>

	<div class="alert alert-info">
		Введите Ваш номер телефона, и мы Вам отправим на Ваш e-mail инструкцию по восстановлению пароля.
	</div>

		<form class="form-inline" role="form" method="post">
			<div class="form-group">
				<label>Номер телефона</label>
				<input class="form-control phone" placeholder="+38" type="text" name="phone" required/>
<!--				<label class="sr-only" for="email">Email</label>-->
<!--				<input type="email" name="email" class="form-control" id="email" placeholder="Введите ваш Email" required>-->
			</div>
			<button type="submit" class="btn btn-default">Отправить</button>
		</form>
<?php } ?>

</div>