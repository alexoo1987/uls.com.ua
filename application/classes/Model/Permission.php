<?php defined('SYSPATH') or die('No direct script access.');
 
class Model_Permission extends ORM {
    protected $_table_name = 'permissions';
	
	protected $_has_many = array(
		'roles' => array('model' => 'Role','through' => 'roles_permissions'),
	);
	
	public function checkPermission($name = false) {
		if(!$name) return false;
		
		if(Auth::instance()->get_user() && Auth::instance()
				->get_user()
				->roles
				->where('role_id', '>', '1')
				->find()
				->permissions
				->where('name', '=', $name)
				->count_all() > 0) return true;
		return false;
	}

	/**
	 * Check user role
	 * @param bool $name - Role name
	 * @return bool
	 */
	public function checkRole($name = false) {
		if(!$name) return false;

		if(Auth::instance()->get_user() && Auth::instance()
				->get_user()
				->roles
				->where('name', '=', $name)
				->count_all() > 0) return true;
		return false;
	}

	/** Check permission by user_id and permission name
	 * @param bool $user_id
	 * @param bool $permission
	 * @return bool
	 */
	public function checkPermissionByUser($user_id = false, $permission = false)
	{
		if (!$user_id OR !$permission) return false;

		$result = DB::select('users.id')
			->from('users')
			->join('roles_users', 'LEFT')
			->on('users.id', '=', 'roles_users.user_id')
			->join('roles_permissions', 'LEFT')
			->on('roles_users.role_id', '=', 'roles_permissions.role_id')
			->join('permissions', 'LEFT')
			->on('roles_permissions.permission_id', '=', 'permissions.id')
			->where('users.id', '=', $user_id)
			->where('permissions.name', '=', $permission)
			->execute()->as_array();

		return empty($result) ? false : true;
	}
}