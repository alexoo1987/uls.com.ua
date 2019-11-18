<?= Form::open($current_url, array('class' => 'form-horizontal cart-form', 'id' => 'validate_form', 'autocomplete' => 'off')); ?>
	<div class="modal-header">
		<button class="btn close" data-dismiss="modal">×</button>
		<span class="bold"><i class=""></i><span class="break"></span>Добавить в корзину</span>
	</div>
	<div class="modal-body">
		<?= Form::hidden('priceitem_id', HTML::chars(Arr::get($data, 'priceitem_id'))) ?>
		<?= Form::hidden('redirect_to', HTML::chars(Arr::get($data, 'redirect_to'))) ?>
		
		<div class="control-group">
			<div class="controls">
				<span class="bold">Артикул:</span> <?=$priceitem->part->article_long?><br>
				<span class="bold">Производитель:</span> <?=$priceitem->part->brand_long?><br>
				<span class="bold">Цена за шт.:</span> <?=$priceitem->get_price_for_client()?> грн.<br>
			</div>
		</div>
		<div class="control-group">
			<?= Form::label('qty', 'Количество', array('class' => 'control-label ')); ?>
			<div class="controls">
				<?= Form::input('qty', '1', array('data-format' => 'ddddd', 'validate' => 'required|number|min,1')); ?>
			</div>
		</div>
	</div>
	<div class="modal-footer"><div class="container">
            <header class="page-header">
                <h1 class="page-title">Оформление заказа</h1>
            </header>

            <?php if($guest == true) : ?>
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#home">Я новый пользователь</a></li>
                    <li><a data-toggle="tab" href="#menu1">Я уже зарегистрирован</a></li>
                </ul>
            <? endif; ?>

            <div class="tab-content">
                <div id="home" class="tab-pane fade in active">
                    <div class="col-md-4 col-md-offset-4">
                        <br>
                        <? if ($message) : ?>
                            <span class="block alert alert-info">
						<?= $message; ?>
					</span>
                        <? endif; ?>
                        <?php if($guest == true) : ?>
                            <div class="row">
                                <span class="block">Для быстрого заказа незарегестрированных пользователей <span class="tooltips circle" tabindex="0">? <span style="z-index: 999">Данная форма заказа не обязывает Вас регестрироватся - Вы остаетесь анонимным покупателем. Если вы совершаете онлайн заказ мы Вам делаем скидку ОПТ 1. После оформления заказа с Вами свяжется наш менеджер и при необходимости проверит правильность подбора запчастей.</span></span></span>
                            </div>
                        <?php endif; ?>
                        <?= Form::open('', array('class' => 'form-horizontal orders_add_form', 'id' => 'validate_form')); ?>

                        <? if ($guest == true) : ?>
                            <div class="form-group">
                                <?= Form::label('client_type', 'Тип клиента*'); ?>
                                <div class="controls">
                                    <select name="client_type" id="client_type" class="form-control">
                                        <option value="0">Частное лицо</option>
                                        <option value="1">Юридическое лицо</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <?= Form::label('name_organization', 'Название организации *'); ?>
                                <div class="controls">
                                    <input type="text" class="form-control" name="name_organization" id="name_organization">
                                </div>
                            </div>

                            <div class="form-group">
                                <?= Form::label('edrpoy', 'Код ЕДРПОУ'); ?>
                                <div class="controls">
                                    <input type="text" class="form-control" name="edrpoy" id="edrpoy">
                                </div>
                            </div>

                            <div class="form-group">
                                <?= Form::label('phone', 'Телефон*', array('class' => 'control-label ')); ?>
                                <div class="controls">
                                    <?= Form::input('phone', HTML::chars(Arr::get($data, 'phone')), array('class' => 'form-control phone', 'placeholder' => '+38', 'required' => 'required')); ?>
                                    <? if ($err = Arr::get($errors, 'phone')) : ?>
                                        <div class="alert alert-error">
                                            <?= $err; ?>
                                        </div>
                                    <? endif; ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <?= Form::label('email', 'Email*'); ?>
                                <div class="controls">
                                    <input type="email" class="form-control" name="email" id="email" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <?= Form::label('password', 'Пароль*'); ?>
                                <div class="controls">
                                    <input type="password" class="form-control" name="password" id="password" required>
                                </div>
                            </div>
                        <? endif; ?>

                        <div class="form-group">
                            <?= Form::label('delivery_method_id', 'Метод доставки*'); ?>
                            <div class="controls">
                                <select name="delivery_method_id" class="form-control">
                                    <!--							<option value="0">---</option>-->
                                    <?php foreach ($delivery_methods AS $method){?>
                                        <option value="<?=$method->id?>" data-order-state="<?=$method->order_state?>"><?=$method->name?></option>
                                    <?php }?>
                                </select>
                            </div>
                            <?= Form::hidden('state'); ?>
                        </div>

                        <div class="form-group">
                            <?= Form::label('np_area_id', 'Область*', array('class' => 'none')); ?>
                            <div class="controls">
                                <?= Form::select('np_area_id', $area, 1, array('validate' => 'required', 'id' => 'region', 'class' => 'none form-control')); ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <?= Form::label('np_city_id', 'Город*', array('class' => 'none')); ?>
                            <div class="controls">
                                <?= Form::select('np_city_id', null, 1, array('validate' => 'required', 'id' => 'city', 'class' => 'none form-control')); ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <?= Form::label('np_warehouse_id', 'Отделение*', array('class' => 'none')); ?>
                            <div class="controls">
                                <?= Form::select('np_warehouse_id', null, 1, array('validate' => 'required', 'id' => 'warehous', 'class' => 'none form-control')); ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <?= Form::label('delivery_address', 'Адрес доставки (по Киеву)'); ?>
                            <div class="controls">
                                <?= Form::textarea('delivery_address', HTML::chars(Arr::get($data, 'delivery_address')), array('rows' => '3', 'class' => 'form-control')); ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <?= Form::checkbox('confirmation', 1, (Arr::get($data, 'confirmation') == 1)); ?>
                            <?= Form::label('confirmation', ' Отправить на перепроверку менеджером <span class="tooltips circle" tabindex="0">? <span>Если вы не поставили перепроверку менеджером - заказ переходит в работу сразу</span></span>'/*, array('class' => 'control-label')*/); ?>
                        </div>

                        <div class="form-group">
                            <div class="controls">
                                <?= Form::submit('create', 'Оформить заказ', array('class' => 'btn btn-primary')); ?>
                            </div>
                        </div>
                        <?= Form::close(); ?>
                    </div>
                </div>
                <div id="menu1" class="tab-pane fade">
                    <div class="col-md-4 col-md-offset-4">
                        <br>
                        <span class="block">Для зарегестрированых пользователей</span>
                        <form action="<?= URL::site("authorization/login"); ?>" method="POST" id="loginForm" autocomplete="off">
                            <div class="form-group">
                                <label>Номер телефона</label>
                                <input class="form-control phone" placeholder="+38" type="text" name="phone" required/>
                            </div>
                            <input class="form-control"  type="hidden" name="redirect_to" value="/orders/add"/>
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
                                <li><a href="#nav-pwd-dialog" class="popup-text">Забыли пароль?</a>
                                </li>
                            </ul>
                        </form>
                    </div>
                </div>
            </div>


            <div class="row">
                <?php if($guest == true): ?>
                    <div class="col-md-4">

                    </div>
                <?php endif; ?>
                <div class="col-md-4 <?php if($guest != true)  echo'col-md-offset-4'; else echo'col-md-offset-1'; ?> vcenter">

                </div>
                <!--		--><?php //if($guest == true) : ?>
                <!--		<div class="col-md-3 hidden-xs hidden-sm vcenter" style="margin-top: -60px;">-->
                <!--			<h4>Будьте внимательны! Указывайте код вашего оператора правильно</h4>-->
                <!--		</div>-->
                <!--		--><?// endif; ?>
            </div>
        </div>
		<button class="btn" data-dismiss="modal">Отмена</button>
		<?= Form::submit('submit', 'Добавить в корзину', array('class' => 'btn btn-success')); ?>
	</div>
<?= Form::close(); ?>
