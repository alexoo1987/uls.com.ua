
<?= Form::open('', array('class' => 'form-horizontal', 'method' => 'get')); ?>

Менеджер <?= Form::select('manager_id', $managers, Arr::get($filters, 'manager_id')); ?><br />

<?= Form::submit('', 'Применить фильтр', array('class' => 'btn btn-primary')); ?>
<?= Form::close(); ?>
<a href="<?=URL::site('admin/clients/debtor');?>" class="btn btn-primary"><i class="icon-white icon-refresh"></i> Сброс</a><br /><br />

<table class="table">
    <thead>
    <tr>
        <th>ФИО</th>
        <th>Телефон</th>
        <th>Получил на сумму<br />свыше 8ми дней</th>
        <th>до 8ми дней назад</th>
        <th>Внес денег</th>
        <!--        <th>Дата последнего полученного заказа</th>-->
        <th style="background-color: red;">дебиторская<br />задолженность</th>
        <th>Общий долг</th>
    </tr>
    </thead>
    <tbody>
    <?php $totalDebth = 0; $totalDebthDebth = 0;?>
    <?php foreach ($clientsBalance as $clientBalance): ?>
        <?php $debth = round((round($clientBalance['pay_cash'], 2) - round($clientBalance['buy_cash'], 2) - round($clientBalance['buy_cash_new'], 2)), 2);
        if($debth < 0): ?>
            <tr>
                <td>
                    <a href="/admin/clientpayment/list?client_id=<?= $clientBalance['ClientId']?>"> <?= $clientBalance['surname']." ".$clientBalance['name']." ".$clientBalance['middlename'] ?></a>
                </td>
                <td><?= $clientBalance['phone'] ?></td>
                <td><?= round($clientBalance['buy_cash'], 2)." грн" ?></td>
                <td><?= round($clientBalance['buy_cash_new'], 2)." грн" ?></td>
                <td><?= round($clientBalance['pay_cash'], 2)." грн" ?></td>
                <!--            <td>--><?//= " " ?><!--</td>-->
                <td style="background-color: red;"><?= $debth + round($clientBalance['buy_cash_new'], 2)." грн" ?></td>
                <td><?= $debth." грн" ?></td>
            </tr>
            <?php $totalDebth += $debth; ?>
            <?php if($debth + round($clientBalance['buy_cash_new'], 2) < 0) $totalDebthDebth += $debth + round($clientBalance['buy_cash_new'], 2); ?>
        <?php endif; ?>
    <?php endforeach; ?>
    <tr>
        <th>Итого</th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th><?= round($totalDebthDebth, 2)." грн" ?></th>
        <th><?= $totalDebth ?></th>
    </tr>
    </tbody>
</table>



