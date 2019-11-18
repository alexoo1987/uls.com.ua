<div class="container">
    <div class="row">
        <h1><i class="fa fa-pencil"></i> Регистрация</h1>
        <p>Зарегестрированым пользователям предоставляется скидка!</p>
        <div class="col-md-3 col-md-offset-4">
            <? if ($message) : ?>
                <span class="block alert alert-info">
                    <?= $message; ?>
                </span>
            <? endif; ?>


            <form action="<?= URL::site("authorization/registration"); ?>" method="POST" id="client-reg-form" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Имя*</label>
                    <input class="form-control" name="name" type="text" oninvalid="this.setCustomValidity('Введите имя')"  required/>
                    <div class="error-input"></div>
                </div>
                <div class="form-group">
                    <label>Фамилия*</label>
                    <input class="form-control" name="surname" type="text" oninvalid="this.setCustomValidity('Введите фамилию')"  required/>
                    <div class="error-input"></div>
                </div>
                <div class="form-group">
                    <label>Отчество</label>
                    <input class="form-control" name="middlename" type="text" />
                    <div class="error-input"></div>
                </div>
                <div class="form-group">
                    <label>Номер телефона*</label>
                    <input class="form-control phone" name="phone" placeholder="+38" id="phone" type="text">
                    <div class="error-input"></div>
                </div>
                <div class="form-group">
                    <label>Пароль</label>
                    <input class="form-control" name="password" type="password" id="password" required/>
                    <div class="error-input"></div>
                </div>
                <div class="form-group">
                    <label>Подтверждения пароля</label>
                    <input class="form-control" name="password_confirm" type="password" id="password_confirm" oninvalid="this.setCustomValidity('Введите пароль еще раз')" required/>
                </div>
                <div class="form-group">
                    <label>Доп. телефон</label>
                    <input class="form-control" name="additional_phone" type="text"/>
                    <div class="error-input"></div>
                </div>
                <div class="form-group">
                    <label>Почта Для востановления или изменения пароля и получения измения статусов запчасти*</label>
                    <input class="form-control" name="email" id="email" type="text"/>
                    <div class="error-input"></div>
                </div>
                <div class="form-group">
                    <label>Форма собственности</label>
                    <select class="form-control" name="client_type" id="type_ownership">
                        <option value="0">Физическое лицо</option>
                        <option value="1">Юридическое лицо</option>
                    </select>
                </div>
                <div class="jur_form" style="display:none">
                    <div class="form-group">
                        <label for="jur_certificate">Свидетельство регистрации</label>
                        <input id="jur_certificate" type="file" name="jur_certificate" accept="image/jpeg,image/png,application/pdf" />
                        <div class="error-input"></div>
                    </div>
                </div>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" name="is_service_station" id="is_service_station"/>
                    <label class="form-check-label" for="is_service_station">Станция ТО</label>
                </div>
                <div class="fiz_station_form" style="display:none">
                    <div class="form-group">
                        <label for="sto_document">Свидетельство регистрации</label>
                        <input id="sto_document" type="file" name="sto_document" accept="image/jpeg,image/png,application/pdf" />
                        <div class="error-input"></div>
                    </div>
                </div>
                <div class="station_form" style="display:none">
                    <div class="form-group">
                        <label for="ss_name">Название станции тех. обслуживания</label>
                        <input class="form-control" name="name_organization" id="ss_name" type="text"/>
                        <div class="error-input"></div>
                    </div>
                    <div class="form-group">
                        <label for="ss_address">Фактический адрес станции</label>
                        <textarea class="form-control" name="service_address" id="ss_address"></textarea>
                        <div class="error-input"></div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-3">
                            <label>День </label>
                            <select class="form-control" style=" width: 68px"
                                    name="birth_day">
                                <option value=""></option>
                                <?php for($i = 1 ;$i <= 31 ;$i++  ):?>
                                    <option value="<?php echo $i ; ?>"><?=$i;?></option>
                                <?php endfor;?>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label>Месяц </label>
                            <select class="form-control"  style="width: 68px" name="birth_month">
                                <option value=""></option>
                                <?php for($i = 1 ;$i <= 12 ;$i++  ):?>
                                    <option value="<?php echo $i ; ?>">
                                        <?php echo $i ; ?>
                                    </option>
                                <?php endfor;?>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label>Год рождения </label>
                            <select class="form-control"  name="birth_year">
                                <option value=""></option>
                                <?php for($i = date("Y")  ;$i >= 1900 ;--$i  ):?>
                                    <option value="<?php echo $i ; ?>"><?php echo $i ; ?></option>
                                <?php endfor;?>
                            </select>
                        </div>
                    </div>
                    <div class="error-input"></div>
                </div>
                <div class="form-group">
                    <label>Метод доставки</label>
                    <?= Form::select('delivery_method_id', $delivery_methods, Arr::get($data, 'delivery_method_id'), ['class' => 'form-control']); ?>
                </div>
                <div class="form-group">
                    <label>Адрес доставки</label>
                    <textarea class="textarea" name="delivery_address"></textarea>
                </div>
                <div class="form-group">
                    <div class="g-recaptcha" data-sitekey="6Le6yAkUAAAAAF6UHu7cdnN9jVtU8l27Q8BjotJY"
                         data-callback="enableBtn"></div>
                </div>
                <input class="btn btn-primary" id="submit" type="submit" value="Зарегестрироваться"/>
            </form>
        </div>
    </div>
</div>
<style>

</style>