<div class="container">
	<a class="btn btn-mini" href="<?=URL::site('admin/user/groupadd');?>"><i class="icon-plus"></i> Добавить</a><br /><br />
	<table class="table table-striped table-bordered">
		<tr>
			<th>ID</th>
			<th>Группа</th>
			<th>Описание</th>
			<th>Права</th>
			<th></th>
		</tr>
		<?php foreach($roles as $role) : ?>
		<tr>
			<td><?=$role->id?></td>
			<td><?=$role->name?></td>
			<td><?=$role->description?></td>
			<td><div class="permission"><?php
				$td_out = "";
				foreach($role->permissions->find_all()->as_array() as $permission) {
					$td_out .= empty($td_out) ? $permission->description : ",<br />".$permission->description;
				}
				echo $td_out;
			?></div></td>
			<td>
				<a class="btn btn-mini" href="<?=URL::site('admin/user/groupedit/'.$role->id);?>"><i class="icon-edit"></i> Редактировать</a>
				<a class="btn btn-mini btn-danger delete_row" href="<?=URL::site('admin/user/groupdelete/'.$role->id);?>"><i class="icon-remove"></i> Удалить</a>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
</div>

		<?/* $user_id = Auth::instance()->get_user()->id;
		$role = ORM::factory('user', $user_id)->roles->find();
		foreach ($roles as $role) echo "<pre>";var_dump($role->as_array());echo "</pre>"; */?>