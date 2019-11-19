<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Salary extends Controller_Admin_Application {
	
	public function action_list() {
		if(!ORM::factory('Permission')->checkPermission('salary')) Controller::redirect('admin');
		
		$this->template->content = View::factory('admin/salary/list')
			->bind('salaries', $salaries);
		$this->template->title = 'Проценты зарплаты';
		$this->template->description = '';
		$this->template->keywords = '';
		$this->template->author = '';
		
		$salaries = ORM::factory('Salary')->find_all()->as_array();
		
		$this->template->scripts[] = "common/salary_list";
	}
	
	public function action_add() {
		if(!ORM::factory('Permission')->checkPermission('salary')) Controller::redirect('admin');
		
		$this->template->content = View::factory('admin/salary/form')
			->bind('permissions', $permissions)
			->bind('errors', $errors)
			->bind('message', $message)
			->bind('managers', $managers)
			->bind('data', $data);
			
        $this->template->title = 'Добавление % з/п';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
			
		if (HTTP_Request::POST == $this->request->method()) 
		{			
			try {
				$salary = ORM::factory('Salary');
				$salary->values($this->request->post(), array(
					'percentage',
					'from_id',
					'to_id'	
				));
				$salary->save();
				
				// Reset values so form is not sticky
				$_POST = array();
				
				Controller::redirect('admin/salary/list');
			} catch (ORM_Validation_Exception $e) {
				$data = $_POST;
				// Set failure message
				$message = 'Исправте ошибки!';
				
				// Set errors using custom messages
				$errors = $e->errors('models');
			}
		}
		
		$managers = array('' => '----');
		
		foreach(ORM::factory('User')->find_all()->as_array() as $user) {
			$managers[$user->id] = $user->surname;
		}
		
		$this->template->scripts[] = 'bootstrap.validate';
		$this->template->scripts[] = 'bootstrap.validate.ru';
		$this->template->scripts[] = 'common/salary_form';
	}
	
	public function action_edit() {
		if(!ORM::factory('Permission')->checkPermission('salary')) Controller::redirect('admin');
		
		$id = $this->request->param('id');
		if(empty($id)) Controller::redirect('admin/salary/list');
		
		$this->template->content = View::factory('admin/salary/form')
			->bind('permissions', $permissions)
			->bind('errors', $errors)
			->bind('message', $message)
			->bind('managers', $managers)
			->bind('data', $data);
			
        $this->template->title = 'Редактирование % з/п';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		$salary = ORM::factory('Salary')->where('id', '=', $id)->find();
		$data = array();
		$data['percentage'] = $salary->percentage;
		$data['from_id'] = $salary->from_id;
		$data['to_id'] = $salary->to_id;
		
		if (HTTP_Request::POST == $this->request->method()) 
		{			
			try {
				$salary->values($this->request->post(), array(
					'percentage',
					'from_id',
					'to_id'	
				));
				$salary->save();
				
				// Reset values so form is not sticky
				$_POST = array();
				
				Controller::redirect('admin/salary/list');
			} catch (ORM_Validation_Exception $e) {
				$data = $_POST;
				// Set failure message
				$message = 'Исправте ошибки!';
				
				// Set errors using custom messages
				$errors = $e->errors('models');
			}
		}
		
		$managers = array('' => '----');
		
		foreach(ORM::factory('User')->find_all()->as_array() as $user) {
			$managers[$user->id] = $user->surname;
		}
		
		$this->template->scripts[] = 'bootstrap.validate';
		$this->template->scripts[] = 'bootstrap.validate.ru';
		$this->template->scripts[] = 'common/salary_form';
	}
	
	public function action_delete() {
		if(!ORM::factory('Permission')->checkPermission('salary')) Controller::redirect('admin');
		
        $this->template->title = '';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		$id = $this->request->param('id');
		if(!empty($id)) {
			$salary = ORM::factory('Salary')->where('id', '=', $id)->find();
			
			$salary->delete();
		}
		
		Controller::redirect('admin/salary/list');
	}
	
	public function action_manager_salary()
	{
		$this->template->title = 'ЗП менеджеров';
		$this->template->description = '';
		$this->template->keywords = '';
		$this->template->author = '';

		$month = false;
		$year = false;

		if(!empty($_GET['month'])) {
			$month = $_GET['month'];
		}

		if(!empty($_GET['year'])) {
			$year = $_GET['year'];
		}

		if($year && $month)
		{
			$this->template->content = View::factory('admin/salary/manager_salary')
				->bind('managersBalance', $managerBalance)
				->bind('errors', $errors)
				->bind('message', $message)
				->bind('managers', $managers)
				->bind('data', $data)
				->bind('month', $month);

			$managerBalance = ORM::factory('ManagerSalary')
				->reset(FALSE)
				->and_where('month', '=', $month)
				->and_where('year', '=', $year)
				->find_all()
				->as_array();

			$managers = ORM::factory('User')->where('status', '=', 1)->find_all()->as_array();

//			print_r($managerBalance); exit();
		}
		else
		{
			$this->template->content = View::factory('admin/salary/manager_salary_select')
				->bind('piriods', $piriods);

			$queryPiriod = "SELECT DISTINCT
				`month`, `year`
				FROM manager_salary";
			$piriods = DB::query(Database::SELECT,$queryPiriod)->execute()->as_array();

//			print_r($piriods); exit();
		}
		
//		$managers = ORM::factory('User')->where('status', '=', 1)->find_all()->as_array();
//		$finish_day = date('Y-m-8 23:59:59');
//		$start_day = date('Y-m-9 00:00:00', strtotime($finish_day."-1 month"));
//
//		$finish_day_penalty = date('Y-m-22 23:59:59');
//		$start_day_penalty  = date('Y-m-23 00:00:00', strtotime($finish_day_penalty."-1 month"));
//
//		$managerBalance = [];
//		$unactiveStates = [1, 4, 7, 13, 14, 15, 17, 18, 19, 34, 35];
//		$unactiveStatesNames = array('order_accept', 'not_available', 'changes_delivery_period', 'stopped', 'returns_from_the_client', 'return_to_supplier', 'unconfirmed_returns', 'withdrawal', 'irretrievable', 'excess_price', 'uncertain');
//
//
//		foreach ($managers as $manager)
//		{
//			$managerBalance[$manager->id]['manager'] = $manager;
//			$circulation = 0;
//			$orderitemsCirculation = ORM::factory('Orderitem')->with('order')->with('order:client')
//				->reset(FALSE)
//				->and_where('order.manager_id', '=', $manager->id)
//				->and_where('order.date_time', '>=', $start_day)
//				->and_where('order.date_time', '<=', $finish_day)
//				->and_where('state_id', 'NOT IN', $unactiveStates)
//				->find_all()
//				->as_array();
//
//			foreach ($orderitemsCirculation as $orderitemCirculation)
//			{
////				if(in_array($orderitemsCirculation->state->text_id, $unactiveStatesNames))
////					continue;
//
//				$circulation += $orderitemCirculation->sale_per_unit*$orderitemCirculation->amount;
//			}
//			$managerBalance[$manager->id]['circulation'] = $circulation;
//
//			$query = "SELECT
//				SUM(orderitems.sale_per_unit*orderitems.amount) as prodaj, sum(orderitems.purchase_per_unit*orderitems.amount) as zakup
//				FROM orderitems
//				INNER JOIN orders ON orderitems.order_id = orders.id
//				INNER JOIN orderitems_log ON orderitems_log.orderitem_id = orderitems.id
//				WHERE
//				orderitems.state_id = 5
//				AND
//				orderitems_log.state_id = 5
//				AND manager_id = ".$manager->id."
//				AND orderitems_log.date_time >= '".$start_day."'
//				AND orderitems_log.date_time <= '".$finish_day."'
//				AND orderitems.salary = 0
//				GROUP BY orders.manager_id";
//			$orderitemsGive = DB::query(Database::SELECT,$query)->execute()->current();
//
//			$queryPenalty = "SELECT
//				SUM(amount) as penalty
//				FROM penalty
//				WHERE penalty.date >= '".$start_day_penalty."'
//				AND penalty.date <= '".$finish_day_penalty."'
//				AND user_id = ".$manager->id."
//				AND status = 0
//				GROUP BY penalty.user_id";
//			$managerPenalty = DB::query(Database::SELECT,$queryPenalty)->execute()->current();
//
//			$queryUserDebth = "SELECT *
//					FROM (SELECT SUM(amount * sale_per_unit) as buy_cash, client_id as ClientId, c.name, c.surname, c.middlename,  c.phone
//					FROM orderitems as oi
//					INNER JOIN orders o ON o.id = oi.order_id
//					INNER JOIN orderitems_log as ol ON ol.orderitem_id = oi.id
//					INNER JOIN clients c ON o.client_id = c.id
//					WHERE c.manager_id = ".$manager->id."
//					AND ol.date_time <= '2018-02-08 23:59:59'
//					AND ol.state_id = 5
//					AND oi.state_id = 5
//					GROUP BY o.client_id) as buy
//
//					INNER JOIN (SELECT SUM(`value`) as pay_cash, client_id
//					FROM client_payments as cp
//					INNER JOIN clients c ON c.id = cp.client_id
//					WHERE c.manager_id = ".$manager->id."
//					AND cp.date_time <=  '2018-02-22 23:59:59'
//					GROUP BY cp.client_id) as pay ON pay.client_id = buy.ClientId";
//			$usersPenalty = DB::query(Database::SELECT,$queryUserDebth)->execute()->as_array();
//
//
//			$managerBalance[$manager->id]['prodaj'] = $orderitemsGive['prodaj'];
//			$managerBalance[$manager->id]['zakup'] = $orderitemsGive['zakup'];
//			$managerBalance[$manager->id]['percent'] = $this->getPercent($circulation);
//			$managerBalance[$manager->id]['penalty'] = $managerPenalty['penalty'];
//
//			$totlalDebth = 0;
//			foreach ($usersPenalty as $userPenalty)
//			{
//				if($userPenalty['pay_cash'] - $userPenalty['buy_cash'] < 0)
//					$totlalDebth += $userPenalty['pay_cash'] - $userPenalty['buy_cash'];
//			}
//			$managerBalance[$manager->id]['debth'] = $totlalDebth;
//
//
////			$orderitemsGive = ORM::factory('OrderitemLog');
////			$orderitemsGive = $orderitemsGive->join('orderitems')
////				->on('orderitemlog.orderitem_id', '=', 'orderitems.id')
////				->where('orderitemlog.state_id', '=', 5)
////				->and_where('orderitems.supplier_id', '=', $id);
//		}

//		print_r($managerBalance); exit();
	}

	public function getPercent($amount)
	{
		if($amount <= 100000)
			return 0.15;
		elseif ($amount > 100000 && $amount <=150000)
			return 0.18;
		elseif ($amount > 150000 && $amount <=250000)
			return 0.20;
		elseif ($amount > 250000 && $amount <=350000)
			return 0.22;
		elseif ($amount > 350000 && $amount <=450000)
			return 0.25;
		elseif ($amount > 450000)
			return 0.30;
	}

} // End Admin_User



class Validation_Exception extends Exception {};
