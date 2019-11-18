<div class="container">

    <b>Фильтр</b>
    <?= Form::open('', array('class' => 'form-horizontal', 'method' => 'get')); ?>
    Дата
    от <?= Form::input('date_from', HTML::chars(Arr::get($filters, 'date_from')), array('class' => 'datepicker')); ?>
    до <?= Form::input('date_to', HTML::chars(Arr::get($filters, 'date_to')), array('class' => 'datepicker')); ?><br/>
    Сотрудник <?= Form::select('user_id', $users, Arr::get($filters, 'user_id')); ?><br/>

    <?= Form::submit('', 'Применить фильтр', array('class' => 'btn btn-primary')); ?>
    <?= Form::close(); ?>
    <a href="<?= URL::site('admin/user/balance'); ?>" class="btn btn-primary"><i class="icon-white icon-refresh"></i>
        Сброс</a><br/><br/>

    <table class="table table-striped table-bordered">
        <tr>
            <th rowspan="2">Сотрудник</th>
            <th rowspan="2">Получил (грн.)</th>
            <th colspan="2" style="text-align: center">Поставщик (грн.)</th>
            <th rowspan="2">Затраты (грн.)</th>
            <th rowspan="2">Денежные движения (грн.)</th>
            <th rowspan="2">Штрафы и выплаты (грн.)</th>
            <th rowspan="2">Разница (грн.)</th>
        </tr>
        <tr>
            <th style="text-align: center">Проплаты</th>
            <th style="text-align: center">Возвраты</th>
        </tr>

        <?php foreach ($data AS $user_id => $list) { ?>
            <?php if (!ORM::factory('user')->where('id', '=', $user_id)->and_where('status', '=', 1)->find()->surname) continue;?>
            <tr>
                <td><?= ORM::factory('user')->where('id', '=', $user_id)->find()->surname ?></td>
                <td><?= $clients = isset($list['clients']) ? $list['clients'] : 0 ?></td>
                <td><?= $suppliers_plus = isset($list['suppliers_plus']) ? round($list['suppliers_plus'], 2) : 0 ?></td>
                <td><?= $suppliers_minus = isset($list['suppliers_minus']) ? round($list['suppliers_minus'], 2) : 0 ?></td>
                <td><?= $costs = isset($list['costs']) ? $list['costs'] : 0 ?></td>
                <td><?php
                    $cash_movements_from = isset($list['cash_movements_from']) ? $list['cash_movements_from'] : 0;
                    $cash_movements_to = isset($list['cash_movements_to']) ? $list['cash_movements_to'] : 0;
                    $cash_movement = $cash_movements_to - $cash_movements_from;
                    echo $cash_movement;
                    ?>
                </td>
                <td><?= $penalties = isset($list['penalties']) ? $list['penalties'] : 0 ?></td>
                <td><?= round($clients + $cash_movement  - $suppliers_plus + $suppliers_minus - $costs - $penalties, 2) ?></td>
            </tr>
        <?php } ?>
    </table>
    <div class="alert alert-info">

        <strong>Формула!</strong> Разница = (Получил + Денежные движения + Поставщик Возвраты) - (Поставщик Проплаты + Затраты + Штрафы и выплаты)
    </div>

</div>

<style type="text/css">
    td, th{
        vertical-align: middle !important;
    }
</style>