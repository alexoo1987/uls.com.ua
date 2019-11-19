<div class="container">
    
    <? if ($message) : ?>
        <h3 class="alert alert-info">
            <?= $message; ?>
        </h3>
    <? endif; ?>

    <div class="flex_box">
        <div class="in_flex_50">
            <?= Form::open('', array('class' => 'form-horizontal', 'id' => 'validate_form')); ?>
            <div class="control-group">
                <?= Form::label('value', 'Внесено', array('class' => 'control-label')); ?>
                <div class="controls">
                    <?= Form::input('value', HTML::chars(Arr::get($data, 'value')), array('validate' => 'required|float')); ?>
                    <cpan class="add-on">грн.</cpan>
                </div>
            </div>

            <div class="control-group">
                <?= Form::label('comment_text', 'Коментарий', array('class' => 'control-label')); ?>
                <div class="controls">
                    <?= Form::input('comment_text', HTML::chars(Arr::get($data, 'comment_text'))); ?>
                </div>
            </div>

            <div class="control-group">
                <div class="controls">
                    <?= Form::submit('create', 'Сохранить', array('class' => 'btn btn-primary')); ?>
                </div>
            </div>
            <?= Form::close(); ?>
        </div>
        <div class="in_flex_50">
            <div class="flex_box">
                <div class="well">
                    <div>
                        <p><a href="<?=URL::site('admin/card/index?confirm=2');?>"><span style="background: red">&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;Неподтвержденные - <?=$count['unconfirmed'] ?></a></p>
                        <p><a href="<?=URL::site('admin/card/index?confirm=1');?>"><span style="background: green">&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;Подтвержденные - <?=$count['confirmed'] ?></a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <?= Form::open('', array('class' => 'form-horizontal', 'method' => 'get')); ?>
        Дата от <?= Form::input('date_from', HTML::chars(Arr::get($filters, 'date_from')), array('class' => 'datepicker')); ?> до <?= Form::input('date_to', HTML::chars(Arr::get($filters, 'date_to')), array('class' => 'datepicker')); ?><br />
        <br><?= Form::submit('', 'Применить фильтр', array('class' => 'btn btn-primary')); ?>
    <?= Form::close(); ?>

    <a href="<?=URL::site('admin/card');?>" class="btn btn-primary"><i class="icon-white icon-refresh"></i> Сброс</a><br><br>

    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th>Дата</th>
            <th>Комментарий</th>
            <th>Пользователь</th>
            <th>Значение</th>
            <th>Подтверждение</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php $all_balance=0; ?>
            <?php foreach ($card_balance as $balance): $all_balance = $all_balance+$balance->value;?>
                <tr>
                    <td><?=$balance->date_time; ?></td>
                    <td><?=$balance->comment; ?></td>
                    <td><?=$balance->user->surname; ?></td>
                    <td><?=$balance->value; ?> грн</td>
                    <td><?=$balance->confirmed == 1 ? "Да" : "Нет"; ?></td>
                    <td>
                        <?php if($balance->confirmed == 0 ): ?>
                            <a class="btn btn-mini btn-warning" href="<?=URL::site('admin/card/confirm?id='.$balance->id);?>"><i class="icon-ok"></i> Подтвердить</a>
                        <?php endif;?>
                        <a class="btn btn-mini btn-danger" href="<?=URL::site('admin/card/delete?id='.$balance->id);?>"><i class="icon-remove"></i> Удалить</a>
                    </td>
                </tr>
        <?php endforeach; ?>
            <tr>
                <th>Общая сумма</th>
                <th></th>
                <th></th>
                <th><?= $all_cash; ?> грн</th>
                <th></th>
            </tr>
        </tbody>
    </table>

    <?=$pagination?>

</div>