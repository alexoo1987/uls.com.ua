<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_SMs extends Controller_Admin_Application {
	
	public function action_index() {
		if(!ORM::factory('Permission')->checkPermission('sms_send')) Controller::redirect('admin');
		$this->template->content = View::factory('admin/sms/form')
			->bind('errors', $errors)
			->bind('message', $message)
			->bind('clients', $clients)
			->bind('data', $data);
			
        $this->template->title = 'Рассылка SMS';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
			
		if (HTTP_Request::POST == $this->request->method()) 
		{
			$clients = array();
			if(!empty($_POST['send_all']) && $_POST['send_all'] == '1') {
				$clients = ORM::factory('Client');
			} elseif(!empty($_POST['client_id'])) {
				$clients = ORM::factory('Client')->where('id', 'IN', $_POST['client_id']);
			}
			
			$sms_text = trim($_POST['message']);
			
			if($clients) {
				$clients = $clients->find_all()->as_array('id', 'phone');
			}
			
			if(count($clients) == 0) {
				$message = "Вы не выбрали ни одного клиента";
			} elseif($sms_text == "") {
				$message = "Введите текст";
			} else {
				Sms::send_to_many($sms_text, "Рассылка", $clients);
				$message = "Сообщения отправленны";
			}
		}
		$this->template->styles[] = 'chosen/chosen.min';
		$this->template->scripts[] = 'chosen/chosen.jquery.min';//chosen
		$this->template->scripts[] = 'common/sms_form';
		
		$clients = array();
		
		foreach(ORM::factory('Client')->find_all()->as_array() as $client) {
			$clients[$client->id] = $client->surname." ".$client->name." (".$client->phone.")";
		}
	}

	/**
	 * Send props to client
	 */
	public function action_props() {
		if(!ORM::factory('Permission')->checkPermission('sms_send_props')) Controller::redirect('admin');
		$this->template->content = View::factory('admin/sms/props')
			->bind('errors', $errors)
			->bind('message', $message)
			->bind('text', $text);

		$text = 'Карта 4731219110437385 Курякова Людмила Hиколаевна.ПриватБанк';
		$amount = '';

		if (HTTP_Request::POST == $this->request->method())
		{
			if ((strlen($_POST['number']) == 13)){
				if(!empty($_POST['amount']))
					$amount = ' Сумма:'.$_POST['amount'].'грн';
				Sms::send($text.$amount, 'ULC' , $_POST['number']);
				$message = "Сообщение с реквизитами успешно отправлено";
			} else {
				$message = "Номер телефона введен неправильно";
			}
		}
		$this->template->title = 'Отравка реквизитов';
		$this->template->description = '';
		$this->template->keywords = '';
		$this->template->author = '';
	}
} // End Admin_User



class Validation_Exception extends Exception {};
