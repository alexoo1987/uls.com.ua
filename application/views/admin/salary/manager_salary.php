<table class="table">
    <thead>
    <tr>
        <th>Фамилия</th>
        <th>Оборот</th>
        <th>Цена закупки по выданым<br />по 8.<?=$month?></th>
        <th>Цена продажи по выданым<br />по 8.<?=$month?></th>
        <th>Процент</th>
        <th>Зарплата</th>
        <th>Штрафы и выплат<br />с 23.<?=$month-1?> по 22.<?=$month?></th>
        <th>Невозвратные/Неподтвержденный<br />по 8.<?=$month?></th>
        <th>Долги по клиентам</th>
        <th>Итого</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($managersBalance as $managerBalance): ?>
        <tr>
            <td><?= $managerBalance->manager->surname ?></td>
            <td><?= $managerBalance->circulation." грн" ?></td>
            <td><?= round($managerBalance->purchase_per_unit,2)." грн" ?></td>
            <td><?= round($managerBalance->sale_per_unit,2)." грн" ?></td>
            <td><?= $managerBalance->percent ."%" ?></td>
            <td><?= round((round($managerBalance->sale_per_unit,2) - round($managerBalance->purchase_per_unit,2)) * $managerBalance->percent/100 ,2) ." грн"  ?></td>
            <td><?= round($managerBalance->penalty,2)." грн" ?></td>
            <td><?= $managerBalance->irrevocable." грн" ?></td>
            <td>
                <a href="/admin/clients/debtor?manager_id=<?= $managerBalance->manager->id?>&salary=1&month=<?=$month?>"> <?= round($managerBalance->debt ,2)." грн" ?></a>
            </td>
            <td><?= round((round($managerBalance->sale_per_unit ,2) - round($managerBalance->purchase_per_unit ,2)) * $managerBalance->percent/100 ,2) - round($managerBalance->penalty ,2)  + round($managerBalance->debt ,2) - round($managerBalance->irrevocable ,2)  ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>



