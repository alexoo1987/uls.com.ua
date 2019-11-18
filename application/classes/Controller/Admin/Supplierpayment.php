<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Supplierpayment extends Controller_Admin_Application {

    public function action_list() {
        if(!ORM::factory('Permission')->checkPermission('supplier_payments')) Controller::redirect('admin');

        $this->template->content = View::factory('admin/supplier_payments/list')
            ->bind('suppliers', $suppliers)
            ->bind('currencies', $currencies)
            ->bind('data', $data)
            ->bind('filters', $filters)
            ->bind('users', $users)
            ->bind('allowed_states', $allowed_states)
            ->bind('total', $total)
            ->bind('orderitems', $orderitems)
            ->bind('states', $states)
            ->bind('sp_pagination', $sp_pagination)
            ->bind('oi_pagination', $oi_pagination)
            ->bind('del_pagination', $del_pagination)
            ->bind('delivery_payments', $delivery_payments)
            ->bind('supplier_payments', $supplier_payments);
        $this->template->title = 'Баланс по поставщикам';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        if (HTTP_Request::POST == $this->request->method())
        {
            $dataPost = $this->request->post();
            $supplierpayment = ORM::factory('SupplierPayment');
            $supplierpayment->values($dataPost, array(
                'supplier_id',
                'value',
                'ratio',
                'comment_text',
            ));
            $supplierpayment->set('date_time', empty($dataPost['date_time']) ? date('Y-m-d H:m:s') : date('Y-m-d 12:m:s', strtotime($dataPost['date_time'])));
            $supplierpayment->set('user_id', Auth::instance()->get_user()->id);
            $supplierpayment->save();

            // Reset values so form is not sticky
            $_POST = array();
            Controller::redirect('admin/supplierpayment/list?supplier_id='.$supplierpayment->supplier_id);
        }

        $total = array('payments' => 0, 'purchase' => array(), 'return' => array(), 'returns' => array(), 'in_work' => array(), 'orderitems' => 0, 'before' => 0, 'oi_in_currency' => array(), 'payments_table' => array(), 'delivery_table' => array(), 'client_payments' => 0);
        $allowed_states = Model_Services::purchasedStates; // закупка

        $supplier_payments = ORM::factory('SupplierPayment');

        $delivery_payments = ORM::factory('Costs')->where('supplier_id', '<>', 0)->and_where('type', '=', 3);

        $supplier_payments->reset(FALSE);
        $delivery_payments->reset(FALSE);
        $orderitems_tmp = ORM::factory('OrderitemLog');
        $orderitems_tmp->reset(FALSE);

        $client_payments = DB::select(array(DB::expr('SUM(value)'), 'payments'))->from('client_payments');

        $orderitems_tmp = $orderitems_tmp->join('orderitems')->on('orderitemlog.orderitem_id', '=', 'orderitems.id')
            ->where('orderitemlog.state_id', 'IN', $allowed_states);

        $in_work = ORM::factory('OrderitemLog');
        $in_work->reset(FALSE);
        $in_work_states = Model_Services::inWork;

        $in_work = $in_work->join('orderitems')->on('orderitemlog.orderitem_id', '=', 'orderitems.id')
            ->where('orderitemlog.state_id', 'IN', $in_work_states)->and_where('orderitems.state_id', 'IN', $in_work_states);

        //Module work only from this date!!!
        if (!isset($_GET['date_from'])) $_GET['date_from'] = '08.08.2016';

        if (!empty($_GET['date_from'])) {
            $filters['date_from'] = $_GET['date_from'];
            $supplier_payments = $supplier_payments->and_where('date_time', '>=', date('Y-m-d 00:00:00', strtotime($filters['date_from'])));
            $delivery_payments = $delivery_payments->and_where('date', '>=', date('Y-m-d 00:00:00', strtotime($filters['date_from'])));
            $orderitems_tmp = $orderitems_tmp->and_where('orderitemlog.date_time', '>=', date('Y-m-d 00:00:00', strtotime($filters['date_from'])));
            $in_work = $in_work->and_where('orderitemlog.date_time', '>=', date('Y-m-d 00:00:00', strtotime($filters['date_from'])));
            $client_payments = $client_payments->and_where('date_time', '>=', date('Y-m-d 00:00:00', strtotime($filters['date_from'])));
        }

        if(!empty($_GET['date_to'])) {
            $filters['date_to'] = $_GET['date_to'];
            $supplier_payments = $supplier_payments->and_where('date_time', '<=', date('Y-m-d 23:59:59', strtotime($filters['date_to'])));
            $delivery_payments = $delivery_payments->and_where('date', '<=', date('Y-m-d 23:59:59', strtotime($filters['date_to'])));
            $orderitems_tmp = $orderitems_tmp->and_where('orderitemlog.date_time', '<=', date('Y-m-d 23:59:59', strtotime($filters['date_to'])));
            $in_work = $in_work->and_where('orderitemlog.date_time', '<=', date('Y-m-d 23:59:59', strtotime($filters['date_to'])));
            $client_payments = $client_payments->and_where('date_time', '<=', date('Y-m-d 23:59:59', strtotime($filters['date_to'])));
        }
        if(!empty($_GET['supplier_id'])) {
            $filters['supplier_id'] = $_GET['supplier_id'];
            $data['supplier_id'] = $filters['supplier_id'];
            $supplier_payments = $supplier_payments->and_where('supplier_id', '=', $filters['supplier_id']);
            $delivery_payments = $delivery_payments->and_where('supplier_id', '=', $filters['supplier_id']);
            $orderitems_tmp = $orderitems_tmp->and_where('orderitems.supplier_id', '=', $filters['supplier_id']);
            $in_work = $in_work->and_where('orderitems.supplier_id', '=', $filters['supplier_id']);
        }

        if (!empty($_GET['user_id'])) {
            $filters['user_id'] = $_GET['user_id'];
            $supplier_payments = $supplier_payments->and_where('user_id', '=', $filters['user_id']);
            $delivery_payments = $delivery_payments->and_where('user_id', '=', $filters['user_id']);
        }

        if(!empty($_GET['state_id'])) {
            $filters['state_id'] = $_GET['state_id'];
            $orderitems_tmp = $orderitems_tmp->and_where('orderitems.state_id', '=', $filters['state_id']);
        }

        $count = $supplier_payments->count_all();
        $count_delivery = $delivery_payments->count_all();

        $sp_pagination = Pagination::factory(array(
            'current_page' => array('source' => 'query_string', 'key' => 'sp_page'),
            'total_items' => $count,
            'items_per_page' => 10,
        ))->route_params(array(
            'controller' =>  'supplierpayment',
            'action' =>  'list'
        ));

        $del_pagination = Pagination::factory(array(
            'current_page' => array('source' => 'query_string', 'key' => 'del_page'),
            'total_items' => $count_delivery,
            'items_per_page' => 10,
        ))->route_params(array(
            'controller' =>  'costs',
            'action' =>  'list'
        ));

        $supplier_payments = $supplier_payments->order_by('date_time', "desc")->find_all()->as_array();
        $delivery_payments = $delivery_payments->order_by('date', "desc")->find_all()->as_array();

        foreach($supplier_payments as $sp) {
            $currency_code = $sp->ratio == 1 ? 'UAH' : $sp->supplier->currency->code;
            if ($sp->value >= 0/* && (!empty($filters['date_from']) || !empty($filters['date_to']))*/) {
                $type = 'payments_table';
            } elseif($sp->value < 0) {
                $type = 'returns';
            }
            if (isset($total[$type][$currency_code])) {
                $total[$type][$currency_code]['amount'] += $sp->value;
                $total[$type][$currency_code]['in_UAH'] += round($sp->value * $sp->ratio);
            } else {
                $total[$type][$currency_code] = array(
                    'amount' => $sp->value,
                    'in_UAH' => round($sp->value * $sp->ratio)
                );
            }
        }

        foreach($delivery_payments as $sp) {
            $currency_code = 'UAH';
            if ($sp->amount >= 0/* && (!empty($filters['date_from']) || !empty($filters['date_to']))*/) {
                $type = 'delivery_table';
            }
            if (isset($total[$type][$currency_code])) {
                $total[$type][$currency_code]['amount'] += $sp->amount;
                $total[$type][$currency_code]['in_UAH'] += $sp->amount;
            } else {
                $total[$type][$currency_code] = array(
                    'amount' => $sp->amount,
                    'in_UAH' => $sp->amount
                );
            }
        }


        $supplier_payments = array_slice($supplier_payments, $sp_pagination->offset, $sp_pagination->items_per_page);

        $delivery_payments = array_slice($delivery_payments, $del_pagination->offset, $del_pagination->items_per_page);

        $states = $orderitems_tmp
//            ->group_by('orderitemlog.orderitem_id','orderitemlog.state_id')
            ->order_by('orderitemlog.orderitem_id', "asc")->order_by('orderitemlog.date_time', "asc")->find_all()->as_array();

        $purchase = array();
        $return = array();
        $temp_id = 0;

        $dont_show = array('irretrievable');

        foreach($states as $state) {

            if ($state->state->text_id == 'return_to_supplier' AND !in_array($state->orderitem->state->text_id, $dont_show) AND !in_array($state, $return)) {
                $return[] = $state;

                $val_in_curr = round(($state->orderitem->currency->code == 'UAH' ? $state->orderitem->purchase_per_unit : $state->orderitem->purchase_per_unit_in_currency) * $state->orderitem->amount, 2);
                if (!isset($total['return'][$state->orderitem->currency_id])) {
                    $total['return'][$state->orderitem->currency_id]['val'] = $val_in_curr;
                    $total['return'][$state->orderitem->currency_id]['currency'] = $state->orderitem->currency;
                } else {
                    $total['return'][$state->orderitem->currency_id]['val'] += $val_in_curr;
                }
            }

            if ($temp_id != $state->orderitem_id) {
                $temp_id = $state->orderitem_id;

                $orderitem = $state->orderitem;
                $firstState = $orderitem->logs->where('orderitemlog.state_id', 'IN', $allowed_states)->find();
                if ($firstState->id != $state->id) continue;

                $purchase[] = $state;

                $val_in_curr = round(($state->orderitem->currency->code == 'UAH' ? $state->orderitem->purchase_per_unit : $state->orderitem->purchase_per_unit_in_currency) * $state->orderitem->amount, 2);
                if (!isset($total['purchase'][$state->orderitem->currency_id])) {
                    $total['purchase'][$state->orderitem->currency_id]['val'] = $val_in_curr;
                    $total['purchase'][$state->orderitem->currency_id]['currency'] = $state->orderitem->currency;
                } else {
                    $total['purchase'][$state->orderitem->currency_id]['val'] += $val_in_curr;
                }
            }
        }

        $in_work = $in_work->group_by('orderitem_id')->order_by('date_time', 'desc')->find_all()->as_array();

        foreach ($in_work AS $state) {
            $orderitem = $state->orderitem;

            $val_in_curr = round(($orderitem->currency->code == 'UAH' ? $orderitem->purchase_per_unit : $orderitem->purchase_per_unit_in_currency) * $orderitem->amount, 2);
            if (!isset($total['in_work'][$orderitem->currency_id])) {
                $total['in_work'][$orderitem->currency_id]['val'] = $val_in_curr;
                $total['in_work'][$orderitem->currency_id]['currency'] = $orderitem->currency;
            } else {
                $total['in_work'][$orderitem->currency_id]['val'] += $val_in_curr;
            }
        }

        $count = count($purchase) > count($in_work) ? count($purchase) :  count($in_work);

        usort($purchase, 'sort_items_by_date');
        usort($return, 'sort_items_by_date');

        $oi_pagination = Pagination::factory(array(
            'current_page' => array('source' => 'query_string', 'key' => 'oi_page'),
            'total_items' => $count,
            'items_per_page' => 10,
        ))->route_params(array(
            'controller' =>  'supplierpayment',
            'action' =>  'list'
        ));


        $orderitems = array(
            'purchase' => array_slice($purchase, $oi_pagination->offset, $oi_pagination->items_per_page),
            'return' => array_slice($return, $oi_pagination->offset, $oi_pagination->items_per_page),
            'in_work' => array_slice($in_work, $oi_pagination->offset, $oi_pagination->items_per_page)
        );


        $total['client_payments'] = $client_payments->execute()->get('payments');

        $suppliers = array('' => '---');

        foreach(ORM::factory('Supplier')->find_all()->as_array() as $supplier) {
            $suppliers[$supplier->id] = $supplier->name;
        }

        $states = array('' => '---');

        foreach(ORM::factory('State')->find_all()->as_array() as $state) {
            $states[$state->id] = $state->name;
        }

        $currencies = array('' => '---');

        foreach(ORM::factory('Currency')->find_all()->as_array() as $currency) {
            $currencies[$currency->id] = $currency->name;
        }

        //users, who can add supplier payment
        $users = array('' => '---');
        foreach (ORM::factory('User')->find_all()->as_array() as $user) {
            if (ORM::factory('Permission')->checkPermissionByUser($user->id, 'supplierpayment_manage')) $users[$user->id] = $user->surname;
        }

        $this->template->scripts[] = 'bootstrap.validate';
        $this->template->scripts[] = 'bootstrap.validate.ru';
        $this->template->scripts[] = "common/supplier_payments_list";
        $this->template->scripts[] = 'jquery-ui-1.10.3.custom.min';
    }

    public function action_new_balance(){
        if(!ORM::factory('Permission')->checkPermission('supplier_payments')) Controller::redirect('admin');

        $this->template->content = View::factory('admin/supplier_payments/new_balance')
            ->bind('suppliers', $suppliers);
        $this->template->title = 'Баланс по поставщикам (новый)';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $suppliers = ORM::factory('Supplier')->find_all()->as_array();
    }

    public function action_balance_one(){

        if(!ORM::factory('Permission')->checkPermission('supplier_payments')) Controller::redirect('admin');

        if(empty($_GET['supplier_id']))
        {
            Controller::redirect('admin/supplierpayment/balance_one?supplier_id=4');
        }

        else
        {
            $id = $_GET['supplier_id'];
            $filters['supplier_id'] = $id;
        }

        $this->template->content = View::factory('admin/supplier_payments/balance_one')
            ->bind('supplier_balance_info', $supplier_balance_info)
            ->bind('total', $total)
            ->bind('total_before', $total_before)
            ->bind('suppliers', $suppliers)
            ->bind('variant', $variant)
            ->bind('data', $data)
            ->bind('message', $message)
            ->bind('sp_pagination', $sp_pagination)
            ->bind('del_pagination', $del_pagination)
            ->bind('filters', $filters);

        $supplier_balance_info = [];
        $total = [
            'payment' => 0,
            'returns' => 0,
            'orders' => 0,
            'delivery' => 0
        ];
        $total_before = [
            'payment' => 0,
            'returns' => 0,
            'orders' => 0,
            'delivery' => 0
        ];
        $suppliers = array('' => '---');
        foreach(ORM::factory('Supplier')->find_all()->as_array() as $supplier_one) {
            $suppliers[$supplier_one->id] = $supplier_one->name;
        }

        $variant = array('' => '---');
        $data = date("Y-m-d");
        $variant[1] = 'Краткий';
        $variant[2] = 'Детальный';

//        Проплаты
        $supplier_payments = ORM::factory('SupplierPayment')->where('supplier_id', '=', $id);
//        $supplier_payments->reset(FALSE);

//        Возвраты
        $orderitems_returns = ORM::factory('OrderitemLog');
//        $orderitems_returns->reset(FALSE);
        $orderitems_returns = $orderitems_returns->join('orderitems')->on('orderitemlog.orderitem_id', '=', 'orderitems.id')->where('orderitemlog.state_id', '=', 14)->and_where('orderitems.supplier_id', '=', $id);

//        Информация о поставщике
        $supplier = ORM::factory('Supplier')->where('id', '=', $id)->find();

//        Заказы
        $supplier_orders = ORM::factory('SupplierOrder')->where('supplier_id', '=', $id);

//        Доставки
        $delivery_payments = ORM::factory('Costs')->where('supplier_id', '=', $id)->and_where('type', '=', 3);
//        $delivery_payments->reset(FALSE);


        if (!isset($_GET['date_from']) || $_GET['date_from'] < '19.10.2017') {
            $_GET['date_from'] = '19.10.2017';
        }
        else
        {
            $filters['date_from'] = $_GET['date_from'];
            //        Баланс до
            $supplier_payments_before = ORM::factory('SupplierPayment')->where('supplier_id', '=', $id)->and_where('date_time', '<', date('Y-m-d 00:00:00', strtotime($filters['date_from'])))->and_where('date_time', '>=', date('Y-m-d 00:00:00', strtotime('19.10.2017')));
            $supplier_payments_before = $supplier_payments_before->find_all()->as_array();

            $orderitems_returns_before  = ORM::factory('OrderitemLog')->join('orderitems')->on('orderitemlog.orderitem_id', '=', 'orderitems.id')->where('orderitemlog.state_id', '=', 14)->and_where('orderitems.supplier_id', '=', $id)->and_where('orderitemlog.date_time', '<', date('Y-m-d 00:00:00', strtotime($filters['date_from'])))->and_where('orderitemlog.date_time', '>=', date('Y-m-d 00:00:00', strtotime('19.10.2017')));
            $orderitems_returns_before = $orderitems_returns_before->find_all()->as_array();

            $supplier_orders_before = ORM::factory('SupplierOrder')->where('supplier_id', '=', $id)->and_where('date_time', '<', date('Y-m-d 00:00:00', strtotime($filters['date_from'])))->and_where('date_time', '>=', date('Y-m-d 00:00:00', strtotime('19.10.2017')));
            $supplier_orders_before = $supplier_orders_before->find_all()->as_array();

            $delivery_payments_before = ORM::factory('Costs')->where('supplier_id', '=', $id)->and_where('type', '=', 3)->and_where('created', '<', date('Y-m-d 00:00:00', strtotime($filters['date_from'])))->and_where('created', '>=', date('Y-m-d 00:00:00', strtotime('19.10.2017')));
            $delivery_payments_before = $delivery_payments_before->find_all()->as_array();

            foreach ($supplier_payments_before as $supplier_payment_before)
            {
                $total_before['payment'] += $supplier_payment_before->value;
            }

            foreach ($delivery_payments_before as $delivery_payment_before)
            {
                $total_before['delivery'] += $delivery_payment_before->amount;
            }

            foreach ($orderitems_returns_before as $orderitems_return_before)
            {
                $total_before['returns'] += $orderitems_return_before->orderitem->purchase_per_unit_in_currency * $orderitems_return_before->orderitem->amount;
            }

            $balance_order_before = 0;
            foreach ($supplier_orders_before as $supplier_order_before){
                foreach ($supplier_order_before->orderitemssupplier->find_all()->as_array() as $one_position){
                    $balance_order_before += $one_position->orderitem->amount*$one_position->orderitem->purchase_per_unit_in_currency;
                }
            }

            $total_before['orders'] = $balance_order_before;
        }

        if(!empty($_GET['date_from']))
        {
            $filters['date_from'] = $_GET['date_from'];
            $supplier_payments = $supplier_payments->and_where('date_time', '>=', date('Y-m-d 00:00:00', strtotime($filters['date_from'])));
            $orderitems_returns = $orderitems_returns->and_where('orderitemlog.date_time', '>=', date('Y-m-d 00:00:00', strtotime($filters['date_from'])));
            $supplier_orders = $supplier_orders->and_where('date_time', '>=', date('Y-m-d 00:00:00', strtotime($filters['date_from'])));
            $delivery_payments = $delivery_payments->and_where('created', '>=', date('Y-m-d 00:00:00', strtotime($filters['date_from'])));
        }

        if(!empty($_GET['date_to']))
        {
            $filters['date_to'] = $_GET['date_to'];
            $supplier_payments = $supplier_payments->and_where('date_time', '<=', date('Y-m-d 23:59:59', strtotime($filters['date_to'])));
            $orderitems_returns = $orderitems_returns->and_where('orderitemlog.date_time', '<=', date('Y-m-d 23:59:59', strtotime($filters['date_to'])));
            $supplier_orders = $supplier_orders->and_where('date_time', '<=', date('Y-m-d 23:59:59', strtotime($filters['date_to'])));
            $delivery_payments = $delivery_payments->and_where('created', '<=', date('Y-m-d 23:59:59', strtotime($filters['date_to'])));
        }


        $supplier_payments = $supplier_payments->find_all()->as_array();
        foreach ($supplier_payments as $supplier_payment)
        {
            $total['payment'] += $supplier_payment->value;
        }

        $delivery_payments = $delivery_payments->find_all()->as_array();
        foreach ($delivery_payments as $delivery_payment)
        {
            $total['delivery'] += $delivery_payment->amount;
        }

        $orderitems_returns = $orderitems_returns->order_by('orderitemlog.orderitem_id', "asc")->order_by('orderitemlog.date_time', "asc")->find_all()->as_array();
        foreach ($orderitems_returns as $orderitems_return)
        {
            $total['returns'] += $orderitems_return->orderitem->purchase_per_unit_in_currency * $orderitems_return->orderitem->amount;
        }

//        echo View::factory('profiler/stats'); exit();
        $supplier_balance_info['costs'] = $supplier_payments;
        $supplier_balance_info['delivery'] = $delivery_payments;
        $supplier_balance_info['returns'] = $orderitems_returns;
        $supplier_balance_info['supplier'] = $supplier;
        $supplier_balance_info['orders'] = $supplier_orders->find_all()->as_array();


        $this->template->title = 'Баланс по поставщику '.$supplier->name;
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $this->template->scripts[] = 'bootstrap.validate';
        $this->template->scripts[] = 'bootstrap.validate.ru';
        $this->template->scripts[] = "common/supplier_payments_list";
        $this->template->scripts[] = 'jquery-ui-1.10.3.custom.min';
    }


    public function action_get_act_excel()
    {
        $this->auto_render = FALSE;
        $this->is_ajax = TRUE;
        if ($this->request->method() == Request::POST)
        {
            $data = [
                'returns' => unserialize($_POST['returns']),
                'delivery' => unserialize($_POST['delivery']),
                'costs' => unserialize($_POST['costs']),
                'orders' => unserialize($_POST['orders']),
                'supplier' => unserialize($_POST['supplier']),
                'date_from' => $_POST['date_from'],
                'date_to' => $_POST['date_to'],
                'balance_before' => unserialize($_POST['balance_before']),
                'balance_total' => unserialize($_POST['balance_total'])
            ];

//            $from = date("Y-m-d", strtotime($_POST["date_from"]));
//            $to = date("Y-m-d", strtotime($_POST["date_to"]));
//            if ($to < '2009-01-01') {
//                $to = date("Y-m-d");
//            }
            if ($_POST["variant"] == 1) {
                $this->get_work_position_short($data)->send();
            } else {
                $this->get_work_position_long($data)->send();
            }
        }
    }

    private function get_work_position_short($data = [])
    {
    }

    private function get_work_position_long($data = [])
    {

        $newData = [];
        $path = 'uploads/';
        $spreadsheet = Spreadsheet::factory(array(
            'author'  => 'Kohana-PHPExcel',
            'title'      => 'Report',
            'subject' => 'Subject',
            'description'  => 'Description',
            'path' => $path,
            'name' => ("suppliers_balance_long_".date('dmYHis')),
            'format' => 'Excel5',
        ));
        $spreadsheet->set_active_worksheet(0);
        $as = $spreadsheet->get_active_worksheet();
        $as->title("Баланс");

        $as->getDefaultStyle()->getFont()->setSize(10);

        $as->getColumnDimension('A')->setWidth(15);
        $as->getColumnDimension('B')->setWidth(50);
        $as->getColumnDimension('C')->setWidth(10);
        $as->getColumnDimension('D')->setWidth(10);
        $as->getColumnDimension('E')->setWidth(10);
        $as->getColumnDimension('F')->setWidth(10);
        $as->getColumnDimension('G')->setWidth(10);
        $as->getColumnDimension('H')->setWidth(10);
        $as->getColumnDimension('I')->setWidth(10);

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

        $as->setCellValue("A$current", "Поставщик:");
        $as->getStyle("A$current")->getFont()->setBold(true)->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);

        $as->setCellValue("B$current", $data['supplier']->name);
        $as->getStyle("B$current")->getFont()->setBold(true);

        $current ++;

        if(!empty($data['date_from'])) {
            $as->setCellValue("A$current", "Дата от:");
            $as->getStyle("A$current")->getFont()->setBold(true)->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);

            $as->setCellValue("B$current", $data['date_from']);
            $as->getStyle("B$current")->getFont()->setBold(true);

            $current ++;
        }

        if(!empty($data['date_to'])) {
            $as->setCellValue("A$current", "Дата до:");
            $as->getStyle("A$current")->getFont()->setBold(true)->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);

            $as->setCellValue("B$current", $data['date_to']);
            $as->getStyle("B$current")->getFont()->setBold(true);

            $current ++;
        }

        $as->setCellValue("A$current", "Валюта:");
        $as->getStyle("A$current")->getFont()->setBold(true)->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);

        $as->setCellValue("B$current", $data['supplier']->currency->code);
        $as->getStyle("B$current")->getFont()->setBold(true);

        $current += 2;


        $as->mergeCells("A$current:A".($current+1));
        $as->setCellValue("A$current", "Дата");
        $as->getStyle("A$current")->getFont()->setBold(true);

        $as->mergeCells("B$current:B".($current+1));
        $as->setCellValue("B$current", "Название");
        $as->getStyle("B$current")->getFont()->setBold(true);

        $as->mergeCells("C$current:C".($current+1));
        $as->setCellValue("C$current", "Сумма дебет");
        $as->getStyle("C$current")->getFont()->setBold(true);
        $as->getStyle("C$current")->getAlignment()->setWrapText(true);

        $as->mergeCells("D$current:D".($current+1));
        $as->setCellValue("D$current", "Возврат денег");
        $as->getStyle("D$current")->getFont()->setBold(true);
        $as->getStyle("D$current")->getAlignment()->setWrapText(true);

        $as->mergeCells("E$current:E".($current+1));
        $as->setCellValue("E$current", "Доставка поставщика");
        $as->getStyle("E$current")->getFont()->setBold(true);
        $as->getStyle("E$current")->getAlignment()->setWrapText(true);

        $as->mergeCells("F$current:G$current");
        $as->getStyle("F$current")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $as->setCellValue("F$current", "Сумма кредит");
        $as->getStyle("F$current")->getFont()->setBold(true);

        $as->setCellValue("F".($current+1), "Платеж");
        $as->getStyle("F".($current+1))->getFont()->setBold(true);
        $as->setCellValue("G".($current+1), "Возврат");
        $as->getStyle("G".($current+1))->getFont()->setBold(true);

        if($data['supplier']->currency->code != 'UAH')
        {
            $as->mergeCells("H$current:I$current");
            $as->getStyle("H$current")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $as->setCellValue("H$current", "Текущее сальдо");
            $as->getStyle("H$current")->getFont()->setBold(true);

            $as->setCellValue("H".($current+1), $data['supplier']->currency->code);
            $as->getStyle("H".($current+1))->getFont()->setBold(true);
            $as->setCellValue("I".($current+1), "UAH");
            $as->getStyle("I".($current+1))->getFont()->setBold(true);
        }
        else
        {
            $as->mergeCells("H$current:H".($current+1));
            $as->setCellValue("H$current", "Текущее сальдо");
            $as->getStyle("H$current")->getFont()->setBold(true);
            $as->getStyle("H$current")->getAlignment()->setWrapText(true);
        }

        $as->getStyle("A$current:I$current")->applyFromArray($styleArray);

        $current +=2;


        $as->mergeCells("A$current:B$current");
        $as->getStyle("A$current")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

        $as->setCellValue("A$current", "Сальдо до:");
        $as->getStyle("A$current")->getFont()->setBold(true);



        $curr_name = $data['supplier']->currency->code;

        foreach ($data['supplier']->saldos->find_all()->as_array() as $saldo)
        {
            if(isset($all_period_balance[$saldo->currency->code]))
                $all_period_balance[$saldo->currency->code] += $saldo->value;

            else
                $all_period_balance[$saldo->currency->code] = $saldo->value;
        }

        if($data['date_from'] != '19.10.2017')
        {
            if(isset($all_period_balance[$curr_name]))
            {
                $all_period_balance[$curr_name] += $data['balance_before']['payment'] + $data['balance_before']['returns'] - $data['balance_before']['orders'];
            }
            else
            {
                $all_period_balance[$curr_name] = $data['balance_before']['payment'] + $data['balance_before']['returns'] - $data['balance_before']['orders'];
            }

            $all_period_balance['UAH'] += $data['balance_before']['delivery'];
        }


        foreach ($all_period_balance as $balanceName => $balanceValue)
        {
            switch ($balanceName) {
                case 'UAH':
                    $as->setCellValue("I$current", $balanceValue);
                    $as->getStyle("I$current")->getFont()->setBold(true);
                    break;

                case $curr_name:
                    $as->setCellValue("H$current", $balanceValue);
                    $as->getStyle("H$current")->getFont()->setBold(true);
                    break;
            }
        }

        $as->getStyle("A$current:I$current")->applyFromArray($styleArray);


        $sum_cost = 0;
        $sum_return = 0;
        $sum_cash_return = 0;
        $sum_payment = 0;
        $sum_result = 0;

        $flag = true;

        foreach ($data['orders'] as $supplier_order)
        {
            $newData[] = [
                'type' => 'orders',
                'date_time' => $supplier_order->date_time,
                'item' => $supplier_order
            ];
        }

//        проплаты
        foreach ($data['costs'] as $sp)
        {
            $newData[] = [
                'type' => 'costs',
                'date_time' => $sp->date_time,
                'item' => $sp
            ];
        }

//        Доставки
        foreach ($data['delivery'] as $del)
        {
            $newData[] = [
                'type' => 'delivery',
                'date_time' => $del->created,
                'item' => $del
            ];
        }

//        Возвраты
        $current ++;

        foreach ($data['returns'] as $return)
        {
            $newData[] = [
                'type' => 'returns',
                'date_time' => $return->date_time,
                'item' => $return
            ];
        }
        usort($newData, 'sort_objects_by_date');

        $sum_cost = 0;
        $sum_return = 0;
        $sum_cash_return = 0;
        $sum_payment = 0;
        $sum_result = 0;

        foreach ($newData as $key => $row)
        {
            switch ($row['type']){
                case 'costs':

                    $cost = 0;
                    $payment = $row['item']->value;
                    $return = 0;
                    $delivery = 0;

                    if ($payment >= 0)
                    {
                        $name = 'Проплата (' . $row['item']->comment_text . ')';
                        $sum_payment += $payment;
                        $cash_return = 0;
                        $all_period_balance[$curr_name] += $payment;
                    }
                    else
                    {
                        $name = 'Возврат денег (' . $row['item']->comment_text . ')';
                        $cash_return = $payment;
                        $sum_cash_return += $cash_return;
                        $payment = 0;
                        $all_period_balance[$curr_name] -= $payment;
                    }
                    break;
                case 'orders':
                    $position_count = 0;
                    $supp_order_total = 0;
                    $delivery = 0;

                    $allOrderPosition = [];
                    $allOrderPosition = $row['item']->orderitemssupplier->find_all()->as_array();

                    foreach ($allOrderPosition as $one_position)
                    {
                        $supp_order_total += round(($one_position->orderitem->amount * $one_position->orderitem->purchase_per_unit_in_currency),2);
                        $position_count += $one_position->orderitem->amount;
                    }

                    $name = "Заказ: ". $row['item']->order_supplier." (".$position_count."шт.)";
                    $cost = $supp_order_total;
                    $payment = 0;
                    $return = 0;
                    $cash_return = 0;
                    $sum_cost += $cost;
                    $all_period_balance[$curr_name] -= $cost;
                    break;
                case 'returns':
                    $name = "Возврат: ". $row['item']->orderitem->brand." ".$row['item']->orderitem->article." (".$row['item']->orderitem->amount."шт.) Заказ№".$row['item']->orderitem->supp_order->supplier_order->order_supplier;
                    $payment = 0;
                    $delivery = 0;
                    $cost = 0;
                    $cash_return = 0;
                    $return = round(($row['item']->orderitem->purchase_per_unit_in_currency) * $row['item']->orderitem->amount, 2);
                    $sum_return += $return;
                    $all_period_balance[$curr_name] += $return;
                    break;
                case 'delivery':
                    $name = "Доставка от поставщика";
                    $cost = 0;
                    $cash_return = 0;
                    $delivery = $row['item']->amount;
                    $payment = 0;
                    $return = 0;
//                    $all_period_balance['UAH'] += $row['item']->amount;
                    break;
            }

            $as->setCellValue("A$current", date('d.m.Y H:i:s', strtotime($row['date_time'])));
            $as->setCellValue("B$current", $name);
            $as->setCellValue("C$current", $cost);
            $as->setCellValue("D$current", -$cash_return);
            $as->setCellValue("E$current", $delivery);
            $as->setCellValue("F$current", $payment);
            $as->setCellValue("G$current", $return);
            if($curr_name != 'UAH')
            {
                $as->setCellValue("H$current", $all_period_balance[$curr_name]);
                $as->setCellValue("I$current", $all_period_balance['UAH']);
            }
            else
                $as->setCellValue("H$current", $all_period_balance[$curr_name]);

            $as->getStyle("A$current:I$current")->applyFromArray($styleArray);

            $current++;

            $lineGrop = $current - 1;
            if($row['type'] == 'orders')
            {
                foreach ($allOrderPosition as $one_position)
                {
                    $as->setCellValue("B$current", "№:".$one_position->orderitem->order_id.";артикул:".$one_position->orderitem->article."(".$one_position->orderitem->amount." шт) ".round(($one_position->orderitem->amount * $one_position->orderitem->purchase_per_unit_in_currency),2)."".$curr_name." ;");

                    $as->getRowDimension($current)->setOutlineLevel(1);
                    $as->getRowDimension($current)->setVisible(false);
                    $current++;
                }
            }
        }

        return $spreadsheet;
    }

    public function action_edit() {
        if(!ORM::factory('Permission')->checkPermission('supplier_payments')) Controller::redirect('admin');

        $id = $this->request->param('id');
        if(empty($id)) Controller::redirect('admin/supplierpayment/list');

        $this->template->content = View::factory('admin/supplier_payments/form')
            ->bind('permissions', $permissions)
            ->bind('errors', $errors)
            ->bind('message', $message)
            ->bind('currencies', $currencies)
            ->bind('data', $data);

        $this->template->title = 'Редактирование поставщика';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $supplierpayment = ORM::factory('SupplierPayment')->where('id', '=', $id)->find();
        $data = array();
        $data['name'] = $supplierpayment->name;
        $data['phone'] = $supplierpayment->phone;
        $data['delivery_days'] = $supplierpayment->delivery_days;
        $data['сomment_text'] = $supplierpayment->сomment_text;
        $data['currency_id'] = $supplierpayment->currency_id;

        if (HTTP_Request::POST == $this->request->method())
        {
            try {
                $supplierpayment->values($this->request->post(), array(
                    'name',
                    'phone',
                    'delivery_days',
                    'сomment_text',
                    'currency_id',
                ));
                $supplierpayment->save();

                // Reset values so form is not sticky
                $_POST = array();

                Controller::redirect('admin/supplierpayment/list');
            } catch (ORM_Validation_Exception $e) {
                $data = $_POST;
                // Set failure message
                $message = 'Исправте ошибки!';

                // Set errors using custom messages
                $errors = $e->errors('models');
            }
        }

        $currencies = array();

        foreach(ORM::factory('Currency')->find_all()->as_array() as $currency) {
            $currencies[$currency->id] = $currency->name;
        }
    }

    public function action_delete() {
        if(!ORM::factory('Permission')->checkPermission('supplier_payments')) Controller::redirect('admin');

        $this->template->title = '';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $id = $this->request->param('id');
        if(!empty($id)) {
            $supplierpayment = ORM::factory('SupplierPayment')->where('id', '=', $id)->find();
            $supplierpayment->delete();
        }

        Controller::redirect('admin/supplierpayment/list');
    }
}

class Validation_Exception extends Exception {};

function sort_items_by_date($a, $b)
{
    if ($a->date_time == $b->date_time) {
        return 0;
    }
    return ($a->date_time > $b->date_time) ? -1 : 1;
}

function sort_objects_by_date($a, $b) {
    if($a['date_time'] == $b['date_time']){ return 0 ; }
    return ($a['date_time'] < $b['date_time']) ? -1 : 1;
}