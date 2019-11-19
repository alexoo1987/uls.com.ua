<p class="help-block">Активные звонки</p>
<table class="table">
    <thead>
        <tr>
            <th>№</th>
            <th>Начало</th>
            <th>Направление</th>
            <th>Сотрудник</th>
            <th>Внешний номер телефона</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($calls as $i => $call): ?>
        <tr>
            <td><?= $i+1 ?></td>
            <td><?= date("H:i:s", $call->bridgeAt​/1000) ?></td>
            <td><?= $directions[$call->lgDirection] ?></td>
            <td>
                <?= $call->leg->displayName .
                    (!empty($call->leg->ext)
                        ? ' (' . $call->leg->ext . ')' : '')
                ?>
            </td>
            <td><?= $call->trunkNum​ ?></td>
        </tr>
        <?php endforeach ?>
    </tbody>
</table>