<p class="help-block">Звонки компании</p>

<style>
    input.datepicker {
        height: 30px;
        margin-bottom: 0px;
    }
</style>
<?= Form::open('', [
        'class'  => 'form-vertical',
        'method' => 'get',
    ]); ?>
    Дата от
    <?= Form::input('date_from', $dateFrom, array('class' => 'form-control datepicker')); ?>
    до
    <?= Form::input('date_to', $dateTo, array('class' => 'datepicker')); ?>
    <?= Form::submit('', 'Применить фильтр', array('class' => 'btn btn-primary')); ?>
<?= Form::close(); ?>

<table class="table">
    <thead>
        <tr>
            <th>№</th>
            <th>Окончание</th>
            <th>Направление</th>
            <th>Сотрудник</th>
            <th>Внешний номер телефона</th>
            <th>Аудиозапись</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($calls as $i => $call): ?>
        <tr>
            <td><?= $i+1 ?></td>
            <td><?= date("d.m.Y H:i:s", $call->endAt/1000) ?></td>
            <td><?= $directions[$call->lgDirection] ?></td>
            <td>
                <?= $call->leg->displayName .
                    (!empty($call->leg->ext)
                        ? ' (' . $call->leg->ext . ')' : '')
                ?>
            </td>
            <td>
                <?= $call->otherLegNum . (
                    isset($call->otherLegName) ? ' (' . $call->otherLegName . ')' : ''
                ) ?>
            </td>
            <td>

                <?php if (!is_null($call->audioRecUrl)): ?>
                <a href="<?= $call->audioRecUrl ?>">Запись</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach ?>
    </tbody>
</table>

<?php if ($prevPageUrl): ?>
<a href="<?= $prevPageUrl ?>">Предыдущая страница</a>
<?php endif ?>
<?php if ($nextPageUrl): ?>
<a href="<?= $nextPageUrl ?>">Следующая страница</a>
<?php endif ?>
