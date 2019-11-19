<div class="container">

    <table class="table table-striped table-bordered">
        <tr>
            <th>ID</th>
            <th>Имя пользователя</th>
            <th>Имя</th>
            <th>Фамилия</th>
            <th>Группа пользователя</th>
            <th>Email</th>

        </tr>
        <?php foreach($current_users_birth_date as $user) : ?>
            <tr>
                <td><?=$user->id?></td>
                <td><?=$user->username?></td>
                <td><?=$user->name?></td>
                <td><?=$user->surname?></td>
                <td><?=ORM::factory('user', $user->id)->roles->where('role_id', '>', '1')->find()->description?></td>
                <td><?=$user->email?></td>

            </tr>
        <?php endforeach; ?>
    </table>
</div>