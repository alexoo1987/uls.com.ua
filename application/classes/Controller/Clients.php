<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Clients extends Controller_Application {

    public function action_index()
    {
        $id = $this->request->param('id');
        if(empty($id)) Controller::redirect('/');

        $this->template->content = View::factory('clients/change_info')
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
        $data['name'] = $client->name;
        $data['surname'] = $client->surname;
        $data['middlename'] = $client->middlename;
        $data['phone'] = $client->phone;
        $data['email'] = $client->email;
        $data['delivery_address'] = $client->delivery_address;
        $data['delivery_method_id'] = $client->delivery_method_id;


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
                    'delivery_address',
                    'delivery_method_id',
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

                Controller::redirect('/orders?archive=all');
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

    }
}
