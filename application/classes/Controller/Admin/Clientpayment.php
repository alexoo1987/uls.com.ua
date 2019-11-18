<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Clientpayment extends Controller_Admin_Application {
	
		
	public $disallowed_states = Model_Services::disableStates;
			
	public function action_list() {
		if(!ORM::factory('Permission')->checkPermission('client_payments')) Controller::redirect('admin');
		
		$this->template->content = View::factory('admin/client_payments/list')
			->bind('data', $data)
			->bind('filters', $filters)
			->bind('cp_pagination', $cp_pagination)
			->bind('oi_pagination', $oi_pagination)
			->bind('total', $total)
			->bind('managers', $managers)
			->bind('users', $users)
			->bind('filters', $filters)
			->bind('orderitems', $orderitems)
			->bind('orders', $orders)
			->bind('client_payments', $client_payments);
		$this->template->title = 'Баланс по клиенту';
		$this->template->description = '';
		$this->template->keywords = '';
		$this->template->author = '';
		
		if (HTTP_Request::POST == $this->request->method()) 
		{			
			$clientpayment = ORM::factory('ClientPayment');
			$clientpayment->values($this->request->post(), array(
				'client_id',
				'value',
				'comment_text',
			));
			$clientpayment->set('date_time', date('Y-m-d', strtotime($_POST['date_time'])));
			if(!empty($_POST['order_id'])) $clientpayment->set('order_id', $_POST['order_id']);
			$clientpayment->save();
			
			// Reset values so form is not sticky
			$_POST = array();
			Controller::redirect('admin/clientpayment/list?client_id='.$clientpayment->client_id);
		}
		
		$client_payments = ORM::factory('ClientPayment')->with('client')->with('order');
		$client_payments->reset(FALSE);
		
		$orderitems = ORM::factory('Orderitem')->with('order');
		$orderitems->reset(FALSE);
		
		$orderitems_before = ORM::factory('Orderitem')->with('order')->with('state');
		$client_payments_before = ORM::factory('ClientPayment')->with('client')->with('order');
		
		$orderitems_before = $orderitems_before->and_where('state.id', 'NOT IN', $this->disallowed_states);

        $filter_type = isset($_GET['filter_type']) ? $_GET['filter_type'] : 1;
        $table = $filter_type == 1 ? 'clientpayment' : 'order';

		if(!empty($_GET['date_from'])) {
			$filters['date_from'] = $_GET['date_from'];
			$client_payments = $client_payments->and_where($table.'.date_time', '>=', date('Y-m-d 00:00:00', strtotime($filters['date_from'])));
			$orderitems = $orderitems->and_where('order.date_time', '>=', date('Y-m-d 00:00:00', strtotime($filters['date_from'])));
				
			$client_payments_before = $client_payments_before->and_where($table.'.date_time', '<', date('Y-m-d 00:00:00', strtotime($filters['date_from'])));
			$orderitems_before = $orderitems_before->and_where('order.date_time', '<', date('Y-m-d 00:00:00', strtotime($filters['date_from'])));
		} else {
			$client_payments_before = false;
			$orderitems_before = false;
		}
		if(!empty($_GET['date_to'])) {
			$filters['date_to'] = $_GET['date_to'];
			$client_payments = $client_payments->and_where($table.'.date_time', '<=', date('Y-m-d 23:59:59', strtotime($filters['date_to'])));
			$orderitems = $orderitems->and_where('order.date_time', '<=', date('Y-m-d 23:59:59', strtotime($filters['date_to'])));
		}
		if(!empty($_GET['client_id'])) {
			$filters['client_id'] = $_GET['client_id'];
			$data['client_id'] = $filters['client_id'];
			$client_payments = $client_payments->and_where('clientpayment.client_id', '=', $filters['client_id']);
			$orderitems = $orderitems->and_where('order.client_id', '=', $filters['client_id']);
			
			if($client_payments_before && $orderitems_before) {
				$client_payments_before = $client_payments_before->and_where('clientpayment.client_id', '=', $filters['client_id']);
				$orderitems_before = $orderitems_before->and_where('client_id', '=', $filters['client_id']);
			}
		}
		if(!empty($_GET['manager_id'])) {
			$filters['manager_id'] = $_GET['manager_id'];
			$client_payments = $client_payments->and_where('client.manager_id', '=', $filters['manager_id']);
			$orderitems = $orderitems->and_where('order.manager_id', '=', $filters['manager_id']);
			
			if($client_payments_before && $orderitems_before) {
				$client_payments_before = $client_payments_before->and_where('client.manager_id', '=', $filters['manager_id']);
				$orderitems_before = $orderitems_before->and_where('order.manager_id', '=', $filters['manager_id']);
			}
		}

		if (!empty($_GET['user_id'])) {
			$filters['user_id'] = $_GET['user_id'];
			$client_payments = $client_payments->and_where('user_id', '=', $filters['user_id']);
		}
		
		$total = array('payments' => 0, 'orderitems' => 0, 'before' => 0, 'payments_table' => 0);
		
		if($client_payments_before && $orderitems_before) {
			$orderitems_before = $orderitems_before->find_all()->as_array();
			$client_payments_before = $client_payments_before->find_all()->as_array();
			foreach($client_payments_before as $cp) {
				$total['before'] += $cp->value;
			}
			
			foreach($orderitems_before as $orderitem) {
				$val = $orderitem->sale_per_unit * $orderitem->amount;
				$total['before'] -= $val;
			}
		}	
		
		
		$count = $client_payments->count_all();
		
		$cp_pagination = Pagination::factory(array(
			'current_page' => array('source' => 'query_string', 'key' => 'cp_page'),
			'total_items' => $count,
			'items_per_page' => 10,
			))->route_params(array(
		  'controller' =>  'clientpayment',
		  'action' =>  'list'
		));
		
		$client_payments = $client_payments->order_by('date_time', "desc")->find_all()->as_array();
		
		foreach($client_payments as $sp) {
			if($sp->value > 0/* && (!empty($filters['date_from']) || !empty($filters['date_to']))*/)
				$total['payments_table'] += $sp->value;
			$total['payments'] += $sp->value;
		}
		
		$client_payments = array_slice($client_payments, $cp_pagination->offset, $cp_pagination->items_per_page);
		
		
		$count = $orderitems->count_all();
		
		$oi_pagination = Pagination::factory(array(
			'current_page' => array('source' => 'query_string', 'key' => 'oi_page'),
			'total_items' => $count,
			'items_per_page' => 10,
			))->route_params(array(
		  'controller' =>  'clientpayment',
		  'action' =>  'list'
		));
		
		$orderitems_tmp = $orderitems->order_by('date_time', "desc")->find_all()->as_array();
		$orderitems = array();
		
		foreach($orderitems_tmp as $orderitem) {
			$val = $orderitem->sale_per_unit*$orderitem->amount;
			if(!in_array($orderitem->state->id, $this->disallowed_states)) {
				$total['orderitems'] += $val;
				$orderitem->val = $val." грн.";
			} else {
				$orderitem->val = "<strike>".$val." грн.</strike>";
			}
			$orderitems[] = $orderitem;
		}
		
		$orderitems = array_slice($orderitems, $oi_pagination->offset, $oi_pagination->items_per_page);
		
		$managers = array('' => '---');
		$users = array('' => '---');

		foreach(ORM::factory('User')->where('status', '=', 1)->find_all()->as_array() as $user) {
			$managers[$user->id] = $user->surname;

			//users, who can add client payment
			if (ORM::factory('Permission')->checkPermissionByUser($user->id, 'clientpayment_add')) $users[$user->id] = $user->surname;
		}
		$orders = array("" => "---");
			
		$orders_tmp = ORM::factory('Order')->where('archive', '=', '0');
		if(ORM::factory('Permission')->checkPermission('show_only_own_orders')) $orders_tmp->and_where('manager_id', '=', Auth::instance()->get_user()->id);
		$orders_tmp = $orders_tmp->order_by("date_time", "desc")->find_all()->as_array();
		
		foreach($orders_tmp as $order) {
			$orders[$order->id] = $order->get_order_number()." (".$order->client->name." ".$order->client->surname.")";
		}
		
		$this->template->scripts[] = 'bootstrap.validate';
		$this->template->scripts[] = 'bootstrap.validate.ru';
		$this->template->scripts[] = "common/client_payments_list";
		$this->template->scripts[] = 'jquery-ui-1.10.4.custom.min';
	}
	
	public function action_edit() {
		if(!ORM::factory('Permission')->checkPermission('client_payments')) Controller::redirect('admin');
		
		$id = $this->request->param('id');
		if(empty($id)) Controller::redirect('admin/clientpayment/list');
		
		$this->template->content = View::factory('admin/client_payments/form')
			->bind('permissions', $permissions)
			->bind('errors', $errors)
			->bind('message', $message)
			->bind('currencies', $currencies)
			->bind('data', $data);
			
        $this->template->title = 'Редактирование поставщика';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		$clientpayment = ORM::factory('ClientPayment')->where('id', '=', $id)->find();
		$data = array();
		$data['name'] = $clientpayment->name;
		$data['phone'] = $clientpayment->phone;
		$data['delivery_days'] = $clientpayment->delivery_days;
		$data['сomment_text'] = $clientpayment->сomment_text;
		$data['currency_id'] = $clientpayment->currency_id;
		
		if (HTTP_Request::POST == $this->request->method()) 
		{			
			try {
				$clientpayment->values($this->request->post(), array(
					'name',
					'phone',
					'delivery_days',
					'сomment_text',
					'currency_id',
				));
				$clientpayment->save();
				
				// Reset values so form is not sticky
				$_POST = array();
				
				Controller::redirect('admin/clientpayment/list');
			} catch (ORM_Validation_Exception $e) {
				$data = $_POST;
				// Set failure message
				$message = 'Исправте ошибки!';
				
				// Set errors using custom messages
				$errors = $e->errors('models');
			}
		}
		
		$currencies = array();
		
		foreach(ORM::factory('Currency')->find_all()->as_array() as $currency) {
			$currencies[$currency->id] = $currency->name;
		}
	}
	
	public function action_delete() {
		if(!ORM::factory('Permission')->checkPermission('client_payments')) Controller::redirect('admin');
		
        $this->template->title = '';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		$id = $this->request->param('id');
		$client_id = "";
		if(!empty($id)) {
			$clientpayment = ORM::factory('ClientPayment')->where('id', '=', $id)->find();
			$client_id = $clientpayment->client_id;
			$clientpayment->delete();
		}
		
		Controller::redirect('admin/clientpayment/list?client_id='.$client_id);
	}
}

class Validation_Exception extends Exception {};
