<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Orders extends Controller_Application {
    public $disallowed_states = Model_Services::disableStates;



    public function action_test()
    {
        require('vendor/autoload.php');

        $client = new Everyman\Neo4j\Client('localhost', 7474);
        print_r($client->getServerInfo());


        $query_category = "SELECT id FROM categories WHERE categories.`level` = 2
           ";
        $result = DB::query(Database::SELECT, $query_category)->execute('tecdoc')->as_array();

        $array = [];


        foreach ($result as $all_category)
        {
            $array[] = [$all_category['id']];
        }

        var_dump($array);
        exit();


        $all_cat_query = "SELECT id FROM categories WHERE `level` = 2";

        $all_cats = DB::query(Database::SELECT,$all_cat_query)->execute('tecdoc')->as_array();

        $array = [];

        $array[] = ["category_id","model_id","type_id","part_id"];

        foreach ($all_cats as $all_cat)
        {
            $id = (integer)$all_cat['id'];

            $results_mongo = $this->mongo->data(['category_id' => $id], ['types' => 1, 'model_id'=> 1, 'category_id'=> 1]);

            foreach ($results_mongo as $result_mongo) {
                foreach ($result_mongo['types'] as $values) {
                    foreach ($values['parts'] as $part)
                    {
                        $array[] = [$result_mongo['category_id'],$result_mongo['model_id'],$values['type_id'],$part['part_id']];
//                    echo $result_mongo['category_id']."; ".$result_mongo['model_id']."; ".$values['type_id']."; ".$part['part_id'].";; ";
                    }
                }
            }
        }

        function toWindow($ii){
            return iconv( "utf-8", "windows-1251",$ii);
        }

        header('Content-Type: text/csv; charset=windows-1251');

        $file = fopen('/var/www/html/eparts/export_'.time().'.csv', 'w');  /* записываем в файл */

        foreach ($array as $fields) {
            fputcsv($file, $fields, ";");   /* записываем строку в csv-файл */
        }

        fclose($file);

        exit();
    }


    public function action_index()
    {

        if(!ORM::factory('Client')->logged_in()) {
            //return Controller::redirect('authorization/login');
            $client = ORM::factory('Client', array('id' => $_GET['order_add']));
        }else{
            $client = ORM::factory('Client')->get_client();
        }


        $this->template->title = 'Личный кабинет';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $this->template->content = View::factory('orders/list')
            ->bind('filters', $filters)
            ->bind('managers', $managers)
            ->bind('order_details', $order_details)
            ->bind('client', $client)
            ->bind('key_bottom', $key_bottom)
//            ->bind('oi_pagination', $oi_pagination)
            ->bind('orders', $orders);

        ///////////////////////////////////////
        $order_details = array();
        $order_details['all_sale'] = 0;
        $order_details['all_in'] = 0;
        $order_details['active_sale'] = 0;
        $order_details['balance'] = 0;
        $order_details['active_balance'] = 0;
        $order_details['debt'] = 0;
        $order_details['return'] = 0;
        $orders_of_user = ORM::factory('Order')->where('client_id', '=', $client->id)
            ->find_all()->as_array();

        $client_payments = ORM::factory('ClientPayment')->where('client_id', '=', $client->id)->order_by('date_time')->find_all()->as_array();

        foreach($client_payments as $cp) {
            $order_details['all_in'] += $cp->value; //Все проплаты
        }
        $order_details['balance'] = $order_details['all_in'];

        foreach($orders_of_user as $order) {

            foreach($order->orderitems->find_all()->as_array() as $oi) {
                if(in_array($oi->state_id, Model_Services::disableStates))
                {
                    $order_details['return'] += $oi->sale_per_unit*$oi->amount;
                    continue;
                }
                $order_details['all_sale'] += $oi->sale_per_unit*$oi->amount; //Сумма всех заказов без учета возврата

                if($oi->order->archive == 1) {
                    $order_details['balance'] -= $oi->sale_per_unit*$oi->amount; //Отнимаем от всех проплат архивные заказы
                } else {
                    $order_details['active_sale'] += $oi->sale_per_unit*$oi->amount; //Сумма активных заказов
                }
            }
        }

        $order_details['debt'] = $order_details['all_in'] - $order_details['all_sale']; //Долг
        //if($order_details['debt'] < 0) $order_details['debt'] = 0;
        $order_details['active_balance'] = $order_details['balance'] - $order_details['active_sale'];
        ///////////////////////////////////////////

        $orders = ORM::factory('Order');

        $orders->and_where('client_id', '=', $client->id);

        if(!empty($_GET['date_from'])) {
            $filters['date_from'] = $_GET['date_from'];
            $orders = $orders->and_where('date_time', '>=', date('Y-m-d', strtotime($filters['date_from'])));
        }
        if(!empty($_GET['date_to'])) {
            $filters['date_to'] = $_GET['date_to'];
            $orders = $orders->and_where('date_time', '<=', date('Y-m-d', strtotime($filters['date_to'])));
        }
        if(!empty($_GET['archive'])) {
            $filters['archive'] = $_GET['archive'];
            if($filters['archive'] != 'all') $orders = $orders->and_where('order.archive', '=', $filters['archive']);
        } else {
            $orders = $orders->and_where('archive', '=', 0);
            $filters['archive'] = 0;
        }

        $orders = $orders->order_by('date_time', "desc")->find_all()->as_array();

    }

    public function action_one_click()
    {
        $phone = $_POST['phone_number'];
        $id_pos = $_POST['id_position'];
        $count = $_POST['count'];

        $create_order = Model::factory('Services');
        $create_order->create_order($id_pos, $count, $phone );
        return Controller::redirect("orders/success");
    }

    public function action_all_balance()
    {
        if($this->request->param('id'))
            $client_id = $this->request->param('id');

        else
        {
            $url_previous = $_SERVER['HTTP_REFERER'];
            Controller::redirect($url_previous);
        }



        $this->template->title = 'Все позиции заказов';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $this->template->content = View::factory('orders/all_order_client')
            ->bind('filters', $filters)
            ->bind('orderitems', $orderitems)
            ->bind('client', $client)
            ->bind('oi_pagination', $oi_pagination)
            ->bind('disallowed_states', $order_disallow)
            ->bind('title', $this->template->title)
            ->bind('key_bottom', $key_bottom)
            ->bind('total', $total);
        $key_bottom = 0;
        $order_disallow = array('return_to_supplier', 'returns_from_the_client', 'irretrievable', 'unconfirmed_returns');

        $orderitems = ORM::factory('Orderitem')->with('order')->where('order.client_id', '=', $client_id);
        $orderitems->reset(FALSE);

        $orderitems_before = ORM::factory('Orderitem')->with('order')->with('state')->where('order.client_id', '=', $client_id);
        $orderitems_before = $orderitems_before->and_where('state.id', 'NOT IN', $this->disallowed_states);

        $filter_type = isset($_GET['filter_type']) ? $_GET['filter_type'] : 1;
        $table = $filter_type == 1 ? 'clientpayment' : 'order';

        if(!empty($_GET['date_from'])) {
            $filters['date_from'] = $_GET['date_from'];
            $orderitems = $orderitems->and_where('order.date_time', '>=', date('Y-m-d 00:00:00', strtotime($filters['date_from'])));
            $orderitems_before = $orderitems_before->and_where('order.date_time', '<', date('Y-m-d 00:00:00', strtotime($filters['date_from'])));
        } else {
            $orderitems_before = false;
        }
        if(!empty($_GET['date_to'])) {
            $filters['date_to'] = $_GET['date_to'];
            $orderitems = $orderitems->and_where('order.date_time', '<=', date('Y-m-d 23:59:59', strtotime($filters['date_to'])));
        }


        $total = array('payments' => 0, 'orderitems' => 0, 'before' => 0, 'payments_table' => 0, 'return' => 0);

        if($orderitems_before) {
            $orderitems_before = $orderitems_before->find_all()->as_array();

            foreach($orderitems_before as $orderitem) {
                $val = $orderitem->sale_per_unit * $orderitem->amount;
                $total['before'] -= $val;
            }
        }

        $count = $orderitems->count_all();

        $oi_pagination = Pagination::factory(array(
            'current_page' => array('source' => 'query_string', 'key' => 'oi_page'),
            'total_items' => $count,
            'items_per_page' => 15,
        ))->route_params(array(
            'controller' =>  'orders',
            'action' =>  'all_balance/'.$client_id
        ));

        $orderitems_tmp = $orderitems->order_by('date_time', "desc")->find_all()->as_array();
        $orderitems = array();

        foreach($orderitems_tmp as $orderitem) {
            $val = $orderitem->sale_per_unit*$orderitem->amount;
            if(!in_array($orderitem->state_id, $this->disallowed_states)) {
                $total['orderitems'] += $val;
                $orderitem->val = $val." грн.";
            } else {
                $total['return'] += $val;
                $orderitem->val = "<strike>".$val." грн.</strike>";
            }
            $orderitems[] = $orderitem;
        }

        $orderitems = array_slice($orderitems, $oi_pagination->offset, $oi_pagination->items_per_page);
        //end

    }
    public function action_return_balance()
    {

        if($this->request->param('id'))
        {
            $client_id = $this->request->param('id');
        }
        else{
            $url_previous = $_SERVER['HTTP_REFERER'];
            Controller::redirect($url_previous);
        }

        $this->template->title = 'Невыполненные позиции заказов';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $this->template->content = View::factory('orders/all_order_client')
            ->bind('filters', $filters)
            ->bind('orderitems', $orderitems)
            ->bind('client', $client)
            ->bind('oi_pagination', $oi_pagination)
            ->bind('disallowed_states', $order_disallow)
            ->bind('title', $this->template->title)
            ->bind('key_bottom', $key_bottom)
            ->bind('total', $total);
        $key_bottom = 2;
        $order_disallow = array('return_to_supplier', 'returns_from_the_client', 'irretrievable', 'unconfirmed_returns');

        $orderitems = ORM::factory('Orderitem')->with('order')->where('order.client_id', '=', $client_id);
        $orderitems->reset(FALSE);

        $orderitems_before = ORM::factory('Orderitem')->with('order')->with('state')->where('order.client_id', '=', $client_id);
        $orderitems_before = $orderitems_before->and_where('state.id', 'NOT IN', $this->disallowed_states);

        $filter_type = isset($_GET['filter_type']) ? $_GET['filter_type'] : 1;
        $table = $filter_type == 1 ? 'clientpayment' : 'order';

        if(!empty($_GET['date_from'])) {
            $filters['date_from'] = $_GET['date_from'];
            $orderitems = $orderitems->and_where('order.date_time', '>=', date('Y-m-d 00:00:00', strtotime($filters['date_from'])));
            $orderitems_before = $orderitems_before->and_where('order.date_time', '<', date('Y-m-d 00:00:00', strtotime($filters['date_from'])));
        } else {
            $orderitems_before = false;
        }
        if(!empty($_GET['date_to'])) {
            $filters['date_to'] = $_GET['date_to'];
            $orderitems = $orderitems->and_where('order.date_time', '<=', date('Y-m-d 23:59:59', strtotime($filters['date_to'])));
        }


        $total = array('payments' => 0, 'orderitems' => 0, 'before' => 0, 'payments_table' => 0, 'return' => 0);

        if($orderitems_before) {
            $orderitems_before = $orderitems_before->find_all()->as_array();

            foreach($orderitems_before as $orderitem) {
                $val = $orderitem->sale_per_unit * $orderitem->amount;
                $total['before'] -= $val;
            }
        }

        $orderitems_tmp = $orderitems->order_by('date_time', "desc")->find_all()->as_array();
        $orderitems = array();
        $key = 0;

        foreach($orderitems_tmp as $orderitem) {
            $val = $orderitem->sale_per_unit*$orderitem->amount;
            if(!in_array($orderitem->state_id, $this->disallowed_states)) {
                continue;
            } else {
                $total['return'] += $val;
                $orderitem->val = "<strike>".$val." грн.</strike>";
                $key++;
            }
            $orderitems[] = $orderitem;
        }

        $count = $key;

        $oi_pagination = Pagination::factory(array(
            'current_page' => array('source' => 'query_string', 'key' => 'oi_page'),
            'total_items' => $count,
            'items_per_page' => 15,
        ))->route_params(array(
            'controller' =>  'orders',
            'action' =>  'return_balance/'.$client_id
        ));

        $orderitems = array_slice($orderitems, $oi_pagination->offset, $oi_pagination->items_per_page);

    }
    public function action_real_balance()
    {

        if($this->request->param('id'))
        {
            $client_id = $this->request->param('id');
        }
        else{
            $url_previous = $_SERVER['HTTP_REFERER'];
            Controller::redirect($url_previous);
        }



        $this->template->title = 'Выполненные позиции заказов';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $this->template->content = View::factory('orders/all_order_client')
            ->bind('filters', $filters)
            ->bind('orderitems', $orderitems)
            ->bind('client', $client)
            ->bind('oi_pagination', $oi_pagination)
            ->bind('disallowed_states', $order_disallow)
            ->bind('key_bottom', $key_bottom)
            ->bind('title', $this->template->title)
            ->bind('total', $total);

        $key_bottom = 1;
        $order_disallow = array('return_to_supplier', 'returns_from_the_client', 'irretrievable', 'unconfirmed_returns');

        $orderitems = ORM::factory('Orderitem')->with('order')->with('state')->where('order.client_id', '=', $client_id)->and_where('state.id', 'NOT IN', $this->disallowed_states);
        $orderitems->reset(FALSE);

        $orderitems_before = ORM::factory('Orderitem')->with('order')->with('state')->where('order.client_id', '=', $client_id);
        $orderitems_before = $orderitems_before->and_where('state.id', 'NOT IN', $this->disallowed_states);

        $filter_type = isset($_GET['filter_type']) ? $_GET['filter_type'] : 1;
        $table = $filter_type == 1 ? 'clientpayment' : 'order';

        if(!empty($_GET['date_from'])) {
            $filters['date_from'] = $_GET['date_from'];
            $orderitems = $orderitems->and_where('order.date_time', '>=', date('Y-m-d 00:00:00', strtotime($filters['date_from'])));
            $orderitems_before = $orderitems_before->and_where('order.date_time', '<', date('Y-m-d 00:00:00', strtotime($filters['date_from'])));
        } else {
            $orderitems_before = false;
        }
        if(!empty($_GET['date_to'])) {
            $filters['date_to'] = $_GET['date_to'];
            $orderitems = $orderitems->and_where('order.date_time', '<=', date('Y-m-d 23:59:59', strtotime($filters['date_to'])));
        }


        $total = array('payments' => 0, 'orderitems' => 0, 'before' => 0, 'payments_table' => 0, 'return' => 0);

        if($orderitems_before) {
            $orderitems_before = $orderitems_before->find_all()->as_array();

            foreach($orderitems_before as $orderitem) {
                $val = $orderitem->sale_per_unit * $orderitem->amount;
                $total['before'] -= $val;
            }
        }

        $count = $orderitems->count_all();

        $oi_pagination = Pagination::factory(array(
            'current_page' => array('source' => 'query_string', 'key' => 'oi_page'),
            'total_items' => $count,
            'items_per_page' => 15,
        ))->route_params(array(
            'controller' =>  'orders',
            'action' =>  'real_balance/'.$client_id
        ));

        $orderitems_tmp = $orderitems->order_by('date_time', "desc")->find_all()->as_array();
        $orderitems = array();

        foreach($orderitems_tmp as $orderitem) {
            $val = $orderitem->sale_per_unit*$orderitem->amount;
            if(!in_array($orderitem->state_id, $this->disallowed_states)) {
                $total['orderitems'] += $val;
                $orderitem->val = $val." грн.";
            } else {
                $total['return'] += $val;
                $orderitem->val = "<strike>".$val." грн.</strike>";
            }
            $orderitems[] = $orderitem;
        }

        $orderitems = array_slice($orderitems, $oi_pagination->offset, $oi_pagination->items_per_page);

    }
    public function action_all_pay()
    {

        if($this->request->param('id'))
        {
            $client_id = $this->request->param('id');
        }
        else{
            $url_previous = $_SERVER['HTTP_REFERER'];
            Controller::redirect($url_previous);
        }



        $this->template->title = 'Ваши оплаты';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $this->template->content = View::factory('orders/all_pay_client')
            ->bind('filters', $filters)
            ->bind('client_payments', $client_payments)
            ->bind('client', $client)
            ->bind('cp_pagination', $cp_pagination)
//            ->bind('oi_pagination', $oi_pagination)
            ->bind('total', $total);

        //test
        $client_payments = ORM::factory('ClientPayment')->with('client')->with('order')->where('clientpayment.client_id', '=', $client_id);
        $client_payments->reset(FALSE);

        $client_payments_before = ORM::factory('ClientPayment')->with('client')->with('order')->where('clientpayment.client_id', '=', $client_id);

        $filter_type = isset($_GET['filter_type']) ? $_GET['filter_type'] : 1;
        $table = $filter_type == 1 ? 'clientpayment' : 'order';

        if(!empty($_GET['date_from'])) {
            $filters['date_from'] = $_GET['date_from'];
            $client_payments = $client_payments->and_where($table.'.date_time', '>=', date('Y-m-d 00:00:00', strtotime($filters['date_from'])));
            $client_payments_before = $client_payments_before->and_where($table.'.date_time', '<', date('Y-m-d 00:00:00', strtotime($filters['date_from'])));
        } else {
            $orderitems_before = false;
        }
        if(!empty($_GET['date_to'])) {
            $filters['date_to'] = $_GET['date_to'];
            $client_payments = $client_payments->and_where($table.'.date_time', '<=', date('Y-m-d 23:59:59', strtotime($filters['date_to'])));
        }


        $total = array('payments' => 0, 'orderitems' => 0, 'before' => 0, 'payments_table' => 0);

        if($client_payments_before) {

            $client_payments_before = $client_payments_before->find_all()->as_array();
            foreach($client_payments_before as $cp) {
                $total['before'] += $cp->value;
            }
        }

        $count = $client_payments->count_all();

        $cp_pagination = Pagination::factory(array(
            'current_page' => array('source' => 'query_string', 'key' => 'cp_page'),
            'total_items' => $count,
            'items_per_page' => 20,
        ))->route_params(array(
            'controller' =>  'orders',
            'action' =>  'all_pay/'.$client_id
        ));

        $client_payments = $client_payments->order_by('date_time', "desc")->find_all()->as_array();

        foreach($client_payments as $sp) {
            if($sp->value > 0/* && (!empty($filters['date_from']) || !empty($filters['date_to']))*/)
                $total['payments_table'] += $sp->value;
            $total['payments'] += $sp->value;
        }

        $client_payments = array_slice($client_payments, $cp_pagination->offset, $cp_pagination->items_per_page);

    }

    public function action_items()
    {

        $id = $this->request->param('id');

        if(!ORM::factory('Client')->logged_in()) {
            //return Controller::redirect('authorization/login');
            $order = ORM::factory('Order', array('id' => $id));
            $client = ORM::factory('Client', array('id' => $order->client_id));
        }else{
            $client = ORM::factory('Client')->get_client();
        }

        if (!is_null($this->request->query('delitem'))) {
            $this->remove_item();
            Controller::redirect(Helper_Url::createUrl(null, ['delitem' => null], true));
        }

        $this->template->title = 'Позиции заказа';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';


        $this->template->content = View::factory('orders/items_list')
            ->bind('filters', $filters)
            ->bind('total', $total)
            ->bind('order_details', $order_details)
            ->bind('data', $data)
            ->bind('order_id', $order_id)
            ->bind('message', $message)
            ->bind('orderitems', $orderitems);

        $total = array();
        $total['purchase'] = 0;
        $total['sale'] = 0;
        $total['delivery'] = 0;

        $orderitems = ORM::factory('Orderitem')->with('order')->with('order:client');

        $orderitems->and_where('state_id', '!=', 38);
        $orderitems->and_where('order.client_id', '=', $client->id);

        $orderitems = $orderitems->and_where('order_id', '=', $id);
        $order_id = $id;

        $order_details = array();
        $order_details['order'] = ORM::factory('order')->where('id', '=', $id)->find();
        $order_details['all_sale'] = 0;
        $order_details['all_in'] = 0;
        $order_details['active_sale'] = 0;
        $order_details['balance'] = 0;
        $order_details['active_balance'] = 0;
        $order_details['debt'] = 0;
        $order_details['debt_in_order'] = 0;

        if($order_details['order']->client->id != $client->id)
            return Controller::redirect('orders');

        $order_disallow = Model_Services::disableStates;

        foreach($order_details['order']->orderitems->find_all()->as_array() as $oi) {
            if(in_array($oi->state->id, $order_disallow)) continue;
            $order_details['debt_in_order'] += $oi->sale_per_unit*$oi->amount;
        }

        $orders_of_user = ORM::factory('Order')->where('client_id', '=', $order_details['order']->client->id)
            ->find_all()->as_array();

        $client_payments = ORM::factory('ClientPayment')->where('client_id', '=', $order_details['order']->client->id)->order_by('date_time')->find_all()->as_array();

        foreach($client_payments as $cp) {
            $order_details['all_in'] += $cp->value;
        }
        $order_details['balance'] = $order_details['all_in'];

        foreach($orders_of_user as $order) {

            foreach($order->orderitems->find_all()->as_array() as $oi) {
                if(in_array($oi->state->id, $order_disallow)) continue;
                $order_details['all_sale'] += $oi->sale_per_unit*$oi->amount;

                if($oi->order->archive == 1) {
                    $order_details['balance'] -= $oi->sale_per_unit*$oi->amount;
                } else {
                    $order_details['active_sale'] += $oi->sale_per_unit*$oi->amount;
                }
            }
        }

        $order_details['debt'] = $order_details['all_sale'] - $order_details['all_in'];
        if($order_details['debt'] < 0) $order_details['debt'] = 0;
        $order_details['active_balance'] = $order_details['balance'] - $order_details['active_sale'];

        $orderitems = $orderitems->find_all()->as_array();

        foreach($orderitems as $orderitem) {
            $total['purchase'] += $orderitem->purchase_per_unit*$orderitem->amount;
            $total['sale'] += $orderitem->sale_per_unit*$orderitem->amount;
            $total['delivery'] += $orderitem->delivery_price;
        }

    }

    public function action_add() {
        if(ORM::factory('Client')->logged_in()) {
            //return Controller::redirect('authorization/login?order_add=true');
            $guest = false;
        }else{
            $guest = true;
        }

        if(count(Cart::instance()->content) <= 0)
            return Controller::redirect("cart/show");

        $this->template->content = View::factory('orders/add_order')
            ->bind('errors', $errors)
            ->bind('message', $message)
            ->bind('delivery_methods', $delivery_methods)
            ->bind('data', $data)
            ->bind('area', $final_area)
            ->bind('guest', $guest)
            ->bind('orderId', $orderId);

        $this->template->title = 'Оформление заказа - интернет магазин автозапчастей Eparts';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
        $this->template->noindex = true;

        $areas = ORM::factory('NpAreas')->find_all()->as_array();
        $final_area = [];

        $final_area['-----'] = '-----';
        foreach ($areas as $area)
        {
            $final_area[$area->id] = $area->name;
        }

        $data = array();
        if(ORM::factory('Client')->logged_in()) {

            $client = ORM::factory('Client')->get_client();
            $data['delivery_method_id'] = $client->delivery_method_id;
            $data['delivery_address'] = $client->delivery_address;
        }

        if (HTTP_Request::POST == $this->request->method())
        {

            try {

                $new = false;

                if(!ORM::factory('Client')->logged_in()) {

                    $client = ORM::factory('Client', array('phone' => $this->request->post('phone')));

                    if (!$client->loaded()) {

                        $last_order = ORM::factory('Order')->order_by('id', 'DESC')->limit(1)->find();

                        //default query
                        $query = DB::select('id')
                            ->from('users')
                            ->join('roles_users', 'LEFT')
                            ->on('users.id', '=', 'roles_users.user_id')
                            ->where('roles_users.role_id', 'IN', array(3,6,10,17))
                            ->and_where('users.status', '=', 1);

                        //all managers
                        $managers = $query->execute()->as_array();
                        $temp = array();
                        foreach ($managers AS $one) {
                            $temp[] = $one['id'];
                        }
                        $managers = $temp;


                        //active managers
                        $active_managers = $query
                            ->and_where('last_activity', '>', DB::expr('DATE_ADD(NOW(), INTERVAL -15 MINUTE)'))
                            ->execute()
                            ->as_array();
                        $temp = array();
                        foreach ($active_managers AS $one) {
                            $temp[] = $one['id'];
                        }
                        $active_managers = $temp;

                        $last_order_manager = $last_order->manager_id;

                        if (empty($active_managers)) {
                            //add nonactive managers to queue
                            if (in_array($last_order_manager, $managers)) {
                                foreach ($managers AS $manager) {
                                    if ($last_order_manager == $manager)
                                        $man = current($managers);
                                }
                            } else {
                                $man = $managers[array_rand($managers)];
                            }
                        } elseif (count($active_managers) == 1) {
                            //take lone manager
                            $man = $active_managers[0];
                        } else {

                            $man = $active_managers[array_rand($active_managers)];

                            //add active managers to queue
                            if (in_array($last_order_manager, $managers)){
                                $success = 0;
                                while ($success != 1) {
                                    foreach ($managers AS $manager) {
                                        if ($last_order_manager == $manager) {
                                            if (in_array(current($managers), $active_managers)) {
                                                $man = current($managers);
                                                $success = 1;
                                                break;
                                            } else {
                                                $last_order_manager = current($managers);
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        $guests = ORM::factory('Client');

                        $guests->name = 'Гость';
                        $guests->surname = 'Гость';
                        $guests->client_type = $this->request->post('client_type');
                        $guests->edrpoy = !empty($this->request->post('edrpoy')) ? $this->request->post('edrpoy'):'';
                        $guests->name_organization = !empty($this->request->post('name_organization')) ? $this->request->post('name_organization'):'';
                        $guests->phone = $this->request->post('phone');
                        $guests->password = $this->request->post('password');
                        $guests->email = $this->request->post('email');
                        $guests->manager_id = $man;
                        $guests->discount_id = ORM::factory('Discount')->getClient_standart();
                        $guests->save();

                        $client->manager_id = $guests->manager_id;
                        $client->id = $guests->id;
                        $client->phone = $this->request->post('phone');

                        ORM::factory('Client')->login($this->request->post('phone'), $this->request->post('password'));


                    } else {
                        $this->redirect('/authorization/login?message=true');
                    }

                    $new = true;

                }

                $order = ORM::factory('Order');
                $values = array(
                    'delivery_address',
                    'delivery_method_id',
                );

                //$order->values($this->request->post(), $values);
                $order->delivery_address = $this->request->post('delivery_address');
                $order->delivery_method_id = $this->request->post('delivery_method_id');
                $order->state = null !==($this->request->post('state')?$this->request->post('state'):'');
                $order->manager_id = $client->manager_id;
                $order->confirmation = $this->request->post('confirmation');
                $order->client_id = $client->id;
                $order->set('np_area_id', isset($_POST['np_area_id']) ? $_POST['np_area_id']:'');
                $order->set('np_city_id', isset($_POST['np_city_id']) ? $_POST['np_city_id']:'');
                $order->set('np_warehouse_id', isset($_POST['np_warehouse_id']) ? $_POST['np_warehouse_id']:'');
                $order->set('np_street', isset($_POST['np_street']) ? $_POST['np_street']:'');
                $order->set('np_build', isset($_POST['np_build']) ? $_POST['np_build']:'');
                $order->set('np_flat', isset($_POST['np_flat']) ? $_POST['np_flat']:'');
                $order->set('archive', 0);
                $order->set('online', 1);
                if ($order->delivery_method_id=1){
                    $order->set('np_area_id', null);
                    $order->set('np_city_id', null);
                    $order->set('np_warehouse_id', null);
                }
                $order->save();
                $orderId = $order->id;

                foreach(Cart::instance()->content as $item) {
                    $this->add_priceitem_to_order($item['id'], $order, $item['qty']);
                }

                Cart::instance()->delete();

                $mail_tpl = View::factory('email/order_done')
                    ->set('order', $order);

                try {

                    if(ORM::factory('Client')->logged_in()) {

                        $email = Email::factory('Заказ оформлен', '')
                            ->to($client->email)
                            ->from('ulc.com.ua@gmail.com')
                            ->message($mail_tpl->render(), 'text/html')
                            ->send();

                    }

                    $sms_text = "Заказ №".$order->get_order_number()." оформлен.";
                    $sms_text .= "\nulc.com.ua\nulc.com.ua@gmail.com\n(098) 092-82-08";
                    if ($new == true) {
                        $sms_text .= "\nLogin " .$client->phone." Pass 11111";
                    }
                    Sms::send($sms_text, "Оформление заказа", $client->phone);
                } catch (Exception $e) {
                }

                return Controller::redirect("orders/success/$orderId");

                // Reset values so form is not sticky
                $_POST = array();
            } catch (ORM_Validation_Exception $e) {
                $data = $_POST;
                // Set failure message
                $message = 'Исправте ошибки!';

                // Set errors using custom messages
                $errors = $e->errors('models');
            }
        }

        $delivery_methods = ORM::factory('DeliveryMethod')->where('id', '!=', 2)->find_all()->as_array();

//		foreach(ORM::factory('DeliveryMethod')->find_all()->as_array() as $deliverymethod) {
//			$delivery_methods[$deliverymethod->id] = $deliverymethod->name;
//		}
        $this->template->scripts[] = 'common/order_form_step3';
    }

    private function add_priceitem_to_order($priceitem_id, $order, $amount) {
        if(is_numeric($priceitem_id)) {
            $priceitem = ORM::factory('Priceitem')->where('id', '=', $priceitem_id)->find();
        } else {
            $json_array = json_decode(base64_decode(str_replace('_','=',$priceitem_id)), true);
            $priceitem = ORM::factory('Priceitem')->get_from_arr($json_array);
        }

        $discount = $priceitem->get_discount_for_client();


        $orderitem = ORM::factory('Orderitem');
        $orderitem->set('order_id', $order->id);
        $orderitem->set('article', $priceitem->part->article_long);
        $orderitem->set('brand', $priceitem->part->brand_long);
        $orderitem->set('name', $priceitem->part->name);
        $orderitem->set('suplier_code_tehnomir', $priceitem->suplier_code_tehnomir);
        $orderitem->set('purchase_per_unit', $priceitem->get_price());
        $orderitem->set('purchase_per_unit_in_currency', $priceitem->price);
        $orderitem->set('currency_id', $priceitem->currency_id);
        $orderitem->set('state_id', ORM::factory('State')->get_state_by_text_id('order_accept')->id);
        $orderitem->set('amount', $amount);
        $orderitem->set('delivery_days', $priceitem->delivery);

        $orderitem->set('sale_per_unit', $priceitem->get_price_for_client());

        $orderitem->set('discount_id', $discount->id);
        $orderitem->set('supplier_id', $priceitem->supplier_id);
        $orderitem->save();
    }

    public function action_print() {
        $id = $this->request->param('id');

        $order = ORM::factory('Order')->where('id', '=', $id)->find();

        $data = array();
        $data['order_number'] = (string)$order->get_order_number();
        $data['client'] = $order->client->name." ".$order->client->surname;
        $data['client_phone'] = $order->client->phone;
        $data['orderitems'] = $order->orderitems->find_all()->as_array();

        $this->create_invoice($data)->send();
    }

    /**
     * Action after success checkout
     */
    public function action_success()
    {
        // TODO: Uncomment when approve LiqPay
        // $orderId = $this->request->param('id');

        $this->template->content = View::factory('orders/success')
            ->bind('title', $title)
            ->bind('message', $message)
            ->bind('orderId', $orderId);

        $this->template->title = $title = 'Заказ выполнен';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $message = 'Заказ успешно выполнен! В ближайшее время с Вами свяжется наш менеджер<br />
Для просмотра своих заказов необходимо войти в личный кабинет используя логин и пароль.';
    }

    private function create_invoice($data = array(), $path = 'uploads/') {
        $spreadsheet = Spreadsheet::factory(array(
            'author'  => 'Kohana-PHPExcel',
            'title'      => 'Report',
            'subject' => 'Subject',
            'description'  => 'Description',
            'path' => $path,
            'name' => 'invoice_'.(!empty($data['order_number']) ? $data['order_number'] : date('dmYHis')),
            'format' => 'Excel5',
        ));
        $spreadsheet->set_active_worksheet(0);
        $as = $spreadsheet->get_active_worksheet();
        $as->title("Report");

        $as->getDefaultStyle()->getFont()->setSize(10);

        $as->getColumnDimension('A')->setWidth(5);
        $as->getColumnDimension('B')->setWidth(15);
        $as->getColumnDimension('C')->setWidth(15);
        $as->getColumnDimension('D')->setWidth(15);
        $as->getColumnDimension('G')->setWidth(15);
        $as->getColumnDimension('H')->setWidth(15);

        $styleArray = array(
            'borders' => array(
                'outline' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('argb' => 'FF000000'),
                ),
                'inside' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('argb' => 'FF000000'),
                ),
            ),
        );

        $styleArrayBottom = array(
            'borders' => array(
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('argb' => 'FF000000'),
                ),
            ),
        );

        $current = 1;

        $as->setCellValue("C$current", "Поставщик:");
        $as->getStyle("C$current")->getFont()->setBold(true)->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);

        $as->setCellValue("D$current", "Епартс");
        $as->getStyle("D$current")->getFont()->setBold(true);

        $current++;

        $as->setCellValue("D$current", "(044)361-96-64, (067)291-18-25, (095)053-00-35");

        $current++;

        $as->setCellValue("C$current", "Получатель:");
        $as->getStyle("C$current")->getFont()->setBold(true)->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);

        $as->setCellValue("D$current", $data['client']);

        $current++;

        $as->setCellValue("D$current", $data['client_phone']);

        $current++;

        $as->setCellValue("C$current", "Основание:");
        $as->getStyle("C$current")->getFont()->setBold(true)->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);

        $as->setCellValue("D$current", "За запчасти");

        $current += 2;

        $as->mergeCells("D$current:E$current");
        $as->getStyle("D$current")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        if(!empty($data['order_number'])) $as->setCellValue("D$current", "Заказ № ".$data['order_number']);
        $as->getStyle("D$current")->getFont()->setBold(true)->setSize(12);
        $current++;

        $as->mergeCells("D$current:E$current");
        $as->getStyle("D$current")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $as->setCellValue("D$current", "Дата: ".date("d.m.Y"));

        $current += 2;

        $as->setCellValue("A$current", "№");
        $as->getStyle("A$current")->getFont()->setBold(true);

        $as->mergeCells("B$current:D$current");
        $as->getStyle("B$current")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $as->setCellValue("B$current", "Товар");
        $as->getStyle("B$current")->getFont()->setBold(true);

        $as->setCellValue("E$current", "Ед.");
        $as->getStyle("E$current")->getFont()->setBold(true);

        $as->setCellValue("F$current", "Кол-во");
        $as->getStyle("F$current")->getFont()->setBold(true);

        $as->setCellValue("G$current", "Цена");
        $as->getStyle("G$current")->getFont()->setBold(true);

        $as->setCellValue("H$current", "Сумма");
        $as->getStyle("H$current")->getFont()->setBold(true);

        $as->getStyle("A$current:H$current")->applyFromArray($styleArray);

        $current++;
        $count = 1;
        $summ = 0;

        foreach($data['orderitems'] as $orderitem) {
            $as->setCellValue("A$current", $count);

            $as->setCellValue("B$current", $orderitem->article);
            $as->getStyle("B$current")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $as->setCellValue("C$current", $orderitem->brand);
            $as->setCellValue("D$current", $orderitem->name);
            $as->getStyle("D$current")->getAlignment()->setWrapText(true);

            $as->setCellValue("E$current", "шт.");

            $as->setCellValue("F$current", $orderitem->amount);

            $as->setCellValue("G$current", round($orderitem->sale_per_unit, 0).",00");

            $as->setCellValue("H$current", round($orderitem->sale_per_unit*$orderitem->amount, 0).",00");
            $summ += round($orderitem->sale_per_unit*$orderitem->amount, 0);

            $as->getStyle("A$current:H$current")->applyFromArray($styleArray);
            $current++;
            $count++;
        }

        $as->setCellValue("G$current", "Общая сумма:");
        $as->getStyle("G$current")->getFont()->setBold(true);

        $as->setCellValue("H$current", round($summ, 0).",00");
        $as->getStyle("H$current")->applyFromArray($styleArray);

        $current += 2;

        $as->mergeCells("A$current:H$current");

        $as->setCellValue("A$current", "Уважаемые покупатели, приобретенный товар можно вернуть или обменять в течение 14 дней с момента покупки при наличии документов на приобретение, при сохранении товарного вида.\nВНИМАНИЕ! Заказные автозапчасти за рубежом обмену и возврату не подлежат, также могут варьироваться сроки поставки в Украину. ");
        $as->getStyle("A$current:E$current")->getAlignment()->setWrapText(true);
        $as->getRowDimension($current)->setRowHeight(50);

        $current += 3;

        $as->mergeCells("E$current:F$current");
        $as->getStyle("E$current")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $as->setCellValue("E$current", "Выписал:");

        $as->mergeCells("G$current:H$current");
        $as->getStyle("G$current")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $as->getStyle("G$current:H$current")->applyFromArray($styleArrayBottom);


        $current++;

        $as->mergeCells("G$current:H$current");
        $as->getStyle("G$current")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $as->setCellValue("G$current", "Куряков Д.О.");

        return $spreadsheet;
    }

    public function remove_item()
    {
        $itemId = $this->request->query('delitem');

        $client = ORM::factory('Client')->get_client();
        $orderItem = ORM::factory('Orderitem', $itemId);

        if (($orderItem->order->client_id != $client->id) || ($orderItem->state_id != 1)) return false;
        $orderItem->state_id = 39;
        $orderItem->save();

        $log = ORM::factory('OrderitemLog');

        $log
            ->set('orderitem_id', $orderItem->id)
            ->set('state_id', 39)
            ->set('tehnomir', 0)
            ->set('date_time', date('Y-m-d H:i:s'))
            ->set('user_id', Auth::instance()->get_user()->id)
            ->save();

        Admin::check_ready_order($orderItem->order_id);
    }
}
