<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Costs extends Controller_Admin_Application
{
    public function action_test()
    {
        $this->money_spacing(); exit();
    }
    public function action_list()
    {
        if (!ORM::factory('Permission')->checkPermission('costs_manage')) Controller::redirect('admin');

        $this->template->content = View::factory('admin/costs/list')
            ->bind('costs_type', $costs_type)
            ->bind('filters', $filters)
            ->bind('users', $users)
            ->bind('suppliers', $suppliers)
            ->bind('costs', $costs);
        $this->template->title = 'Затраты';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        //активные типы затрат
        $costs_type = DB::select()->from('costs_type')->where('status', '=', 0)->execute()->as_array();

//        $client = ORM::factory('Costs')->where('supplier_id', '=' , '0')->find_all();
//        foreach ($client as $item)
//        {
//            echo $item->user->name;
//        }
//        exit();

        //suppliers
        $suppliers = array('' => '---');
        foreach(ORM::factory('Supplier')->find_all()->as_array() as $supplier) {
            $suppliers[$supplier->id] = $supplier->name;
        }
        //users
        $users = array('' => '---');
        foreach(ORM::factory('User')->where('status', '=' , '1')->find_all()->as_array() as $user) {
            $managers[$user->id] = $user->surname;

            //users, who can add client payment
            if (ORM::factory('Permission')->checkPermissionByUser($user->id, 'costs_manage')) $users[$user->id] = $user->surname;
        }

        if (HTTP_Request::POST == $this->request->method()) {
            $comment = isset($_POST['comment']) ? $_POST['comment'] : '';
            $supplier = isset($_POST['supplier']) ? $_POST['supplier'] : 0;
//            $date = new DateTime($_POST['date']);
            DB::insert('costs', array('type', 'user_id', 'date', 'amount', 'comment', 'created', 'supplier_id'))
                ->values(array($_POST['type'], Auth::instance()->get_user()->id, date('Y-m-d'), $_POST['amount'], $comment, date('Y-m-d H:i:s'), $supplier))->execute();

            header("Refresh:0");
        }

        $costs = DB::select()->from('costs')->where('status_user', '=', '1');


        if (!empty($_GET['date_from'])) {
            $filters['date_from'] = $_GET['date_from'];
            $date_from = new DateTime($filters['date_from']);
            $costs = $costs->and_where('date', '>=', $date_from->format('Y-m-d'));
        }

        if (!empty($_GET['date_to'])) {
            $filters['date_to'] = $_GET['date_to'];
            $date_to = new DateTime($filters['date_to']);
            $costs = $costs->and_where('date', '<=', $date_to->format('Y-m-d'));
        }

        if (!empty($_GET['user_id'])) {
            $filters['user_id'] = $_GET['user_id'];
            $costs = $costs->and_where('user_id', '=', $filters['user_id']);
        }

        $costs = $costs->order_by('id', 'DESC')->execute()->as_array(); //->limit(80)


        if (!empty($_GET['all_costs'])) {
            foreach ($costs as $one => $row_cost)
            {
                $row_cost->arhive = 1;
                $row_cost->save();
            }
            //var_dump($orderitems);

            $url_previous = $_SERVER['HTTP_REFERER'];
            Controller::redirect($url_previous);
        }

        $this->template->scripts[] = "common/costs_list";
        $this->template->scripts[] = 'jquery-ui-1.10.4.custom.min';
    }

    public function action_personal_costs()
    {
        if (!ORM::factory('Permission')->checkRole('Владелец') && !ORM::factory('Permission')->checkRole('Директор') && !ORM::factory('Permission')->checkRole('Програмист')) Controller::redirect('admin');

        $this->template->content = View::factory('admin/costs/list-personal')
            ->bind('costs_personal', $costs_personal)
            ->bind('costs_static', $costs_static)
            ->bind('filters', $filters)
            ->bind('totalCost', $totalCost)
            ->bind('pagination', $pagination)
            ->bind('types', $types)
            ->bind('data', $data);

        $this->template->title = 'Затраты личные';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $types = [
            '' => '---',
            0 => 'Разносятся',
            1 => 'Мои затраты'
        ];

        if (HTTP_Request::POST == $this->request->method())
        {
            try
            {
                $dataPost = $this->request->post();
                $newPersonalCost = ORM::factory('CostsPersonal');
                $newPersonalCost->values($dataPost, array(
                    'amount',
                    'comment'
                ));

                $newPersonalCost->user_id = Auth::instance()->get_user()->id;
                $newPersonalCost->created = empty($dataPost['created']) ? date('Y-m-d H:m:s') : date('Y-m-d H:m:s', strtotime($dataPost['created']));
                $newPersonalCost->type = 1;
                $newPersonalCost->save();
                $_POST = array();
                Controller::redirect('admin/costs/personal_costs');
            }
            catch (ORM_Validation_Exception $e)
            {
                $data = $_POST;
                $message = 'Исправте ошибки!';
                $errors = $e->errors('models');
            }
        }

        $costs_personal = ORM::factory('CostsPersonal');
        $costs_personal->reset(FALSE);
        $costs_static = ORM::factory('CostsPersonalStatic')->order_by('id', 'DESC')->find_all();

        if (empty($_GET['date_to']))
        {
            $_GET['date_to'] = date('d.m.Y');
            $filters['date_to'] = $_GET['date_to'];
        }

        if (!empty($_GET['date_from'])) {
            $filters['date_from'] = $_GET['date_from'];
            $costs_personal = $costs_personal->and_where('created', '>=', date('Y-m-d 00:00:00', strtotime($filters['date_from'])));
        }

        if (!empty($_GET['date_to'])) {
            $filters['date_to'] = $_GET['date_to'];
            $costs_personal = $costs_personal->and_where('created', '<=', date('Y-m-d 23:59:59', strtotime($filters['date_to'])));
        }

        if(!empty($_GET['type'])) {
            $filters['type'] = $_GET['type'];
            $costs_personal = $costs_personal->and_where('type', '=', $filters['type']);
        }


        $count = $costs_personal->count_all();

        $pagination = Pagination::factory(array('total_items' => $count))->route_params(array(
            'controller' =>  'costs',
            'action' =>  'personal_costs'
        ));

        $costs_personal = $costs_personal->order_by('created', 'DESC')->find_all()->as_array();

        if (!empty($_GET['all_costs'])) {
            foreach ($costs_personal as $one => $row_cost)
            {
                $row_cost->arhive = 1;
                $row_cost->save();
            }
            //var_dump($orderitems);

            $url_previous = $_SERVER['HTTP_REFERER'];
            Controller::redirect($url_previous);
        }

        $totalCost = 0;
        foreach ($costs_personal as $cost_personal)
        {
            $totalCost += $cost_personal->amount;
        }

        $costs_personal = array_slice($costs_personal, $pagination->offset, $pagination->items_per_page);

        $this->template->scripts[] = "common/costs_list";
        $this->template->scripts[] = 'jquery-ui-1.10.4.custom.min';
    }

    public function action_personal_costs_static()
    {
        if (HTTP_Request::POST == $this->request->method())
        {
            try
            {
                $newPersonalCost = ORM::factory('CostsPersonalStatic');
                $newPersonalCost->values($this->request->post(), array(
                    'amount',
                    'comment'
                ));
                $newPersonalCost->user_id = Auth::instance()->get_user()->id;
                $newPersonalCost->save();
                $this->money_spacing($newPersonalCost->id);
                $_POST = array();
                Controller::redirect('admin/costs/personal_costs');
            }
            catch (ORM_Validation_Exception $e)
            {
                $data = $_POST;
                $message = 'Исправте ошибки!';
                $errors = $e->errors('models');
            }
        }

        if(!empty($_SERVER['HTTP_REFERER'])) {
            return Controller::redirect($_SERVER['HTTP_REFERER']);
        }
        return Controller::redirect('/');
    }

    public function action_personal_costs_static_delete()
    {
        $id = $this->request->param('id');
        if(!empty($id))
        {
            $deletePersonalCost = ORM::factory('CostsPersonalStatic')->where('id', '=', $id)->find();
            $deletePersonalCost->delete();
        }

        if(!empty($_SERVER['HTTP_REFERER']))
            return Controller::redirect($_SERVER['HTTP_REFERER']);

        return Controller::redirect('/');
    }

    public function action_costs_delete()
    {
        $id = $this->request->param('id');
        if(!empty($id))
        {
            $deletePersonalCost = ORM::factory('Costs')->where('id', '=', $id)->find();
            $deletePersonalCost->delete();
        }

        if(!empty($_SERVER['HTTP_REFERER']))
            return Controller::redirect($_SERVER['HTTP_REFERER']);

        return Controller::redirect('/');
    }

    public function action_edit_static()
    {
        if (HTTP_Request::POST == $this->request->method())
        {
            $dataPost = $this->request->post();
            $staticCost = ORM::factory('CostsPersonalStatic')->where('id', '=', $dataPost['id'])->find();
            $staticCost->amount = $dataPost['amount'];
            $staticCost->comment = $dataPost['comment'];
            $staticCost->save();
        }

        if(!empty($_SERVER['HTTP_REFERER']))
            return Controller::redirect($_SERVER['HTTP_REFERER']);

        return Controller::redirect('/');
    }

    public function action_edit_personal()
    {
        if (HTTP_Request::POST == $this->request->method())
        {
            $dataPost = $this->request->post();
            $staticCost = ORM::factory('CostsPersonal')->where('id', '=', $dataPost['id'])->find();
            $staticCost->amount = $dataPost['amount'];
            $staticCost->comment = $dataPost['comment'];
            $staticCost->created = date('Y-m-d H:m:s', strtotime($dataPost['created']));
            $staticCost->save();
        }

        if(!empty($_SERVER['HTTP_REFERER']))
            return Controller::redirect($_SERVER['HTTP_REFERER']);

        return Controller::redirect('/');
    }

    public function action_personal_costs_delete()
    {
        $id = $this->request->param('id');
        if(!empty($id))
        {
            $deletePersonalCost = ORM::factory('CostsPersonal')->where('id', '=', $id)->find();
            $deletePersonalCost->delete();
        }

        if(!empty($_SERVER['HTTP_REFERER']))
            return Controller::redirect($_SERVER['HTTP_REFERER']);

        return Controller::redirect('/');
    }


    public function action_list_archive()
    {
        if (!ORM::factory('Permission')->checkPermission('costs_manage')) Controller::redirect('admin');

        $this->template->content = View::factory('admin/costs/list')
            ->bind('costs_type', $costs_type)
            ->bind('filters', $filters)
            ->bind('users', $users)
            ->bind('costs', $costs);
        $this->template->title = 'Затраты';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        //активные типы затрат
        $costs_type = DB::select()->from('costs_type')->where('status', '=', 0)->execute()->as_array();


        //users
        $users = array('' => '---');
        foreach(ORM::factory('User')->where('status', '=' , '0')->find_all()->as_array() as $user) {
            $managers[$user->id] = $user->surname;

            //users, who can add client payment
            if (ORM::factory('Permission')->checkPermissionByUser($user->id, 'costs_manage')) $users[$user->id] = $user->surname;
        }

        if (HTTP_Request::POST == $this->request->method()) {
            $comment = isset($_POST['comment']) ? $_POST['comment'] : '';
//            $date = new DateTime($_POST['date']);
            DB::insert('costs', array('type', 'user_id', 'date', 'amount', 'comment', 'created'))
                ->values(array($_POST['type'], Auth::instance()->get_user()->id, date('Y-m-d'), $_POST['amount'], $comment, date('Y-m-d H:i:s')))->execute();

            header("Refresh:0");
        }

        $costs = DB::select()->from('costs')->where('status_user', '=', '0');

        if (!empty($_GET['date_from'])) {
            $filters['date_from'] = $_GET['date_from'];
            $date_from = new DateTime($filters['date_from']);
            $costs = $costs->and_where('date', '>=', $date_from->format('Y-m-d'));
        }

        if (!empty($_GET['date_to'])) {
            $filters['date_to'] = $_GET['date_to'];
            $date_to = new DateTime($filters['date_to']);
            $costs = $costs->and_where('date', '<=', $date_to->format('Y-m-d'));
        }

        if (!empty($_GET['user_id'])) {
            $filters['user_id'] = $_GET['user_id'];
            $costs = $costs->and_where('user_id', '=', $filters['user_id']);
        }

        $costs = $costs->order_by('id', 'DESC')->execute()->as_array(); //->limit(80)

        $this->template->scripts[] = "common/costs_list";
        $this->template->scripts[] = 'jquery-ui-1.10.4.custom.min';
    }


    public function money_spacing($id = false)
    {
        if($id)
            $allStatics = ORM::factory('CostsPersonalStatic')->where('id', '=', $id)->find_all();
        else
            $allStatics = ORM::factory('CostsPersonalStatic')->find_all();


        $todayDay = date('d');
        if($todayDay < 8)
        {
            $start_day = date('Y-m-8 00:00:00');
            $start_day = date('Y-m-d 00:00:00', strtotime($start_day."-1 month"));
            $finish_day = date('Y-m-d 00:00:00', strtotime($start_day."+1 month"));
        }
        else
        {
            $start_day = date('Y-m-8 00:00:00');
            $finish_day = date('Y-m-d 00:00:00', strtotime($start_day."+1 month"));
        }

        $datetime1 = date_create($start_day);
        $datetime2 = date_create($finish_day);
        $interval = date_diff($datetime1, $datetime2);
        $daysInMonth = $interval->days;

        foreach ($allStatics as $allStatic)
        {
            $ids = [];
            $workDays = 0;

            for($i = 0; $i < $daysInMonth; $i++)
            {
                $currentDay = date('Y-m-d 00:00:00', strtotime($start_day."+".$i." day"));
                $dayNumber = date( 'w', strtotime($start_day."+".$i." day"));

                if($dayNumber != 6 && $dayNumber != 0)
                {
                    $workDays++;
                    $dinamicCost = ORM::factory('CostsPersonal');
                    $dinamicCost->user_id = Auth::instance()->get_user()->id;
                    $dinamicCost->created = $currentDay;
                    $dinamicCost->type = 0;
                    $dinamicCost->comment = $allStatic->comment;
                    $dinamicCost->save();
                    $ids[] = $dinamicCost->id;
                }
            }
            $dayPay = $allStatic->amount / $workDays;
            DB::update('costs_personal')->set(array('amount' => $dayPay))->where('id', 'IN', $ids)->execute();
        }
    }

    public function action_costs_personal_arhive()
    {

        $this->auto_render = FALSE;
        $this->is_ajax = TRUE;

        $json = array();

        if ($this->request->method() == Request::POST)
        {
            if(!empty($_POST['choices'])) {
                foreach($_POST['choices'] as $val) {
                    ORM::factory('CostsPersonal')->where('id', '=', $val)->find()->set('arhive', 1)->save();
                }

                $json['status'] = "ok";
            }
        }

        echo json_encode($json);
    }

    public function action_costs_arhive()
    {

        $this->auto_render = FALSE;
        $this->is_ajax = TRUE;

        $json = array();

        if ($this->request->method() == Request::POST)
        {
            if(!empty($_POST['choices'])) {
                foreach($_POST['choices'] as $val) {
                    ORM::factory('Costs')->where('id', '=', $val)->find()->set('arhive', 1)->save();
                }

                $json['status'] = "ok";
            }
        }

        echo json_encode($json);
    }
}


class Validation_Exception extends Exception
{
}

;
