<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Penalty extends Controller_Admin_Application {

    public function action_list()
    {
        if (!ORM::factory('Permission')->checkPermission('view_penalty')) Controller::redirect('admin');
        $this->template->content = View::factory('admin/user/penalty')
            ->bind('filters', $filters)
            ->bind('managers', $managers)
            ->bind('users', $users)
            ->bind('pagination', $pagination)
            ->bind('total', $total)
            ->bind('data', $data)
            ->bind('status_filter', $status_filter)
            ->bind('status_but', $status_but);

        $this->template->title = 'Штрафы и выплаты';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        if(!ORM::factory('Permission')->checkRole('Владелец') && !ORM::factory('Permission')->checkRole('Програмист') && !ORM::factory('Permission')->checkRole('Директор'))
            $_GET['user_id'] = Auth::instance()->get_user()->id;


        if (HTTP_Request::POST == $this->request->method()){
            try {

                $penalty = ORM::factory('Penalty');

                $penalty->values($this->request->post(), array(
                    'user_id',
                    'amount',
                    'description'
                ));
                $penalty
                    ->set('date', date('Y-m-d H:i:s'))
                    ->set('creator', Auth::instance()->get_user()->id);

                if ((int)($this->request->post('order_id')))
                    $penalty
                        ->set('order_id', $this->request->post('order_id'));

                $penalty->save();


                Controller::redirect('admin/penalty/list');
            } catch (ORM_Validation_Exception $e) {
                $data = $_POST;
                // Set failure message
                $message = 'Исправте ошибки!';

                // Set errors using custom messages
                $errors = $e->errors('models');
            }
        }

        $status_but = 0;
        $status_filter = 1;
        $users = array(""=>"---");
        $managers = array(""=>"---");
        foreach(ORM::factory('User')->where('status','=','1')->find_all()->as_array() as $user) {
            $users[$user->id] = $user->surname;

            if (ORM::factory('Permission')->checkPermissionByUser($user->id, 'penalized'))
                $managers[$user->id] = $user->surname;
        }

        $data = ORM::factory('Penalty')->where('status', '=', 0);

        if (isset($_GET['delete'])) {
            if (!ORM::factory('Permission')->checkPermission('delete_penalty')) Controller::redirect('admin');
            ORM::factory('Penalty')->where('id', '=', $_GET['delete'])->find()->delete();
            $url_previous = $_SERVER['HTTP_REFERER'];
            Controller::redirect($url_previous);
        }

        if (!empty($_GET['date_from'])) {
            $filters['date_from'] = $_GET['date_from'];
            $date_from = new DateTime($filters['date_from']);
            $data = $data->and_where(DB::expr('DATE(date)'), '>=', $date_from->format('Y-m-d'));
        }

        if (!empty($_GET['date_to'])) {
            $filters['date_to'] = $_GET['date_to'];
            $date_to = new DateTime($filters['date_to']);
            $data = $data->and_where(DB::expr('DATE(date)'), '<=', $date_to->format('Y-m-d'));
        }

        if (!empty($_GET['user_id'])) {
            $filters['user_id'] = $_GET['user_id'];
            $data = $data->and_where('user_id', '=', $filters['user_id']);
            $status_but = 1;
        }

        $data = $data->order_by('id', 'desc')->find_all();


        $pagination = Pagination::factory(array('total_items' => $data->count()))->route_params(array(
            'controller' =>  'penalty',
            'action' =>  'list'
        ));


        $data = $data->as_array();


        $total = 0;
        foreach ($data AS $key => $row) {
            if ($row->status==0) {
                $total += $row->amount;
            }
        }
        if (!empty($_GET['status'])) {
            foreach ($data as $user_penalty=> $row_penalty)
            {
                $row_penalty->status = 1;
                $row_penalty->save();

            }

            $url_previous = $_SERVER['HTTP_REFERER'];
            Controller::redirect($url_previous);
        }

        $data = array_slice($data, $pagination->offset, $pagination->items_per_page);

        $this->template->scripts[] = 'bootstrap.validate';
        $this->template->scripts[] = 'bootstrap.validate.ru';
        $this->template->scripts[] = 'bootstrap-formhelpers-phone.format';
        $this->template->scripts[] = 'bootstrap-formhelpers-phone';
        $this->template->scripts[] = 'jquery-ui-1.10.4.custom.min';
        $this->template->scripts[] = 'jquery.jeditable';
        $this->template->scripts[] = 'common/orders_list_items';

    }
    public function action_archivelist()
    {
        if (!ORM::factory('Permission')->checkPermission('view_penalty')) Controller::redirect('admin');
        $this->template->content = View::factory('admin/user/penalty')
            ->bind('filters', $filters)
            ->bind('users', $users)
            ->bind('pagination', $pagination)
            ->bind('total', $total)
            ->bind('data', $data)
            ->bind('status_filter', $status_filter)
            ->bind('status_but', $status_but);

        $this->template->title = 'Архив штрафов и выплат';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';


        $status_but = 0;
        $status_filter = 0;
        $users = array(""=>"---");

        foreach(ORM::factory('User')->where('status','=','1')->find_all()->as_array() as $user) {
            $users[$user->id] = $user->surname;


        }

        $data = ORM::factory('Penalty')->where('status', '=', 1);

        if (isset($_GET['delete'])) {
            if (!ORM::factory('Permission')->checkPermission('delete_penalty')) Controller::redirect('admin');
            ORM::factory('Penalty')->where('id', '=', $_GET['delete'])->find()->delete();
            Controller::redirect('admin/penalty/list');
        }

        if (!empty($_GET['date_from'])) {
            $filters['date_from'] = $_GET['date_from'];
            $date_from = new DateTime($filters['date_from']);
            $data = $data->and_where(DB::expr('DATE(date)'), '>=', $date_from->format('Y-m-d'));
        }

        if (!empty($_GET['date_to'])) {
            $filters['date_to'] = $_GET['date_to'];
            $date_to = new DateTime($filters['date_to']);
            $data = $data->and_where(DB::expr('DATE(date)'), '<=', $date_to->format('Y-m-d'));
        }

        if (!empty($_GET['user_id'])) {
            $filters['user_id'] = $_GET['user_id'];
            $data = $data->and_where('user_id', '=', $filters['user_id']);
        }

        $data = $data->order_by('id', 'desc')->find_all();


        $pagination = Pagination::factory(array('total_items' => $data->count()))->route_params(array(
            'controller' =>  'penalty',
            'action' =>  'archivelist'
        ));


        $data = $data->as_array();


        $total = 0;
        foreach ($data AS $key => $row) {
            if ($row->status==1)
            {
                $total += $row->amount;
            }
        }

        $data = array_slice($data, $pagination->offset, $pagination->items_per_page);

        $this->template->scripts[] = 'bootstrap.validate';
        $this->template->scripts[] = 'bootstrap.validate.ru';
        $this->template->scripts[] = 'bootstrap-formhelpers-phone.format';
        $this->template->scripts[] = 'bootstrap-formhelpers-phone';
        $this->template->scripts[] = 'jquery-ui-1.10.4.custom.min';
        $this->template->scripts[] = 'jquery.jeditable';
        $this->template->scripts[] = 'common/orders_list_items';

    }

    public function action_delete()
    {
        if (!ORM::factory('Permission')->checkPermission('delete_penalty')) Controller::redirect('admin');
//        $this->template->content = View::factory('admin/user/penalty')
//            ->bind('errors', $errors)
//            ->bind('message', $message)
//            ->bind('comments', $comments)
//            ->bind('pagination', $pagination)
//            ->bind('data', $data);

        $this->template->title = 'Удаление штрафов';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        if (HTTP_Request::POST == $this->request->method())
        {
            if (isset($_POST['delete_all']))
            {
                try {
                    $delete_arr = $_POST['delete'];
                    $url = $_POST['url'];

                    //var_dump($delete_arr);
                    foreach ($delete_arr as $var=>$key){
                        $comment = ORM::factory('Penalty')->where('id', '=', $key)->find();
                        $comment->delete();
                    }
                    Controller::redirect($url);

                    //$message = "Ваш комментарий принят к рассмотрению и будет опубликован после проверки модератором.";

                } catch (ORM_Validation_Exception $e) {
                    $data = $_POST;
                    // Set failure message
                    $message = 'Исправьте ошибки!';

                    // Set errors using custom messages
                    $errors = $e->errors('models');
                }
            }
            else if(isset($_POST['archive_all']))
            {
                try {
                    $delete_arr = $_POST['delete'];
                    $url = $_POST['url'];

                    //var_dump($delete_arr);
                    foreach ($delete_arr as $var=>$key){
                        $comment = ORM::factory('Penalty')->where('id', '=', $key)->find();
                        $comment->status = 1;
                        $comment->save();
                    }
                    Controller::redirect($url);

                    //$message = "Ваш комментарий принят к рассмотрению и будет опубликован после проверки модератором.";

                } catch (ORM_Validation_Exception $e) {
                    $data = $_POST;
                    // Set failure message
                    $message = 'Исправьте ошибки!';

                    // Set errors using custom messages
                    $errors = $e->errors('models');
                }
            }

        }
    }

} // End Admin_User



class Validation_Exception extends Exception {};
