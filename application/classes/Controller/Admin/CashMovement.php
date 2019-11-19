<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_CashMovement extends Controller_Admin_Application
{

    public function action_list()
    {
        if (!ORM::factory('Permission')->checkPermission('cash_movement')) Controller::redirect('admin');

        $this->template->content = View::factory('admin/cash_movement/list')
            ->bind('movements', $movements)
            ->bind('filters', $filters)
            ->bind('users', $users)
            ->bind('user_id', $user_id);
        $this->template->title = 'Денежные движения';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $user_id = Auth::instance()->get_user()->id;


        $movements = ORM::factory('CashMovement');

        if (ORM::factory('Permission')->checkPermission('show_all_cash_movement')){

            $users = array('' => '---');

            foreach(ORM::factory('User')->where('status', '=', 1)->find_all()->as_array() as $user) {
                //filter users by permission
                if (ORM::factory('Permission')->checkPermissionByUser($user->id, 'cash_movement')) $users[$user->id] = $user->surname;
            }

            if (!empty($_GET['date_from'])) {
                $filters['date_from'] = $_GET['date_from'];
                $date_from = new DateTime($filters['date_from']);
                $movements = $movements->and_where(DB::expr('Date(date)'), '>=', $date_from->format('Y-m-d'));
            }

            if (!empty($_GET['date_to'])) {
                $filters['date_to'] = $_GET['date_to'];
                $date_to = new DateTime($filters['date_to']);
                $movements = $movements->and_where(DB::expr('Date(date)'), '<=', $date_to->format('Y-m-d'));
            }

            if (!empty($_GET['user_id'])) {
                $filters['user_id'] = $_GET['user_id'];
                $movements->and_where('from_user', '=', $filters['user_id'])->or_where('to_user', '=', $filters['user_id']);
            }

        } else {
            $movements = $movements->where('from_user', '=', $user_id)->or_where('to_user', '=', $user_id);
        }

        $movements = $movements->order_by('id', 'DESC')->limit(150)->find_all()->as_array();


        $this->template->scripts[] = "common/costs_list";
        $this->template->scripts[] = 'jquery-ui-1.10.4.custom.min';
    }


    public function action_create()
    {
        if (!ORM::factory('Permission')->checkPermission('cash_movement')) Controller::redirect('admin');

        $user_id = Auth::instance()->get_user()->id;

        $this->template->content = View::factory('admin/cash_movement/create')
            ->bind('message', $message)
            ->bind('users', $users);
        $this->template->title = 'Денежные движения::Создать';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';



        $users = array('' => '---');

        foreach(ORM::factory('User')->where('status', '=', 1)->find_all()->as_array() as $user) {
            //filter users by permission
            if (ORM::factory('Permission')->checkPermissionByUser($user->id, 'cash_movement') AND $user->id != $user_id) $users[$user->id] = $user->surname;
        }

        $message = '';

        if (HTTP_Request::POST == $this->request->method())
        {
            if ($this->request->post('to_user')){
                $cash_movement = ORM::factory('CashMovement');
                $cash_movement->values($this->request->post(), array(
                    'to_user',
                    'amount',
                    'comment',
                ));

                $cash_movement->set('date', date('Y-m-d H:i:s'))
                    ->set('from_user', $user_id);

                $cash_movement->save();
                Controller::redirect('admin/CashMovement/list?user_id='.$user_id);

            } else {
                $message = 'Ошибка! Необходимо выбрать получателя';
            }
        }


        $this->template->scripts[] = "common/costs_list";
        $this->template->scripts[] = 'jquery-ui-1.10.4.custom.min';
    }

    public function action_confirm()
    {
        if ($_GET['id']) {
            $movement = ORM::factory('CashMovement')->where('id', '=', $_GET['id'])->find();

            $user_id = Auth::instance()->get_user()->id;

            if ($movement->to_user = $user_id){
                $movement->set('confirmed', 1)->save();
            }
        }
        Controller::redirect($this->request->referrer());

    }
}


class Validation_Exception extends Exception
{
}

;
