<div class="navbar navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container">
			<a class="btn btn-navbar" data-toggle="collapse" data-target="#admin-menu">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</a>
			<a class="brand" href="<?=URL::site('admin');?>"><?= $site_name; ?></a>
			<div class="nav-collapse collapse" id="admin-menu">
				<ul class="nav" id="main-menu-left">
					<li class="dropdown">
						<a class="dropdown-toggle" data-toggle="dropdown" href="#">Системное <b class="caret"></b></a>
						<ul class="dropdown-menu" id="swatch-menu">
							<li><a href="<?=URL::site('admin/pages/list');?>">Страницы</a></li>
							<li><a href="<?=URL::site('admin/videos/list');?>">Видео</a></li>
						</ul>

						<!--a class="dropdown-toggle" data-toggle="dropdown" href="#">Контент <b class="caret"></b></a>
						<ul class="dropdown-menu" id="swatch-menu">
							<li><a href="<?=URL::site('admin/menu/list');?>">Меню</a></li>
							<li><a href="<?=URL::site('admin/pages/list');?>">Страницы</a></li>
						</ul-->
					</li>
					<li class="dropdown">
						<a class="dropdown-toggle" data-toggle="dropdown" href="#">Заказы <b class="caret"></b></a>
						<ul class="dropdown-menu" id="swatch-menu">
							<li><a href="<?=URL::site('admin/orders');?>">Заказы</a></li>
							<li><a href="<?=URL::site('admin/orders/items');?>">Позиции заказов</a></li>
							<li><a href="<?=URL::site('admin/orders/nova_poshta');?>">Отследить посылку</a></li>
							<li><a href="<?=URL::site('admin/managerrequest');?>">Запросы менеджеру</a></li>
							<li><a href="<?= URL::site('admin/costs/personal_costs'); ?>">Затраты личные</a></li>
							<li><a href="<?= URL::site('admin/costs/list'); ?>">Затраты</a></li>
							<li><a href="<?= URL::site('admin/orders/get_act'); ?>">Акт позиций в работе</a></li>
							<li><a href="<?= URL::site('admin/sms/props'); ?>">Отправка реквизитов</a></li>
							<li><a href="<?= URL::site('/admin/CashMovement/list'); ?>">Денежные движения</a></li>
							<li class="dropdown-submenu">
								<a tabindex="-1" href="#">Акт доставок</a>
								<ul class="dropdown-menu">
									<li><a tabindex="-1" href="<?=URL::site('/admin/delivery/get_act');?>">Акт доставки клиентов</a></li>
									<li><a tabindex="-1" href="<?=URL::site('/admin/delivery/get_act_suppliers');?>">Акт доставки поставщиков</a></li>
								</ul>
							</li>
						</ul>
					</li>
					<li class="dropdown">
						<a class="dropdown-toggle" data-toggle="dropdown" href="#">Пользователи <b class="caret"></b></a>
						<ul class="dropdown-menu" id="swatch-menu">
							<li><a href="<?=URL::site('admin/user/list');?>">Администраторы</a></li>
							<li><a href="<?=URL::site('admin/user/groups');?>">Группы администраторов</a></li>
							<li><a href="<?=URL::site('admin/clients');?>">Покупатели</a></li>
							<li><a href="<?=URL::site('admin/discount/list');?>">Управление накрутками</a></li>
							<li><a href="<?=URL::site('admin/salary/list');?>">Управление з/п</a></li>
							<li><a href="<?=URL::site('admin/clientpayment/list');?>">Баланс по клиентам</a></li>
							<li><a href="<?= URL::site('admin/user/balance'); ?>">Баланс сотрудников</a></li>
							<li><a href="<?= URL::site('admin/penalty/list'); ?>">Штрафы и выплаты</a></li>
							<li><a href="<?= URL::site('admin/penalty/archivelist'); ?>">Архив штрафов и выплаты</a></li>
							<li><a href="<?=URL::site('admin/salary/manager_salary');?>">Зарплаты менеджеров</a></li>
							<li><a href="<?=URL::site('admin/clients/debtor');?>">Задолженность по клиентам</a></li>
						</ul>
					</li>
					<li class="dropdown">
						<a class="dropdown-toggle" data-toggle="dropdown" href="#">Поставщики <b class="caret"></b></a>
						<ul class="dropdown-menu" id="swatch-menu">
							<li><a href="<?=URL::site('admin/suppliers/list');?>">Список поставщиков</a></li>
							<li><a href="<?=URL::site('admin/suppliers/unlist');?>">Список неактивных поставщиков</a></li>
							<li><a href="<?=URL::site('admin/suppliers/update');?>">Загрузка прайсов</a></li>
							<li><a href="<?=URL::site('admin/importSetting/index');?>">Автозагрузка прайсов</a></li>
							<li><a href="<?=URL::site('admin/supplierpayment/list');?>">Баланс</a></li>
							<li><a href="<?=URL::site('admin/supplierpayment/new_balance');?>">Баланс new</a></li>
							<li><a href="<?=URL::site('admin/pricedownload/get');?>">Выгрузка CSV</a></li>
							<li><a href="<?=URL::site('admin/supplieract');?>">Акт сверки</a></li>
							<li><a href="<?=URL::site('admin/suppliershortact');?>">Короткая статистика</a></li>
						</ul>
					</li>
					<li class="dropdown">
						<a class="dropdown-toggle" data-toggle="dropdown" href="#">Настройки <b class="caret"></b></a>
						<ul class="dropdown-menu" id="swatch-menu">
							<li><a href="<?=URL::site('admin/delivery/list');?>">Методы доставки</a></li>
							<li><a href="<?=URL::site('admin/card');?>">Баланс карты</a></li>
							<li><a href="<?=URL::site('admin/videos');?>">Настройка видео</a></li>
							<li><a href="<?=URL::site('admin/currency/list');?>">Курсы валют</a></li>
							<li><a href="<?=URL::site('admin/crosses/update');?>">Кроссы</a></li>
							<li><a href="<?=URL::site('admin/images/update');?>">Изображения</a></li>
							<li><a href="<?=URL::site('admin/operations/list');?>">Операции загрузки</a></li>
							<li><a href="<?=URL::site('admin/operations/brands');?>">Брэнды</a></li>
							<li><a href="<?=URL::site('admin/operations/parts');?>">Запчасти</a></li>
							<li><a href="<?=URL::site('admin/settings/list');?>">Настройки</a></li>
							<li><a href="<?=URL::site('admin/birthdaySetting/list');?>">Поздравление</a></li>
							<li><a href="<?=URL::site('admin/sms');?>">СМС рассылка</a></li>
							<li class="dropdown-submenu">
								<a tabindex="-1" href="#">TecDoc</a>
								<ul class="dropdown-menu">
									<li><a tabindex="-1" href="<?=URL::site('admin/tecdoc/manufacturers_list');?>">Марки авто</a></li>
									<li><a tabindex="-1" href="<?=URL::site('admin/categories');?>">Сопоставление категорий</a></li>
								</ul>
							</li>
							<li class="dropdown-submenu">
								<a tabindex="-1" href="#">Phonet</a>
								<ul class="dropdown-menu">
									<li><a tabindex="-1" href="<?=URL::site('admin/phonet');?>">Список пользователей</a></li>
									<li><a tabindex="-1" href="<?=URL::site('admin/phonet/missed');?>">Пропущенные звонки</a></li>
									<li><a tabindex="-1" href="<?=URL::site('admin/phonet/companycalls');?>">Звонки компании</a></li>
									<li><a tabindex="-1" href="<?=URL::site('admin/phonet/userscalls');?>">Звонки пользователей</a></li>
									<li><a tabindex="-1" href="<?=URL::site('admin/phonet/active_calls');?>">Активные звонки</a></li>
								</ul>
							</li>
						</ul>
					</li>
					<li class="dropdown">
						<a href="<?=URL::site('admin/comments');?>">Отзывы</a>
					</li>
					<li class="dropdown">
						<a href="<?=URL::site('admin/vacancies');?>">Вакансии</a>
					</li>
					<li class="dropdown"><a href="<?=URL::site('admin/carsales');?>">Автовыкуп</a></li>
					<li class="dropdown"><a href="<?=URL::site('admin/seodata');?>">SEO</a></li>
				</ul>
				<ul class="nav pull-right">
					<li><a href="<?=URL::site('admin/user/logout');?>"><i class="icon-minus-sign"></i> Выход</a></li>
				</ul>
			</div>
		</div>
	</div>
</div>