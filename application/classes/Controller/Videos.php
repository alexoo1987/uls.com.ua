<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Videos extends Controller_Application {

    public function action_index() {
        $this->template->content = View::factory('video/index')->bind('videos', $videos);

        $videos = ORM::factory('Videos')->where('site', '=', 1)->find_all()->as_array();

        $this->template->title = 'Видео - интернет магазин автозапчастей Eparts';
        $this->template->description = 'Видео компании Куряков Eparts';
        $this->template->keywords = '';
        $this->template->author = '';

//        print_r(Cookie::get('warning_np '));
//
//        if(Cookie::get('warning_np '))
//            echo 1;
//
//        exit();

    }

} // End Admin_User



class Validation_Exception extends Exception {};
