<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Settings extends Controller_Admin_Application {
	
	public function action_list() { 
		if(!ORM::factory('Permission')->checkPermission('manage_settings')) Controller::redirect('admin');
		
		$this->template->content = View::factory('admin/settings/list')
			->bind('costs_type', $costs_type)
			->bind('settings', $settings);

		$this->template->title = 'Настройки';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		$settings = ORM::factory('Setting')->find_all()->as_array();

		##################costs_types############################
		if (HTTP_Request::POST == $this->request->method()) {
			$subtract = isset($_POST['costs_subtract']) ? 1 : 0;
			$type = $_POST['costs_type'];

			if (isset($_POST['id'])) {
				DB::update('costs_type')->set(array('type' => $type, 'subtract' => $subtract))->where('id', '=', $_POST['id'])->execute();
			} else {
				DB::insert('costs_type', array('type', 'subtract'))->values(array($type, $subtract))->execute();
			}
			//refresh page
			header("Refresh:0");
		}

		$costs_type = DB::select()->from('costs_type')->where('status', '=', 0)->execute()->as_array();
		##########################################################

		$this->template->scripts[] = "common/settings_list";
	}

    public function action_list_modal() {
        if(!ORM::factory('Permission')->checkPermission('manage_settings')) Controller::redirect('admin');

        $this->template->content = View::factory('admin/settings/list-modal')
            ->bind('modals', $modals);

        $this->template->title = 'Настройки модального окна';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $modals = ORM::factory('Modal')->find_all()->as_array();
    }
	
	public function action_edit() {
		if(!ORM::factory('Permission')->checkPermission('manage_settings')) Controller::redirect('admin');
		
		$id = $this->request->param('id');
		if(empty($id)) Controller::redirect('admin/settings/list');
		
		$this->template->content = View::factory('admin/settings/form')
			->bind('errors', $errors)
			->bind('message', $message)
			->bind('types', $types)
			->bind('data', $data);
			
        $this->template->title = 'Настройки';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		$setting = ORM::factory('Setting')->where('id', '=', $id)->find();
		$data = array();
		$data = $setting->as_array();
		
		if (HTTP_Request::POST == $this->request->method()) 
		{			
			try {
				$setting->values($this->request->post(), array(
					'value',
				));
				$setting->save();
				
				// Reset values so form is not sticky
				$_POST = array();
				
				Controller::redirect('admin/settings/list');
			} catch (ORM_Validation_Exception $e) {
				$data = $_POST;
				// Set failure message
				$message = 'Fix errors!!';
				
				// Set errors using custom messages
				$errors = $e->errors('models');
			}
		}
		
		$this->template->scripts[] = 'bootstrap.validate';
		$this->template->scripts[] = 'bootstrap.validate.ru';
		$this->template->scripts[] = 'common/settings_form';
	}


    public function action_edit_modal() {
        if(!ORM::factory('Permission')->checkPermission('manage_settings')) Controller::redirect('admin');

        $id = $this->request->param('id');
        if(empty($id)) Controller::redirect('admin/settings/list_modal');

        $this->template->content = View::factory('admin/settings/form-modal')
            ->bind('errors', $errors)
            ->bind('message', $message)
            ->bind('types', $types)
            ->bind('data', $data);

        $this->template->title = 'Настройки модального окна';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $modal = ORM::factory('Modal')->where('id', '=', $id)->find();
        $data = array();
        $data = $modal->as_array();

        if (HTTP_Request::POST == $this->request->method())
        {
            try {
                $modal->values($this->request->post(), array(
                    'text',
                    'active',
                ));
                $modal->save();

                // Reset values so form is not sticky
                $_POST = array();

                Controller::redirect('admin/settings/list_modal');
            } catch (ORM_Validation_Exception $e) {
                $data = $_POST;
                // Set failure message
                $message = 'Fix errors!!';

                // Set errors using custom messages
                $errors = $e->errors('models');
            }
        }

        $this->template->scripts[] = 'bootstrap.validate';
        $this->template->scripts[] = 'bootstrap.validate.ru';
    }

    public function action_delete() {
        if(!ORM::factory('Permission')->checkPermission('manage_settings')) Controller::redirect('admin');

        $this->template->title = '';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $id = $this->request->param('id');
        if(!empty($id)) {
            //$query = DB::delete('costs_type')->where('id', '=', $id)->execute();
            DB::update('costs_type')->set(array('status' => 1))->where('id', '=', $id)->execute();
        }
        Controller::redirect('admin/settings/list');
    }
	
}
