<?php defined('SYSPATH') or die('No direct script access.');

//require ("/var/www/html/eparts/eparts/vendor/lis-dev/nova-poshta-api-2/src/Delivery/NovaPoshtaApi2.php");
use LisDev\Delivery\NovaPoshtaApi2;

class Controller_Admin_Orders extends Controller_Admin_Application {

    private static $googleApiKey = 'AIzaSyBo6g5IjDm5-3DYOycG280qViQjDdlWDdU';

    public function action_index()
    {
//        Kohana::$log->add(Log::INFO, 'php index.php processfile --sess_data="'.base64_encode($sess_data).'" &');
//        Kohana::$log->attach(new Log_File(APPPATH.'logs/tehnomir'), Log::NOTICE, 'Мой первый лог');
//        Log::instance()->add(new Log_File(APPPATH.'logs/tehnomir'), Log::NOTICE, 'My Logged Message Here');
//        Auth::instance()->get_user()->id;

//        print_r(ORM::factory('Order')->and_where_open()->and_where_open()->where('ready_order', '=', 1)->or_where('ready_order', '=', '2')->and_where_close()->and_where('state', '=', 2)->and_where_close()->find_all()); exit();


        if(!ORM::factory('Permission')->checkPermission('manage_orders')) Controller::redirect('admin');
        $this->template->title = 'Заказы';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $this->template->content = View::factory('admin/orders/list')
            ->bind('filters', $filters)
            ->bind('managers', $managers)
            ->bind('pagination', $pagination)
            ->bind('order_by', $order_by)
            ->bind('states', $states)
            ->bind('orders_states', $orders_states)
            ->bind('orders', $orders)
            ->bind('clients_on_page', $clients_on_page);
        $states = ORM::factory('State')->order_by('sort', 'asc')->find_all()->as_array();

        $orders_states = ORM::factory('OrderState')->find_all()->as_array();

        $orders = ORM::factory('Order')->with('client');
        $orders->reset(FALSE);

        if (empty($_GET['order_id']) AND empty($_GET['client']) AND empty($_GET['phone']) AND ORM::factory('Permission')->checkRole('manager'))
            $orders->and_where('order.manager_id', '=', Auth::instance()->get_user()->id);

        if(!empty($_GET['date_from'])) {
            $filters['date_from'] = $_GET['date_from'];
            $orders = $orders->and_where('date_time', '>=', date('Y-m-d 00:00:00', strtotime($filters['date_from'])));
        }
        if(!empty($_GET['date_to'])) {
            $filters['date_to'] = $_GET['date_to'];
            $orders = $orders->and_where('date_time', '<=', date('Y-m-d 23:59:59', strtotime($filters['date_to'])));
        }
        if(!empty($_GET['manager_id'])) {
            $filters['manager_id'] = $_GET['manager_id'];
            $orders = $orders->and_where('order.manager_id', '=', $filters['manager_id']);
        }
        if(!empty($_GET['order_id'])) {
            $filters['order_id'] = $_GET['order_id'];
            $orders = $orders->and_where('order.id', '=', intval($filters['order_id']));
        }
        if(!empty($_GET['ttn'])) {
            $filters['ttn'] = $_GET['ttn'];
            $orders = $orders->and_where('ttn', '=', $filters['ttn']);
        }
        if(!empty($_GET['archive'])) {
            $filters['archive'] = $_GET['archive'];
            if($filters['archive'] != 'all') $orders = $orders->and_where('order.archive', '=', $filters['archive']);
        } else {
            if(empty($_GET['ready_order']) AND empty($_GET['state'])){
                $orders = $orders->and_where('archive', '=', 0);
                $filters['archive'] = 0;
            }

        }
        if(!empty($_GET['online'])) {
            $filters['online'] = $_GET['online'];
            if($filters['online'] != 'all')
                $orders = $orders->and_where('order.online', '=', $filters['online']);
        }
        else
        {
            if(isset($_GET['online']))
            {
                $orders = $orders->and_where('order.online', '=', 0);
                $filters['online'] = 0;
            }
        }


        if(!empty($_GET['ready'])) {
            $filters['ready'] = ($_GET['ready']==1);
        }
        if(!empty($_GET['ttn_is'])) {
            $filters['ttn_is'] = ($_GET['ttn_is']==1);
            $orders = $orders->and_where('order.ttn', 'IS NOT', NULL);
            $orders = $orders->and_where('order.ttn', '<>', '');
        }
        if(!empty($_GET['client'])) {
            $filters['client'] = $_GET['client'];
            $orders = $orders->and_where('client.surname', 'LIKE', "%".$filters['client']."%");
        }
        if(!empty($_GET['phone'])) {
            $filters['phone'] = $_GET['phone'];
            $orders = $orders->and_where('client.phone', 'LIKE', "%".$filters['phone']."%");
        }

        if (!empty($_GET['state'])) {
            $filters['state'] = $_GET['state'];
            $orders = $orders->and_where('order.state', '=', $filters['state']);
        }

        if (!empty($_GET['ready_order'])) {
            $filters['ready_order'] = $_GET['ready_order'];

            if($filters['ready_order'] == 1)
            {
                if(ORM::factory('Permission')->checkPermission('packaging'))
                {
                    $orders = $orders->and_where('ready_order', '=', 1);
                }
            }

            else
            {
                if(ORM::factory('Permission')->checkPermission('show_ready_orders'))
                {
                    $orders = $orders->and_where('ready_order', '=', 2);
                }

                if(ORM::factory('Permission')->checkPermission('show_my_ready_oredrs'))
                {
                    $orders = $orders->and_where('ready_order', '=', 2);
                    $orders = $orders->and_where('order.manager_id', '=', Auth::instance()->get_user()->id);
                }
            }



        }

        if (isset($_GET['delivery_status'])) {
            if (((ORM::factory('Permission')->checkPermission('show_only_own_return'))&&($_GET['delivery_status'] == 2))||((ORM::factory('Permission')->checkPermission('show_only_own_return'))&&($_GET['delivery_status'] == 3))){
                $filters['delivery_status'] = $_GET['delivery_status'];
                $orders = $orders
                    ->and_where('id_purchasing_agent', '=', Auth::instance()->get_user()->id)
                    ->and_where('order.delivery_status', '=', $filters['delivery_status']);
            }
            else{
                $filters['delivery_status'] = $_GET['delivery_status'];
                $orders = $orders->and_where('order.delivery_status', '=', $filters['delivery_status']);
            }

        }

        $count = $orders->count_all();

        $pagination = Pagination::factory(array('total_items' => $count))->route_params(array(
            'controller' =>  'orders',
            'action' =>  'index'
        ));


        if(!empty($_GET['order_by'])) {
            $order_by['column'] = $_GET['order_by'];
        } else {
            $order_by['column'] = "date_time";
        }
        if(!empty($_GET['order_direction'])) {
            $order_by['direction'] = $_GET['order_direction'];
        } else {
            $order_by['direction'] = "desc";
        }

        $orders_tmp = $orders->order_by($order_by['column'], $order_by['direction'])->find_all()->as_array();

        $orders = array();

        foreach($orders_tmp as $order) {
            $order->ready = true;
            foreach($order->orderitems->find_all()->as_array() as $orderitem) {
                if($orderitem->state->text_id != 'in_office')
                    $order->ready = false;

            }

            if(!empty($filters['ready']) && $filters['ready'] && !$order->ready) continue;

            $orders[] = $order;
        }

        $orders = array_slice($orders, $pagination->offset, $pagination->items_per_page);


        $clients_on_page = array();
        foreach($orders as $order) {
            if(!isset($clients_on_page[$order->client_id])) {
                $clients_on_page[$order->client_id] = $order->client->get_user_balance();
            }
        }

        $managers = array('' => '---');
        if((!empty($_GET['date_to']))||(!empty($_GET['date_from']))) {
            foreach(ORM::factory('User')->find_all()->as_array() as $user) {
                $managers[$user->id] = $user->surname;
            }
        }
        else
        {
            foreach(ORM::factory('User')->where('status', '=', 1)->find_all()->as_array() as $user) {
                $managers[$user->id] = $user->surname;
            }
        }


        $this->template->scripts[] = 'bootstrap-formhelpers-phone.format';
        $this->template->scripts[] = 'bootstrap-formhelpers-phone';
        $this->template->scripts[] = 'jquery-ui-1.10.4.custom.min';
        $this->template->scripts[] = 'common/orders_list';

    }

    public function action_packaging_all_position()
    {
        if(!empty($_GET['order_id'])) {
            $order_id = $_GET['order_id'];
            $all_position = ORM::factory('Orderitem')->where('order_id', '=', $order_id)->find_all()->as_array();
            foreach ($all_position as $one)
            {
                if($one->state_id == 3)
                {
                    $log = ORM::factory('OrderitemLog');

                    $log
                        ->set('orderitem_id', $one->id)
                        ->set('state_id', 37)
                        ->set('tehnomir', 0)
                        ->set('date_time', date('Y-m-d H:i:s'))
                        ->set('user_id', Auth::instance()->get_user()->id) //Auth::instance()->get_user()->id
                        ->save();
                    $one->state_id = 37;
                    $one->save();
                }
            }
            Admin::check_ready_order($order_id);
        }
        return Controller::redirect($_SERVER['HTTP_REFERER']);
    }

    public function action_give_all_position()
    {
        if(!empty($_GET['order_id'])) {
            $order_id = $_GET['order_id'];
            $all_position = ORM::factory('Orderitem')->where('order_id', '=', $order_id)->find_all()->as_array();
            foreach ($all_position as $one)
            {
                if($one->state_id == 3 OR $one->state_id == 36)
                {
                    $log = ORM::factory('OrderitemLog');

                    $log
                        ->set('orderitem_id', $one->id)
                        ->set('state_id', 5)
                        ->set('tehnomir', 0)
                        ->set('date_time', date('Y-m-d H:i:s'))
                        ->set('user_id', Auth::instance()->get_user()->id) //Auth::instance()->get_user()->id
                        ->save();
                    $one->state_id = 5;
                    $one->save();
                }
            }
            Admin::check_ready_order($order_id);
        }
        return Controller::redirect($_SERVER['HTTP_REFERER']);
    }

    public function action_delivery_all_position()
    {
        if(!empty($_GET['order_id'])) {
//            echo $_GET['order_id']; exit();
            $order_id = $_GET['order_id'];
            $all_position = ORM::factory('Orderitem')->where('order_id', '=', $order_id)->find_all()->as_array();
            foreach ($all_position as $one)
            {
                if($one->state_id == 3)
                {
                    $log = ORM::factory('OrderitemLog');

                    $log
                        ->set('orderitem_id', $one->id)
                        ->set('state_id', 36)
                        ->set('tehnomir', 0)
                        ->set('date_time', date('Y-m-d H:i:s'))
                        ->set('user_id', Auth::instance()->get_user()->id) //Auth::instance()->get_user()->id
                        ->save();
                    $one->state_id = 36;
                    $one->save();
                }
            }
            Admin::check_ready_order($order_id);
        }
        return Controller::redirect($_SERVER['HTTP_REFERER']);
    }

    public function action_nova_poshta()
    {
        $this->template->title = 'Отслеживать по ТТН';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $this->template->content = View::factory('admin/orders/nova_poshta');

        $this->template->styles[] = 'dist/nova_poshta';

    }

    public function action_get_act()
    {
        if(!ORM::factory('Permission')->checkPermission('show_act_ready')) Controller::redirect('admin');
        $this->template->title = 'Акт сверки по позициях в работе';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $this->template->content = View::factory('admin/orders/actworkposition')
            ->bind('data', $data)
            ->bind('message', $message)
            ->bind('variant', $variant);

        $variant = array('' => '---');
        $data = date("Y-m-d");
        $variant[1] = 'Краткий';
        $variant[2] = 'Детальный';

        /* // $this->template->scripts[] = 'jquery-ui-1.10.4.custom.min'; */
        $this->template->scripts[] = 'bootstrap.validate';
        $this->template->scripts[] = 'bootstrap.validate.ru';
        $this->template->scripts[] = 'common/supplieract';

    }

    public function action_get_act_excel()
    {
        $this->auto_render = FALSE;
        $this->is_ajax = TRUE;
        if ($this->request->method() == Request::POST) {

            $from = date("Y-m-d", strtotime($_POST["date_from"]));
            $to = date("Y-m-d", strtotime($_POST["date_to"]));
            if ($to < '2009-01-01') {
                $to = date("Y-m-d");
            }
            if ($_POST["variant"] == 1) {
                $this->get_work_position_short($from, $to)->send();
            } else {
                $this->get_work_position_long($from, $to)->send();
            }
        }
    }

    private function get_work_position_short($from = false, $to = false)
    {
        if(!$to) $to = date("Y-m-d");
        if(!$from) $from = "2016-08-08";
        $query_all_category = "
            SELECT 
            SUM(t.purchase_per_unit_in_currency*t.amount) as sum, t.id, t.`name` as supplier, t.`code` as cur, COUNT(t.article) as count_position
            FROM
            (
                SELECT o.article, o.brand, o.amount, o.purchase_per_unit_in_currency, s.id, s.`name`, c.`code`
                FROM orderitems_log as ol
                JOIN orderitems as o ON o.id = ol.orderitem_id
                JOIN currencies as c ON o.currency_id = c.id
                JOIN suppliers as s ON s.id = o.supplier_id
                AND DATE(ol.date_time) >= '".$from."' 
                AND DATE(ol.date_time) <= '".$to."'
                AND ol.state_id IN (2, 6, 8, 19, 31, 32)
                AND o.state_id IN (2, 6, 8, 19, 31, 32)
                GROUP BY ol.orderitem_id
                ORDER BY ol.date_time DESC
            ) as t
            GROUP BY t.id
            ";
        $all_act = DB::query(Database::SELECT, $query_all_category)->execute('tecdoc')->as_array(); // вытягиваем все категории

        $path = 'uploads/';
        $spreadsheet = Spreadsheet::factory(array(
            'author'  => 'Kohana-PHPExcel',
            'title'      => 'Report',
            'subject' => 'Subject',
            'description'  => 'Description',
            'path' => $path,
            'name' => ("suppliers__".date('dmYHis')),
            'format' => 'Excel5',
        ));
        $spreadsheet->set_active_worksheet(0);
        $as = $spreadsheet->get_active_worksheet();
        $as->title("Report");

        $as->getDefaultStyle()->getFont()->setSize(10);

        $as->getColumnDimension('A')->setWidth(15);
        $as->getColumnDimension('B')->setWidth(10);
        $as->getColumnDimension('C')->setWidth(20);
        $as->getColumnDimension('D')->setWidth(20);
        $as->getColumnDimension('E')->setWidth(20);

        $current = 1;

        $as->setCellValue("A$current", "Поставщик:");
        $as->getStyle("A$current")->getFont()->setBold(true)->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);

        $as->setCellValue("B$current", "Кол-во позиций в работе:");
        $as->getStyle("B$current")->getFont()->setBold(true);

        $as->setCellValue("C$current", "Сумма:");
        $as->getStyle("C$current")->getFont()->setBold(true);

        $as->setCellValue("D$current", "Баланс:");
        $as->getStyle("D$current")->getFont()->setBold(true);

        $as->setCellValue("E$current", "Сумма доставок:");
        $as->getStyle("E$current")->getFont()->setBold(true);

        $current ++;

        foreach ($all_act as $act)
        {
            $balance = $act['id'];
            $real_balance = Article::get_supplier_balance($balance);

            $as->setCellValue("A$current", $act['supplier']);
            $as->getStyle("A$current")->getFont()->setBold(true)->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);

            $as->setCellValue("B$current", $act['count_position']);
            $as->getStyle("B$current")->getFont()->setBold(true);

            $as->setCellValue("C$current", round($act['sum'],2)." ".$act['cur']);
            $as->getStyle("C$current")->getFont()->setBold(true);

            $as->setCellValue("D$current", $real_balance." ".$act['cur']);
            $as->getStyle("D$current")->getFont()->setBold(true);


            $delivery_costs = "SELECT * FROM costs where type = 3 AND supplier_id = ".$balance." AND DATE(date)>= '".$from."'  AND DATE(date) <= '".$to."'";
            $delivery_costs = DB::query(Database::SELECT, $delivery_costs)->execute('tecdoc')->as_array();
            $all_supplier_cost = 0;

            if(!empty($delivery_costs)){
                foreach ($delivery_costs as $supp_cost){
                    $all_supplier_cost += $supp_cost['amount'];
                }

            }

            $as->setCellValue("E$current", $all_supplier_cost." грн");
            $as->getStyle("E$current")->getFont()->setBold(true);

            $current ++;
        }

        return $spreadsheet;
    }

    private function get_work_position_long($from = false, $to = false)
    {
        if(!$to) $to = date("Y-m-d");
        if(!$from) $from = "2016-08-08";
        $query_all_category = "
            SELECT DISTINCT o.id, o.order_id, o.article as article, o.brand, o.`name`, o.amount, o.purchase_per_unit_in_currency, c.`code`, s.`name` as supplier, s.id as supp_id
            FROM orderitems as o
            INNER JOIN currencies as c ON o.currency_id = c.id
            INNER JOIN suppliers as s ON s.id = o.supplier_id
            INNER JOIN orderitems_log as ol ON o.state_id = ol.state_id AND o.id = ol.orderitem_id 
            WHERE DATE(ol.date_time)>= '".$from."'  AND DATE(ol.date_time) <= '".$to."' AND (o.state_id = 2 OR o.state_id = 6 OR o.state_id = 8 OR o.state_id = 19 OR o.state_id = 31 OR o.state_id = 32)
            ORDER BY s.`name`
            ";
        $all_act = DB::query(Database::SELECT, $query_all_category)->execute('tecdoc')->as_array(); // вытягиваем все категории

        $path = 'uploads/';
        $spreadsheet = Spreadsheet::factory(array(
            'author'  => 'Kohana-PHPExcel',
            'title'      => 'Report',
            'subject' => 'Subject',
            'description'  => 'Description',
            'path' => $path,
            'name' => ("suppliers__".date('dmYHis')),
            'format' => 'Excel5',
        ));
        $spreadsheet->set_active_worksheet(0);
        $as = $spreadsheet->get_active_worksheet();
        $as->title("Report");

        $as->getDefaultStyle()->getFont()->setSize(10);

        $as->getColumnDimension('A')->setWidth(15);
        $as->getColumnDimension('B')->setWidth(30);
        $as->getColumnDimension('C')->setWidth(10);
        $as->getColumnDimension('D')->setWidth(50);
        $as->getColumnDimension('E')->setWidth(10);
        $as->getColumnDimension('F')->setWidth(20);
        $as->getColumnDimension('G')->setWidth(30);

        $current = 1;

        $as->setCellValue("A$current", "Номер заказа:");
        $as->getStyle("A$current")->getFont()->setBold(true)->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);

        $as->setCellValue("B$current", "Article:");
        $as->getStyle("B$current")->getFont()->setBold(true);

        $as->setCellValue("C$current", "Brand:");
        $as->getStyle("C$current")->getFont()->setBold(true);

        $as->setCellValue("D$current", "Название:");
        $as->getStyle("D$current")->getFont()->setBold(true);

        $as->setCellValue("E$current", "Кол-во:");
        $as->getStyle("E$current")->getFont()->setBold(true);

        $as->setCellValue("F$current", "Стоимость в валюте:");
        $as->getStyle("F$current")->getFont()->setBold(true);

        $as->setCellValue("G$current", "Поставщик:");
        $as->getStyle("G$current")->getFont()->setBold(true);


        $current ++;

        $unique_brand = $all_act[0]['supplier'];
        $unique_supp_id = $all_act[0]['supp_id'];
        $unique_code = $all_act[0]['code'];
        $sum_curr = 0;

        foreach ($all_act as $act)
        {

            if($unique_brand == $act['supplier'])
            {
                $sum_curr = $sum_curr + (double)$act['purchase_per_unit_in_currency']*$act['amount'];
            }
            else
            {
                $delivery_costs = "SELECT * FROM costs where type = 3 AND supplier_id = ".$unique_supp_id." AND DATE(date)>= '".$from."'  AND DATE(date) <= '".$to."'";
                $delivery_costs = DB::query(Database::SELECT, $delivery_costs)->execute('tecdoc')->as_array();
                if(!empty($delivery_costs)){
                    $all_supplier_cost = 0;
                    foreach ($delivery_costs as $supp_cost){
                        $all_supplier_cost += $supp_cost['amount'];
                    }

                    $as->mergeCells("A$current:E$current");
                    $as->getStyle("A$current")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $as->getStyle("A$current")->getFont()->setBold(true);
                    $as->setCellValue("A$current", "Общая сумма доставок:");

                    $as->setCellValue("F$current", $all_supplier_cost." грн");
                    $as->getStyle("F$current")->getFont()->setBold(true);

                    $current ++;
                }

                $as->mergeCells("A$current:E$current");
                $as->getStyle("A$current")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $as->getStyle("A$current")->getFont()->setBold(true);
                $as->setCellValue("A$current", "Общая сумма:");

                $as->setCellValue("F$current", $sum_curr." ".$unique_code);
                $as->getStyle("F$current")->getFont()->setBold(true);

                $current ++;
                $current ++;

                $unique_brand = $act['supplier'];
                $unique_supp_id = $act['supp_id'];
                $unique_code = $act['code'];
                $sum_curr = (double)$act['purchase_per_unit_in_currency'];
            }

            $as->setCellValue("A$current", $act['order_id']);
            $as->getStyle("A$current")->getFont()->setBold(false)->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
//
            $as->setCellValue("B$current", $act['article']);
            $as->getStyle("B$current")->getFont()->setBold(false);

            $as->setCellValue("C$current", $act['brand']);
            $as->getStyle("C$current")->getFont()->setBold(false);
//
            $as->setCellValue("D$current", $act['name']);
            $as->getStyle("D$current")->getFont()->setBold(false);
//
            $as->setCellValue("E$current", $act['amount']);
            $as->getStyle("E$current")->getFont()->setBold(false);
//
            $as->setCellValue("F$current", $act['purchase_per_unit_in_currency']*$act['amount']." ".$act['code']);
            $as->getStyle("F$current")->getFont()->setBold(false);

            $as->setCellValue("G$current", $act['supplier']);
            $as->getStyle("G$current")->getFont()->setBold(false);

            if($act == end($all_act)) {
                $current ++;
                $as->mergeCells("A$current:E$current");
                $as->getStyle("A$current")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $as->getStyle("A$current")->getFont()->setBold(true);
                $as->setCellValue("A$current", "Общая сумма:");

                $as->setCellValue("F$current", $sum_curr." ".$unique_code);
                $as->getStyle("F$current")->getFont()->setBold(true);
            }

            $current ++;

//            $current ++;
        }
//        exit();

        return $spreadsheet;

//        exit();
    }
//
//    public function action_test()
//    {
//        echo date('Y-m-d H:i:s', strtotime('10.07.2018'));
//        $time = mktime(date("H"), date("i"),date("s"), '02', '22', '2018');
//        echo "<br />".$time;
//        echo "<br />".date("Y-m-d H:i:s", $time);
//        exit();
//    }

    public function action_tie()
    {
        $sup_order_to_orderitem = ORM::factory('OrderitemToSupplier');

        if(!empty($_POST['supplier_order'])){
            $sup_order = ORM::factory('SupplierOrder');

            $date_supp_order = explode('.', $_POST['date_supp_order']);
            $date_supp_order_time = mktime(date("H"), date("i"),date("s"), $date_supp_order[1], $date_supp_order[0], $date_supp_order[2]);
            $date_supp_order = date("Y-m-d H:i:s", $date_supp_order_time);

            $sup_order
                ->set('order_supplier', $_POST['supplier_order'])
                ->set('supplier_id', $_POST['supp_id'])
                ->set('date_time', $date_supp_order)
                ->save();

            $sup_order_to_orderitem
                ->set('orderitem_id', $_POST['orderitem_id'])
                ->set('order_supplier_id', $sup_order->id)
                ->set('user_id', Auth::instance()->get_user()->id )
                ->save();

            if(!empty($_POST['supplier_order_payment']))
            {
                $ratio = !empty($_POST['rate']) ? $_POST['rate'] : 1;
                $value = !empty($_POST['rate']) ? round(($_POST['supplier_order_payment'] / $_POST['rate']), 2) : $_POST['supplier_order_payment'];
                $suppPayment = ORM::factory('SupplierPayment');
                $suppPayment
                    ->set('supplier_id', $_POST['supp_id'])
                    ->set('user_id', Auth::instance()->get_user()->id)
                    ->set('date_time', $date_supp_order)
                    ->set('value', $value)
                    ->set('ratio', $ratio)
                    ->set('supp_order_id', $sup_order->id)
                    ->set('comment_text', 'Водитель')
                    ->save();
            }

            if(!empty($_POST['supplier_order_delivery']))
            {
                $suppPayment = ORM::factory('Costs');
                $suppPayment
                    ->set('supplier_id', $_POST['supp_id'])
                    ->set('type', 3)
                    ->set('user_id', Auth::instance()->get_user()->id)
                    ->set('date', date('Y-m-d', $date_supp_order_time))
                    ->set('amount', $_POST['supplier_order_delivery'])
                    ->set('created', $date_supp_order)
                    ->set('supp_order_id', $sup_order->id)
                    ->save();
            }
        }

        else{
            $sup_order_to_orderitem
                ->set('orderitem_id', $_POST['orderitem_id'])
                ->set('order_supplier_id', $_POST['created_supplier_order_id'])
                ->set('user_id', Auth::instance()->get_user()->id )
                ->save();
        }

        $orderitem = ORM::factory('Orderitem')->where('id', '=', $_POST['orderitem_id'])->find();
        $log = ORM::factory('OrderitemLog');
        $log
            ->set('orderitem_id', $orderitem->id)
            ->set('state_id', 3)
            ->set('date_time', date('Y-m-d H:i:s'))
            ->set('user_id', Auth::instance()->get_user()->id)
            ->save();

        $orderitem->state_id = 3;
        $orderitem->save();

        // изменение статуса заказа при увидомлении о возврате, если пришли все товары
        Admin::check_ready_order($orderitem->order_id);

        if(!empty($_SERVER['HTTP_REFERER'])) {
            return Controller::redirect($_SERVER['HTTP_REFERER']);
        }
        return Controller::redirect('/');
    }
    public function action_synchronization()
    {
        $order_tm = ORM::factory('OrderEpartsTm')->where('status','=', 1)->find_all()->as_array();

        $order_tm_array = array();
        $INFO = Tminfo::instance();
        $INFO->SetLogin('Mir@eparts.kiev.ua');
        $INFO->SetPasswd('9506678d');
        $array_states['tm_states'] =  array(1, 2, 3,4,5,6, 7,8,9,10,11,12,13,14,15,16,17,18,19,20,21);
        $array_states['our_states'] = array(34,7,34,2,2,6,31,6,6, 6, 6,32,34,34,15,15,14,35,34,15,15);

        foreach ($order_tm as $key=>$value)
        {
            echo $value->order_tm_id."<br>";
            $tm_create_order = $INFO->GetOrderPositions($value->order_tm_id);
            $order_tm_array[$value->order_id] = $tm_create_order;
        }

        foreach ($order_tm_array as $key=>$value) {
            foreach ($value as $item=>$var) {
                $position = ORM::factory('Orderitem')->where('id','=', $key)->and_where('supplier_id','=',38)->find();
                echo $position->id."<br>";
                for ($i =0; $i<count($array_states['tm_states']); $i++)
                {
                    if($var['StateId']==$array_states['tm_states'][$i])
                    {
                        if($position->state_id != $array_states['our_states'][$i])
                        {
                            if($position->state_id == 18)
                            {
                                if($array_states['tm_states'][$i]!=12)
                                {
                                    $order_tm_position = ORM::factory('OrderEpartsTm')->where('order_id','=', $position->id)->find();
                                    $order_tm_position->status = 0;
                                    $order_tm_position ->save();

                                    $position->state_id = $array_states['our_states'][$i];
                                    $position ->save();
                                    $log = ORM::factory('OrderitemLog');

                                    $log
                                        ->set('orderitem_id', $position->id)
                                        ->set('state_id', $array_states['our_states'][$i])
                                        ->set('tehnomir', 1)
                                        ->set('date_time', date('Y-m-d H:i:s'))
                                        ->set('user_id', 94 ) //Auth::instance()->get_user()->id
                                        ->save();
                                    break;
                                }
                                else
                                {
                                    continue;
                                }
                            }
                            else
                            {
                                if($array_states['tm_states'][$i] == 12 OR $array_states['tm_states'][$i] == 21 OR $array_states['tm_states'][$i] == 20 OR $array_states['tm_states'][$i] == 16 OR $array_states['tm_states'][$i] == 15 )
                                {
                                    $order_tm_position = ORM::factory('OrderEpartsTm')->where('order_id','=', $position->id)->find();
                                    $order_tm_position->status = 0;
                                    $order_tm_position ->save();

                                    $position->state_id = $array_states['our_states'][$i];
                                    $position ->save();
                                }
                                else
                                {
                                    $position->state_id = $array_states['our_states'][$i];
                                    $position ->save();
                                }


                                $log = ORM::factory('OrderitemLog');

                                $log
                                    ->set('orderitem_id', $position->id)
                                    ->set('state_id', $array_states['our_states'][$i])
                                    ->set('tehnomir', 1)
                                    ->set('date_time', date('Y-m-d H:i:s'))
                                    ->set('user_id', 94)
                                    ->save();
                                break;
                            }

                        }
                    }
                }
            }
        }
        if(!empty($_SERVER['HTTP_REFERER'])) {
            return Controller::redirect($_SERVER['HTTP_REFERER']);
        }
        return Controller::redirect('/');
    }
    public function action_backet()
    {
        $id = 26123;
        $one_position = ORM::factory('Orderitem')->where('id', '=', $id)->find();
        $INFO = Tminfo::instance();
        $INFO->SetLogin('Mir@eparts.kiev.ua');
        $INFO->SetPasswd('9506678d');
        $result = $INFO->BasketList($one_position->purchase_per_unit_in_currency, $one_position->suplier_code_tehnomir, $one_position->article, $one_position->amount);
        if($result) echo "True";
        else echo "false";
    }
    public function action_curl()
    {
        $url = "https://ajax.googleapis.com/ajax/services/search/images?" .
            "v=1.0&q=barack%20obama&userip=INSERT-USER-IP";

        // sendRequest
        // note how referer is set manually
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_REFERER, "https://eparts.kiev.ua/");
        $body = curl_exec($ch);
        curl_close($ch);

        // now, process the JSON string
        $json = json_decode($body);
        var_dump($json);
        exit();
        // now have some fun with the results...
    }
    public function action_create_order_tm()
    {
        $this->auto_render = FALSE;
        $this->is_ajax = TRUE;
        header('content-type: application/json');
        $id = Request::current()->post('id');

        $order_in_old_order = ORM::factory('OrderEpartsTm')->where('order_id', '=', $id)->find();

        if(!empty($order_in_old_order->order_tm_id))
        {
            $temp = array(
                'result' => 3,
            );
            echo json_encode($temp);
            exit();
        }

        $z = 0;

        $brand_replace['from1'] = array('kia', 'citroen', 'acura', 'nissan', 'lexus');
        $brand_replace['from2'] = array('hyundai', 'peugeot', 'honda', 'infiniti', 'toyota');
        $brand_replace['to'] = array('hyundai/kia', 'citroen/peugeot', 'honda/acura', 'nissan/infiniti', 'toyota/lexus');
        $brand_replace['vag_from'] = array( 'audi', 'vw', 'seat', 'skoda', 'vag');
        $brand_replace['vag_to'] = array('vag');
        $flag = false;
        $key_i = -1;

        $one_position = ORM::factory('Orderitem')->where('id', '=', $id)->find();

        if ($one_position->supplier_id == 38)
        {
            $INFO = Tminfo::instance();
            $INFO->SetLogin('Mir@eparts.kiev.ua');
            $INFO->SetPasswd('9506678d');
            for($i = 0; $i<count($brand_replace['to']); $i++) {
                if (strnatcasecmp($one_position->brand, $brand_replace['to'][$i]) == 0) {
                    $flag = true;
                    $key_i = $i;
                }
            }

            if($flag)
            {
                $tm_add_busket = $INFO->BasketAddPos($brand_replace['from1'][$key_i], $one_position->suplier_code_tehnomir, $one_position->article, $one_position->amount);
                if(empty($tm_add_busket))
                {
                    $tm_add_busket = $INFO->BasketAddPos($brand_replace['from2'][$key_i], $one_position->suplier_code_tehnomir, $one_position->article, $one_position->amount);
                }
            }
            else
            {
                $tm_add_busket = $INFO->BasketAddPos($one_position->brand, $one_position->suplier_code_tehnomir, $one_position->article, $one_position->amount);
            }

            if($tm_add_busket) $z++;
            $result = $INFO->BasketList($one_position->purchase_per_unit_in_currency, $one_position->suplier_code_tehnomir, $one_position->article, $one_position->amount);

            if($result)
            {
                $log = ORM::factory('OrderEpartsTm');
                $get_order = $log->where('order_id', '=', $id)->find();
                if(!empty($get_order->order_tm_id))
                {
                    $INFO->BasketClear();

                    $temp = array(
                        'result' => 3,
                    );
                    $INFO->BasketClear();

                    echo json_encode($temp);
                    exit();
                }
                else{
                    $tm_create_order = $INFO->BasketMakeOrder($one_position->order->id);
                    $log = ORM::factory('OrderEpartsTm');
                    $log
                        ->set('order_id', $id)
                        ->set('order_tm_id', $tm_create_order)
                        ->save();
                    $number = (int)$tm_create_order[0];
                    $temp = array(
                        'order_tm_id' => $number,//$suplier->currency_id,
                        'number' => $z,
                        'result' => 1,
                    );
                    $INFO->BasketClear();

                    echo json_encode($temp);
                    exit();
                }

            }
            else
            {
                $INFO->BasketClear();
                $temp = array(
                    'result' => 2,
                );
                echo json_encode($temp);
                exit();
            }
        }
    }
    public function action_return_order_inform()
    {

        $id = $this->request->param('id');
        if(empty($id)) Controller::redirect('admin/orders');

        $order = ORM::factory('Order')->where('id', '=', $id)->find();
        $order->delivery_status = 1;
        $order->save();

        $message = "Уведомление отправлено";
        Controller::redirect('admin/orders');

    }

    public function action_ready_order()
    {

        $id = $this->request->param('id');
        if(empty($id)) Controller::redirect('admin/orders');

        $order = ORM::factory('Order')->where('id', '=', $id)->find();
        $order->ready_order = 2;
        $order->save();

        $message = "Уведомление принято";
        Controller::redirect('admin/orders?ready_order=1');

    }

    public function action_confirmation_order_inform()
    {

        $id = $this->request->param('id');
        if(empty($id)) Controller::redirect('admin/orders');


        $this->template->title = 'Уведомление';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $id_agent = Auth::instance()->get_user()->id;

        $order = ORM::factory('Order')->where('id', '=', $id)->find();
        $order->delivery_status = 2;
        $order->id_purchasing_agent = $id_agent;
        $order->date_time_return_confim = date('Y-m-d H:i:s');
        $order->save();

        $message = "Уведомление принято";
        Controller::redirect('admin/orders');

    }

    public function action_show_return_list()
    {

        if(!ORM::factory('Permission')->checkPermission('manage_orders')) Controller::redirect('admin');
        $this->template->title = 'Позиции заказа(ов)';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $id = $this->request->param('id');
        $this->template->content = View::factory('admin/orders/items_list')
            ->bind('filters', $filters)
            ->bind('managers', $managers)
            ->bind('states', $states)
            ->bind('suppliers', $suppliers)
            ->bind('order_id', $order_id)
            ->bind('total', $total)
            ->bind('order_details', $order_details)
            ->bind('data', $data)
            ->bind('message', $message)
            ->bind('pagination', $pagination)
            ->bind('orderitems', $orderitems)
            ->bind('orders_to_move', $orders_to_move)
            ->bind('salary', $salary)
            ->bind('penalty', $user_penalty)
            ->bind('order_by', $order_by)
            ->bind('clients_on_page', $clients_on_page);

        $total = array();
        $total['purchase'] = 0;
        $total['sale'] = 0;
        $total['delivery'] = 0;
        $pagination = "";

        $orderitems = ORM::factory('Orderitem')->with('order')->with('order:client');
        $orderitems->reset(FALSE);

        if(ORM::factory('Permission')->checkPermission('show_only_own_orders'))
            $orderitems->and_where('order.manager_id', '=', Auth::instance()->get_user()->id);

        if(!empty($id)) {
            $orderitems = $orderitems->and_where('order_id', '=', $id);
            $order_id = $id;

            $order = ORM::factory('order')->where('id', '=', $id)->find();
            $order_details = $order->client->get_user_balance();
            $order_details['order'] = $order;

            $data['comment_text'] = "За заказ №".$order_details['order']->get_order_number();
            if (HTTP_Request::POST == $this->request->method()) {
                $clientpayment = ORM::factory('ClientPayment');
                $clientpayment->values($this->request->post(), array(
                    'value',
                    'comment_text',
                ));
//				$date = !empty($_POST['date_time']) ? date('Y-m-d', strtotime($_POST['date_time'])) : date('Y-m-d');
                $clientpayment->set('date_time', date('Y-m-d H:i:s'));
                $clientpayment->set('client_id', $order_details['order']->client_id);
                $clientpayment->set('order_id', $order_details['order']->id);
                $clientpayment->set('user_id', Auth::instance()->get_user()->id);
                $clientpayment->save();

                $message = "Проплата добавлена";
                Controller::redirect(URL::base().$this->request->uri().URL::query());
            }

            $order_details['debt_in_order'] = 0;

            $order_disallow = Model_Services::disableStates;

            foreach($order_details['order']->orderitems->find_all()->as_array() as $oi) {
                if(in_array($oi->state->id, $order_disallow)) continue;
                $order_details['debt_in_order'] += $oi->sale_per_unit*$oi->amount;
            }

            $orders_tmp = ORM::factory('Order')->where('archive', '=', '0');
            if(ORM::factory('Permission')->checkPermission('show_only_own_orders')) $orders_tmp->and_where('manager_id', '=', Auth::instance()->get_user()->id);
            $orders_tmp->and_where('client_id', '=', $order_details['order']->client_id);
            $orders_tmp = $orders_tmp->order_by("date_time", "desc")->find_all()->as_array();

            $orders_to_move = array('add_new' => 'Добавить новый');

            foreach($orders_tmp as $order) {
                $orders_to_move[$order->id] = $order->get_order_number()." (".$order->client->name." ".$order->client->surname.")";
            }
        }
        else {
            if (empty($_GET['order_id']) AND empty($_GET['client']) AND empty($_GET['client_name']) AND empty($_GET['phone']) AND ORM::factory('Permission')->checkRole('manager')) {
                $orderitems->and_where('order.manager_id', '=', Auth::instance()->get_user()->id);
            }

            if(!empty($_GET['date_from'])) {
                $filters['date_from'] = $_GET['date_from'];
                $orderitems = $orderitems->and_where('order.date_time', '>=', date('Y-m-d 00:00:00', strtotime($filters['date_from'])));
            }
            if(!empty($_GET['date_to'])) {
                $filters['date_to'] = $_GET['date_to'];
                $orderitems = $orderitems->and_where('order.date_time', '<=', date('Y-m-d 23:59:59', strtotime($filters['date_to'])));
            }
            if(!empty($_GET['article'])) {
                $filters['article'] = $_GET['article'];
                $orderitems = $orderitems->and_where('article', 'LIKE', '%'.trim($filters['article']).'%');
            }
            if(!empty($_GET['manager_id'])) {
                $filters['manager_id'] = $_GET['manager_id'];
                $orderitems = $orderitems->and_where('order.manager_id', '=', $filters['manager_id']);
            }
            if(!empty($_GET['state_id'])) {
                $filters['state_id'] = $_GET['state_id'];
                $orderitems = $orderitems->and_where('state_id', '=', $filters['state_id']);
            }
            if (isset($_GET['confirmed'])) {
                $filters['confirmed'] = $_GET['confirmed'];
                $orderitems = $orderitems->and_where('confirmed', '=', $filters['confirmed']);
            }
            if(!empty($_GET['supplier_id'])) {
                $filters['supplier_id'] = $_GET['supplier_id'];
                $orderitems = $orderitems->and_where('supplier_id', '=', $filters['supplier_id']);
            }
            if(!empty($_GET['order_id'])) {
                $filters['order_id'] = $_GET['order_id'];
                $orderitems = $orderitems->and_where('order_id', '=', intval($filters['order_id']));
            }
            if(!empty($_GET['client'])) {
                $filters['client'] = $_GET['client'];
                $orderitems = $orderitems->and_where('order:client.surname', 'LIKE', "%".$filters['client']."%");
            }
            if(!empty($_GET['client_name'])) {
                $filters['client_name'] = $_GET['client_name'];
                $orderitems = $orderitems->and_where('order:client.name', 'LIKE', "%".$filters['client_name']."%");
            }
            if(!empty($_GET['phone'])) {
                $filters['phone'] = $_GET['phone'];
                $orderitems = $orderitems->and_where('order:client.phone', 'LIKE', "%".$filters['phone']."%");
            }
            if(!empty($_GET['archive'])) {
                $filters['archive'] = $_GET['archive'];
                if($filters['archive'] != 'all') $orderitems = $orderitems->and_where('order.archive', '=', $filters['archive']);
            } else {
                $orderitems = $orderitems->and_where('order.archive', '=', 0);
                $filters['archive'] = 0;
            }
            if(!empty($_GET['ids'])) {
                $orderitems = $orderitems->and_where('orderitem.id', 'IN', explode(',', $_GET['ids']));
            }
        }

        $count = $orderitems->count_all();

        $pagination = Pagination::factory(array('total_items' => $count))->route_params(array(
            'controller' =>  'orders',
            'action' =>  'items'
        ));

        if(!empty($_GET['order_by'])) {
            $order_by['column'] = $_GET['order_by'];
        } else {
            $order_by['column'] = "order.date_time";
        }
        if(!empty($_GET['order_direction'])) {
            $order_by['direction'] = $_GET['order_direction'];
        } else {
            $order_by['direction'] = "desc";
        }

        $items_in_work = clone $orderitems;
        $items_in_work = $items_in_work->and_where('state_id', 'IN', Model_Services::inWork)->find_all()->as_array();

        $in_work = array(
            'expired' => [
                'label' => 'Просроченные',
                'color' => '#ff0000',
                'ids' => []
            ],
            'today' => [
                'label' => 'Сегодня',
                'color' => '#fbff00',
                'ids' => []

            ],
            'scheduled' => [
                'label' => 'По графику',
                'color' => '#c1fac1',
                'ids' => []
            ]
        );
        foreach ($items_in_work AS $one) {
            $order_date = new DateTime($one->date_time ? $one->date_time : $one->order->date_time);
            $delivery_days = $one->delivery_days;

            if ($one->supplier->order_to) {
                $order_to = str_replace('.', ':', $one->supplier->order_to);
                if ($order_date->format('H:i') < date('H:i', strtotime($order_to))) {
                    $delivery_days--;
                }
            }

            $order_date->modify('+' . $delivery_days . 'days');
            $expected = clone $order_date->setTime(19,00);
            $last_day = clone $order_date->setTime(00,00);
            $now = new DateTime();


            if ($now > $last_day AND $now < $expected) {
                $key = 'today';
            } elseif ($now > $expected) {
                $key = 'expired';
            } else {
                $key = 'scheduled';
            }
            $in_work[$key]['ids'][] = $one->id;
        }

        $filters['in_work'] = $in_work;

        $penalties = ORM::factory('Penalty')->where('user_id', 'IS NOT', NULL);

        if(!empty($_GET['date_from'])) {
            $filters['date_from'] = $_GET['date_from'];
//            $date_from = new DateTime($filters['date_from']);
//            $costs = $costs->and_where('date', '>=', $date_from->format('Y-m-d'));
            $penalties = $penalties->and_where('date', '>=', date('Y-m-d 00:00:00', strtotime($filters['date_from'])));
        }
        if(!empty($_GET['date_to'])) {
            $filters['date_to'] = $_GET['date_to'];
            $penalties = $penalties->and_where('date', '<=', date('Y-m-d 23:59:59', strtotime($filters['date_to'])));
        }

        if(!empty($_GET['manager_id'])) {
            $filters['manager_id'] = $_GET['manager_id'];
            $penalties = $penalties->and_where('user_id', '=', $filters['manager_id']);
        }

        $penalties = $penalties->find_all()->as_array();


        $user_penalty = array();
        foreach ($penalties AS $one) {
            $user_penalty[$one->user_id][] = $one;
        }


        $orderitems = $orderitems->order_by($order_by['column'], $order_by['direction'])->find_all()->as_array();

        $salary_exclude_states = Model_Services::disableStates;
        $salary = array();
        foreach($orderitems as $orderitem)
        {
            if(in_array($orderitem->state->id, $salary_exclude_states)) continue;

            $total['purchase'] += $orderitem->purchase_per_unit*$orderitem->amount;
            $total['sale'] += $orderitem->sale_per_unit*$orderitem->amount;
            $total['delivery'] += $orderitem->delivery_price;

            $all = $orderitem->sale_per_unit * $orderitem->amount - $orderitem->purchase_per_unit * $orderitem->amount - $orderitem->delivery_price;
            foreach(ORM::factory('Salary')->get_salary_manager_id($orderitem->order->manager_id) as $s) {

                if(!ORM::factory('Permission')->checkPermission('orders_show_only_my_salary')) {
                    if($s->to_id != Auth::instance()->get_user()->id) continue;
                }
                if($s->user_to->show_salary_only_me) {
                    if($s->user_to->id != Auth::instance()->get_user()->id) continue;
                }
                $orderitem->salary_arr[$s->to_id] = array();
                $orderitem->salary_arr[$s->to_id]['name'] = $s->user_to->name;
                $orderitem->salary_arr[$s->to_id]['dont_show_salary'] = $s->user_to->dont_show_salary;
                $orderitem->salary_arr[$s->to_id]['value'] = round($all * ($s->percentage / 100), 0);

                if(!isset($salary[$s->to_id])) {
                    $salary[$s->to_id] = array();
                    $salary[$s->to_id]['name'] = $s->user_to->name." ".$s->user_to->surname;
                    $salary[$s->to_id]['dont_show_salary'] = $s->user_to->dont_show_salary;
                    $salary[$s->to_id]['value'] = 0;
                }
                if($orderitem->salary == 0) $salary[$s->to_id]['value'] += $orderitem->salary_arr[$s->to_id]['value'];
            }
        }

        $orderitems = array_slice($orderitems, $pagination->offset, $pagination->items_per_page);

        $clients_on_page = array();
        foreach($orderitems as $orderitem) {
            $client = ORM::factory('Client')->where('id', '=', $orderitem->order->client_id)->find();
            if(!isset($clients_on_page[$client->id])) {
                $clients_on_page[$client->id] = $client->get_user_balance();
            }
            $orderitem->order->client = $client;
        }

        $managers = array('' => '---');

        foreach(ORM::factory('User')->where('status', '=', 1)->find_all()->as_array() as $user) {
            $managers[$user->id] = $user->surname;
        }

        $suppliers = array('' => '---');

        foreach(ORM::factory('Supplier')->find_all()->as_array() as $supplier) {
            $suppliers[$supplier->id] = $supplier->name;
        }

        $states = array('' => '---');

        foreach(ORM::factory('State')->find_all()->as_array() as $state) {
            $states[$state->id] = $state->name;
        }

        $this->template->scripts[] = 'bootstrap.validate';
        $this->template->scripts[] = 'bootstrap.validate.ru';
        $this->template->scripts[] = 'bootstrap-formhelpers-phone.format';
        $this->template->scripts[] = 'bootstrap-formhelpers-phone';
        $this->template->scripts[] = 'jquery-ui-1.10.4.custom.min';
        $this->template->scripts[] = 'jquery.jeditable';
        $this->template->scripts[] = 'common/orders_list_items';

    }

    public function action_save_order_position_comment()
    {
        $order_item_id = $this->request->query('order_item_id');
        $author_id = Auth::instance()->get_user()->id;
        $commentText = $this->request->query('comment');

        $comment = ORM::factory('Orderitemcomment');
        $comment->order_item_id = $order_item_id;
        $comment->author_id = $author_id;
        $comment->comment = $commentText;
        $comment->created_date = date("Y-m-d H:i:s");
        $comment->save();



        echo json_encode(array('created_date' => $comment->created_date, 'comment' => $commentText, 'author_name' => sprintf('%s %s', $comment->author->name, $comment->author->surname))); die;
    }

    public function action_items()
    {
        
        if(!ORM::factory('Permission')->checkPermission('manage_orders')) Controller::redirect('admin');
        if (!is_null($this->request->query('delitem'))) {
            $this->remove_item();
            Controller::redirect(Helper_Url::createUrl());
        }
        $this->template->title = 'Позиции заказа(ов)';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $id = $this->request->param('id');
        $this->template->content = View::factory('admin/orders/items_list')
            ->bind('filters', $filters)
            ->bind('managers', $managers)
            ->bind('states', $states)
            ->bind('suppliers', $suppliers)
            ->bind('order_id', $order_id)
            ->bind('total', $total)
            ->bind('order_details', $order_details)
            ->bind('data', $data)
            ->bind('message', $message)
            ->bind('pagination', $pagination)
            ->bind('orderitems', $orderitems)
            ->bind('orders_to_move', $orders_to_move)
            ->bind('totalCost', $totalCost)
            ->bind('totalCostAll', $totalCostAll)
            ->bind('salary', $salary)
            ->bind('penalty', $user_penalty)
            ->bind('penalty2', $user_penalty2)
            ->bind('order_by', $order_by)
            ->bind('new_result_group_by', $new_result_group_by)
            ->bind('button_status', $button_status)
            ->bind('orderitem_tm_array', $orderitem_tm_array)
            ->bind('massagePermission', $massagePermission)
            ->bind('orderitem_supplier', $orderitem_supplier)
            ->bind('created_orders_supplier', $created_orders_supplier)
            ->bind('clients_on_page', $clients_on_page);


        $orders_supplier = ORM::factory('SupplierOrder')->find_all()->as_array();

        $suppliers = ORM::factory('Supplier')->where('dont_show', '=', 0)->find_all()->as_array();
        $created_orders_supplier = [];

        $costs_personal = ORM::factory('CostsPersonal');
        $costs_personal->reset(FALSE);

        $costs_all = ORM::factory('Costs')->where('status_user', '=', '1');
        $costs_all->reset(FALSE);

        foreach ($suppliers as $supplier)
        {
            $created_orders_supplier[$supplier->id] = [];
            $created_orders_supplier[$supplier->id]['----'] = '-----';
            foreach ($supplier->orders->find_all()->as_array() as $order)
            {
                $created_orders_supplier[$supplier->id][$order->id] = $order->order_supplier ."  (".$supplier->name.")";
            }
        }

        if(!isset($_GET['date_from']))
        {
            $_GET['date_from'] = '01.01.2018';
            $filters['date_from'] = $_GET['date_from'];
        }

        $new_result_group_by = [];
        $order_tm = ORM::factory('OrderEpartsTm')->find_all()->as_array();
        $orderitem_tm_array = array();
        foreach ($order_tm as $key=>$value)
        {
            $orderitem_tm_array[] = $value->order_id;
        }

        $order_supplier = ORM::factory('OrderitemToSupplier')->find_all()->as_array();
        $orderitem_supplier = array();
        foreach ($order_supplier as $key=>$value)
        {
            $orderitem_supplier[] = $value->orderitem_id;
        }

        if (!empty($id)) {
            DB::update('client_payments')
                ->set([
                    'manager_got_acquainted' => 1
                ])
                ->where('order_id', '=', $id)
                ->execute();
        }

        $total = array();
        $total['purchase'] = 0;
        $total['sale'] = 0;
        $total['delivery'] = 0;
        $button_status = 0;
        $pagination = "";

        $orderitems = ORM::factory('Orderitem')->with('order')->with('order:client');
        $orderitems->reset(FALSE);
//Не показивает позиции заказа пока нет разрешение на показа по менеджеру
        if(ORM::factory('Permission')->checkPermission('show_only_own_orders')) {
            $orderitems->and_where('order.manager_id', '=', Auth::instance()->get_user()->id);
            $massagePermission="У вас нет разрешения";
        }

        if(!empty($id)) {
            $orderitems = $orderitems->and_where('order_id', '=', $id);
            $order_id = $id;

            $order = ORM::factory('order')->where('id', '=', $id)->find();
            $order_details = $order->client->get_user_balance();

            $order_details['order'] = $order;

            $partialPayment = (int) $this->request->post('partial_payment');
            if (!empty($partialPayment)) {
                $order->set('partial_payment', $partialPayment);
                $order->save();
            }

            $data['comment_text'] = "За заказ №".$order_details['order']->get_order_number();
            if (HTTP_Request::POST == $this->request->method()) {
                $clientpayment = ORM::factory('ClientPayment');
                $clientpayment->values($this->request->post(), array(
                    'value',
                    'comment_text',
                ));

                $clientpayment->set('date_time', date('Y-m-d H:i:s'));
                $clientpayment->set('client_id', $order_details['order']->client_id);
                $clientpayment->set('order_id', $order_details['order']->id);
                $clientpayment->set('user_id', Auth::instance()->get_user()->id);
                if(isset($this->request->post()['type'])){
                    if($this->request->post()['type'] == 2)
                    {
                        $clientpayment->set('type', 1);
                        $card = ORM::factory('Card');
                        $card->set('date_time', date('Y-m-d H:i:s'));
                        $card->set('user_id', Auth::instance()->get_user()->id);
                        $card->set('comment', $this->request->post()['comment_text']);
                        $card->set('value', $this->request->post()['value']);
                        $card->save();
                    }
                }
                $clientpayment->save();

                $message = "Проплата добавлена";
                Controller::redirect(URL::base().$this->request->uri().URL::query());
            }

            $order_details['debt_in_order'] = 0;

            $order_disallow = Model_Services::disableStates;

            foreach($order_details['order']->orderitems->find_all()->as_array() as $oi) {
                if(in_array($oi->state->id, $order_disallow)) continue;
                $order_details['debt_in_order'] += $oi->sale_per_unit*$oi->amount;
            }

            $orders_tmp = ORM::factory('Order')->where('archive', '=', '0');
            if(ORM::factory('Permission')->checkPermission('show_only_own_orders')) $orders_tmp->and_where('manager_id', '=', Auth::instance()->get_user()->id);
            $orders_tmp->and_where('client_id', '=', $order_details['order']->client_id);
            $orders_tmp = $orders_tmp->order_by("date_time", "desc")->find_all()->as_array();

            $orders_to_move = array('add_new' => 'Добавить новый');

            foreach($orders_tmp as $order) {
                $orders_to_move[$order->id] = $order->get_order_number()." (".$order->client->name." ".$order->client->surname.")";
            }
            $pagination=false;
        }
        else {
            if (empty($_GET['order_id']) AND empty($_GET['client']) AND empty($_GET['client_name']) AND empty($_GET['phone']) AND ORM::factory('Permission')->checkRole('manager')) {
                $orderitems->and_where('order.manager_id', '=', Auth::instance()->get_user()->id);
            }

            if(!empty($_GET['date_from'])) {
                $filters['date_from'] = $_GET['date_from'];
                $orderitems = $orderitems->and_where('order.date_time', '>=', date('Y-m-d 00:00:00', strtotime($filters['date_from'])));
                $costs_personal = $costs_personal->and_where('created', '>=', date('Y-m-d 00:00:00', strtotime($filters['date_from'])));
                $costs_all = $costs_all->and_where('created', '>=', date('Y-m-d 00:00:00', strtotime($filters['date_from'])));
            }
            if(!empty($_GET['date_to'])) {
                $filters['date_to'] = $_GET['date_to'];
                $orderitems = $orderitems->and_where('order.date_time', '<=', date('Y-m-d 23:59:59', strtotime($filters['date_to'])));
                $costs_personal = $costs_personal->and_where('created', '<=', date('Y-m-d 23:59:59', strtotime($filters['date_to'])));
                $costs_all = $costs_all->and_where('created', '<=', date('Y-m-d 23:59:59', strtotime($filters['date_to'])));
            }
            if(!empty($_GET['article'])) {
                $filters['article'] = $_GET['article'];
                $orderitems = $orderitems->and_where('article', 'LIKE', '%'.trim($filters['article']).'%');
            }
            if(!empty($_GET['manager_id'])) {
                $filters['manager_id'] = $_GET['manager_id'];
                $orderitems = $orderitems->and_where('order.manager_id', '=', $filters['manager_id']);
                $button_status = 1;
            }
            if(!empty($_GET['state_id'])) {
                $filters['state_id'] = $_GET['state_id'];
                $orderitems = $orderitems->and_where('state_id', '=', $filters['state_id']);
                $group_by_suppliers = "SELECT COUNT(orderitems.id) as count, suppliers.`name`, suppliers.order_to, suppliers.id FROM orderitems 
                INNER JOIN suppliers ON orderitems.supplier_id = suppliers.id
                WHERE orderitems.state_id = ".$filters['state_id']."
                GROUP BY orderitems.supplier_id";
                $result_group_by = DB::query(Database::SELECT,$group_by_suppliers)->execute('tecdoc')->as_array();
                foreach ($result_group_by as $result_supp)
                {
                    $new_result_group_by[$result_supp['id']] = [$result_supp['name'], $result_supp['count'], $result_supp['order_to'] ];
                }
            }
            if (isset($_GET['confirmed'])) {
                $filters['confirmed'] = $_GET['confirmed'];
                $orderitems = $orderitems->and_where('confirmed', '=', $filters['confirmed']);
            }
            if(!empty($_GET['supplier_id'])) {
                $filters['supplier_id'] = $_GET['supplier_id'];
                $orderitems = $orderitems->and_where('supplier_id', '=', $filters['supplier_id']);
            }
            if(!empty($_GET['order_id'])) {
                $filters['order_id'] = $_GET['order_id'];
                $orderitems = $orderitems->and_where('order_id', '=', intval($filters['order_id']));
            }
            if(!empty($_GET['client'])) {
                $filters['client'] = $_GET['client'];
                $orderitems = $orderitems->and_where('order:client.surname', 'LIKE', "%".$filters['client']."%");
            }
            if(!empty($_GET['client_name'])) {
                $filters['client_name'] = $_GET['client_name'];
                $orderitems = $orderitems->and_where('order:client.name', 'LIKE', "%".$filters['client_name']."%");
            }
            if(!empty($_GET['phone'])) {
                $filters['phone'] = $_GET['phone'];
                $orderitems = $orderitems->and_where('order:client.phone', 'LIKE', "%".$filters['phone']."%");
            }
            if(!empty($_GET['archive']))
            {
                $filters['archive'] = $_GET['archive'];
                if($filters['archive'] != 'all') $orderitems = $orderitems->and_where('order.archive', '=', $filters['archive']);
            } else {
                $orderitems = $orderitems->and_where('order.archive', '=', 0);
                $filters['archive'] = 0;
            }
            if(isset($_GET['salary']) && ($_GET['salary'] == 0 || $_GET['salary'] == 1))
            {
//                print_r($_GET['salary']); exit();
                $filters['salary'] = $_GET['salary'];
                if($filters['salary'] != 'all')$orderitems = $orderitems->and_where('orderitem.salary', '=', $filters['salary']);
            }
            else
            {
//                exit();
//                $orderitems = $orderitems->and_where('orderitems.salary', '=', 0);
                $filters['salary'] = 'all';
            }
            if(!empty($_GET['ids'])) {
                $orderitems = $orderitems->and_where('orderitem.id', 'IN', explode(',', $_GET['ids']));

                $explode_id =  $_GET['ids'];

                $group_by_suppliers = "SELECT COUNT(orderitems.id) as count, suppliers.`name`, suppliers.id, GROUP_CONCAT(orderitems.id ORDER BY orderitems.id ASC SEPARATOR ',' ) as orderitems_id 
                FROM orderitems
                INNER JOIN suppliers ON orderitems.supplier_id = suppliers.id
                WHERE orderitems.id IN (".$explode_id.")
                GROUP BY orderitems.supplier_id";
                $result_group_by = DB::query(Database::SELECT,$group_by_suppliers)->execute('tecdoc')->as_array();
                foreach ($result_group_by as $result_supp)
                {
                    $new_result_group_by[$result_supp['id']] = [$result_supp['name'], $result_supp['count'], $result_supp['orderitems_id'] ];
                }
            }
            $count = $orderitems->count_all();

            $pagination = Pagination::factory(array('total_items' => $count))->route_params(array(
                'controller' =>  'orders',
                'action' =>  'items'
            ));
        }

        $costs_personal = $costs_personal->and_where('arhive', '=', 0)->find_all()->as_array();
        $costs_all = $costs_all->and_where('arhive', '=', 0)->find_all()->as_array();

        $totalCost = 0;
        foreach ($costs_personal as $cost_personal)
        {
            $totalCost += $cost_personal->amount;
        }

        $totalCostAll = 0;
        foreach ($costs_all as $cost_all)
        {
            $totalCostAll += $cost_all->amount;
        }


        if(!empty($_GET['order_by'])) {
            $order_by['column'] = $_GET['order_by'];
        } else {
            $order_by['column'] = "order.date_time";
        }
        if(!empty($_GET['order_direction'])) {
            $order_by['direction'] = $_GET['order_direction'];
        } else {
            $order_by['direction'] = "desc";
        }

        $items_in_work = clone $orderitems;
        $items_in_work = $items_in_work->and_where_open()->where('state_id', '=', 2)->or_where('state_id', '=', 8) ->and_where_close()->find_all()->as_array();

        $in_work = array(
            'expired' => [
                'label' => 'Просроченные',
                'color' => '#ff0000',
                'ids' => []
            ],
            'today' => [
                'label' => 'Сегодня',
                'color' => '#fbff00',
                'ids' => []

            ],
            'scheduled' => [
                'label' => 'По графику',
                'color' => '#c1fac1',
                'ids' => []
            ]
        );
        foreach ($items_in_work AS $one) {
            $order_date = new DateTime($one->date_time ? $one->date_time : $one->order->date_time);
            $delivery_days = $one->delivery_days;

            if ($one->supplier->order_to) {
                $order_to = str_replace('.', ':', $one->supplier->order_to);
                if ($order_date->format('H:i') < date('H:i', strtotime($order_to))) {
                    $delivery_days--;
                }
            }

            $order_date->modify('+' . $delivery_days . 'days');
            $expected = clone $order_date->setTime(19,00);
            $last_day = clone $order_date->setTime(00,00);
            $now = new DateTime();

            if ($now > $last_day AND $now < $expected) {
                $key = 'today';
            } elseif ($now > $expected) {
                $key = 'expired';
            } else {
                $key = 'scheduled';
            }
            $in_work[$key]['ids'][] = $one->id;
        }

        $filters['in_work'] = $in_work;

        $items_in_work_return = clone $orderitems;
        $items_in_work_return = $items_in_work_return->where('state_id', '=', 13)->find_all()->as_array();

        $in_work_return = array(
            'expired_return' => [
                'label' => 'Срок возврата просрочен',
                'color' => '#ff0000',
                'ids' => []
            ],
            'today_return' => [
                'label' => 'Срок возврата заканчивается',
                'color' => '#fbff00',
                'ids' => []

            ],
            'scheduled_return' => [
                'label' => 'Срок возврата в норме',
                'color' => '#c1fac1',
                'ids' => []
            ]
        );
        foreach ($items_in_work_return AS $one) {
            $order_date_return_max = new DateTime($one->date_time ? $one->date_time : $one->order->date_time);
            $order_date_return_normal = clone $order_date_return_max;
            $order_date_return_normal->add(new DateInterval('P10D'));
            $order_date_return_max->add(new DateInterval('P14D'));

            $today = new DateTime("now");

            if ($today < $order_date_return_normal) {
                $key = 'scheduled_return';
            } elseif($today >= $order_date_return_normal AND $today <= $order_date_return_max){
                $key = 'today_return';
            }
            else
            {
                $key = 'expired_return';
            }

            $in_work_return[$key]['ids'][] = $one->id;

        }

        $filters['in_work_return'] = $in_work_return;


        $penalties = ORM::factory('Penalty')->where('user_id', 'IS NOT', NULL)->and_where('status', '=', '0');
        $penalties2 = ORM::factory('Penalty')->where('user_id', 'IS NOT', NULL);

        if(!empty($_GET['date_from'])) {
            $filters['date_from'] = $_GET['date_from'];
            $penalties = $penalties->and_where('date', '>=', date('Y-m-d 00:00:00', strtotime($filters['date_from'])));
            $penalties2 = $penalties2->and_where('date', '>=', date('Y-m-d 00:00:00', strtotime($filters['date_from'])));
        }
        if(!empty($_GET['date_to'])) {
            $filters['date_to'] = $_GET['date_to'];
            $penalties = $penalties->and_where('date', '<=', date('Y-m-d 23:59:59', strtotime($filters['date_to'])));
            $penalties2 = $penalties2->and_where('date', '<=', date('Y-m-d 23:59:59', strtotime($filters['date_to'])));
        }

        if(!empty($_GET['manager_id'])) {
            $filters['manager_id'] = $_GET['manager_id'];
            $penalties = $penalties->and_where('user_id', '=', $filters['manager_id']);
            $penalties2 = $penalties2->and_where('user_id', '=', $filters['manager_id']);
        }

        $penalties = $penalties->find_all()->as_array();
        $penalties2 = $penalties2->find_all()->as_array();


        $user_penalty = array();
        foreach ($penalties AS $one) {
            $user_penalty[$one->user_id][] = $one;
        }

        $user_penalty2 = array();
        foreach ($penalties2 AS $one2) {
            $user_penalty2[$one2->user_id][] = $one2;
        }


        $orderitems = $orderitems->order_by($order_by['column'], $order_by['direction'])->find_all()->as_array();

        if (!empty($_GET['status'])) {
            foreach ($orderitems as $user_penalty=> $row_penalty)
            {
                $row_penalty->salary = 1;
                $row_penalty->save();
                echo $row_penalty->salary;
            }
            //var_dump($orderitems);

            $url_previous = $_SERVER['HTTP_REFERER'];
            Controller::redirect($url_previous);
        }

        $salary_exclude_states = $salary_exclude_states = Model_Services::disableStates;
        $salary = array();

        foreach($orderitems as $orderitem) {
            if(in_array($orderitem->state->id, $salary_exclude_states)) continue;

            $total['purchase'] += $orderitem->purchase_per_unit*$orderitem->amount;
            $total['sale'] += $orderitem->sale_per_unit*$orderitem->amount;
            $total['delivery'] += $orderitem->delivery_price;

            $all = $orderitem->sale_per_unit * $orderitem->amount - $orderitem->purchase_per_unit * $orderitem->amount - $orderitem->delivery_price;
            foreach(ORM::factory('Salary')->get_salary_manager_id($orderitem->order->manager_id) as $s) {

                if(!ORM::factory('Permission')->checkPermission('orders_show_only_my_salary')) {
                    if($s->to_id != Auth::instance()->get_user()->id) continue;
                }
                if($s->user_to->show_salary_only_me) {
                    if($s->user_to->id != Auth::instance()->get_user()->id) continue;
                }
                $orderitem->salary_arr[$s->to_id] = array();
                $orderitem->salary_arr[$s->to_id]['name'] = $s->user_to->name;
                $orderitem->salary_arr[$s->to_id]['dont_show_salary'] = $s->user_to->dont_show_salary;
                $orderitem->salary_arr[$s->to_id]['value'] = round($all * ($s->percentage / 100), 0);
                $orderitem->salary_arr[$s->to_id]['value2'] = round($all * ($s->percentage / 100), 0);

                if(!isset($salary[$s->to_id])) {
                    $salary[$s->to_id] = array();
                    $salary[$s->to_id]['name'] = $s->user_to->name." ".$s->user_to->surname;
                    $salary[$s->to_id]['dont_show_salary'] = $s->user_to->dont_show_salary;
                    $salary[$s->to_id]['value'] = 0;
                    $salary[$s->to_id]['value2'] = 0;
                }
                if($orderitem->salary == 0)
                    $salary[$s->to_id]['value'] += $orderitem->salary_arr[$s->to_id]['value'];

                $salary[$s->to_id]['value2'] += $orderitem->salary_arr[$s->to_id]['value2'];
                $salary[$s->to_id]['circulation'] += $orderitem->sale_per_unit*$orderitem->amount;
            }
        }

        if(empty($id))
        {
            $orderitems = array_slice($orderitems, $pagination->offset, $pagination->items_per_page);
        }


        $clients_on_page = array();
        foreach($orderitems as $orderitem) {
            $client = ORM::factory('Client')->where('id', '=', $orderitem->order->client_id)->find();
            if(!isset($clients_on_page[$client->id])) {
                $clients_on_page[$client->id] = $client->get_user_balance();
            }
            $orderitem->order->client = $client;
        }

        $managers = array('' => '---');

        foreach(ORM::factory('User')->where('status', '=', 1)->find_all()->as_array() as $user) {
            $managers[$user->id] = $user->surname;
        }

        $suppliers = array('' => '---');

        foreach(ORM::factory('Supplier')->find_all()->as_array() as $supplier) {
            $suppliers[$supplier->id] = $supplier->name;
        }

        $states = array('' => '---');

        foreach(ORM::factory('State')->find_all()->as_array() as $state) {
            $states[$state->id] = $state->name;
        }

        $this->template->scripts[] = 'bootstrap.validate';
        $this->template->scripts[] = 'bootstrap.validate.ru';
        $this->template->scripts[] = 'bootstrap-formhelpers-phone.format';
        $this->template->scripts[] = 'bootstrap-formhelpers-phone';
        $this->template->scripts[] = 'jquery-ui-1.10.4.custom.min';
        $this->template->scripts[] = 'jquery.jeditable';
        $this->template->scripts[] = 'common/orders_list_items';
        $this->template->scripts[] = 'common/nova_poshta_load_nva';

//        $this->template->scripts[] = 'bootstrap-tooltip';
//        $this->template->scripts[] = 'bootstrap-popover';
//        $this->template->scripts[] = 'common/find_list';

    }

    public function action_create_express()
    {
        $this->template->title = 'Создать Експресс';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $id = $this->request->param('id');
        $this->template->content = View::factory('admin/orders/item_express')
            ->bind('orderitem', $orderitem)
            ->bind('platezh', $platezh)
            ->bind('area', $area_form)
            ->bind('city', $city_form)
            ->bind('warehouse', $warehouse_form)
            ->bind('warehouse_from', $warehouse_from)
            ->bind('summ', $summ);

        $np = new NovaPoshtaApi2(
            '6a8ca3163492bb644bc33dde1265f6cd',
            'ru',
            FALSE,
            'curl'
        );

        $orderitem = ORM::factory('Orderitem')->where('id', '=', $id)->find();

        $data = array();
        $data['orderitems'] = array();
        $data['orderitems'] = $orderitem;

        if(count($data['orderitems']) > 0) {

            $data['order_number'] = (string)$data['orderitems']->order->get_order_number();
            $data['client_obj'] = $data['orderitems']->order->client;
        }

        $balance = $data['client_obj']->get_user_balance();
        $dolg = - $balance['active_balance'];
        $summ = 0;
        $platezh = 0;

        $summ += round($data['orderitems']->sale_per_unit*$data['orderitems']->amount, 0);

        $platezh = $summ;
        if($dolg < $platezh) {
            if($dolg <= 0) {
                $platezh = 0;
            } else {
                $platezh = $dolg;
            }
        }


        $area_form = [];
        $city_form = [];
        $warehouse_form = [];
        $area_form[$orderitem->order->area->ref] =  $orderitem->order->area->name;
        $city_form[$orderitem->order->city->ref] = $orderitem->order->city->name;
        $warehouse_form[$orderitem->order->warehouse->ref] = $orderitem->order->warehouse->name;

        $warehouse_from = [];

        $warehouse_from['47402e8b-e1c2-11e3-8c4a-0050568002cf'] = 'Отделение №96 (до 30 кг на одно место): просп. Комарова, 38а';
        $warehouse_from['7b422fc5-e1b8-11e3-8c4a-0050568002cf'] = 'ул. Кричевского, 19, м. Житомирская (Святошин)';
        $warehouse_from['1ec09d8b-e1c2-11e3-8c4a-0050568002cf'] = 'Отделение №6: ул. Николая Василенко, 2 (метро Берестейская)';
        $warehouse_from['10dc7a89-ba83-11e7-becf-005056881c6b'] = 'Вацлава Гавели (Лепсе Івана) бульвар 18в';



        $summ = round($summ, 0);
        $platezh = round($platezh, 0);

        $this->template->scripts[] = 'common/nova_poshta_load_nva';
    }

    public function action_create_express_for_order()
    {
        $this->template->title = 'Создать Експресс';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $id = $this->request->param('id');
        $this->template->content = View::factory('admin/orders/item_express_order')
            ->bind('order', $order)
            ->bind('platezh', $platezh)
            ->bind('area', $area_form)
            ->bind('city', $city_form)
            ->bind('warehouse', $warehouse_form)
            ->bind('warehouse_from', $warehouse_from)
            ->bind('summ', $summ);

        $np = new NovaPoshtaApi2(
            '6a8ca3163492bb644bc33dde1265f6cd',
            'ru',
            FALSE,
            'curl'
        );


        $data = [];

        $order = ORM::factory('Order')->where('id', '=', $id)->find();
        $data['orderitems'] = ORM::factory('Orderitem')->where('order_id', '=', $id)->and_where_open()->where('state_id', '=', 3)->or_where('state_id', '=', 37)->and_where_close()->find_all()->as_array();

        if(count($data['orderitems']) > 0) {

            $data['order_number'] = (string)$data['orderitems'][0]->order->get_order_number();
            $data['client_obj'] = $data['orderitems'][0]->order->client;
        }

        $warehouse_from = [];

        $warehouse_from['47402e8b-e1c2-11e3-8c4a-0050568002cf'] = 'Отделение №96 (до 30 кг на одно место): просп. Комарова, 38а';
        $warehouse_from['7b422fc5-e1b8-11e3-8c4a-0050568002cf'] = 'ул. Кричевского, 19, м. Житомирская (Святошин)';
        $warehouse_from['1ec09d8b-e1c2-11e3-8c4a-0050568002cf'] = 'Отделение №6: ул. Николая Василенко, 2 (метро Берестейская)';


        $balance = $data['client_obj']->get_user_balance();
        $dolg = -$balance['active_balance'];
        $summ = 0;
        $platezh = 0;

        foreach($data['orderitems'] as $orderitem) {
            $summ += round($orderitem->sale_per_unit*$orderitem->amount, 0);
        }

        $platezh = $summ;
        if($dolg < $platezh) {
            if($dolg <= 0) {
                $platezh = 0;
            } else {
                $platezh = $dolg;
            }
        }

        $area_form = [];
        $city_form = [];
        $warehouse_form = [];
        $area_form[$order->area->ref] =  $order->area->name;
        $city_form[$order->city->ref] = $order->city->name;
        $warehouse_form[$order->warehouse->ref] = $order->warehouse->name;

        $summ = round($summ, 0);
        $platezh = round($platezh, 0);

        $this->template->scripts[] = 'common/nova_poshta_load_nva';

    }

    public function action_items_warning()
    {
        $this->template->title = 'Позиции заказа(ов), которые нужно заказать';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
        $id = $this->request->param('id');
        $this->template->content = View::factory('admin/orders/item_list_warning');

        $this->template->scripts[] = 'bootstrap.validate';
        $this->template->scripts[] = 'bootstrap.validate.ru';
        $this->template->scripts[] = 'bootstrap-formhelpers-phone.format';
        $this->template->scripts[] = 'bootstrap-formhelpers-phone';
        $this->template->scripts[] = 'jquery-ui-1.10.4.custom.min';
        $this->template->scripts[] = 'jquery.jeditable';
        $this->template->scripts[] = 'common/orders_list_items';
    }

   /**
     * Send order or item with state_id = 1 to purchase
     */
    public function action_parts_image() {
        $items = ORM::factory('Part')->where('images', '=', NULL)->and_where('tecdoc_id', 'IS NOT', NULL)->and_where('tecdoc_id', '<>', '0')->find_all()->as_array();

        foreach ($items as $item=>$key)
        {
            $id = $key->tecdoc_id;
            echo $key->tecdoc_id."<br>";
            if ($id) {
                $query = "SELECT image FROM tof_graphics 
                WHERE article_id = \'{$id}\' ";
                $result = DB::query(Database::SELECT,$query)->execute('tecdoc')->as_array();
                if(!empty($result))
                {
                    echo $result[0];
                    echo "<br><br>";
                    $key->tecdoc_id = $result[0];
                    $key->save();
                }
                else
                {
                    continue;
                }
            }
        }

        exit();
    }
    public function action_to_purchase() {
        $this->auto_render = FALSE;
        $this->is_ajax = TRUE;
        $order_id = Request::current()->post('order_id');
        $item_id = Request::current()->post('item_id');

//        $order_id = 20713;
//        $item_id = '';

        $items = $order_id ? ORM::factory('Order')->where('id', '=', $order_id)->find()->orderitems->find_all()->as_array() : (array(ORM::factory('Orderitem')->where('id', '=', $item_id)->find()));

        foreach ($items AS $key => $item) {

            if ($item->state_id == 1) {
                $item->state_id = 16;
                $item->date_time = date('Y-m-d H:i:s');
                $item->save();

                $log = ORM::factory('OrderitemLog');

                $log
                    ->set('orderitem_id', $item->id)
                    ->set('state_id', 16)
                    ->set('date_time', date('Y-m-d H:i:s'))
                    ->set('user_id', Auth::instance()->get_user()->id)
                    ->save();
            }
        }

        exit();
    }

    public function action_edit_item_state() {
        $this->auto_render = FALSE;
        $this->is_ajax = TRUE;

        $id = str_replace("state_", "" , $_GET['id']);

        $states = array();
        $states['selected'] = ORM::factory('Orderitem')->where('id', '=', $id)->find()->state->id;
        foreach(ORM::factory('State')->find_all()->as_array() as $state) {
            $states[$state->id] = $state->name;
        }

        if(($states['selected']) == 1)
        {
            unset($states[3], $states[4], $states[5], $states[6], $states[7], $states[8], $states[13], $states[14], $states[15], $states[17], $states[18],
                $states[31], $states[32], $states[33], $states[34], $states[35], $states[36], $states[37], $states[38], $states[41]);
        }
        elseif(($states['selected']) == 2)
        {
            unset($states[1], $states[3], $states[4], $states[5], $states[6], $states[7], $states[13], $states[14], $states[16], $states[17], $states[18], $states[31], $states[32], $states[34], $states[36], $states[37], $states[38], $states[39], $states[41]);
        }
        elseif(($states['selected']) == 3)
        {
            unset($states[1], $states[2], $states[3], $states[4], $states[6], $states[7], $states[8], $states[13], $states[15], $states[16],  $states[31], $states[32], $states[34], $states[35], $states[39]);
        }
        elseif(($states['selected']) == 5)
        {
            unset($states[1], $states[2], $states[3], $states[4], $states[6], $states[7], $states[8], $states[15], $states[16],  $states[31], $states[32], $states[33], $states[35], $states[36], $states[37], $states[39], $states[41]);
        }
        elseif(($states['selected']) == 6)
        {
            unset($states[1], $states[2], $states[3], $states[4], $states[7], $states[13], $states[14], $states[15], $states[16], $states[17], $states[18], $states[34], $states[35], $states[36], $states[37], $states[39]);
        }
        elseif(($states['selected']) == 7)
        {
            unset($states[1], $states[3], $states[4], $states[5], $states[6], $states[8], $states[13], $states[14], $states[16], $states[17], $states[18],
                $states[31], $states[32], $states[33], $states[34], $states[35], $states[36], $states[37], $states[39], $states[41]);
        }
        elseif(($states['selected']) == 8)
        {
            unset($states[1], $states[2], $states[3], $states[5], $states[7], $states[13], $states[14], $states[16], $states[17], $states[18],  $states[34], $states[35], $states[36], $states[37], $states[39]);
        }
        elseif(($states['selected']) == 13)
        {
            unset($states[1], $states[2], $states[3], $states[4], $states[5], $states[6], $states[7], $states[8], $states[15], $states[16],
                $states[31], $states[32], $states[33], $states[34], $states[35], $states[36], $states[37], $states[39], $states[41]);
        }
        elseif(($states['selected']) == 16)
        {
            unset($states[1], $states[3], $states[5], $states[6], $states[8], $states[13], $states[14], $states[17], $states[18],
                $states[31], $states[32], $states[33], $states[34], $states[35], $states[36], $states[37], $states[38], $states[39], $states[41]);
        }
        elseif(($states['selected']) == 18)
        {
            unset($states[1], $states[2], $states[3], $states[4], $states[5], $states[6], $states[7], $states[8], $states[13], $states[15], $states[16],
                $states[31], $states[32], $states[33], $states[34], $states[35], $states[36], $states[37], $states[39], $states[41]);
        }
        elseif(($states['selected']) == 31)
        {
            unset($states[1], $states[2], $states[3], $states[5], $states[8], $states[13], $states[14], $states[16], $states[17], $states[18], $states[33], $states[34], $states[35], $states[36], $states[37], $states[39]);
        }
        elseif(($states['selected']) == 32)
        {
            unset($states[1], $states[2], $states[3], $states[4], $states[6], $states[7], $states[8], $states[13], $states[14], $states[15], $states[16], $states[17], $states[18],
                $states[31], $states[33], $states[34], $states[35], $states[36], $states[37], $states[39]);
        }
        elseif(($states['selected']) == 33)
        {
            unset($states[1], $states[2], $states[3], $states[4], $states[5], $states[6], $states[7], $states[8], $states[14], $states[15], $states[16], $states[18],
                $states[31], $states[32], $states[34], $states[35], $states[36], $states[37], $states[39], $states[41]);
        }
        elseif(($states['selected']) == 34)
        {
            unset($states[1], $states[2], $states[3], $states[4], $states[6], $states[7], $states[8], $states[14], $states[15], $states[16],
                $states[31], $states[32], $states[33], $states[35], $states[36], $states[37], $states[38], $states[39], $states[41]);
        }
        elseif(($states['selected']) == 35)
        {
            unset($states[1], $states[2], $states[3], $states[4], $states[5], $states[6], $states[7], $states[8], $states[13], $states[14], $states[16], $states[17], $states[18],
                $states[31], $states[32], $states[33], $states[34], $states[36], $states[37], $states[39], $states[41]);
        }

        print json_encode($states);
    }

    protected function changeAmountInOrderItems($order_date,$order_item_amount,$artilce,$brand ,$supplier_id ,$sign )
    {

        if($supplier_id == 38 ){
            return false;
        }
        if($order_date != date('Y-m-d')){
            return false;
        }

        //find amount for current  product
        $sql = 'SELECT priceitems.id , priceitems.amount , parts.id AS part_id FROM
                priceitems  LEFT JOIN  parts
                ON (priceitems.part_id = parts.id )
                WHERE parts.brand_long = "'. $brand.'"
                AND parts.article_long = "'. $artilce.'"
                AND supplier_id = ' .$supplier_id .' limit 1' ;

        //var_dump($sql );
        $r = DB::query(Database::SELECT,$sql)->execute()->current();


        if($r) {
            $priceitem_amount = $r['amount'];
            $priceitem_id = $r['id'];

            if($sign == '+'){
                if($priceitem_amount == -100) {
                    $priceitem_amount  = 0;
                }
                $new_amount =   $priceitem_amount + $order_item_amount;
            }elseif($sign == '-') {
                $new_amount = $priceitem_amount - $order_item_amount;
                if ($new_amount == 0) {
                    $new_amount = -100;
                }

            }else{

            }
            $priceitem = ORM::factory('Priceitem')->where('id', '=', $priceitem_id)->find();
            $priceitem->amount = $new_amount ;
            $priceitem->save();
        }

    }


    // аякс
    public function action_save_item_state()
    {
        $this->auto_render = FALSE;
        $this->is_ajax = TRUE;

        $state_which_plus_qty_items = Model_Services::disableStates;
        $state_which_minus_qty_items = Model_Services::activeStates;


        $id = str_replace("state_", "" , $_POST['id']);
        $value = $_POST['value'];

        $orderitem = ORM::factory('Orderitem')->where('id', '=', $id)->find();

        if($value == 18 and $orderitem->supplier_id == 38)
        {
            $order_tm_position = ORM::factory('OrderEpartsTm')->where('order_id','=', $orderitem->id)->find();
            $order_tm_position->status = 1;
            $order_tm_position ->save();
        }

        $order_datatime = ORM::factory('Order')->select('date_time')->where('id', '=', $orderitem->order_id)->find()->date_time;

        $order_supplier_id = $orderitem->supplier_id;
        $order_article = $orderitem->article;
        $order_brand = $orderitem->brand;
        $order_amount = $orderitem->amount;
        $order_oldstate = (int)$orderitem->state_id;

        $order_date = date('Y-m-d',strtotime($order_datatime));

        $oldstate = $orderitem->state;

        if(in_array((int)$value ,$state_which_plus_qty_items) and !(in_array($order_oldstate,$state_which_plus_qty_items)) ){
            $this->changeAmountInOrderItems($order_date, $order_amount ,$order_article,$order_brand ,$order_supplier_id,'+');
        }

        if(in_array((int)$value ,$state_which_minus_qty_items) and !(in_array($order_oldstate,$state_which_minus_qty_items)) ){
            $this->changeAmountInOrderItems($order_date , $order_amount ,$order_article,$order_brand ,$order_supplier_id ,'-' );
        }

        if ($oldstate->id == 1 AND $value == 16) {
            $orderitem->date_time = date('Y-m-d H:i:s');
            $orderitem->save();
        }

        if ($oldstate->id != $value) {

            $log = ORM::factory('OrderitemLog');

            $log
                ->set('orderitem_id', $orderitem->id)
                ->set('state_id', $value)
                ->set('date_time', date('Y-m-d H:i:s'))
                ->set('user_id', Auth::instance()->get_user()->id)
                ->save();
        }

        //distribute delivery price if state change
        if ($orderitem->delivery_price > 0 AND  in_array($value, Model_Services::disableStates)){
            $order_date = new DateTime($orderitem->order->date_time);
            $orders = ORM::factory('Order')->where(DB::expr('DATE(date_time)'), '=', $order_date->format('Y-m-d'))->find_all()->as_array();
            if (!empty($orders)) {
                //calculate sum
                $sum = 0;
                foreach ($orders AS $order) {
                    foreach ($order->orderitems->find_all()->as_array() AS $item) {
                        if (!in_array($item->state_id, Model_Services::disableStates) AND $item->id != $orderitem->id)
                            $sum += $item->purchase_per_unit;
                    }
                }
                //distribute cost
                if ($sum != 0) {
                    foreach ($orders AS $order) {
                        foreach ($order->orderitems->find_all()->as_array() AS $item) {
                            $percent = $item->purchase_per_unit / $sum;
                            if (!in_array($item->state_id, Model_Services::disableStates) AND $item->id != $orderitem->id) {
                                $item->delivery_price += round($percent * $orderitem->delivery_price, 2);
                                $item->save();
                            }
                        }
                    }
                    $orderitem->delivery_price = 0;
                }
            }
        }
        ////////////////////////////////////

        $orderitem->state_id = $value;
        $orderitem->save();


        // изменение статуса готового заказа
        Admin::check_ready_order($orderitem->order_id);

        $orderitem = ORM::factory('Orderitem')->where('id', '=', $id)->find();
        $newstate = $orderitem->state;
//        TODO::костиль  -> підправити newstate , бо витягуєтсья два рази з база . строчки 978 , 948
        if($oldstate->id != $newstate->id) {
            $this->send_status_change($orderitem, $oldstate, $newstate);
        }
        print '<img src="'.URL::base().'media/img/states/'.$orderitem->state->img.'" title="'.$orderitem->state->description.'" />'.$orderitem->state->name;
    }

    public function action_edit() {
        if(!ORM::factory('Permission')->checkPermission('manage_orders')) Controller::redirect('admin');

        $id = $this->request->param('id');
        if(empty($id)) Controller::redirect('admin/orders');

        $this->template->content = View::factory('admin/orders/form')
            ->bind('permissions', $permissions)
            ->bind('errors', $errors)
            ->bind('message', $message)
            ->bind('delivery_methods', $delivery_methods)
            ->bind('managers', $managers)
            ->bind('final_area', $final_area)
            ->bind('final_city', $final_city)
            ->bind('final_warehous', $final_warehous)
            ->bind('data', $data);

        $this->template->title = 'Редактировать заказ';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $order = ORM::factory('Order')->where('id', '=', $id)->find();

        $final_warehous['-----'] = '-----';
        $final_city['-----'] = '-----';
        $final_area = array('-----' => '-----');
        foreach(ORM::factory('NpAreas')->find_all()->as_array() as $area) {
            $final_area[$area->id] = $area->name;
        }

        if($order->delivery_method_id == 3){

//      ---- select city
            $cities = $order->area->cities->find_all()->as_array();

            foreach ($cities as $city)
            {
                $final_city[$city->id] = $city->name;
            }
//      ---- end

//      ---- select warehouses
            $warehouses = $order->city->warehouses->find_all()->as_array();

            foreach ($warehouses as $warehouse) {
                $final_warehous[$warehouse->id] = $warehouse->name;
            }
//      ---- end
        }

        $data = array();
        $data['id'] = $order->id;
        $data['manager_id'] = $order->manager_id;
        $data['manager_comment'] = $order->manager_comment;
        $data['client_comment'] = $order->client_comment;
        $data['archive'] = $order->archive;
        $data['delivery_method_id'] = $order->delivery_method_id;

        $data['np_area_id'] = $order->np_area_id;
        $data['np_city_id'] = $order->np_city_id;
        $data['np_warehouse_id'] = $order->np_warehouse_id;

        $data['delivery_address'] = $order->delivery_address;
        $data['ttn'] = $order->ttn;

        if (HTTP_Request::POST == $this->request->method())
        {
            try {
                $values = array(
                    'manager_id',
                    'manager_comment',
                    'client_comment',
                    'delivery_method_id',
                    'delivery_address',
                    'ttn',
                );

                $order->values($this->request->post(), $values);
                $order->archive = !empty($_POST['archive']) && $_POST['archive'] == 1 ? 1 : 0;
                if($_POST['delivery_method_id']==1)
                {
                    $order->state = 3;
                }
                elseif($_POST['delivery_method_id']==6)
                {
                    $order->state = 1;
                }
                else
                {
                    $order->state = 2;
                }
                if(isset($_POST['np_area_id']) && isset($_POST['np_city_id']) && isset($_POST['np_warehouse_id']))
                {
                    $order->np_area_id = $_POST['np_area_id'];
                    $order->np_city_id = $_POST['np_city_id'];
                    $order->np_warehouse_id = $_POST['np_warehouse_id'];
                }
                $order->save();

                // Reset values so form is not sticky
                $_POST = array();

                Controller::redirect('admin/orders'/*/items/'.$order->id*/);
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
        $this->template->scripts[] = 'common/orders_item_form';
        $this->template->scripts[] = 'common/nova_poshta_load_nva_edit_admin';



        $managers = array();

        foreach(ORM::factory('User')->where('status', '=', 1)->find_all()->as_array() as $user) {
            $managers[$user->id] = $user->surname;
        }

        $delivery_methods = array(0 => "---");

        foreach(ORM::factory('DeliveryMethod')->find_all()->as_array() as $deliverymethod) {
            $delivery_methods[$deliverymethod->id] = $deliverymethod->name;
        }
    }

    public function action_send_liqpay_details() {
        if(!ORM::factory('Permission')->checkPermission('manage_orders')) Controller::redirect('admin');

        $id = $this->request->param('id');
        if(empty($id)) Controller::redirect('admin/orders');

        $order = ORM::factory('Order')->where('id', '=', $id)->find();

        try {
            $payLink = Helper_Url::createUrl('/liqpay/order_pay/' . $id);
            $payLink = $this->getShortUrl($payLink);

            $sms_text = "Оплата заказа №".$order->get_order_number()."."
                . "\n" . $payLink
                . "\n(067) 291-18-25";
            Sms::send($sms_text, "Оплата заказа", $order->client->phone);
        } catch (Exception $e) {

        }
        Controller::redirect('admin/orders/items/'.$id);
    }

    public function action_send_details() {
        if(!ORM::factory('Permission')->checkPermission('manage_orders')) Controller::redirect('admin');

        $id = $this->request->param('id');
        if(empty($id)) Controller::redirect('admin/orders');

        $order = ORM::factory('Order')->where('id', '=', $id)->find();

        $mail_tpl = View::factory('email/order_done')
            ->set('order', $order);

        try {
            $email = Email::factory('Заказ оформлен', '')
                ->to($order->client->email)
                ->from('no-reply@eparts.kiev.ua')
                ->message($mail_tpl->render(), 'text/html')
                ->send();

            $sms_text = "Заказ №".$order->get_order_number()." оформлен.";
            $sms_text .= "\nulc.com.ua\noffice@eparts.kiev.ua\n(067) 291-18-25";
            Sms::send($sms_text, "Оформление заказа", $order->client->phone);
        } catch (Exception $e) {

        }
        Controller::redirect('admin/orders/items/'.$id);
    }

    protected function getShortUrl($link)
    {
        $serviceUrl = 'https://www.googleapis.com/urlshortener/v1/url?key=' . self::$googleApiKey;
        $postfields = json_encode([
            'longUrl' => $link
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $serviceUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        $this->_server_response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $response = json_decode($server_output);

        return $response->id;
    }

    private function send_status_change($orderitem, $oldstatus, $newstatus) {
        $email_statuses = array('in_work', 'in_office', 'packing_goods', 'in_delivery', 'withdrawal', 'delivery_time_delay', 'changes_delivery_period', 'not_available', 'unconfirmed_returns');
        $sms_statuses = array('not_available');

        if(in_array($newstatus->text_id, $email_statuses)) {
            $mail_tpl = View::factory('email/item_status_change')
                ->set('orderitem', $orderitem)
                ->set('oldstatus', $oldstatus)
                ->set('newstatus', $newstatus);

            try {
                $email = Email::factory('Изменение статуса', '')
                    ->to($orderitem->order->client->email)
                    ->from('no-reply@eparts.kiev.ua')
                    ->message($mail_tpl->render(), 'text/html')
                    ->send();

            } catch (Exception $e) {

            }
        }

        if(in_array($newstatus->text_id, $sms_statuses)) {
            try {
                $sms_text = "Статус ".$orderitem->brand." ".$orderitem->article." изменен на \"".$newstatus->name."\"";
                $sms_text .= "\nulc.com.ua\nulc.com.ua@gmail.com\n(098) 092-82-08";
                Sms::send($sms_text, "Изменение статуса", $orderitem->order->client->phone);
            } catch (Exception $e) {

            }
        }
    }

    public function action_edit_item() {
        if(!ORM::factory('Permission')->checkPermission('manage_orders')) Controller::redirect('admin');

        $id = $this->request->param('id');
        if(empty($id)) Controller::redirect('admin/orders/items');

        $this->template->content = View::factory('admin/orders/item_form')
            ->bind('permissions', $permissions)
            ->bind('errors', $errors)
            ->bind('message', $message)
            ->bind('states', $states)
            ->bind('suppliers', $suppliers)
            ->bind('currencies', $currencies)
            ->bind('data', $data);

        $this->template->title = 'Редактировать позицию заказа';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';



        $orderitem = ORM::factory('Orderitem')->where('id', '=', $id)->find();
        $value_currency = ORM::factory('Supplier')->where('id', '=', $orderitem->supplier_id)->find();
        $value_currency = $value_currency->currency->ratio;
        $data = array();
        $data['id'] = $orderitem->id;
        $data['name'] = $orderitem->name;
        $data['article'] = $orderitem->article;
        $data['brand'] = $orderitem->brand;
        $data['state_id'] = $orderitem->state_id;
        $data['supplier_id'] = $orderitem->supplier_id;
        $data['amount'] = $orderitem->amount;
        $data['purchase_per_unit'] = $orderitem->purchase_per_unit;
        $data['purchase_per_unit_in_currency'] = $orderitem->purchase_per_unit_in_currency;
        $data['currency_id'] = $orderitem->currency_id;
        $data['sale_per_unit'] = $orderitem->sale_per_unit;
        $data['delivery_price'] = $orderitem->delivery_price;
        $data['delivery_days'] = $orderitem->delivery_days;
        $data['salary'] = $orderitem->salary;
        $data['manager_comment'] = $orderitem->manager_comment;
        $data['currency_id_value'] = $value_currency;

        $oldstate = $orderitem->state;

        if (HTTP_Request::POST == $this->request->method())
        {
            try {
                $values = array(
                    'name',
                    'article',
                    'brand',
                    'state_id',
                    'supplier_id',
                    'amount',
                    'purchase_per_unit',
                    'purchase_per_unit_in_currency',
                    'currency_id',
                    'sale_per_unit',
                    'delivery_price',
                    'manager_comment',
                    'delivery_days',
                );

                $request = $this->request->post();

                if (isset($request['state_id'])) {

                    if ($oldstate->id == 1 AND $request['state_id'] == 16) {
                        $orderitem->date_time = date('Y-m-d H:i:s');
                        $orderitem->save();
                    }

                    if ($oldstate->id != $request['state_id']) {

                        $log = ORM::factory('OrderitemLog');
                        $log
                            ->set('orderitem_id', $orderitem->id)
                            ->set('state_id', $request['state_id'])
                            ->set('date_time', date('Y-m-d H:i:s'))
                            ->set('user_id', Auth::instance()->get_user()->id)
                            ->save();
                    }
                }

                //distribute delivery_price between orders
                if ($orderitem->delivery_price > 0 AND isset ($request['state_id']) AND in_array($request['state_id'], Model_Services::disableStates)){
                    $order_date = new DateTime($orderitem->order->date_time);
                    $orders = ORM::factory('Order')->where(DB::expr('DATE(date_time)'), '=', $order_date->format('Y-m-d'))->find_all()->as_array();
                    if (!empty($orders)) {
                        //calculate sum
                        $sum = 0;
                        foreach ($orders AS $order) {
                            foreach ($order->orderitems->find_all()->as_array() AS $item) {
                                if (!in_array($item->state_id, Model_Services::disableStates) AND $item->id != $orderitem->id)
                                    $sum += $item->purchase_per_unit;
                            }
                        }
                        //distribute cost
                        if ($sum != 0) {
                            foreach ($orders AS $order) {
                                foreach ($order->orderitems->find_all()->as_array() AS $item) {
                                    $percent = $item->purchase_per_unit / $sum;
                                    if (!in_array($item->state_id, Model_Services::disableStates) AND $item->id != $orderitem->id) {
                                        $item->delivery_price += round($percent * $orderitem->delivery_price, 2);
                                        $item->save();
                                    }
                                }
                            }
                            $request['delivery_price'] = 0;
                        }
                    }
                }

                $orderitem->values($request, $values);
                $orderitem->salary = !empty($_POST['salary']) && $_POST['salary'] == 1 ? 1 : 0;
//                var_dump($orderitem);
//                exit();
                $orderitem->save();



                $orderitem = ORM::factory('Orderitem')->where('id', '=', $id)->find();
                $newstate = $orderitem->state;



                if($oldstate->id != $newstate->id) {
                    $this->send_status_change($orderitem, $oldstate, $newstate);
                }

                // Reset values so form is not sticky
                $_POST = array();

                Controller::redirect('admin/orders/items/'.$orderitem->order_id);
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
        $this->template->scripts[] = 'common/orders_item_form';
        $this->template->scripts[] = 'common/edit_item_position';

        $suppliers = array();

        foreach(ORM::factory('Supplier')->find_all()->as_array() as $supplier) {
            $suppliers[$supplier->id] = $supplier->name;
        }

        $currencies = array();

        foreach(ORM::factory('Currency')->find_all()->as_array() as $currency) {
            $currencies[$currency->id] = $currency->name;
        }

        $states = array();

        foreach(ORM::factory('State')->find_all()->as_array() as $state) {
            $states[$state->id] = $state->name;
        }
    }

    public function action_delete() {
        if(!ORM::factory('Permission')->checkPermission('manage_orders')) Controller::redirect('admin');

        $this->template->title = '';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $id = $this->request->param('id');
        if(!empty($id)) {
            $order = ORM::factory('Order')->where('id', '=', $id)->find();
            $order->delete();

            $orderitems = ORM::factory('Orderitem')->where('order_id', '=', $id)->find_all()->as_array();
            foreach($orderitems as $orderitem) {
                $orderitem->delete();
            }
        }

        Controller::redirect('admin/orders');
    }

    public function action_delete_item() {
        if(!ORM::factory('Permission')->checkPermission('manage_orders')) Controller::redirect('admin');

        $this->template->title = '';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $id = $this->request->param('id');
        if(!empty($id)) {
            $orderitem = ORM::factory('Orderitem')->where('id', '=', $id)->find();

            $orderitem->delete();
        }

        Controller::redirect('admin/orders/items');
    }

    public function action_add_by_price_id() {
        if(!ORM::factory('Permission')->checkPermission('manage_orders')) Controller::redirect('admin');

        $this->template->content = View::factory('admin/orders/add_order_step1')
            ->bind('permissions', $permissions)
            ->bind('errors', $errors)
            ->bind('message', $message)
            ->bind('data', $data)
            ->bind('orders', $orders);

        $data = array();
        $data['amount'] = 1;

        $this->template->title = 'Выберите заказ, в который хотите добавить выбранную позицию';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $orders_tmp = ORM::factory('Order')->where('archive', '=', '0');
        if(ORM::factory('Permission')->checkPermission('show_only_own_orders')) $orders_tmp->and_where('manager_id', '=', Auth::instance()->get_user()->id);
        $orders_tmp = $orders_tmp->order_by("date_time", "desc")->find_all()->as_array();

        $orders = array('add_new' => 'Добавить новый');

        foreach($orders_tmp as $order) {
            $orders[$order->id] = $order->get_order_number()." (".$order->client->name." ".$order->client->surname.")";
        }

        if(isset($_GET['discount_id']) && !empty($_GET['discount_id'])) {
            $discount_id = $_GET['discount_id'];
            $discount_str = "&discount_id=".$discount_id;
        } else {
            $discount_id = false;
            $discount_str = "";
        }

        if (HTTP_Request::POST == $this->request->method())
        {
            if(!empty($_POST['order_id'])) {
                if($_POST['order_id'] == 'add_new') {
                    Controller::redirect('admin/orders/select_client?priceitem_id='.$_GET['priceitem_id'].'&amount='.$_POST['amount'].$discount_str);
                } else {
                    $order = ORM::factory('Order')->where('id', '=', $_POST['order_id'])->find();
                    $this->add_priceitem_to_order($_GET['priceitem_id'], $order, $_POST['amount'], $discount_id);
                }
            }
        }

        $this->template->scripts[] = 'bootstrap.validate';
        $this->template->scripts[] = 'bootstrap.validate.ru';
        $this->template->scripts[] = 'common/order_form_step1';
    }

    public function action_select_client() {
        if(!ORM::factory('Permission')->checkPermission('manage_orders')) Controller::redirect('admin');

        $this->template->content = View::factory('admin/orders/add_order_step2')
            ->bind('permissions', $permissions)
            ->bind('errors', $errors)
            ->bind('message', $message)
            ->bind('data', $data)
            ->bind('orders', $orders);

        $this->template->title = 'Поиск клиента по номеру телефона';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        if(isset($_GET['discount_id']) && !empty($_GET['discount_id'])) {
            $discount_id = $_GET['discount_id'];
            $discount_str = "&discount_id=".$discount_id;
        } else {
            $discount_id = false;
            $discount_str = "";
        }

        if (HTTP_Request::POST == $this->request->method())
        {
            if(!empty($_POST['phone'])) {
                $client = ORM::factory('Client')->where('phone', '=', $_POST['phone'])->find_all()->as_array();
                if(count($client) > 0) {
                    Controller::redirect('admin/orders/add_new?priceitem_id='.$_GET['priceitem_id']."&client_id=".$client[0]->id."&amount=".$_GET['amount'].$discount_str);
                } else {
                    Controller::redirect('admin/clients/add?priceitem_id='.$_GET['priceitem_id']."&phone_num=".$_POST['phone']."&amount=".$_GET['amount'].$discount_str);
                }
            }
        }


        $this->template->scripts[] = 'bootstrap-formhelpers-phone.format';
        $this->template->scripts[] = 'bootstrap-formhelpers-phone';
        $this->template->scripts[] = 'bootstrap.validate';
        $this->template->scripts[] = 'bootstrap.validate.ru';
        $this->template->scripts[] = 'common/order_form_step2';
    }

    public function action_add_new() {
        if(!ORM::factory('Permission')->checkPermission('manage_orders')) Controller::redirect('admin');

        $this->template->content = View::factory('admin/orders/add_order_step3')
            ->bind('permissions', $permissions)
            ->bind('errors', $errors)
            ->bind('message', $message)
            ->bind('delivery_methods', $delivery_methods)
            ->bind('orders_states', $orders_states)
            ->bind('delivery_to', $delivery_to)
            ->bind('area', $final_area)
            ->bind('data', $data);

        $this->template->title = 'Дополнительные данные о клиенте';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $areas = ORM::factory('NpAreas')->find_all()->as_array();
        $final_area = [];

        $final_area['-----'] = '-----';
        foreach ($areas as $area)
        {
            $final_area[$area->id] = $area->name;
        }

        $data = array();
        $data['client_id'] = $_GET['client_id'];
        $data['manager_id'] = Auth::instance()->get_user()->id;
        $client = ORM::factory('Client')->where('id', '=', $_GET['client_id'])->find();
        $data['delivery_method_id'] = $client->delivery_method_id;
        $data['delivery_address'] = $client->delivery_address;


        $priceitem = ORM::factory('Priceitem')->where('id', '=', $_GET['priceitem_id'])->find();

        $delivery_to = new DateTime();

        if (!is_numeric($_GET['priceitem_id'])) {
            $json_array = json_decode(base64_decode(str_replace('_','=',$_GET['priceitem_id'])), true);
            $priceitem = ORM::factory('Priceitem')->get_from_arr($json_array);
        }

        $delivery_days = $priceitem->delivery;

        if ($priceitem->supplier->order_to) {
            $order_to = str_replace('.', ':', $priceitem->supplier->order_to);
            if ($delivery_to->format('H:i') < date('H:i', strtotime($order_to))) {
                $delivery_days--;
            }
        }

        $delivery_to->modify('+ ' . $delivery_days . ' days');

        if (HTTP_Request::POST == $this->request->method())
        {

            try {
                $order = ORM::factory('Order');
                $values = array(
                    'manager_comment',
                    'delivery_address',
                    'manager_id',
                    'delivery_method_id',
                    'client_id',
                    'state'
                );

                $order->values($this->request->post(), $values);
                $partialPayment = $this->request->post('partial_payment');
                if (!empty($partialPayment)) {
                    $order->set('partial_payment', (float) $partialPayment);
                }

                $order->set('np_area_id', $_POST['np_area_id']);
                $order->set('np_city_id', $_POST['np_city_id']);
                $order->set('np_warehouse_id', $_POST['np_warehouse_id']);
                $order->set('archive', 0);
                //$order->set('date_time', time());
                if (isset($_POST['delivery_date']))
                    $order->manager_comment .= ' Дата доставки: ' . $_POST['delivery_date'];
                $order->save();

                if(isset($_GET['discount_id']) && !empty($_GET['discount_id'])) {
                    $discount_id = $_GET['discount_id'];
                    $discount_str = "&discount_id=".$discount_id;
                } else {
                    $discount_id = false;
                    $discount_str = "";
                }

                $this->add_priceitem_to_order($_GET['priceitem_id'], $order, $_GET['amount'], $discount_id);

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

        $delivery_methods = ORM::factory('DeliveryMethod')->find_all()->as_array();

//		foreach(ORM::factory('DeliveryMethod')->find_all()->as_array() as $deliverymethod) {
//			$delivery_methods[$deliverymethod->id] = $deliverymethod->name;
//		}


        foreach(ORM::factory('OrderState')->find_all()->as_array() as $state) {
            $orders_states[$state->id] = $state->name;
        }


        $this->template->scripts[] = 'bootstrap.validate';
        $this->template->scripts[] = 'bootstrap.validate.ru';
        $this->template->scripts[] = 'common/order_form_step3';
        $this->template->scripts[] = 'common/nova_poshta_load_nva';

    }

    private function add_priceitem_to_order($priceitem_id, $order, $amount, $discount_id = false) {
        if(is_numeric($priceitem_id)) {
            $priceitem = ORM::factory('Priceitem')->where('id', '=', $priceitem_id)->find();
        } else {
            $json_array = json_decode(base64_decode(str_replace('_','=',$priceitem_id)), true);
            $priceitem = ORM::factory('Priceitem')->get_from_arr($json_array);
        }

        $discount = $priceitem->get_discount_for_client($order->client, false, $discount_id);



        $orderitem = ORM::factory('Orderitem');
        $orderitem->set('order_id', $order->id);
        $orderitem->set('article', $priceitem->part->article_long);
        $orderitem->set('brand', $priceitem->part->brand_long);
        $orderitem->set('name', $priceitem->part->name);
        $orderitem->set('suplier_code_tehnomir', $priceitem->suplier_code_tehnomir);
        $orderitem->set('delivery_days', $priceitem->delivery);
        $orderitem->set('purchase_per_unit', $priceitem->get_price());
        $orderitem->set('purchase_per_unit_in_currency', $priceitem->price);
        $orderitem->set('currency_id', $priceitem->currency_id);
        $orderitem->set('state_id', ORM::factory('State')->get_state_by_text_id('order_accept')->id);
        $orderitem->set('amount', $amount);

        $orderitem->set('sale_per_unit', $priceitem->get_price_for_client($order->client, false, $discount_id));

        $orderitem->set('discount_id', $discount->id);
        $orderitem->set('supplier_id', $priceitem->supplier_id);
        $orderitem->set('date_time', date('Y-m-d H:i:s'));
        $orderitem->save();

        Controller::redirect('admin/orders/items/'.$order->id);
    }

    public function get_work_day($count, $order_date)
    {
        $date              = $order_date;
        $day_week          = date( 'N', strtotime( $date ) );
        $day_count         = $count + $day_week;
        $week_count        = floor($day_count/5);
        $holiday_count     = ( $day_count % 5 > 0 ) ? 0 : 2;
        $week_day          = $week_count * 7 - $day_week + ( $day_count % 5 ) - $holiday_count;
        $date_end          = date( "d-m-Y", strtotime( $date . " + $week_day day " ) );
        $date_end_count    = date( 'N', strtotime( $date_end ) );
        $holiday_shift     = $date_end_count > 5 ? 7 - $date_end_count + 1 : 0;
        return date("d-m-Y", strtotime($date_end . " + $holiday_shift day "));
    }

    public function action_salary_ok() {
        if(!ORM::factory('Permission')->checkPermission('manage_orders')) Controller::redirect('admin');

        $this->auto_render = FALSE;
        $this->is_ajax = TRUE;

        $json = array();

        if ($this->request->method() == Request::POST)
        {
            if(!empty($_POST['choices'])) {
                foreach($_POST['choices'] as $val) {
                    ORM::factory('Orderitem')->where('id', '=', $val)->find()->set('salary', 1)->save();
                }

                $json['status'] = "ok";
            }
        }

        echo json_encode($json);
    }

    public function action_to_archive() {
        if(!ORM::factory('Permission')->checkPermission('manage_orders')) Controller::redirect('admin');

        $this->auto_render = FALSE;
        $this->is_ajax = TRUE;

        $json = array();

        if ($this->request->method() == Request::POST)
        {
            if(!empty($_POST['choices'])) {
                foreach($_POST['choices'] as $val) {
                    ORM::factory('Order')->where('id', '=', $val)->find()->set('archive', 1)->save();
                }

                $json['status'] = "ok";
            }
        }

        echo json_encode($json);
    }

    public function action_print() {
        if(!ORM::factory('Permission')->checkPermission('manage_orders')) Controller::redirect('admin');

        $id = $this->request->param('id');

        $order = ORM::factory('Order')->where('id', '=', $id)->find();

        $data = array();
        $data['order_number'] = (string)$order->get_order_number();
        $data['client'] = $order->client->name." ".$order->client->surname;
        $data['client_phone'] = $order->client->phone;
        $data['orderitems'] = $order->orderitems->find_all()->as_array();
        $data['date_time'] = $order->date_time;

//        $this->create_invoice($data)->send();
        Controller::redirect($this->create_invoice($data));
    }

    public function action_move_items() {
        if(!ORM::factory('Permission')->checkPermission('move_items')) Controller::redirect('admin');

        $this->auto_render = FALSE;
        $this->is_ajax = TRUE;

        $orderitems = array();

        if ($this->request->method() == Request::POST)
        {
            if(!empty($_POST['choices'])) {
                foreach($_POST['choices'] as $val) {
                    $orderitems[] = ORM::factory('Orderitem')->where('id', '=', $val)->find();
                }
            }
            if(!empty($_POST['to_order'])) {
                $to_order = $_POST['to_order'];
            } else {
                return false;
            }
            if(!empty($_POST['current_order'])) {
                $current_order = $_POST['current_order'];
            } else {
                return false;
            }
        }

        if(count($orderitems > 0)) {
            if($to_order == "add_new") {
                $order = ORM::factory('Order')->where('id', '=', $current_order)->find();
                $new_order = ORM::factory('Order');

                $new_order->manager_comment = $order->manager_comment;
                $new_order->client_comment = $order->client_comment;
                $new_order->delivery_address = $order->delivery_address;
                $new_order->manager_id = $order->manager_id;
                $new_order->delivery_method_id = $order->delivery_method_id;
                $new_order->client_id = $order->client_id;
                $new_order->archive = $order->archive;
                $new_order->save();
            }
            else {
                $new_order = ORM::factory('Order')->where('id', '=', $to_order)->find();
            }
            foreach($orderitems as $orderitem) {
                $orderitem->order_id = $new_order->id;
                $orderitem->save();
            }
            $json = array();
            $json['url'] = URL::site('admin/orders/items/'.$new_order->id);
            echo json_encode($json);
        }
    }

    public function action_print_items() {
        if(!ORM::factory('Permission')->checkPermission('manage_orders')) Controller::redirect('admin');

        $this->auto_render = FALSE;
        $this->is_ajax = TRUE;

        $data = array();
        $data['orderitems'] = array();

        if ($this->request->method() == Request::POST)
        {
            if(!empty($_POST['choices'])) {
                foreach($_POST['choices'] as $val) {
                    $data['orderitems'][] = ORM::factory('Orderitem')->where('id', '=', $val)->find()/*->set('state_id', ORM::factory('State')->get_state_by_text_id('issued'))->save()*/;
                }
            }
            $data['type'] = $_POST['type'];
        }

        if(count($data['orderitems'] > 0)) {

            $data['order_number'] = (string)$data['orderitems'][0]->order->get_order_number();
            $data['client'] = $data['orderitems'][0]->order->client->name." ".$data['orderitems'][0]->order->client->surname;
            $data['client_phone'] = $data['orderitems'][0]->order->client->phone;
            $data['date_time'] = $data['orderitems'][0]->order->date_time;

            $json = array();
            $json['url'] = URL::base().$this->create_invoice($data, 'uploads/');
            echo json_encode($json);
        }
    }

    private function create_invoice($data = array(), $path = 'uploads/') {
        if(!ORM::factory('Permission')->checkPermission('manage_orders')) Controller::redirect('admin');
        $file_location = $path.'invoice_'.(!empty($data['order_number']) ? $data['order_number'] : date('dmYHis')).".xls";
        $spreadsheet = new PHPExcel();

        $as = $spreadsheet->getActiveSheet();

        $as->getDefaultStyle()->getFont()->setSize(9);

        $as->getColumnDimension('A')->setWidth(5);
        $as->getColumnDimension('B')->setWidth(15);
        $as->getColumnDimension('C')->setWidth(15);
        $as->getColumnDimension('D')->setWidth(15);
        $as->getColumnDimension('G')->setWidth(15);
//        $as->getColumnDimension('E')->setWidth(15);
//        $as->getColumnDimension('F')->setWidth(15);
        $as->getColumnDimension('H')->setWidth(15);
//        $as->getRowDimension(1)->setRowHeight(25);

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
//		 add logo to top
        $objDrawing = new PHPExcel_Worksheet_Drawing();
        $objDrawing->setName('Logo');
        $objDrawing->setDescription('Logo');
        $objDrawing->setPath('media/img/logo.png');
        $objDrawing->setWorksheet($as);

        $as->setCellValue("D$current", "Постачальник:");
        $as->getStyle("D$current")->getFont()->setBold(true)->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);

        if (in_array($data['type'], array('sales_invoice_tov','cashless_tov'))) {
            $as->setCellValue("E$current", "ТОВ Епартс");
            $current++;
            $as->setCellValue("E$current", "ЄДРПОУ 41991593  , тел. (044)361-96-64");
            $current++;
            $as->setCellValue("E$current", "Р/р 26007052676154 в  Ф \"РОЗРАХ.ЦЕНТР\"ПАТ КБ\"ПРИВАТБАНК\", КИЇВ");
            $current++;
            $as->setCellValue("E$current", "МФО 320649");
            $current++;
            $as->setCellValue("E$current", "Не є платником податку на прибуток");
            $current++;
            $as->setCellValue("E$current", "на загальних підставах");
            $current++;
            $as->setCellValue("E$current", "Адреса Київ, вул. Вацлава Гавела 18");
        }
        elseif (in_array($data['type'], array('sales_invoice','cashless'))) {
            $as->setCellValue("E$current", "Куряков Дмитро Олександрович");
            $current++;
            $as->setCellValue("E$current", "ЄДРПОУ 2999317134  , тел. (044)3619664");
            $current++;
            $as->setCellValue("E$current", "Р/р 26004060297725 в АТ КБ \"ПРИВАТБАНК\"");
            $current++;
            $as->setCellValue("E$current", "МФО 321842");
            $current++;
            $as->setCellValue("E$current", "Не є платником податку на прибуток");
            $current++;
            $as->setCellValue("E$current", "на загальних підставах");
            $current++;
            $as->setCellValue("E$current", "Адреса Київ, вул. Якуба Коласа  29 кв. 172");
        }
        else {
            $as->setCellValue("E$current", "Епартс");
            $as->getStyle("E$current")->getFont()->setBold(true);
            $current++;
            $as->setCellValue("E$current", "(044)361-96-64, (067)291-18-25, (095)053-00-35");
        }

        $current++;

        $as->setCellValue("D$current", "Отримувач:");
        $as->getStyle("D$current")->getFont()->setBold(true)->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);

        $as->setCellValue("E$current", $data['client']);
        $current++;

        $as->setCellValue("E$current", 'тел. '.$data['client_phone']);
        $current++;

        if (in_array($data['type'], array('sales_invoice','cashless', 'sales_invoice_tov','cashless_tov'))) {
            $as->setCellValue("D$current", "Платник:");
            $as->getStyle("D$current")->getFont()->setBold(true)->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
            $as->setCellValue("E$current", "той же:");
            $current++;
        }

        $as->setCellValue("D$current", "Підстава:");
        $as->getStyle("D$current")->getFont()->setBold(true)->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);

        $as->setCellValue("E$current", ($data['type'] == 'sales_invoice' || $data['type'] == 'sales_invoice_tov') ? "Рахунок-фактура № ".$data['order_number'] : "За запчастини");

        $current += 2;

        $as->mergeCells("C$current:F$current");
        $as->getStyle("C$current")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        if ($data['type'] == 'sales_invoice' || $data['type'] == 'sales_invoice_tov'){
            $name =  'Видаткова накладна № В-'.intval($data['order_number']);
        } elseif ($data['type'] == 'cashless' || $data['type'] == 'cashless_tov') {
            $name = "Рахунок-фактура № ".$data['order_number'];
        } else {
            $name = "Накладна за замовлення № ".$data['order_number'];
        }
        if(!empty($data['order_number'])) $as->setCellValue("C$current", $name);
        $as->getStyle("C$current")->getFont()->setBold(true)->setSize(12);
        $current++;

        $as->mergeCells("C$current:F$current");
        $as->getStyle("C$current")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $as->setCellValue("C$current", ($data['type'] == 'sales_invoice' || $data['type'] == 'sales_invoice_tov') ? "від: ".date('d-m-Y') : "від: ".date('d.m.Y',strtotime($data['date_time'])));
        if (in_array($data['type'], array('sales_invoice','cashless', 'sales_invoice_tov','cashless_tov'))) $as->getStyle("C$current")->getFont()->setBold(true)->setSize(12);
        $current += 2;

        $as->setCellValue("A$current", "№");
        $as->getStyle("A$current")->getFont()->setBold(true);

        $as->mergeCells("B$current:D$current");
        $as->getStyle("B$current")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $as->setCellValue("B$current", "Товар");
        $as->getStyle("B$current")->getFont()->setBold(true);

        $as->setCellValue("E$current", ((in_array($data['type'], array('sales_invoice','cashless', 'sales_invoice_tov','cashless_tov'))) ? "Од." : "Ед."));
        $as->getStyle("E$current")->getFont()->setBold(true);

        $as->setCellValue("F$current", ((in_array($data['type'], array('sales_invoice','cashless', 'sales_invoice_tov','cashless_tov'))) ? "Кіл-ть." : "Кол-во."));
        $as->getStyle("F$current")->getFont()->setBold(true);

        $as->setCellValue("G$current", "Ціна" .((in_array($data['type'], array('sales_invoice','cashless', 'sales_invoice_tov','cashless_tov'))) ? " без ПДВ" : ""));
        $as->getStyle("G$current")->getFont()->setBold(true);

        $as->setCellValue("H$current", "Сума".((in_array($data['type'], array('sales_invoice','cashless', 'sales_invoice_tov','cashless_tov'))) ? " без ПДВ" : ""));
        $as->getStyle("H$current")->getFont()->setBold(true);

        $as->getStyle("A$current:H$current")->applyFromArray($styleArray);

        $current++;
        $count = 1;
        $summ = 0;

        foreach($data['orderitems'] as $orderitem) {

            $k = in_array($data['type'], array('sales_invoice_tov','cashless_tov')) ? '1.05' : (in_array($data['type'], array('sales_invoice','cashless')) ? '1.01' : '1');
//            $orderitem->sale_per_unit *= $k;
            $price = $orderitem->sale_per_unit * $k;
            $as->setCellValue("A$current", $count);

            $as->setCellValue("B$current", $orderitem->article);
            $as->getStyle("B$current")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $as->setCellValue("C$current", $orderitem->brand);
            $as->setCellValue("D$current", $orderitem->name);
            $as->getStyle("D$current")->getAlignment()->setWrapText(true);

            $as->setCellValue("E$current", "шт.");

            $as->setCellValue("F$current", $orderitem->amount);

            $as->setCellValue("G$current", round($price, 0).",00");

            $as->setCellValue("H$current", round($price*$orderitem->amount, 0).",00");
            $summ += round($price*$orderitem->amount, 0);

            $as->getStyle("A$current:H$current")->applyFromArray($styleArray);
            $current++;
            $count++;
        }

        if($data['type'] == 'default'){

            $order_cost = $new_order = ORM::factory('Order')->where('id', '=',  $data['order_number'])->find();

            if($order_cost->delivery_method_id == 6){

                $as->setCellValue("A$current", $count++);
                $as->getStyle("A$current")->applyFromArray($styleArray);

                $as->mergeCells("B$current:D$current");

                $as->setCellValue("B$current", "Доставка");
                $as->getStyle("B$current")->applyFromArray($styleArray);

                $as->setCellValue("E$current", " ");
                $as->getStyle("E$current")->applyFromArray($styleArray);

                $as->setCellValue("F$current", "1");
                $as->getStyle("F$current")->applyFromArray($styleArray);

                $as->setCellValue("G$current", "40");
                $as->getStyle("G$current")->applyFromArray($styleArray);

                $as->setCellValue("H$current", "40");
                $as->getStyle("H$current")->applyFromArray($styleArray);

                $summ += 40;

                $current++;
            }
        }

        $as->setCellValue("G$current", (in_array($data['type'], array('sales_invoice','cashless','sales_invoice_tov','cashless_tov'))) ? "Всього без ПДВ:" : "Загальна сума:");
        $as->getStyle("G$current")->getFont()->setBold(true);

        $as->setCellValue("H$current", round($summ, 0).",00");
        $as->getStyle("H$current")->applyFromArray($styleArray);

        $current += 2;

        $as->mergeCells("A$current:H$current");

        $as->setCellValue("A$current", "Шановні покупці , придбаний товар можна повернути або обміняти протягом 14 днів з моменту покупки при наявності документів на придбання та при збереженні товарного вигляду.\nУВАГА! Замовлені за кордоном автозапчастини обміну та поверненню не підлягають, також можуть варіюватися терміни поставки в Україну .");
        $as->getStyle("A$current:E$current")->getAlignment()->setWrapText(true);
        $as->getRowDimension($current)->setRowHeight(50);

        $current += 3;

//        $as->mergeCells("A$current:F$current");

        //$as->getStyle("A$current")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        if ($data['type'] != 'cashless' && $data['type'] != 'cashless_tov' ){
            $as->getStyle("A$current")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $as->setCellValue("A$current", "Отримав:");

            $as->mergeCells("B$current:C$current");
            $as->getStyle("B$current")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $as->getStyle("B$current:C$current")->applyFromArray($styleArrayBottom);
        }


        $as->mergeCells("E$current:F$current");
        $as->getStyle("E$current")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $as->setCellValue("E$current", "Виписав:");

        $as->mergeCells("G$current:H$current");
        $as->getStyle("G$current")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $as->getStyle("G$current:H$current")->applyFromArray($styleArrayBottom);


        $current++;

        $as->mergeCells("G$current:H$current");
        $as->getStyle("G$current")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        if ($data['type'] == 'sales_invoice' || $data['type'] == 'sales_invoice_tov'){
            $as->setCellValue("G$current", "Белявский М. В.");
        } elseif ($data['type'] == 'cashless' || $data['type'] == 'cashless_tov' ) {
            $as->setCellValue("G$current", "Белявский М. В.");
        } else {
            $as->setCellValue("G$current", Auth::instance()->get_user()->surname);
        }

        $objWriter = PHPExcel_IOFactory::createWriter($spreadsheet, 'Excel5');
        $objWriter->save($file_location);
        return $file_location;
    }

    public function action_print_sticker() {
        if(!ORM::factory('Permission')->checkPermission('manage_orders')) Controller::redirect('admin');

        $id = $this->request->param('id');

        $order = ORM::factory('Order')->where('id', '=', $id)->find();

        $data = array();
        $data['order_number'] = (string)$order->get_order_number();
        $data['client'] = $order->client->name." ".$order->client->surname;
        $data['client_phone'] = $order->client->phone;
        $data['orderitems'] = $order->orderitems->find_all()->as_array();
        $data['delivery_method'] = $order->delivery_method->name;
        $data['delivery_address'] = $order->delivery_address;
        $data['manager_comment'] = $order->manager_comment;
        $data['client_comment'] = $order->client_comment;
        $data['client_obj'] = $order->client;
        $data['date_time'] = $order->date_time;

        $this->create_sticker($data)->send();
    }

    public function action_print_sticker_items() {
        if(!ORM::factory('Permission')->checkPermission('manage_orders')) Controller::redirect('admin');

        $this->auto_render = FALSE;
        $this->is_ajax = TRUE;

        $data = array();
        $data['orderitems'] = array();

        if ($this->request->method() == Request::POST)
        {
            if(!empty($_POST['choices'])) {
                foreach($_POST['choices'] as $val) {
                    $data['orderitems'][] = ORM::factory('Orderitem')->where('id', '=', $val)->find();
                }
            }
        }

        if(count($data['orderitems'] > 0)) {

            $data['order_number'] = (string)$data['orderitems'][0]->order->get_order_number();
            $data['client'] = $data['orderitems'][0]->order->client->name." ".$data['orderitems'][0]->order->client->surname;
            $data['client_phone'] = $data['orderitems'][0]->order->client->phone;
            $data['delivery_method'] = $data['orderitems'][0]->order->delivery_method->name;
            $data['delivery_address'] = $data['orderitems'][0]->order->delivery_address;
            $data['manager_comment'] = $data['orderitems'][0]->order->manager_comment;
            $data['client_comment'] = $data['orderitems'][0]->order->client_comment;
            $data['client_obj'] = $data['orderitems'][0]->order->client;
            $data['date_time'] = $data['orderitems'][0]->order->date_time;

            $json = array();
            $json['url'] = URL::base().$this->create_sticker($data, 'uploads/')->save();
            echo json_encode($json);
        }
    }


    private function create_sticker($data = array(), $path = 'uploads/') {
        if(!ORM::factory('Permission')->checkPermission('manage_orders')) Controller::redirect('admin');

        $spreadsheet = Spreadsheet::factory(array(
            'author'  => 'Kohana-PHPExcel',
            'title'      => 'Report',
            'subject' => 'Subject',
            'description'  => 'Description',
            'path' => $path,
            'name' => 'sticker_'.(!empty($data['order_number']) ? $data['order_number'] : date('dmYHis')),
            'format' => 'Excel5',
        ));
        $spreadsheet->set_active_worksheet(0);
        $as = $spreadsheet->get_active_worksheet();
        $as->title("Report");

        $as->getDefaultStyle()->getFont()->setSize(10);

        $as->getColumnDimension('A')->setWidth(20);
        $as->getColumnDimension('B')->setWidth(80);

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

        $as->setCellValue("A1", "Фамилия, Имя");
        $as->setCellValue("B1", $data['client']);

        $as->setCellValue("A2", "Город");
        $as->setCellValue("B2", '---');

        $as->setCellValue("A3", "Телефон");
        $as->setCellValue("B3", $data['client_phone']);

        $as->setCellValue("A4", "Перевозчик");
        $as->setCellValue("B4", $data['delivery_method']);

        $as->setCellValue("A5", "Отделение");
        $as->setCellValue("B5", $data['delivery_address']);

        $balance = $data['client_obj']->get_user_balance();
        $dolg = -$balance['active_balance'];
        $summ = 0;
        $platezh = 0;

        foreach($data['orderitems'] as $orderitem) {
            $summ += round($orderitem->sale_per_unit*$orderitem->amount, 0);
        }

        $platezh = $summ;
        if($dolg < $platezh) {
            if($dolg <= 0) {
                $platezh = 0;
            } else {
                $platezh = $dolg;
            }
        }


        $summ = round($summ, 0).",00 грн.";
        $platezh = round($platezh, 0).",00 грн.";


        $as->setCellValue("A6", "Сумма");
        $as->setCellValue("B6", $summ);

        $as->setCellValue("A7", "Комментарии");
        $as->setCellValue("B7", $data['manager_comment'] . "\n" . $data['client_comment']);

        $as->getRowDimension(7)->setRowHeight(50);
        $as->getStyle("B7")->getAlignment()->setWrapText(true);

        $as->setCellValue("A8", "Наложенный");
        $as->setCellValue("B8", $platezh);

        $as->getStyle("A1:A8")->getFont()->setBold(true);

        $as->getStyle("A1:B8")->applyFromArray($styleArray);


        return $spreadsheet;
    }



    public function action_print_bill() {
        if(!ORM::factory('Permission')->checkPermission('manage_orders')) Controller::redirect('admin');

        $this->auto_render = FALSE;
        $this->is_ajax = TRUE;

        $data = array();
        $data['orderitems'] = array();
        $data['cash_amount'] = false;

        if ($this->request->method() == Request::POST)
        {
            if(!empty($_POST['choices'])) {
                foreach($_POST['choices'] as $val) {
                    $data['orderitems'][] = ORM::factory('Orderitem')->where('id', '=', $val)->find();
                }
            }

            if(!empty($_POST['cash_amount'])) {
                $data['cash_amount'] = $_POST['cash_amount'];
            }
        }

        if(count($data['orderitems']) > 0 && $data['cash_amount']) {

            $data['order_number'] = (string)$data['orderitems'][0]->order->get_order_number();
            $data['client'] = $data['orderitems'][0]->order->client->name." ".$data['orderitems'][0]->order->client->surname;
            $data['client_phone'] = $data['orderitems'][0]->order->client->phone;
            $data['delivery_method'] = $data['orderitems'][0]->order->delivery_method->name;
            $data['delivery_address'] = $data['orderitems'][0]->order->delivery_address;
            $data['manager_comment'] = $data['orderitems'][0]->order->manager_comment;
            $data['client_comment'] = $data['orderitems'][0]->order->client_comment;
            $data['client_obj'] = $data['orderitems'][0]->order->client;

            $json = array();
            $json['url'] = URL::base().$this->create_bill($data, 'uploads/')->save();
            echo json_encode($json);
        }
    }

    private function create_bill($data = array(), $path = 'uploads/') {
        if(!ORM::factory('Permission')->checkPermission('manage_orders')) Controller::redirect('admin');

        $spreadsheet = Spreadsheet::factory(array(
            'author'  => 'Kohana-PHPExcel',
            'title'      => 'Report',
            'subject' => 'Subject',
            'description'  => 'Description',
            'path' => $path,
            'name' => 'bill_'.(!empty($data['order_number']) ? $data['order_number'] : date('dmYHis')),
            'format' => 'Excel5',
        ));
        $spreadsheet->set_active_worksheet(0);
        $as = $spreadsheet->get_active_worksheet();
        $as->title("Report");

        $as->getDefaultStyle()->getFont()->setSize(10);

        $as->getColumnDimension('A')->setWidth(5);
        $as->getColumnDimension('B')->setWidth(15);
        $as->getColumnDimension('C')->setWidth(20);
        $as->getColumnDimension('D')->setWidth(30);
        $as->getColumnDimension('E')->setWidth(15);
        $as->getColumnDimension('F')->setWidth(15);


        $current = 1;
        $current = $this->bill_part($data, $current, $as);
        $current += 4;
        $current = $this->bill_part($data, $current, $as, true);


        return $spreadsheet;
    }

    private function bill_part($data, $current, $as, $client = false) {
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

        $as->setCellValue("B$current", "Плательщик:");
        $as->getStyle("B$current")->getFont()->setBold(true)->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);

        $as->setCellValue("C$current", $data['client']);

        $current++;

        $as->setCellValue("C$current", $data['client_phone']);

        $current++;

        $as->setCellValue("B$current", "Получатель:");
        $as->getStyle("B$current")->getFont()->setBold(true)->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);

        $as->setCellValue("C$current", "Епартс");
        $as->getStyle("C$current")->getFont()->setBold(true);

        $current++;

        $as->setCellValue("C$current", "(044)361-96-64, (067)291-18-25, (095)053-00-35");

        $current++;

        $as->setCellValue("B$current", "Основание:");
        $as->getStyle("B$current")->getFont()->setBold(true)->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);

        $as->setCellValue("C$current", "За запчасти");

        $current += 2;

        $as->mergeCells("C$current:D$current");
        $as->getStyle("C$current")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        if(!empty($data['order_number'])) $as->setCellValue("C$current", "Квитанция за заказ № ".$data['order_number']);
        $as->getStyle("C$current")->getFont()->setBold(true)->setSize(12);
        $current++;

        $as->mergeCells("C$current:D$current");
        $as->getStyle("C$current")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $as->setCellValue("C$current", "Дата: ".date("d.m.Y"));

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

        // $as->setCellValue("G$current", "Цена");
        // $as->getStyle("G$current")->getFont()->setBold(true);

        // $as->setCellValue("H$current", "Сумма");
        // $as->getStyle("H$current")->getFont()->setBold(true);

        $as->getStyle("A$current:F$current")->applyFromArray($styleArray);

        $current++;
        $count = 1;

        foreach($data['orderitems'] as $orderitem) {
            $as->setCellValue("A$current", $count);

            $as->setCellValue("B$current", $orderitem->article);
            $as->getStyle("B$current")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $as->setCellValue("C$current", $orderitem->brand);
            $as->setCellValue("D$current", $orderitem->name);
            $as->getStyle("D$current")->getAlignment()->setWrapText(true);

            $as->setCellValue("E$current", "шт.");

            $as->setCellValue("F$current", $orderitem->amount);

            // $as->setCellValue("G$current", round($orderitem->sale_per_unit, 0).",00");

            // $as->setCellValue("H$current", round($orderitem->sale_per_unit*$orderitem->amount, 0).",00");

            $as->getStyle("A$current:F$current")->applyFromArray($styleArray);
            $current++;
            $count++;
        }

        $as->setCellValue("E$current", "");
        $as->getStyle("E$current")->getFont()->setBold(true);

        $as->setCellValue("F$current", round($data['cash_amount'], 0).",00");
        $as->getStyle("F$current")->applyFromArray($styleArray);

        $current += 2;

        $as->mergeCells("C$current:D$current");
        $as->getStyle("C$current")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $as->setCellValue("C$current", ($client ? "Оплачен:" : "Выписал"));

        $as->mergeCells("E$current:F$current");
        $as->getStyle("E$current")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $as->getStyle("E$current:F$current")->applyFromArray($styleArrayBottom);


        $current++;

        $as->mergeCells("E$current:F$current");
        $as->getStyle("E$current")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $as->setCellValue("E$current", ($client ? $data['client'] : Auth::instance()->get_user()->surname));

        $current +=2;
        $as->getStyle("A$current:F$current")->applyFromArray($styleArrayBottom);

        return $current;
    }

    protected function remove_item()
    {
        $itemId = (int) $this->request->query('delitem');
        $orderItem = ORM::factory('Orderitem', $itemId);

        if ($orderItem->state_id != 1) return false;

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
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                