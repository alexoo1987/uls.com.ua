<div class="container">
	<?php if($success) { ?>
	<span class="block alert alert-success">
		Вы успешно активировали свой аккаунт!<br>
		Теперь Вы можете ввойти на сайт, используя данный введенные при регистрации.
	</span>
	<?php } else { ?>
	<span class="block alert alert-error">
		Ваш аккаунт не активен или Вы его уже активировали
	</span>
	<?php } ?>
</div>