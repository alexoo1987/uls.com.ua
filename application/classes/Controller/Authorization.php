<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Authorization extends Controller_Application {

    public function action_login()
    {
        $this->template->content = View::factory('clients/login')
            ->bind('errors', $errors)
            ->bind('message', $message)
            ->bind('data', $data);

        $this->template->title = 'Авторизация';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        if (HTTP_Request::POST == $this->request->method())
        {
            $data['phone'] = $this->request->post('phone');
            $data['redirect_to'] = $this->request->post('redirect_to');
            $data['password'] = $this->request->post('password');

            if(ORM::factory('Client')->login($data['phone'], $data['password'])) {
                if (!empty($_GET['order_add']))
                    return Controller::redirect('orders/add');
                if(!empty($data['redirect_to'])) {
                    return Controller::redirect($data['redirect_to']);
                } else {
                    return Controller::redirect('/orders?archive=all');
                }
            } else {
                $message = "Номер телефона отсутствует в базе или неверный пароль.<br>Проверьте правильность вводимых данных.";
            }
        }

        $this->template->scripts[] = 'common/login_form';
    }

    public function action_logout()
    {
        ORM::factory('Client')->logout();
//        if(!empty($_SERVER['HTTP_REFERER'])) {
//			return Controller::redirect($_SERVER['HTTP_REFERER']);
//		}
        //Auth::instance()->logout(FALSE, TRUE);
        // Redirect to login page
        //HTTP::redirect('admin/access/login');
        return Controller::redirect('/');
    }

    public function action_recall()
    {
        $this->template->content = View::factory('clients/recall')
            ->bind('errors', $errors)
            ->bind('message', $message)
            ->bind('delivery_methods', $delivery_methods)
            ->bind('data', $data);

        $this->template->title = '';
        $this->template->description = '';
        $this->template->robots = 'noindex,nofollow';
        $this->template->keywords = '';
        $this->template->author = '';

        $phone_recal_auto = preg_replace('![^0-9]+!', '', $_POST['phone_number']);

//        $url = "http://sip2.it-center.kiev.ua/eparts2/otzvon.php?extnumber=".$phone_recal_auto;
//        file_get_contents($url);

        $phone = $_POST['phone_number'];
        $to = 'ulc.com.ua@gmail.com';
        $subject = 'Перезвоните мне';
        Email::factory($subject, '')
            ->to($to)
            ->from('no-reply@ulc.com.ua')
            ->message("Здравствуйте! Наберите меня пожайлуста " . $phone, 'text/html')
            ->send();
        $message = 'Спасибо Вам. Мы обязательно свяжемся с Вами в ближайшее время по номеру  "'.$phone.'"!';


    }

    protected function getReCaptchaResult($reCaptha)
    {
        $postdata = http_build_query(
            array(
                'secret'=>RECAPTCHA_KEY ,
                'response'=>$reCaptha)
        );

        $opts = array('http' =>
            array(
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => $postdata
            )
        );
        $context  = stream_context_create($opts);
        $result = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
        $check = json_decode($result);
        return  $check->success;
    }

    public function action_registration()
    {
        $this->template->content = View::factory('clients/form')
            ->bind('errors', $errors)
            ->bind('message', $message)
            ->bind('delivery_methods', $delivery_methods)
            ->bind('data', $data);

        $this->template->title = 'Регистрация - интернет магазин автозапчастей Eparts';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        if (HTTP_Request::POST == $this->request->method())
        {
            try {
                $recaptcha = $this->getReCaptchaResult($_POST['g-recaptcha-response']);
                if(!$recaptcha ){
                    Controller::redirect('/authorization/registration');
                }
                $client = ORM::factory('Client')->create_user($this->request->post(), array(
                    'name',
                    'surname',
                    'phone',
                    'password',
                    'email',
                    'additional_phone',
                    'delivery_address',
                    'delivery_method_id',
                    'is_service_station',
                    'client_type',
                ));

                if ((int) $client->client_type === Model_Client::TYPE_JUR) {
                    $this->checkAttachment('jur_certificate');
                } elseif ($client->is_service_station) {
                    $this->checkAttachment('sto_document');
                }

                $post = $this->request->post() ;
                if(!empty($post['birth_year']) and !empty($post['birth_month']) and !empty($post['birth_day'])) {
                    $client->birth_data = $post['birth_year'] . '-' . $post['birth_month'] . '-' . $post['birth_day'];
                }
                $client->discount_id = ORM::factory('Discount')->getClient_standart();
                $client->active = 1;
                $client->activation_key = md5($client->id . date("dmYHis"));

//              mail($client->email,
//                  "Активация аккаунта",
//                  "Для активации перейдите по ссылке\n".
//                  URL::site("authorization/activate")."?key=".$client->activation_key);


                $query = DB::select('id')
                    ->from('users')
                    ->join('roles_users', 'LEFT')
                    ->on('users.id', '=', 'roles_users.user_id')
                    ->where('roles_users.role_id', 'IN', array(3,10,17))
                    ->and_where('users.status', '=', 1);

                //all managers
                $managers = $query->execute()->as_array();
//                $temp = array();
//                foreach ($managers AS $one) {
//                    $temp[] = $one['id'];
//                }
//                $managers = array_rand($temp,1);
                //$managers = ORM::factory('User')->where('status', '=', 1)->find_all()->as_array();
                $rand_manager_key = array_rand($managers);
                $client->manager_id = $managers[$rand_manager_key]['id'];
                //$client->manager_id = $managers;

                $client->save();

                if ((int) $client->client_type === Model_Client::TYPE_JUR) {
                    if ($this->saveDocument($client->id, 'jur_certificate')) {
                        $message = 'На сайте зарегестрирован новый пользователь. Форма собственности: Юридическое лицо.';
                        Email::factory('Новый пользователь', '')
                            ->to('dima@eparts.kiev.ua')
                            ->from('no-reply@ulc.com.ua')
                            ->message($message, 'text/html')
                            ->send();
                    } else {
                        Controller::redirect('/authorization/registration');
                    }
                } elseif ($client->is_service_station) {
                    if ($this->saveDocument($client->id, 'sto_document')) {
                        Email::factory('Зарегистрировался представитель СТО', '')
                            ->to('dima@eparts.kiev.ua')
                            ->from('no-reply@eparts.kiev.ua')
                            ->message('', 'text/html')
                            ->send();
                    } else {
                        Controller::redirect('/authorization/registration');
                    }
                }

                // Reset values so form is not sticky
                $_POST = array();

                Controller::redirect('/');

            } catch (ORM_Validation_Exception $e) {
                $data = $_POST;

                $message = 'Исправте ошибки!';

                // Set errors using custom messages
                $errors = $e->errors('models');
            }
        }

        $this->template->scripts[] = 'common/registration_form';


        $delivery_methods = array(0 => "---");

        foreach(ORM::factory('DeliveryMethod')->find_all()->as_array() as $deliverymethod) {
            $delivery_methods[$deliverymethod->id] = $deliverymethod->name;
        }
    }

    private function saveDocument($clientId, $fieldName)
    {
        $this->checkAttachment($fieldName);

        $file = Validation::factory($_FILES);
        $newName = uniqid() . '.' . pathinfo($file[$fieldName]['name'])['extension'];
        $filename = Upload::save($file[$fieldName], $newName, 'uploads/');
        if ($filename === false) {
            throw new Exception('Unable to save uploaded file!');
        }
        $document = ORM::factory('ClientDocument');
        $document->set('client_id', $clientId);
        $document->set('url', '/uploads/' . pathinfo($filename)['basename']);
        $document->save();

        return true;
    }

    private function checkAttachment($fieldName)
    {
        $file = Validation::factory($_FILES);
        if (!$file->check() || empty($file[$fieldName])) {
            Controller::redirect('/authorization/registration');
        }
        return true;
    }

    public function action_password_reset()
    {
        $this->template->content = View::factory('clients/password_reset')
            ->bind('errors', $errors)
            ->bind('message', $message)
            ->bind('data', $data);

        $this->template->title = 'Восстановление пароля';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';



        if (HTTP_Request::POST == $this->request->method())
        {
            $user = ORM::factory('Client', array('phone' => $this->request->post('phone'))); // а теперь действительно ищем - есть ли пользователь со введенным адресом
            if ($user->loaded()) // если есть
            {
//                  <div style='margin:10px 0 7px'> С уважением, <br> <strong>компания Куряков Eparts</strong> <br> <a href='mailto:office@eparts.kiev.ua' style='color:#467fb2;text-decoration:none' target='_blank'>office@eparts.kiev.ua</a> </div>
                $session = Session::instance();
                $hash = md5(time().$user->email); // записываем в сессию хэш, который будем проверять
                $session->set('forgotpass', $hash);
                $session->set('forgotmail', $user->email);
                $to = $user->email;
                $subject = 'Eparts::Сброс пароля';
                $messages = "<div style='max-width: 800px; margin: 0 auto; padding: 40px 25px; background: #4f8e63;'><div style='border: 1px solid #429063; background: white; max-width: 750px; margin: 0 auto; padding: 40px;'><div style='overflow: hidden;'><div style='width: 50%; float: left;'><img src='https://eparts.kiev.ua/media/img/dist/logo-w.png'></div><div style='width: 50%; float: left;'><p style='margin-bottom: 15px; color: #429063; font-size: 18px; font-weight: bold;'><span style='color: #429063; font-size: 16px; font-weight: 400;'>(044)</span> 361-96-64<br><span style='color: #429063; font-size: 16px; font-weight: 400;'>(067)</span> 291-18-25</p><p style='margin-bottom: 15px; color: #429063; font-size: 18px; font-weight: bold;'><span style='color: #429063; font-size: 16px; font-weight: 400;'>(095)</span> 053-00-35<br><span style='color: #429063; font-size: 16px; font-weight: 400;'>(063)</span> 631-84-39</p></div></div><div style='font-size: 16px; line-height: 1.4em; color: #343535;'><span><br>Здаствуйте, ".$user->name." ".$user->surname."</span><br><br>Вы сделали запрос на восстановление пароля для учетной записи с номером телефона ".$user->phone." <br><br>Что бы получить новый пароль, нажмите на кнопку ниже:<br><br> <a style='display: inline-block; padding: 6px 12px; text-decoration: none; margin-bottom: 0; font-size: 14px; font-weight: 400; line-height: 1.42857143; text-align: center; -ms-touch-action: manipulation; touch-action: manipulation; cursor: pointer; -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none; border: 1px solid transparent; border-radius: 4px; background: #1B7340; border-color: #1B7340; color: #fff !important; border-radius: 4px; font-size: 20px; 
    text-transform: uppercase;' href='https://eparts.kiev.ua/authorization/password_reset?change=".$hash."'>Сбросить пароль</a><br><br><span style='color:#656565; font-size: 16px; line-height: 1.4em;'> <strong>Продуктивного дня!</strong><br><br> </span> С уважением, <br> <strong>компания Куряков Eparts</strong></div></div></div>";
                Email::factory($subject, '')
                    ->to($to)
                    ->from('no-reply@eparts.kiev.ua')
                    ->message($messages, 'text/html')
                    ->send();
//				  mail($to,$subject,$messages);
                $message = 'Для дальнейшего востановления проверьте Вашу почту "'.$to.'"!';
            }else{
                $errors = 'Такого номера телефона в базе нет!';
            }
        }
        $restore = Arr::get($_GET, 'change');
        if ($restore) // если человек прошел по ссылке в письме
        {
            $session = Session::instance();
            if ($session->get('forgotpass') === $restore) // проверяем его сессию - действительно ли именно он запросил сброс?
            {
                $newpass = substr(md5(date("dmYHi").$session->get('forgotmail')),0,8); // генерируем новый пароль
                $to = $session->get('forgotmail');

                $user = ORM::factory('Client')->where('email', '=', $to)->find();

                if($user->id) {
                    $user->password = $newpass;
                    $user->save();
                }

                $session->delete('forgotpass');
                $session->delete('forgotmail'); // очищаем сессию
                $subject = 'Eparts::Новый пароль';
                $messages = "<div style='max-width: 800px; margin: 0 auto; padding: 40px 25px; background: #4f8e63;'><div style='border: 1px solid #429063; background: white; max-width: 750px; margin: 0 auto; padding: 40px;'><div style='overflow: hidden;'><div style='width: 50%; float: left;'><img src='https://eparts.kiev.ua/media/img/dist/logo-w.png'></div><div style='width: 50%; float: left;'><p style='margin-bottom: 15px; color: #429063; font-size: 18px; font-weight: bold;'><span style='color: #429063; font-size: 16px; font-weight: 400;'>(044)</span> 361-96-64<br><span style='color: #429063; font-size: 16px; font-weight: 400;'>(067)</span> 291-18-25</p><p style='margin-bottom: 15px; color: #429063; font-size: 18px; font-weight: bold;'><span style='color: #429063; font-size: 16px; font-weight: 400;'>(095)</span> 053-00-35<br><span style='color: #429063; font-size: 16px; font-weight: 400;'>(063)</span> 631-84-39</p></div></div><div style='font-size: 16px; line-height: 1.4em; color: #343535;'><span><br>Здаствуйте, ".$user->name." ".$user->surname."</span><br><br>Вы сделали запрос на восстановление пароля для учетной записи с номером телефона ".$user->phone." <br><br>Ваш новый пароль:<br><br><strong>".$newpass."</strong> <br><br><span style='color:#656565; font-size: 16px; line-height: 1.4em;'> <strong>Продуктивного дня!</strong><br><br> </span> С уважением, <br> <strong>компания Куряков Eparts</strong></div></div></div>";//'Ваш новый пароль - '. $newpass; // отправляем новый пароль пользователю
                Email::factory($subject, '')
                    ->to($to)
                    ->from('no-reply@eparts.kiev.ua')
                    ->message($messages, 'text/html')
                    ->send();				  //Controller::redirect('authorization/login');
                $message = 'Спасибо ! Ваш новый пароль отправлен на почту "'.$to.'"!';
            }
        }

    }

    public function action_registration_success()
    {
        $this->template->content = View::factory('clients/registration_success');

        $this->template->title = 'Регистрация';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

    }

    public function action_activate()
    {
        if(!empty($_GET['key'])) $activation_key = $_GET['key'];

        $this->template->content = View::factory('clients/activation')
            ->bind('message', $message)
            ->bind('managers', $managers)
            ->bind('success', $success);

        $this->template->title = 'Активация';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $client = ORM::factory('Client')->where('activation_key', '=', $activation_key)->find();

        if($client->id) {
            $client->active = 1;
            $client->activation_key = null;
            $client->save();
            $success = true;
        } else {
            $success = false;
        }
    }
}
