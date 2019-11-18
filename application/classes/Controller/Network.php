<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Network extends Controller_Application
{
	public function action_index()
    {
        $this->auto_render = FALSE;
        $this->is_ajax = TRUE;
        header('content-type: application/json');

        $social_network = $this->request->post()['social_network'];
        $name = $this->request->post()['name'];
        $first_name = $this->request->post()['first_name'];
        $last_name = $this->request->post()['last_name'];
        $social_id = $this->request->post()['id'];
        $email = $this->request->post()['email'];


        $client_data  = ORM::factory('SocialNetworks')
                            ->where('social_network_id', '=',(string) $social_id )
                            ->find()->as_array();

        if (!$client_data['social_network_id']) {
            $json = array('exist_user'=> false);
        }else {
            if (ORM::factory('Client')->social_network_login($client_data['client_id'])) {
                $json = array('exist_user' => true);
            }
        }
        echo json_encode($json);
    }

    public function  action_registration()   
    {
		
		$this->auto_render = FALSE;


        if (HTTP_Request::POST == $this->request->method())
        {
        	
            try {

                
                $client = ORM::factory('Client')->create_user($this->request->post(), array(
                    'name',
                    'surname',
                    'phone',
                    'password',
                    'email',
                    'additional_phone',
                    'delivery_address',
                    'delivery_method_id',
                ));
				


                $client->discount_id = ORM::factory('Discount')->getStandartId();
                $client->active = 0;
                $client->activation_key = md5($client->id . date("dmYHis"));

                
                $managers = ORM::factory('User')->find_all()->as_array();
                $rand_manager_key = array_rand($managers);
                $client->manager_id = $managers[$rand_manager_key]->id;

                if($client->save()){
                    $social_client = ORM::factory('SocialNetworks');
                    $social_client->social_network = $this->request->post()['social_network'];
                    $social_client->social_network_id = $this->request->post()['social_network_id'];
                    $social_client->client_id = $client->id;
                    $social_client->save();
                }

                ORM::factory('Client')->social_network_login($client->id) ;

                // Reset values so form is not sticky
                $_POST = array();

                Controller::redirect('/');

            } catch (ORM_Validation_Exception $e) {

                $data = $_POST;
                // Set failure message
                $message = 'Исправте ошибки!';

                // Set errors using custom messages
                $errors = $e->errors('models');
            }
        }        
        Controller::redirect('/');
    }

}