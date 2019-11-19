<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_BirthdaySetting extends Controller_Admin_Application
{
    public function action_list() {
        if(!ORM::factory('Permission')->checkPermission('manage_settings')) Controller::redirect('admin');

        $this->template->content = View::factory('admin/birthday_settings/list')
            ->bind('settings', $settings);

        $this->template->title = 'Настройки';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
        $settings = ORM::factory('BirthdaySettings')->find_all()->as_array();

    }

    public function action_add()
    {
        if(!ORM::factory('Permission')->checkPermission('manage_settings')) Controller::redirect('admin');

        $this->template->content = View::factory('admin/birthday_settings/form')
            ->bind('errors', $errors)
            ->bind('message', $message)
            ->bind('data', $data);

        $this->template->title = 'Редактирование правила';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';


        if (HTTP_Request::POST == $this->request->method()) {
            try {
                $birthday_settings = ORM::factory('BirthdaySettings');
                $birthday_settings ->values($this->request->post(), array(
                    'name',
                    'desc',
                    'value',
                ));

                $birthday_settings ->save();
                $_POST = array();

                Controller::redirect('admin/birthdaySetting/list/');
            } catch (ORM_Validation_Exception $e) {
                $data = $_POST;
                $message = 'Исправте ошибки!';
                $errors = $e->errors('models');
            }
        }

    }

    public function action_edit() {
        if(!ORM::factory('Permission')->checkPermission('manage_operations')) Controller::redirect('admin');

        $id = $this->request->param('id');
        if(empty($id)) Controller::redirect('admin/birthdaySetting/list');

        $this->template->content = View::factory('admin/birthday_settings/form')
            ->bind('errors', $errors)
            ->bind('message', $message)
            ->bind('data', $data);

        $this->template->title = 'Редактирование правила';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $birthday_settings = ORM::factory('BirthdaySettings')->where('id', '=', $id)->find();
        $data = array();
        $data['name'] = $birthday_settings->name;
        $data['desc'] = $birthday_settings ->desc;
        $data['value'] = $birthday_settings ->value;

        if (HTTP_Request::POST == $this->request->method())
        {
            try {
                $birthday_settings ->values($this->request->post(), array(
                    'name',
                    'desc',
                    'value',
                ));
                $birthday_settings ->save();

                // Reset values so form is not sticky
                $_POST = array();

                Controller::redirect('admin/birthdaySetting/list/');
            } catch (ORM_Validation_Exception $e) {
                $data = $_POST;
                // Set failure message
                $message = 'Исправте ошибки!';

                // Set errors using custom messages
                $errors = $e->errors('models');
            }
        }
    }

    public function action_delete() {
        if(!ORM::factory('Permission')->checkPermission('manage_operations')) Controller::redirect('admin');

        $this->template->title = '';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $id = $this->request->param('id');
        if(!empty($id)) {
            $birthday_settings = ORM::factory('BirthdaySettings')->where('id', '=', $id)->find();

            $birthday_settings->delete();
        }

        Controller::redirect('admin/birthdaySetting/list/');
    }

    public function action_send_mail_congratulation()
    {
        $this->auto_render = FALSE;
        $sql = "SELECT *
                FROM clients
                WHERE  DATE_FORMAT(birth_data,'%m-%d') = DATE_FORMAT(NOW(),'%m-%d')";
        $current_birthday_users = DB::query(Database::SELECT,$sql )->execute()->as_array();
        $clients_congr = ORM::factory('BirthdaySettings')->where('name','=','clients')->find()->as_array()['value'] ;
        foreach($current_birthday_users as $user ){
            if(!empty($user['email'])){
                $to =$user['email'] ;
                $subject = 'Eparts: З днем рождения';
                $messages = "Дорогой ". $user['name'].'!!! '. $clients_congr ;

                Email::factory($subject, '')
                    ->to($to)
                    ->from('no-reply@eparts.kiev.ua')
                    ->message($messages, 'text/html')
                    ->send();

            }
        }

    }
}