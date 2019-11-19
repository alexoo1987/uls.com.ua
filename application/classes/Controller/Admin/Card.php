<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Card extends Controller_Admin_Application
{

    public function action_index()
    {
        $this->template->title = 'Баланс карты';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
        if(!ORM::factory('Permission')->checkPermission('card_managment')) Controller::redirect('admin');

        $this->template->content = View::factory('admin/card/list')
            ->bind('data', $data)
            ->bind('filters', $filters)
            ->bind('message', $message)
            ->bind('pagination', $pagination)
            ->bind('all_cash',$all_cash)
            ->bind('count',$count_confirm)
            ->bind('card_balance', $card_balance);

        $card_balance = ORM::factory('Card');
        $card_balance->reset(FALSE);

        $confirmed = ORM::factory('Card');
        $unconfirmed = ORM::factory('Card');
        $unconfirmed->reset(FALSE);
        $confirmed->reset(FALSE);
        $confirmed = $confirmed->and_where('confirmed','=',1);
        $unconfirmed = $unconfirmed->and_where('confirmed','=',0);

        if(!empty($_GET['date_from'])) {
            $filters['date_from'] = $_GET['date_from'];
            $card_balance = $card_balance->and_where('date_time', '>=', date('Y-m-d 00:00:00', strtotime($filters['date_from'])));
            $unconfirmed = $unconfirmed->and_where('date_time', '>=', date('Y-m-d 00:00:00', strtotime($filters['date_from'])));
            $confirmed = $confirmed->and_where('date_time', '>=', date('Y-m-d 00:00:00', strtotime($filters['date_from'])));
        }
        if(!empty($_GET['date_to'])) {
            $filters['date_to'] = $_GET['date_to'];
            $card_balance = $card_balance->and_where('date_time', '<=', date('Y-m-d 23:59:59', strtotime($filters['date_to'])));
            $confirmed = $confirmed->and_where('date_time', '<=', date('Y-m-d 23:59:59', strtotime($filters['date_to'])));
            $unconfirmed = $unconfirmed->and_where('date_time', '<=', date('Y-m-d 23:59:59', strtotime($filters['date_to'])));
        }
        if(!empty($_GET['confirm'])) {
            $filters['confirm'] = $_GET['confirm'];
            if($filters['confirm'] == 1){
                $card_balance = $card_balance->and_where('confirmed', '=', 1 );
            }
            else{
                $card_balance = $card_balance->and_where('confirmed', '=', 0 );
            }
        }

        $count_confirm['confirmed'] = $confirmed->count_all();
        $count_confirm['unconfirmed'] = $unconfirmed->count_all();

        $count = $card_balance->count_all();
        $card_balance = $card_balance->order_by('date_time','DESC')->find_all()->as_array();

        $pagination = Pagination::factory(array('total_items' => $count))->route_params(array(
            'controller' =>  'card',
            'action' =>  'index'
        ));

        $all_cash = 0;

        foreach ($card_balance as $balance)
        {
            $all_cash = $all_cash+$balance->value;
        }

        $card_balance = array_slice($card_balance, $pagination->offset, $pagination->items_per_page);

        if (HTTP_Request::POST == $this->request->method()) {

            $card = ORM::factory('Card');
            $card->set('date_time', date('Y-m-d H:i:s'));
            $card->set('user_id', Auth::instance()->get_user()->id);
            $card->set('comment', $this->request->post()['comment_text']);
            $card->set('value', $this->request->post()['value']);
            $card->save();

            $message = "Проплата добавлена";
            Controller::redirect(URL::base().$this->request->uri().URL::query());
        }

        $this->template->scripts[] = 'common/orders_list_items';
    }

    public function action_delete()
    {
        if (isset($_GET['id'])) {
            ORM::factory('Card')->where('id', '=', $_GET['id'])->find()->delete();
            $url_previous = $_SERVER['HTTP_REFERER'];
            Controller::redirect($url_previous);
        }
    }

    public function action_confirm()
    {
        if (isset($_GET['id'])) {
            $change_confirm = ORM::factory('Card')->where('id', '=', $_GET['id'])->find();
            $change_confirm->confirmed = 1;
            $change_confirm->save();
            $url_previous = $_SERVER['HTTP_REFERER'];
            Controller::redirect($url_previous);
        }
    }
}