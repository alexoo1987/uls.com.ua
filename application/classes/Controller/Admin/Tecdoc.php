<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Tecdoc extends Controller_Admin_Application {
	
	public function action_manufacturers_list() {
		if(!ORM::factory('Permission')->checkPermission('tecdoc')) Controller::redirect('admin');
		
		$this->template->content = View::factory('admin/tecdoc/manufacturers_list')
			->bind('manufacturers', $manufacturers);
		$this->template->title = 'Tecdoc марки авто';
		$this->template->description = '';
		$this->template->keywords = '';
		$this->template->author = '';
		$tecdoc = Model::factory('Tecdoc');
		
		$manufacturers = $tecdoc->get_manufacturers();
	}
	
	public function action_manufacturers_edit() {
		if(!ORM::factory('Permission')->checkPermission('tecdoc')) Controller::redirect('admin');
		
		$id = $this->request->param('id');
		if(empty($id)) Controller::redirect('admin/tecdoc/manufacturers_list');
		
		$this->template->content = View::factory('admin/tecdoc/manufacturers_form')
			->bind('permissions', $permissions)
			->bind('errors', $errors)
			->bind('message', $message)
			->bind('data', $data);
			
        $this->template->title = 'Редактирование марки авто';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		$tecdoc = Model::factory('Tecdoc');
		
		$data = $tecdoc->get_manufacturers(false, $id);
		$data = $data[0];
		
		if (HTTP_Request::POST == $this->request->method()) 
		{			
			try {
				$upd_data = array();
				$directory = DOCROOT.'media/tecdoc_manufacturers/';
				if(Arr::get($this->request->post(), 'delete_logo', '') == 1) {
					if(!empty($data['logo']) && file_exists($directory.$data['logo'])) {
						unlink($directory.$data['logo']);
						$upd_data['logo'] = null;
					}
				}
				if (isset($_FILES['filename']))
				{
					$filename = $this->_save_image($_FILES['filename']);
					if($filename) {
						if(!empty($data['logo']) && file_exists($directory.$data['logo'])) {
							unlink($directory.$data['logo']);
						}
						$upd_data['logo'] = $filename;
					}
				}
				$upd_data['brand'] = Arr::get($this->request->post(), 'brand', '');
				$upd_data['code'] = Arr::get($this->request->post(), 'code', '');
				$upd_data['description'] = Arr::get($this->request->post(), 'description', '');
				$upd_data['title'] = Arr::get($this->request->post(), 'title', '');
				$upd_data['meta_keywords'] = Arr::get($this->request->post(), 'meta_keywords', '');
				$upd_data['meta_description'] = Arr::get($this->request->post(), 'meta_description', '');
				$upd_data['active'] = (Arr::get($this->request->post(), 'active', '') == 1 ? 1 : 0);
				
				$tecdoc->update_manufacturers($upd_data, $id);
				
				// Reset values so form is not sticky
				$_POST = array();
				
				Controller::redirect('admin/tecdoc/manufacturers_list');
			} catch (ORM_Validation_Exception $e) {
				$data = $_POST;
				// Set failure message
				$message = 'Исправте ошибки!';
				
				// Set errors using custom messages
				$errors = $e->errors('models');
			}
		}
		
		$this->template->scripts[] = 'bootstrap.validate';
		$this->template->scripts[] = 'bootstrap.validate.ru';
		$this->template->scripts[] = 'common/tecdoc_manufacturers_form';
	}
	
	protected function _save_image($image)
	{
		if (
			! Upload::valid($image) OR
			! Upload::not_empty($image) OR
			! Upload::type($image, array('jpg', 'jpeg', 'png', 'gif')))
		{
			return FALSE;
		}	

		$directory = DOCROOT.'media/tecdoc_manufacturers/';

		if ($file = Upload::save($image, NULL, $directory))
		{
			$filename = strtolower(Text::random('alnum', 20)).'.png';

			$img = Image::factory($file);
			
			$w = 110;
			$h = 110;
			if($img->width >= $w || $img->height >= $h)
				$img->resize($w, $h, Image::NONE);
			$img->save($directory.$filename);

			// Delete the temporary file
			unlink($file);

			return $filename;
		}

		return FALSE;
	}

} // End Admin_User



class Validation_Exception extends Exception {};
