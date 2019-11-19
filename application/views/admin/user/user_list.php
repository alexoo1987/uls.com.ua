<div class="container">
	<?php if ($_SERVER['REQUEST_URI'] == '/admin/user/list'): ?>
		<a href="<?= URL::site('admin/user/list_archive'); ?>" class="btn btn-primary">Архив пользователей</a><br/><br/>
	<?php else: ?>
		<a href="<?= URL::site('admin/user/list'); ?>" class="btn btn-primary">Все пользователи</a><br/><br/>
	<?php endif ?>

	<a class="btn btn-mini" href="<?=URL::site('admin/user/create');?>"><i class="icon-plus"></i> Добавить</a><br /><br />
	<table class="table table-striped table-bordered">
		<tr>
			<th>ID</th>
			<th>Имя пользователя</th>
			<th>Имя</th>
			<th>Фамилия</th>
			<th>Отчество</th>
			<th>Группа пользователя</th>
			<th>Email</th>
			<th>Последний вход</th>
            <th>Сумма наложек</th>
			<th></th>
		</tr>
		<?php foreach($users as $user) : ?>
		<tr>
			<td><?=$user->id?></td>
			<td><?=$user->username?></td>
			<td><?=$user->name?></td>
			<td><?=$user->surname?></td>
			<td><?=$user->middle_name?></td>
			<td><?=ORM::factory('user', $user->id)->roles->where('role_id', '>', '1')->find()->description?></td>
			<td><?=$user->email?></td>
			<td><?=empty($user->last_login) ? "---" : date("d.m.Y H:i:s", $user->last_login)?></td>
            <td><?=$user->total_ttns_sum?></td>
			<td>
				<a class="btn btn-mini" href="<?=URL::site('admin/user/edit/'.$user->id);?>"><i class="icon-edit"></i> Редактировать</a><br>
				<a class="btn btn-mini" href="<?=URL::site('admin/user/hide/'.$user->id);?>"><i class="icon-edit"></i><?php echo ($user->status == 1) ? "Скрыть" : "Восстановить"; ?></a><br>
				<a class="btn btn-mini btn-danger delete_row" href="<?=URL::site('admin/user/delete/'.$user->id);?>"><i class="icon-remove"></i> Удалить</a>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
</div>

		<?/* $user_id = Auth::instance()->get_user()->id;
		$role = ORM::factory('user', $user_id)->roles->find();
		foreach ($roles as $role) echo "<pre>";var_dump($role->as_array());echo "</pre>"; */?>