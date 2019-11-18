<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Videos extends Controller_Admin_Application {

    public function action_index() {
        if(!ORM::factory('Permission')->checkPermission('comments')) Controller::redirect('admin');

        $this->template->content = View::factory('admin/videos/index')
            ->bind('videos', $videos);

        $this->template->title = 'Видео';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $videos = ORM::factory('Videos')->find_all()->as_array();

        $this->template->scripts[] = 'jquery-ui-1.10.4.custom.min';
    }

    public function action_list() {

        $this->template->content = View::factory('admin/videos/list')
            ->bind('videos', $videos);

        $this->template->title = 'Видео';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $videos = ORM::factory('Videos')->where('admin', '=', 1)->find_all()->as_array();

        $this->template->scripts[] = 'jquery-ui-1.10.4.custom.min';
    }

    public function action_edit() {

        $id = $this->request->param('id');
        if(empty($id)) Controller::redirect('admin/videos');

        $this->template->content = View::factory('admin/videos/form')
            ->bind('errors', $errors)
            ->bind('message', $message)
            ->bind('statuses', $statuses)
            ->bind('data', $data);

        $this->template->title = 'Редактирование Видео';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $videos = ORM::factory('Videos')->where('id', '=', $id)->find();
        $data = $videos->as_array();

        if (HTTP_Request::POST == $this->request->method())
        {
            try {

                $videos->url = $_POST['url'];
                $videos->site = empty($_POST['site']) ? 0 : 1;
                $videos->admin = empty($_POST['admin']) ? 0 : 1;
                $videos->save();


                // Reset values so form is not sticky
                $_POST = array();

                Controller::redirect('admin/videos');
            } catch (ORM_Validation_Exception $e) {
                $data = $_POST;
                // Set failure message
                $message = 'Исправте ошибки!';

                // Set errors using custom messages
                $errors = $e->errors('models');
            }
        }
    }

    public function action_create() {
        $this->template->content = View::factory('admin/videos/form')
            ->bind('errors', $errors)
            ->bind('message', $message)
            ->bind('videos', $videos)
            ->bind('data', $data);

        $this->template->title = 'Видео';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        if (HTTP_Request::POST == $this->request->method())
        {
            try {
                $videos = ORM::factory('Videos')->values($this->request->post(), array(
                    'url',
                ));
                $videos->site = empty($_POST['site']) ? 0 : 1;
                $videos->admin = empty($_POST['admin']) ? 0 : 1;
                $videos->save();

                // Reset values so form is not sticky
                $_POST = array();

                $message = "Видео добавлено";

            } catch (ORM_Validation_Exception $e) {
                $data = $_POST;
                // Set failure message
                $message = 'Исправьте ошибки!';

                // Set errors using custom messages
                $errors = $e->errors('models');
            }
        }
    }


} // End Admin_User



class Validation_Exception extends Exception {};
