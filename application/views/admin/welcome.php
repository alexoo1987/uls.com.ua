<div class="conteiner">
	<div class="row">
		<div class="span4 offset4" style="font-weight: bold; text-align: center;">Добро пожаловать в админ раздел!</div>
	</div>
	<div class="row">
		<div class="span4 offset4" style="font-weight: bold; text-align: center;">
			Вы вошли как <?=Auth::instance()->get_user()->name?> <?=Auth::instance()->get_user()->surname?>
		</div>
	</div>
	<div class="row">
		<div class="span6 offset3" style="text-align: center;">
			<a class="btn" href="<?=URL::site('admin/orders');?>"><i class="icon-shopping-cart"></i> Заказы</a>
			<a class="btn" href="<?=URL::site('admin/clients');?>"><i class="icon-user"></i> Покупатели</a>
			<a class="btn" href="<?=URL::site('admin/user/list');?>"><i class="icon-eye-open"></i> Пользователи</a>
			<a class="btn" href="<?=URL::site('admin/suppliers/update');?>"><i class="icon-refresh"></i> Обновить прайсы</a>
		</div>
	</div>
</div>