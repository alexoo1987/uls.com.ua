<link rel="stylesheet" href="/media/css/dist/bootstrap-datetimepicker.min.css" />
<link rel="stylesheet" href="/media/css/dist/bootstrap-datetimepicker.css">

<div class="container">
    <header class="page-header">
        <h1 class="page-title">Личный кабинет</h1>
    </header>
    <div class="gap gap-small"></div>
    <div class="col-md-4 col-md-offset-4">
        <?= Form::open('', array('class' => 'form-horizontal', 'id' => 'validate_form', 'autocomplete' => 'off')); ?>
        <?= Form::hidden('id', HTML::chars(Arr::get($data, 'id')), array('id' => 'id')); ?>
        <div class="form-group">
            <?= Form::label('client_type', 'Тип клиента*', array('class' => 'control-label')); ?>
            <div class="controls">
                <select name="client_type" id="client_type" class="form-control">
                    <option value="0">Частное лицо</option>
                    <option value="1">Юридическое лицо</option>
                </select>

            </div>
        </div><br>
        <div class="form-group">
            <?= Form::label('name', 'Имя*', array('class' => 'control-label')); ?>
            <div class="controls">
                <?= Form::input('name', HTML::chars(Arr::get($data, 'name')), array('validate' => 'required', 'class' => 'form-control')); ?>
                <? if ($err = Arr::get($errors, 'name')) : ?>
                    <div class="alert alert-error">
                        <?= $err; ?>
                    </div>
                <? endif; ?>
            </div>
        </div>
        <div class="form-group">
            <?= Form::label('surname', 'Фамилия*', array('class' => 'control-label')); ?>
            <div class="controls">
                <?= Form::input('surname', HTML::chars(Arr::get($data, 'surname')), array('validate' => 'required', 'class' => 'form-control')); ?>
                <? if ($err = Arr::get($errors, 'surname')) : ?>
                    <div class="alert alert-error">
                        <?= $err; ?>
                    </div>
                <? endif; ?>
            </div>
        </div>
        <div class="form-group">
            <?= Form::label('middlename', 'Отчество', array('class' => 'control-label')); ?>
            <div class="controls">
                <?= Form::input('middlename', HTML::chars(Arr::get($data, 'middlename')), array('class' => 'form-control')); ?>
                <? if ($err = Arr::get($errors, 'middlename')) : ?>
                    <div class="alert alert-error">
                        <?= $err; ?>
                    </div>
                <? endif; ?>
            </div>
        </div>
        <div class="form-group">
            <?= Form::label('phone', 'Телефон*', array('class' => 'control-label ')); ?>
            <div class="controls">
                <?= Form::input('phone', HTML::chars(Arr::get($data, 'phone')), array('class' => 'bfh-phone form-control', 'data-format' => '+38(ddd)ddd-dd-dd', 'data-number' => preg_replace('/[^0-9]/', '', HTML::chars(Arr::get($data, 'phone'))), 'validate' => 'required|phone|unique_phone')); ?>
                <? if ($err = Arr::get($errors, 'phone')) : ?>
                    <div class="alert alert-error">
                        <?= $err; ?>
                    </div>
                <? endif; ?>
            </div>
        </div>
        <div class="form-group">
            <?= Form::label('password', 'Новый пароль', array('class' => 'control-label')); ?>
            <div class="controls">
                <?= Form::password('password', '', array('id' => 'password', 'class' => 'form-control')); ?>
                <? if ($err = Arr::path($errors, '_external.password')) : ?>
                <div class="alert alert-error">
                    <?= $err; ?>
                </div>
                <? endif; ?>
            </div>
        </div>

        <div class="form-group">
            <?= Form::label('password_confirm', 'Подверждение пароля', array('class' => 'control-label')); ?>
            <div class="controls">
                <?= Form::password('password_confirm', '', array('id' => 'password_confirm', 'validate' => 'pass_eq', 'class' => 'form-control')); ?>
                <? if ($err = Arr::path($errors, '_external.password_confirm')) : ?>
                <div class="alert alert-error">
                    <?= $err; ?>
                </div>
                <? endif; ?>
            </div>
        </div>

        <div class="form-group">
            <?= Form::label('email', 'Email*', array('class' => 'control-label ')); ?>
            <div class="controls">
                <?= Form::input('email', HTML::chars(Arr::get($data, 'email')), array('id'=>'email','validate' => 'email', 'class' => 'form-control')); ?>
                <div class="submit-error" style="color: red"></div>
                <? if ($err = Arr::get($errors, 'email')) : ?>
                    <div class="alert alert-error">
                        <?= $err; ?>
                    </div>
                <? endif; ?>
            </div>
        </div>

        <div class="form-group">
            <?= Form::label('delivery_method_id', 'Метод доставки', array('class' => 'control-label')); ?>
            <div class="controls">
                <?= Form::select('delivery_method_id', $delivery_methods, Arr::get($data, 'delivery_method_id'), array('class' => 'form-control')); ?>
            </div>
        </div>

        <div class="form-group">
            <?= Form::label('delivery_address', 'Адрес доставки', array('class' => 'control-label')); ?>
            <div class="controls">
                <?= Form::textarea('delivery_address', HTML::chars(Arr::get($data, 'delivery_address')), array('rows' => '4', 'class' => 'form-control')); ?>
            </div>
        </div>

<!--        <div class="form-group">-->
<!--            --><?//= Form::label('birth_date', 'Дата рождения', array('class' => 'control-label')); ?>
<!--            <div class="controls">-->
<!--                <div class="form-group">-->
<!--                    <div class="birthdate-fields">-->
<!--                        <div class="ttt">-->
<!--                            <div>День</div>-->
<!--                            --><?//= Form::input('birth_day', HTML::chars(Arr::get($data, 'birth_day')),
//                                array('class'=>'input-width form-control','placeholder'=>'День','data-mask'=>'00')) ?>
<!--                        </div>-->
<!--                        <div class="ttt">-->
<!--                            <div>Месяц</div>-->
<!--                            --><?//= Form::input('birth_month', HTML::chars(Arr::get($data, 'birth_month')),
//                                array('class'=>'form-control input-width','placeholder'=>'Месяць','data-mask'=>'00')) ?>
<!--                        </div>-->
<!--                        <div class="ttt year">-->
<!--                            <div>Год</div>-->
<!--                            --><?//= Form::input('birth_year', HTML::chars(Arr::get($data, 'birth_year')),
//                                array('class'=>'form-control input-width','placeholder'=>'Год рождения','data-mask'=>'0000')) ?>
<!--                        </div>-->
<!--                        <div class="clearfix"></div>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->

        <div class="form-group">
            <div class="controls">
                <?= Form::submit('submit', 'Сохранить', array('class' => 'btn btn-primary')); ?>
            </div>
        </div>
        <?= Form::close(); ?>
    </div>

</div>