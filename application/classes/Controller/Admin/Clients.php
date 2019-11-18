<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Clients extends Controller_Admin_Application {

	public function action_index() {
		if(!ORM::factory('Permission')->checkPermission('manage_clients')) Controller::redirect('admin');

		$this->template->content = View::factory('admin/clients/list')
			->bind('filters', $filters)
			->bind('managers', $managers)
			->bind('pagination', $pagination)
			->bind('data', $data)
			->bind('clients', $clients);

		$this->template->title = 'Clients';
		$this->template->description = '';
		$this->template->keywords = '';
		$this->template->author = '';

		$clients_orm = ORM::factory('Client');
		$clients_orm->reset(FALSE);

		if(ORM::factory('Permission')->checkPermission('clients_show_only_my'))
			$clients_orm = $clients_orm->and_where('manager_id', '=', Auth::instance()->get_user()->id);

		if(!empty($_GET['manager_id'])) {
			$filters['manager_id'] = $_GET['manager_id'];
			$clients_orm = $clients_orm->and_where('manager_id', '=', $filters['manager_id']);
		}
		if(!empty($_GET['surname'])) {
			$filters['surname'] = $_GET['surname'];
			$clients_orm = $clients_orm->and_where('surname', 'LIKE', "%".$filters['surname']."%");
		}
		if(!empty($_GET['phone'])) {
			$filters['phone'] = "+".trim(urldecode($_GET['phone']));
			if($filters['phone'] == "+38") unset($filters['phone']);
			if(!empty($filters['phone']))
				$clients_orm = $clients_orm->and_where('phone', '=', $filters['phone']);
		}
		if(isset($_GET['client_type']) && $_GET['client_type'] !== '') {
			$filters['client_type'] = $_GET['client_type'];
			$clients_orm = $clients_orm->and_where('client_type', '=', $filters['client_type']);
		}
		if(isset($_GET['only_actions']) && $_GET['only_actions'] == '1') {
			$filters['only_actions'] = $_GET['only_actions'];
			$clients_orm = $clients_orm
				->and_where('client_type', '=', Model_Client::TYPE_JUR)
				->or_where('is_service_station', '=', 1);
		}

		$count = $clients_orm->count_all();

		$pagination = Pagination::factory(array('total_items' => $count))->route_params(array(
			'controller' =>  'clients',
			'action' =>  'index'
		));
		$clients = $clients_orm->limit($pagination->items_per_page)->offset($pagination->offset)->order_by('registration_date', 'desc')->find_all()->as_array();

		$this->template->scripts[] = 'jquery-ui-1.10.4.custom.min';
		$this->template->scripts[] = 'common/clients_list';


		$managers = array(""=>"---");

		$query = DB::select('*')
			->from('users')
			->join('roles_users', 'LEFT')
			->on('users.id', '=', 'roles_users.user_id')
			->where('roles_users.role_id', 'IN', array(3,10,17))
			->and_where('users.status', '=', 1);

		//all managers
		$managers_real = $query->execute()->as_array();

		foreach($managers_real as $user) {
			$managers[$user['id']] = $user['surname'];
		}

		$this->template->scripts[] = 'bootstrap-formhelpers-phone.format';
		$this->template->scripts[] = 'bootstrap-formhelpers-phone';
		$this->template->scripts[] = 'bootstrap.validate';
		$this->template->scripts[] = 'bootstrap.validate.ru';
		$this->template->scripts[] = 'common/order_form_step2';
	}

	public function action_add() {
		if(!ORM::factory('Permission')->checkPermission('manage_clients')) Controller::redirect('admin');

		$this->template->content = View::factory('admin/clients/form')
			->bind('permissions', $permissions)
			->bind('errors', $errors)
			->bind('message', $message)
			->bind('delivery_methods', $delivery_methods)
			->bind('managers', $managers)
			->bind('discounts', $discounts)
			->bind('data', $data);

		$this->template->title = 'Добавить';
		$this->template->description = '';
		$this->template->keywords = '';
		$this->template->author = '';

		if(!empty($_GET['phone_num'])) $data = array('phone' => $_GET['phone_num']);

		if (HTTP_Request::POST == $this->request->method())
		{
			try {
				$client = ORM::factory('Client')->create_user($this->request->post(), array(
					'name',
					'surname',
					'phone',
					'middlename',
					'password',
					'email',
					'additional_phone',
					'delivery_address',
					'manager_id',
					'delivery_method_id',
					'discount_id',
					'comment'
				));
				$post = $this->request->method();
				if(!empty($post['birth_year']) and !empty($post['birth_month']) and !empty($post['birth_day'])) {
					$client->birth_data = $post['birth_year'] . '-' . $post['birth_month'] . '-' . $post['birth_day'];
				}
				$client->active = !empty($_POST['active']) && $_POST['active'] == 1 ? 1 : 0;
				$client->save();

				// Reset values so form is not sticky
				$_POST = array();

				if(isset($_GET['discount_id']) && !empty($_GET['discount_id'])) {
					$discount_id = $_GET['discount_id'];
					$discount_str = "&discount_id=".$discount_id;
				} else {
					$discount_id = false;
					$discount_str = "";
				}

				if(!empty($_GET['priceitem_id'])) {
					Controller::redirect('admin/orders/add_new?priceitem_id='.$_GET['priceitem_id']."&client_id=".$client->id."&amount=".$_GET['amount'].$discount_str);
				} else {
					Controller::redirect('admin/clients');
				}
			} catch (ORM_Validation_Exception $e) {
				$data = $_POST;
				// Set failure message
				$message = 'Исправте ошибки!';

				// Set errors using custom messages
				$errors = $e->errors('models');
			}
		}
		$this->template->scripts[] = 'bootstrap-formhelpers-phone.format';
		$this->template->scripts[] = 'bootstrap-formhelpers-phone';
		$this->template->scripts[] = 'bootstrap.validate';
		$this->template->scripts[] = 'bootstrap.validate.ru';
		$this->template->scripts[] = 'common/clients_form';


		$delivery_methods = array(0 => "---");

		foreach(ORM::factory('DeliveryMethod')->find_all()->as_array() as $deliverymethod) {
			$delivery_methods[$deliverymethod->id] = $deliverymethod->name;
		}

		$managers = array();

		$query = DB::select('*')
			->from('users')
			->join('roles_users', 'LEFT')
			->on('users.id', '=', 'roles_users.user_id')
			->where('roles_users.role_id', 'IN', array(3,10,17))
			->and_where('users.status', '=', 1);

		//all managers
		$managers_real = $query->execute()->as_array();

		foreach($managers_real as $user) {
			$managers[$user['id']] = $user['surname'];
		}


		$data['manager_id'] = Auth::instance()->get_user()->id;

		$discounts = array();

		foreach(ORM::factory('Discount')->order_by('id', 'asc')->find_all()->as_array() as $discount) {
			$discounts[$discount->id] = $discount->name;
		}
	}

	public function action_edit() {
		if(!ORM::factory('Permission')->checkPermission('manage_clients')) Controller::redirect('admin');

		$id = $this->request->param('id');
		if(empty($id)) Controller::redirect('admin/clients');

		$this->template->content = View::factory('admin/clients/form')
			->bind('permissions', $permissions)
			->bind('errors', $errors)
			->bind('message', $message)
			->bind('delivery_methods', $delivery_methods)
			->bind('managers', $managers)
			->bind('discounts', $discounts)
			->bind('data', $data);

		$this->template->title = 'Редактировать';
		$this->template->description = '';
		$this->template->keywords = '';
		$this->template->author = '';

		$client = ORM::factory('Client')->where('id', '=', $id)->find();
		$data = array();
		$data['id'] = $client->id;
		$data['client_type'] = $client->client_type;
		$data['name'] = $client->name;
		$data['surname'] = $client->surname;
		$data['middlename'] = $client->middlename;
		$data['phone'] = $client->phone;
		$data['email'] = $client->email;
		$data['additional_phone'] = $client->additional_phone;
		$data['delivery_address'] = $client->delivery_address;
		$data['manager_id'] = $client->manager_id;
		$data['delivery_method_id'] = $client->delivery_method_id;
		$data['discount_id'] = $client->discount_id;
		$data['active'] = $client->active;
		$data['comment'] = $client->comment;
		$data['documents'] = $client->documents->find_all()->as_array();

		$birth_day = date('d',strtotime($client->birth_data));
		$birth_month = date('m',strtotime($client->birth_data));
		$birth_year = date('Y',strtotime($client->birth_data));


		$data['birth_day'] = $birth_day ;
		$data['birth_month'] = $birth_month ;
		$data['birth_year'] = $birth_year ;

		if (HTTP_Request::POST == $this->request->method())
		{
			try {
				$values = array(
					'name',
					'surname',
					'middlename',
					'phone',
					'email',
					'client_type',
					'additional_phone',
					'delivery_address',
					'manager_id',
					'delivery_method_id',
					'discount_id',
					'comment'
				);

				if(!empty($_POST['password'])) $values[] = 'password';

				$client->values($this->request->post(), $values);
				$post = $this->request->post();
				if(!empty($post['birth_year']) and !empty($post['birth_month']) and !empty($post['birth_day'])) {
					$client->birth_data = $post['birth_year'] . '-' . $post['birth_month'] . '-' . $post['birth_day'];
				}
				$client->active = !empty($_POST['active']) && $_POST['active'] == 1 ? 1 : 0;
				$client->save();

				// Reset values so form is not sticky
				$_POST = array();

				Controller::redirect('admin/clients');
			} catch (ORM_Validation_Exception $e) {
				$data = $_POST;
				// Set failure message
				$message = 'Исправте ошибки!';

				// Set errors using custom messages
				$errors = $e->errors('models');
			}
		}

		$this->template->scripts[] = 'bootstrap-formhelpers-phone.format';
		$this->template->scripts[] = 'bootstrap-formhelpers-phone';
		$this->template->scripts[] = 'bootstrap.validate';
		$this->template->scripts[] = 'bootstrap.validate.ru';
		$this->template->scripts[] = 'common/clients_form';

		$delivery_methods = array(0 => "---");

		foreach(ORM::factory('DeliveryMethod')->find_all()->as_array() as $deliverymethod) {
			$delivery_methods[$deliverymethod->id] = $deliverymethod->name;
		}

		$managers = array();

		$query = DB::select('*')
			->from('users')
			->join('roles_users', 'LEFT')
			->on('users.id', '=', 'roles_users.user_id')
			->where('roles_users.role_id', 'IN', array(3,10))
			->and_where('users.status', '=', 1);

		//all managers
		$managers_real = $query->execute()->as_array();

		foreach($managers_real as $user) {
			$managers[$user['id']] = $user['surname'];
		}

		$discounts = array();

		foreach(ORM::factory('Discount')->order_by('id', 'asc')->find_all()->as_array() as $discount)
		{
			$discounts[$discount->id] = $discount->name;
		}

		if(!ORM::factory('Permission')->checkPermission('vip_price'))
			unset($discounts[11]);
	}

	public function action_delete() {
		if(!ORM::factory('Permission')->checkPermission('manage_clients')) Controller::redirect('admin');

		$this->template->title = '';
		$this->template->description = '';
		$this->template->keywords = '';
		$this->template->author = '';

		$id = $this->request->param('id');
		if(!empty($id)) {
			$client = ORM::factory('Client')->where('id', '=', $id)->find();

			$client->delete();
		}

		Controller::redirect('admin/clients');
	}

	public function action_debtor()
	{
		$this->template->title = 'Задолженность по клиентам';
		$this->template->description = '';
		$this->template->keywords = '';
		$this->template->author = '';

		$this->template->content = View::factory('admin/clients/debtor')
			->bind('clientsBalance', $clientsBalance)
			->bind('managers', $managers);

		$clientsBalance = [];
		$managers = [];
		if(!empty($_GET['manager_id'])) {
			$manager_id = $_GET['manager_id'];
		}

		if(!empty($_GET['salary']))
		{
			$date = "2018-".$_GET['month']."-08 23:59:59";
			$date2 = "2018-".$_GET['month']."-22 23:59:59";
			$allQuery = "SELECT *
					FROM (SELECT SUM(amount * sale_per_unit) as buy_cash, client_id as ClientId, c.name, c.surname, c.middlename,  c.phone 
					FROM orderitems as oi
					INNER JOIN orders o ON o.id = oi.order_id
					INNER JOIN orderitems_log as ol ON ol.orderitem_id = oi.id
					INNER JOIN clients c ON o.client_id = c.id
					WHERE c.manager_id = ".$manager_id."
					AND ol.date_time <= '".$date."'
					AND ol.state_id = 5
					AND oi.state_id = 5
					GROUP BY o.client_id) as buy
					
					INNER JOIN (SELECT SUM(`value`) as pay_cash, client_id
					FROM client_payments as cp
					INNER JOIN clients c ON c.id = cp.client_id
					WHERE c.manager_id = ".$manager_id."
					AND cp.date_time <= '".$date2."'
					GROUP BY cp.client_id) as pay ON pay.client_id = buy.ClientId";

			$clientsBalance = DB::query(Database::SELECT,$allQuery)->execute()->as_array();
		}
		else
		{
			if(ORM::factory('Permission')->checkPermission('debt_all_managers'))
			{
				$query = DB::select('*')
					->from('users')
					->join('roles_users', 'LEFT')
					->on('users.id', '=', 'roles_users.user_id')
					->where('roles_users.role_id', 'IN', array(3,10,17))
					->and_where('users.status', '=', 1);

				$managers_real = $query->execute()->as_array();

				foreach($managers_real as $user)
					$managers[$user['id']] = $user['surname'];
			}
			else
				$manager_id = Auth::instance()->get_user()->id;


			if(!empty($manager_id))
			{

				$dateDebth = new DateTime();
				$dateDebth->modify('- 8 day');

				$allQuery = "SELECT *
					FROM (SELECT SUM(amount * sale_per_unit) as buy_cash, client_id as ClientId, c.name, c.surname, c.middlename,  c.phone 
					FROM orderitems as oi
					INNER JOIN orders o ON o.id = oi.order_id
					INNER JOIN orderitems_log as ol ON ol.orderitem_id = oi.id
					INNER JOIN clients c ON o.client_id = c.id
					WHERE c.manager_id = ".$manager_id."
					AND ol.date_time <= '".$dateDebth->format('Y-m-d 23:59:59')."'
					AND ol.state_id = 5
					AND oi.state_id = 5
					GROUP BY o.client_id) as buy
					
					INNER JOIN (SELECT SUM(`value`) as pay_cash, client_id
					FROM client_payments as cp
					INNER JOIN clients c ON c.id = cp.client_id
					WHERE c.manager_id = ".$manager_id."
					GROUP BY cp.client_id) as pay ON pay.client_id = buy.ClientId
					
					LEFT JOIN (
							SELECT SUM(amount * sale_per_unit) as buy_cash_new, client_id
							FROM orderitems as oi
							INNER JOIN orders o ON o.id = oi.order_id
							INNER JOIN orderitems_log as ol ON ol.orderitem_id = oi.id
							INNER JOIN clients c ON o.client_id = c.id
							WHERE c.manager_id = 3
							AND oi.state_id = 5
							AND ol.state_id = 5
							AND ol.date_time > '".$dateDebth->format('Y-m-d 23:59:59')."'
							GROUP BY o.client_id) as buy_new ON pay.client_id = buy_new.client_id";

				$clientsBalance = DB::query(Database::SELECT,$allQuery)->execute()->as_array();
			}
		}
	}

//	public function action_test_oilog()
//	{
//		$allQuery = "SELECT oi.id, o.date_time, oi.delivery_days
//			FROM orderitems as oi
//			INNER JOIN orders as o ON o.id = oi.order_id
//			WHERE state_id = 5
//			AND oi.id NOT IN (SELECT orderitems_log.orderitem_id as id FROM orderitems_log WHERE state_id = 5)";
//
//		$logs = DB::query(Database::SELECT,$allQuery)->execute()->as_array();
//
//		foreach ($logs as $log)
//		{
//			$date = new DateTime($log['date_time']);
//			$date->modify('+'.$log['delivery_days'].' day');
//			echo $date->format('Y-m-d H:i:s');
//			echo " ".$log['id'];
//
//			$id = $log['id'];
//			$log = ORM::factory('OrderitemLog');
//			$log
//				->set('orderitem_id', $id)
//				->set('state_id', 5)
//				->set('tehnomir', 0)
//				->set('date_time', $date->format('Y-m-d H:i:s'))
//				->set('user_id', 58) //Auth::instance()->get_user()->id
//				->save();
//
////			exit();
//
////			$log = ORM::factory('OrderitemLog');
////
////			$log
////				->set('orderitem_id', $log['id'])
////				->set('state_id', 5)
////				->set('tehnomir', 0)
////				->set('date_time', date('Y-m-d H:i:s'))
////				->set('user_id', 2) //Auth::instance()->get_user()->id
////				->save();
//		}
//	}

} // End Admin_User



class Validation_Exception extends Exception {};
