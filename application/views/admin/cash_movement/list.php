<div class="container">
    <?php if (ORM::factory('Permission')->checkPermission('show_all_cash_movement')){ ?>
    <div class="row">
        <b>Фильтр</b>
        <?= Form::open('', array('class' => 'form-vertical', 'method' => 'get')); ?>
        Дата
        от <?= Form::input('date_from', HTML::chars(Arr::get($filters, 'date_from')), array('class' => 'datepicker')); ?>
        до <?= Form::input('date_to', HTML::chars(Arr::get($filters, 'date_to')), array('class' => 'datepicker')); ?>
        <br/>
        Сотрудник: <?= Form::select('user_id', $users, Arr::get($filters, 'user_id')); ?>
        <br/>

        <?= Form::submit('', 'Применить фильтр', array('class' => 'btn btn-primary')); ?>
        <a href="<?= URL::site('admin/CashMovement/list'); ?>" class="btn btn-danger"><i
                class="icon-white icon-refresh"></i> Сброс</a>
        <?= Form::close(); ?>

    </div>
    <?php } ?>
    <div class="row">
    <a href="create"><button class="btn btn-success" type="button">Добавить</button></a>
        <table class="table table-striped table-bordered">
            <tr>
                <th>От</th>
                <th>До</th>
                <th>Дата</th>
                <th>Сумма (грн.)</th>
                <th>Комментарий</th>
                <th>Подтверждение</th>
            </tr>
            <?php foreach ($movements as $key => $movement) : ?>
                <tr class="<?=($movement->confirmed) ? 'success' : 'error'?>">
                    <td><?= ORM::factory('user')->where('id', '=', $movement->from_user)->find()->surname ?></td>
                    <td><?= ORM::factory('user')->where('id', '=', $movement->to_user)->find()->surname ?></td>
                    <td><?= $movement->date ?></td>
                    <td><?= $movement->amount ?></td>
                    <td><?= $movement->comment ?></td>
                    <td>
                        <?php if ($movement->confirmed){
                            echo "Подтверждено";
                        } elseif (!$movement->confirmed AND $movement->to_user == $user_id){ ?>
                            <a href="confirm?id=<?=$movement->id?>"><button class="btn btn-primary" type="button">Подтвердить</button></a>
                        <?php } else {
                            echo "Не подтверджено";
                        } ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
</div>
</div>