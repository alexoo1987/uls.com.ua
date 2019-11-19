<p class="help-block">Нужно перезвонить</p>
<table class="table">
    <thead>
        <tr>
            <th>№</th>
            <th>Окончание звонка</th>
            <th>Направление</th>
            <th>Сотрудник</th>
            <th>Номер телефона</th>
            <th>Аудиозапись</th>
        </tr>
    </thead>
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
        <td><?= $call->otherLegNum ?></td>
        <td><?= isset($call->audioRecUrl​) ? $call->audioRecUrl​ : '' ?></td>
    </tr>
    <?php endforeach ?>
</table>