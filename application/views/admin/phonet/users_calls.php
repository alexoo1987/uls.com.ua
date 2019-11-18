<p class="help-block">Звонки пользователей</p>
<table class="table">
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
    </tr>
    <?php endforeach ?>
</table>