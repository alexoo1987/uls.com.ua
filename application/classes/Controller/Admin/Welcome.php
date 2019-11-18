<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Welcome extends Controller_Admin_Application {
	
	public function action_index()
	{
		if(!Auth::instance()->logged_in('login')) Controller::redirect('admin');
        $this->template->title = 'Добро пожаловать в админ раздел';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
        $content = View::factory('admin/welcome');
		
        $this->template->content = $content;
	}
} // End Welcome
