<p class="help-block">Сотрудники компании</p>
<table class="table">
    <thead>
        <th>ID</th>
        <th>Имя</th>
        <th>E-mail</th>
        <th>Код</th>
    </thead>
    <tbody>
        <?php foreach ($customers as $customer): ?>
        <tr>
            <td><?= $customer->id ?></td>
            <td><?= $customer->displayName ?></td>
            <td><?= $customer->email ?></td>
            <td><?= $customer->code ?></td>
        </tr>
        <?php endforeach ?>
    </tbody>
</table>