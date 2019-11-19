<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_User extends Controller_Admin_Application {

    public function action_index()
    {
        if(Auth::instance()->logged_in('login')) Controller::redirect('admin/welcome');
        $this->template->title = 'Administrator';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $this->template->content = View::factory('admin/user/login_form')
            ->bind('message', $message);

        if (HTTP_Request::POST == $this->request->method())
        {
            // Attempt to login user
            $remember = array_key_exists('remember', $this->request->post()) ? (bool) $this->request->post('remember') : FALSE;
            $user = Auth::instance()->login($this->request->post('username'), $this->request->post('password'), $remember);


            // If successful, redirect user
            if ($user)
            {
                $var_id = Auth::instance()->get_user()->id;
                $result = ORM::factory('User')->where('status', '=', 1)->and_where('id', '=', $var_id)->find_all()->as_array();
                if(count($result)<1)
                {
                    Auth::instance()->logout();
                    Controller::redirect('admin');
                }
                Controller::redirect('admin/welcome');
            }
            else
            {
                $message = 'Login failed';
            }
        }
    }

    public function action_logout()
    {
        // Log user out
        Auth::instance()->logout();

        // Redirect to login page
        Controller::redirect('admin');
    }

    public function action_create_csv()
    {
        $managers = ORM::factory('User')->where('status', '=', 1)->find_all()->as_array();

        foreach ($managers as $manager)
        {
            $clients = ORM::factory('Client')->where('manager_id', '=', $manager->id)->find_all()->as_array();
            $file = fopen('/var/www/eparts.kiev.ua/uploads/manager_'.$manager->surname.'.csv', 'w');  /* записываем в файл */

            foreach ($clients as $client)
            {
                $phone = preg_replace('![^0-9]+!', '', $client->phone);
                $line = [$phone, $client->name." ".$client->surname." ".$client->middlename, $manager->surname];
                fputcsv($file, $line, ';');
            }
            fclose($file);
        }
        exit();

    }

    public function action_create()
    {
        if(!ORM::factory('Permission')->checkPermission('manage_users')) Controller::redirect('admin');
        $this->template->title = 'User create';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $this->template->content = View::factory('admin/user/user_form')
            ->bind('errors', $errors)
            ->bind('message', $message)
            ->bind('roles', $roles)
            ->bind('data', $data);

        if (HTTP_Request::POST == $this->request->method())
        {
            try {
                if(empty($_POST['role']) || $_POST['role'] == 0) throw new Validation_Exception('Вы должны выбрать группу полльзователя.');
                // Create the user using form values
                $_POST['dont_show_salary'] = (!empty($_POST['dont_show_salary']) && $_POST['dont_show_salary'] == 1) ? 1 : 0;
                $_POST['show_salary_only_me'] = (!empty($_POST['show_salary_only_me']) && $_POST['show_salary_only_me'] == 1) ? 1 : 0;

                $user = ORM::factory('User')->create_user($this->request->post(), array(
                    'username',
                    'password',
                    'name',
                    'surname',
                    'middle_name',
                    'email',

                    'place_registration'
                ));
                $post = $this->request->post();

                $user->birth_date = $post['birth_year'].'-'.$post['birth_month'].'-'.$post['birth_day'];

                $user->dont_show_salary = $_POST['dont_show_salary'];
                $user->show_salary_only_me = $_POST['show_salary_only_me'];
                $user->save();

                // Grant user login role
                $user->add('roles', ORM::factory('role', array('name' => 'login')));
                $user->add('roles', ORM::factory('role', array('id' => $_POST['role'])));

                // Reset values so form is not sticky
                $_POST = array();

                // Set success message
                $message = "You have added user '{$user->username}' to the database";
                Controller::redirect('admin/user/list');
            } catch (ORM_Validation_Exception $e) {
                $data = $_POST;
                // Set failure message
                $message = 'There were errors, please see form below.';

                // Set errors using custom messages
                $errors = $e->errors('models');
            } catch (Validation_Exception $e) {
                $data = $_POST;
                // Set failure message
                $message = $e->getMessage();
            }
        }

        $roles = array(0 => "---");

        foreach(ORM::factory('role')->where('id', '>', '1')->find_all()->as_array() as $role) {
            $roles[$role->id] = $role->description;
        }
    }

    public function action_edit()
    {
        if(!ORM::factory('Permission')->checkPermission('manage_users')) Controller::redirect('admin');

        $id = $this->request->param('id');
        if(empty($id)) Controller::redirect('admin/user/list');

        $this->template->content = View::factory('admin/user/user_form')
            ->bind('errors', $errors)
            ->bind('message', $message)
            ->bind('roles', $roles)
            ->bind('data', $data);

        $this->template->title = 'User create';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $user = ORM::factory('user', $id);
        $data = array();
        $data['username'] = $user->username;
        $data['email'] = $user->email;
        $data['name'] = $user->name;
        $data['surname'] = $user->surname;
        $data['middle_name'] = $user->middle_name;
        $data['dont_show_salary'] = $user->dont_show_salary;
        $data['phone_number'] = $user->phone_number;
        $data['inside_code'] = $user->inside_code;

        $birth_day = date('d',strtotime($user->birth_date));
        $birth_month = date('m',strtotime($user->birth_date));
        $birth_year = date('Y',strtotime($user->birth_date));


        $data['birth_day'] = $birth_day ;
        $data['birth_month'] = $birth_month ;
        $data['birth_year'] = $birth_year ;
        $data['place_registration'] = $user->place_registration;

        $data['show_salary_only_me'] = $user->show_salary_only_me;
        $data['role'] = $user->roles->where('role_id', '>', '1')->find()->id;

        if (HTTP_Request::POST == $this->request->method())
        {
            try {
                if(empty($_POST['role']) || $_POST['role'] == 0) throw new Validation_Exception('Вы должны выбрать группу полльзователя.');

                $values = array(
                    'username',
                    'name',
                    'surname',
                    'inside_code',
                    'middle_name',
                    'email',
                    'phone_number',
                    'place_registration'
                );

                if(!empty($_POST['password'])) $values[] = 'password';

                $user->values($this->request->post(), $values);
                $post = $this->request->post();

                $user->birth_date = $post ['birth_year'].'-'.$post['birth_month'].'-'.$post['birth_day'];

                $user->dont_show_salary = (!empty($_POST['dont_show_salary']) && $_POST['dont_show_salary'] == 1) ? 1 : 0;
                $user->show_salary_only_me = (!empty($_POST['show_salary_only_me']) && $_POST['show_salary_only_me'] == 1) ? 1 : 0;
                $user->save();

                $user->remove('roles');
                // Grant user login role
                $user->add('roles', ORM::factory('role', array('name' => 'login')));
                $user->add('roles', ORM::factory('role', array('id' => $_POST['role'])));

                // Reset values so form is not sticky
                $_POST = array();

                Controller::redirect('admin/user/list');
            } catch (ORM_Validation_Exception $e) {
                $data = $_POST;
                // Set failure message
                $message = 'There were errors, please see form below.';

                // Set errors using custom messages
                $errors = $e->errors('models');
            } catch (Validation_Exception $e) {
                $data = $_POST;
                // Set failure message
                $message = $e->getMessage();
            }
        }

        $roles = array();

        foreach(ORM::factory('role')->where('id', '>', '1')->find_all()->as_array() as $role) {
            $roles[$role->id] = $role->description;
        }
    }
    public function action_hide()
    {
        if(!ORM::factory('Permission')->checkPermission('manage_users')) Controller::redirect('admin');

        $this->template->title = 'User hide';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $id = $this->request->param('id');
        if(!empty($id)) {
            $user = ORM::factory('User')->where('id', '=', $id)->find();

            $user->status = ($user->status==1) ? 0 : 1;
            $user->save();

            $managers_client = ORM::factory('Client')->where('manager_id', '=', $id)->find_all()->as_array();

            $query = DB::select('id')
                ->from('users')
                ->join('roles_users', 'LEFT')
                ->on('users.id', '=', 'roles_users.user_id')
                ->where('roles_users.role_id', 'IN', array(3,10,17))
                ->and_where('users.status', '=', 1);

            //all managers
            $managers_real = $query->execute()->as_array();

            $count_real = 0;
            for ($i = 0; $i < count($managers_client); $i++)
//            if(count($managers_client)>count($managers_real))
            {
                if($count_real==count($managers_real))
                {
                    $count_real=0;
                }
                $managers_client[$i]->manager_id = $managers_real[$count_real]['id'];
                $managers_client[$i]->save();
                $count_real++;
            }
            //exit();

//            foreach (array_combine($managers_client, $managers_real) as $manager_client => $manager_real)
//            {
//
//            }


            $costs_type = DB::select()->from('costs')->where('user_id', '=', $id)->execute()->as_array();
            if(count($costs_type)>0){
                DB::update('costs')->set(array('status_user' => 0))->where('status_user', '=', 1)->and_where('user_id', '=', $id)->execute();
                //    DB::update('costs')->set(array('status_user' => 1))->where('status_user', '=', 0)->and_where('user_id', '=', $id)->execute();
            }

        }

        Controller::redirect('admin/user/list');
    }

    public function action_list()
    {
        if(!ORM::factory('Permission')->checkPermission('manage_users')) Controller::redirect('admin');
        $this->template->title = 'Users list';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $this->template->content = View::factory('admin/user/user_list')
            ->bind('users', $users);

        $this->template->scripts[] = "common/users_list";
        $users = ORM::factory('User')->where('status', '=', 1)->find_all()->as_array();;
    }

    public function action_list_archive()
    {
        if(!ORM::factory('Permission')->checkPermission('manage_users')) Controller::redirect('admin');
        $this->template->title = 'Users list';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $this->template->content = View::factory('admin/user/user_list')
            ->bind('users', $users);

        $this->template->scripts[] = "common/users_list";
        $users = ORM::factory('User')->where('status', '=', 0)->find_all()->as_array();;
    }

    public function action_delete()
    {
        if(!ORM::factory('Permission')->checkPermission('manage_users')) Controller::redirect('admin');

        $this->template->title = 'User create';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $id = $this->request->param('id');
        if(!empty($id)) {
            $user = ORM::factory('User')->where('id', '=', $id)->find();

            $user->remove('roles');
            $user->delete();
        }

        Controller::redirect('admin/user/list');
    }

    public function action_groups() {
        if(!ORM::factory('Permission')->checkPermission('manage_groups')) Controller::redirect('admin');
        $this->template->title = 'Groups';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $this->template->content = View::factory('admin/user/group_list')
            ->bind('roles', $roles);
        $roles = ORM::factory('role')->where('id', '>', '1')->find_all()->as_array();
        $this->template->scripts[] = "jquery.shorten.1.0";
        $this->template->scripts[] = "common/groups_list";
    }

    public function action_groupadd() {
        if(!ORM::factory('Permission')->checkPermission('manage_groups')) Controller::redirect('admin');

        $this->template->content = View::factory('admin/user/group_form')
            ->bind('permissions', $permissions)
            ->bind('errors', $errors)
            ->bind('message', $message)
            ->bind('data', $data);

        $this->template->title = 'User create';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        if (HTTP_Request::POST == $this->request->method())
        {
            try {
                $group = ORM::factory('role');
                $group->values($this->request->post(), array(
                    'name',
                    'description'
                ));
                $group->save();

                foreach(ORM::factory('Permission')->find_all()->as_array() as $permission) {
                    if(Arr::get($_POST, $permission->name) == "1") $group->add('permissions', ORM::factory('permission', array('name' => $permission->name)));
                }

                // Reset values so form is not sticky
                $_POST = array();

                // Set success message
                $message = "You have added new group to the database";
                Controller::redirect('admin/user/groups');
            } catch (ORM_Validation_Exception $e) {
                $data = $_POST;
                // Set failure message
                $message = 'Исправте ошибки!';

                // Set errors using custom messages
                $errors = $e->errors('models');
            }
        }

        $permissions = ORM::factory('Permission')->find_all()->as_array();
    }

    public function action_groupedit() {
        if(!ORM::factory('Permission')->checkPermission('manage_groups')) Controller::redirect('admin');

        $id = $this->request->param('id');
        if(empty($id)) Controller::redirect('admin/user/groups');

        $this->template->content = View::factory('admin/user/group_form')
            ->bind('permissions', $permissions)
            ->bind('errors', $errors)
            ->bind('message', $message)
            ->bind('data', $data);

        $this->template->title = 'User create';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $group = ORM::factory('role')->where('id', '=', $id)->find();
        $data = array();
        $data['name'] = $group->name;
        $data['description'] = $group->description;

        foreach($group->permissions->find_all()->as_array() as $permission) {
            $data[$permission->name] = 1;
        }

        if (HTTP_Request::POST == $this->request->method())
        {
            try {
                $group->values($this->request->post(), array(
                    'name',
                    'description'
                ));
                $group->save();

                $group->remove('permissions');

                foreach(ORM::factory('Permission')->find_all()->as_array() as $permission) {
                    if(Arr::get($_POST, $permission->name) == "1") $group->add('permissions', ORM::factory('permission', array('name' => $permission->name)));
                }

                // Reset values so form is not sticky
                $_POST = array();

                // Set success message
                $message = "You have added new group to the database";
                Controller::redirect('admin/user/groups');
            } catch (ORM_Validation_Exception $e) {
                $data = $_POST;
                // Set failure message
                $message = 'Исправте ошибки!';

                // Set errors using custom messages
                $errors = $e->errors('models');
            }
        }

        $permissions = ORM::factory('Permission')->find_all()->as_array();
    }

    public function action_groupdelete() {
        if(!ORM::factory('Permission')->checkPermission('manage_groups')) Controller::redirect('admin');

        $this->template->title = 'User create';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $id = $this->request->param('id');
        if(!empty($id)) {
            $group = ORM::factory('role')->where('id', '=', $id)->find();

            $group->remove('permissions');
            $group->remove('users');
            $group->delete();
        }

        Controller::redirect('admin/user/groups');
    }

    /**
     * Users balance statistics
     * @throws Kohana_Exception
     */
    public function action_balance()
    {
        if (!ORM::factory('Permission')->checkPermission('user_balance')) Controller::redirect('admin');

        $this->template->title = 'Статистика баланса сотрудников';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $this->template->content = View::factory('admin/user/balance')
            ->bind('users', $users)
            ->bind('data', $data)
            ->bind('filters', $filters);

        $clients = DB::select(array(DB::expr('SUM(value)'), 'value'), 'user_id')
            ->from('client_payments')
            ->where('user_id', 'IS NOT', NULL)
            ->and_where('type', '=', 0)
            ->and_where(DB::expr('DATE(date_time)'), '>=', '2016-03-30');

        $suppliers_plus = DB::select(array(DB::expr('SUM(value*ratio)'), 'value'), 'user_id')
            ->from('supplier_payments')
            ->where('user_id', 'IS NOT', NULL)
            ->and_where(DB::expr('DATE(date_time)'), '>=', '2016-03-30')
            ->and_where('value', '>', 0);

        $suppliers_minus = DB::select(array(DB::expr('SUM(value*ratio)'), 'value'), 'user_id')
            ->from('supplier_payments')
            ->where('user_id', 'IS NOT', NULL)
            ->and_where(DB::expr('DATE(date_time)'), '>=', '2016-03-30')
            ->and_where('value', '<', 0);

        $costs = DB::select(array(DB::expr('SUM(amount)'), 'value'), 'user_id')
            ->from('costs')
            ->where('user_id', 'IS NOT', NULL)
            ->and_where(DB::expr('DATE(date)'), '>=', '2016-03-30');

        $cash_movements_from = DB::select(array(DB::expr('SUM(amount)'), 'value'), 'from_user')
            ->from('cash_movement')
            ->where('from_user', 'IS NOT', NULL)
            ->and_where('confirmed', '=', 1);

        $cash_movements_to = DB::select(array(DB::expr('SUM(amount)'), 'value'), 'to_user')
            ->from('cash_movement')
            ->where('to_user', 'IS NOT', NULL)
            ->and_where('confirmed', '=', 1);

        $penalties = DB::select(array(DB::expr('SUM(amount)'), 'value'), 'creator')
            ->from('penalty')
            ->where('creator', 'IS NOT', NULL);

        if (!empty($_GET['date_from'])) {
            $filters['date_from'] = $_GET['date_from'];
            $date_from = new DateTime($filters['date_from']);
            $clients = $clients->and_where(DB::expr('DATE(date_time)'), '>=', $date_from->format('Y-m-d'));
            $suppliers_plus = $suppliers_plus->and_where(DB::expr('DATE(date_time)'), '>=', $date_from->format('Y-m-d'));
            $suppliers_minus = $suppliers_minus->and_where(DB::expr('DATE(date_time)'), '>=', $date_from->format('Y-m-d'));
            $costs = $costs->and_where(DB::expr('DATE(date)'), '>=', $date_from->format('Y-m-d'));
            $cash_movements_from = $cash_movements_from->and_where(DB::expr('DATE(date)'), '>=', $date_from->format('Y-m-d'));
            $cash_movements_to = $cash_movements_to->and_where(DB::expr('DATE(date)'), '>=', $date_from->format('Y-m-d'));
            $penalties = $penalties->and_where(DB::expr('DATE(date)'), '>=', $date_from->format('Y-m-d'));
        }

        if (!empty($_GET['date_to'])) {
            $filters['date_to'] = $_GET['date_to'];
            $date_to = new DateTime($filters['date_to']);
            $clients = $clients->and_where(DB::expr('DATE(date_time)'), '<=', $date_to->format('Y-m-d'));
            $suppliers_plus = $suppliers_plus->and_where(DB::expr('DATE(date_time)'), '<=', $date_to->format('Y-m-d'));
            $suppliers_minus = $suppliers_minus->and_where(DB::expr('DATE(date_time)'), '<=', $date_to->format('Y-m-d'));
            $costs = $costs->and_where(DB::expr('DATE(date)'), '<=', $date_to->format('Y-m-d'));
            $cash_movements_from = $cash_movements_from->and_where(DB::expr('DATE(date)'), '<=', $date_to->format('Y-m-d'));
            $cash_movements_to = $cash_movements_to->and_where(DB::expr('DATE(date)'), '<=', $date_to->format('Y-m-d'));
            $penalties = $penalties->and_where(DB::expr('DATE(date)'), '<=', $date_to->format('Y-m-d'));
        }

        if (!empty($_GET['user_id'])) {
            $filters['user_id'] = $_GET['user_id'];
            $clients = $clients->and_where('user_id', '=', $filters['user_id']);
            $suppliers_plus = $suppliers_plus->and_where('user_id', '=', $filters['user_id']);
            $suppliers_minus = $suppliers_minus->and_where('user_id', '=', $filters['user_id']);
            $costs = $costs->and_where('user_id', '=', $filters['user_id']);
            $cash_movements_from = $cash_movements_from->and_where('from_user', '=', $filters['user_id']);
            $cash_movements_to = $cash_movements_to->and_where('to_user', '=', $filters['user_id']);
            $penalties = $penalties->and_where('creator', '=', $filters['user_id']);
        }

        $clients = $clients->group_by('user_id')->execute()->as_array();
        $suppliers_plus = $suppliers_plus->group_by('user_id')->execute()->as_array();
        $suppliers_minus = $suppliers_minus->group_by('user_id')->execute()->as_array();
        $costs = $costs->group_by('user_id')->execute()->as_array();
        $cash_movements_from = $cash_movements_from->group_by('from_user')->execute()->as_array();
        $cash_movements_to = $cash_movements_to->group_by('to_user')->execute()->as_array();
        $penalties = $penalties->group_by('creator')->execute()->as_array();

        $all_users_active = ORM::factory('Users')->where('status', '=', 1)->find_all()->as_array();

        $all_users_active_ids = [];

        foreach ($all_users_active as $user)
        {
            $all_users_active_ids[]=$user->id;
        }


        $data = array();
        foreach ($clients AS $key => $one) {
//		    if(in_array($one['user_id'],$all_users_active_ids))
            $data[$one['user_id']]['clients'] = $one['value'];
        }

        foreach ($suppliers_plus AS $key => $one) {
//            if(in_array($one['user_id'],$all_users_active_ids))
            $data[$one['user_id']]['suppliers_plus'] = $one['value'];
        }

        foreach ($suppliers_minus AS $key => $one) {
//            if(in_array($one['user_id'],$all_users_active_ids))
            $data[$one['user_id']]['suppliers_minus'] = -$one['value'];
        }

        foreach ($costs AS $key => $one) {
//            if(in_array($one['user_id'],$all_users_active_ids))
            $data[$one['user_id']]['costs'] = $one['value'];
        }

        foreach ($cash_movements_from AS $key => $one) {
//            if(in_array($one['from_user'],$all_users_active_ids))
            $data[$one['from_user']]['cash_movements_from'] = $one['value'];
        }

        foreach ($cash_movements_to AS $key => $one) {
//            if(in_array($one['to_user'],$all_users_active_ids))
            $data[$one['to_user']]['cash_movements_to'] = $one['value'];
        }

        foreach ($penalties AS $key => $one) {
//            if(in_array($one['creator'],$all_users_active_ids))
            $data[$one['creator']]['penalties'] = $one['value'];
        }

        //users, who can add supplier payment
        $users = array('' => '---');
        foreach (ORM::factory('User')->where('status', '=', 1)->find_all()->as_array() as $user) {
            if (ORM::factory('Permission')->checkPermissionByUser($user->id, 'supplierpayment_manage') OR ORM::factory('Permission')->checkPermissionByUser($user->id, 'cash_movement')) $users[$user->id] = $user->surname;
        }

        $this->template->scripts[] = 'bootstrap.validate';
        $this->template->scripts[] = 'bootstrap.validate.ru';
        $this->template->scripts[] = "common/client_payments_list";
        $this->template->scripts[] = 'jquery-ui-1.10.4.custom.min';
    }


    public function action_birth_date()
    {
        if(!ORM::factory('Permission')->checkPermission('manage_users')) Controller::redirect('admin');
        $this->template->title = 'День рождения ';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $current_date = date("m-d");

        $users = ORM::factory('User')->where('status', '=', 1)->find_all()->as_array();

        $current_users_birth_date = array();
        foreach ($users as $user) {
            if ($user->birth_date) {
                $birth_date = DateTime::createFromFormat("Y-m-d", $user->birth_date)->format('m-d');
                if ($birth_date === $current_date ) {
                    $current_users_birth_date[] = $user;
                }
            }
        }

        $this->template->content = View::factory('admin/user/birth_date')
            ->bind('errors', $errors)
            ->bind('message', $message)
            ->bind('roles', $roles)
            ->bind('current_users_birth_date', $current_users_birth_date);


    }


    public function action_percent()
    {
        $this->template->title = 'Продуктивность сотрудников';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
        $this->template->content = View::factory('admin/user/percent')
            ->bind('users', $users)
            ->bind('userPercent', $userPercent);

        $query = DB::select('*')
            ->from('users')
            ->join('roles_users', 'LEFT')
            ->on('users.id', '=', 'roles_users.user_id')
            ->where('roles_users.role_id', 'IN', array(3,10,17))
            ->and_where('users.status', '=', 1);

        $users = $query->execute()->as_array();
        $userPercent = [];
        foreach ($users as $user)
        {
            $countStates = "SELECT COUNT(orderitems.id) as count, states.`name`
                FROM orderitems
                INNER JOIN orders ON orders.id = orderitems.order_id
                INNER JOIN states ON state_id = states.id
                WHERE manager_id = ".$user['id']."
                GROUP BY state_id";
            $managerPercents = DB::query(Database::SELECT, $countStates)->execute()->as_array();

            $allCount = "SELECT COUNT(orderitems.id) as count
                FROM orderitems
                INNER JOIN orders ON orders.id = orderitems.order_id
                INNER JOIN states ON state_id = states.id
                WHERE manager_id = ".$user['id']."";
            $allContPosition = DB::query(Database::SELECT, $allCount)->execute()->get('count',0);

            if(!empty($managerPercents))
            {
                $userPercent[$user['id']] = $managerPercents;
                $userPercent[$user['id']]['all'] = $allContPosition;
            }
        }

//        print_r($userPercent); exit();
    }

} // End Admin_User



class Validation_Exception extends Exception {};
