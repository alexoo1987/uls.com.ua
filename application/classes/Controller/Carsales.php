<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Carsales extends Controller_Application {	
	public function action_index()
	{
		$this->template->content = View::factory('carsales/form')
			->bind('errors', $errors)
			->bind('message', $message)
			->bind('data', $data)
			->bind('page', $page);
		$page = ORM::factory('Page')->where('syn', '=', 'carsales')->find();
			
        $this->template->title = $page->title;
        $this->template->h1 = $page->h1_title;
        $this->template->description = $page->meta_description;
        $this->template->keywords = $page->meta_keywords;
			
		if (HTTP_Request::POST == $this->request->method()) 
		{			
			try {
				$carsale = ORM::factory('Carsale')->values($this->request->post(), array(
					'name',
					'phone',
					'email',
					'manufacturer',
					'model',
					'modification',
					'year',
					'volume',
					'transmission',/////
					'color',/////
					'parts_number',/////
					'description',
					'price',
				));
				
				$carsale->save();
	
				foreach(array('photo1', 'photo2', 'photo3') as $key) {
					if(!empty($_FILES[$key]) && !empty($_FILES[$key]['name'])) {
						$ext = pathinfo($_FILES[$key]['name'], PATHINFO_EXTENSION);
						$filepath = Upload::save($_FILES[$key], $key."_".$carsale->id.'.'.$ext, "uploads/carsale");
					
						$carsale->{$key} = $key."_".$carsale->id.'.'.$ext;;
					}
				}
				$carsale->save();
				
				// Reset values so form is not sticky
				$_POST = array();
				
				$message = "Запрос отправлен. В ближайшее время с Вами свяжутся.";
				
				
				$mail_tpl = View::factory('email/carsale')
					->set('carsale', $carsale);
				$sms_text = $carsale->manufacturer." ".$carsale->model." ".$carsale->year;
				$sms_text .= "\n".$carsale->price."\n".$carsale->phone."\n".$carsale->name;
				$mail_tpl_html = $mail_tpl->render();
				
				
				$phones = ORM::factory('Setting')->where('code_name', '=', 'carsales_phones')->find()->value;
				$emails = ORM::factory('Setting')->where('code_name', '=', 'carsales_email')->find()->value;
				
				try {
					foreach(explode(';', $emails) as $email) {
						$email = Email::factory('Автовыкуп', '')
							->to($email)
							->from('no-reply@eparts.kiev.ua')
							->message($mail_tpl_html, 'text/html')
							->send();
					}
				} catch (Exception $e) {

				}
				

				try {
					foreach(explode(';', $phones) as $phone) {
						Sms::send($sms_text, "Автовыкуп", $phone);
					}
				} catch (Exception $e) {

				}
				
			} catch (ORM_Validation_Exception $e) {
				$data = $_POST;
				// Set failure message
				$message = 'Исправьте ошибки!';
				
				// Set errors using custom messages
				$errors = $e->errors('models');
			}
		}
		
		$this->template->scripts[] = 'common/carsales_form';
	}
}
