<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Welcome extends Controller_Application {
	
	public function action_index()
	{
        $this->template->title = 'Главная';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
       // $content = View::factory('welcome');
		
        $this->template->content = $content;
	}

	public function action_test()
	{
		$this->response->body('Ololo test!');
	}

} // End Welcome
