<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Delivery extends Controller_Admin_Application {
	
	public function action_list() {
		if(!ORM::factory('Permission')->checkPermission('delivery')) Controller::redirect('admin');

		$this->template->content = View::factory('admin/delivery/list')
			->bind('delivery_methods', $delivery_methods);
		$this->template->title = 'Delivery';
		$this->template->description = '';
		$this->template->keywords = '';
		$this->template->author = '';
		
		$delivery_methods = ORM::factory('DeliveryMethod')->find_all()->as_array();
		
		$this->template->scripts[] = "common/delivery_list";
	}

    public function action_get_act()
    {
        $this->template->title = 'Акт доставок';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $this->template->content = View::factory('admin/delivery/act')
            ->bind('data', $data)
            ->bind('message', $message)
            ->bind('results', $results);

        $query = "SELECT  o.id as order_id, oi.id as position_id, oi.article, oi.amount, oi.sale_per_unit*oi.amount as sale_per_unit, oi.brand, o.client_id, c.`name`, c.surname, c.phone, o.delivery_address,  o.manager_comment, o.ready_order FROM
        orderitems as oi
        INNER JOIN orders as o ON o.id = oi.order_id
        INNER JOIN clients as c ON c.id = o.client_id
        WHERE oi.state_id = 3 AND o.delivery_method_id = 6 AND o.ready_order = 0
        ORDER BY o.client_id";
        $results = DB::query(Database::SELECT,$query)->execute('default')->as_array();


        $this->template->scripts[] = 'bootstrap.validate';
        $this->template->scripts[] = 'bootstrap.validate.ru';
        $this->template->scripts[] = 'common/supplieract';
        $this->template->scripts[] = 'common/orders_list_items';
    }

    public function action_get_act_suppliers()
    {
        $this->template->title = 'Акт доставок поставщиков';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $this->template->content = View::factory('admin/delivery/act_supplier')
            ->bind('data', $data)
            ->bind('message', $message)
            ->bind('results_tsks', $results_tsks)
            ->bind('results', $new_results);

        $query = "SELECT oi.order_id, oi.date_time, oi.id as position_id, oi.article, oi.brand, oi.`name`, oi.amount, s.`name`, oi.supplier_id,  oi.purchase_per_unit, s.address FROM orderitems as oi
            INNER JOIN suppliers as s ON s.id = oi.supplier_id
            INNER JOIN orders as o ON o.id = oi.order_id
            WHERE oi.state_id = 13 AND o.archive = 0
            ORDER BY oi.supplier_id";

        $results = DB::query(Database::SELECT,$query)->execute('default')->as_array();
        $today = date("Y-m-d");
        $query_tsks = "SELECT ds.id, ds.data, ds.text, ds.value FROM delivery_tasks as ds LEFT JOIN users ON ds.user_id = users.id where DATE(data) = '".$today."'";
        $results_tsks = DB::query(Database::SELECT,$query_tsks)->execute('default')->as_array();

        $new_results=[];

        foreach ($results as $result)
        {
            if(!isset($new_results[$result['supplier_id']]))
            {
                $new_results[$result['supplier_id']][] = ['article' => $result['article'], 'date_time' => $result['date_time'], 'amount' => $result['amount'], 'purchase_per_unit' => $result['purchase_per_unit'], 'name' => $result['name'], 'address' => $result['address'], 'name' => $result['name']];
            }
            else
            {
                $new_results[$result['supplier_id']][] = ['article' => $result['article'], 'date_time' => $result['date_time'], 'amount' => $result['amount'], 'purchase_per_unit' => $result['purchase_per_unit'], 'name' => $result['name'], 'address' => $result['address'], 'name' => $result['name']];
            }
        }

        $this->template->scripts[] = 'bootstrap.validate';
        $this->template->scripts[] = 'bootstrap.validate.ru';
        $this->template->scripts[] = 'common/supplieract';
        $this->template->scripts[] = 'common/orders_list_items';
    }

    public function action_add_task()
    {
        if(empty($_POST['value']) )$_POST['value'] =0 ;
        $query_tsks = "INSERT INTO delivery_tasks (delivery_tasks.text, delivery_tasks.`value`) VALUES ('".$_POST['comment_text']."',".$_POST['value'].");";
        $results_tsks = DB::query(Database::INSERT,$query_tsks)->execute('default');
        Controller::redirect('/admin/delivery/get_act_suppliers');
    }
    public function action_delete_task()
    {
        $query_tsks = "DELETE FROM delivery_tasks WHERE id = ".$_GET['id'].";";
        $results_tsks = DB::query(Database::DELETE,$query_tsks)->execute('default');
        Controller::redirect('/admin/delivery/get_act_suppliers');
    }

//    public function action_get_act_excel()
//    {
//        $this->auto_render = FALSE;
//        $this->is_ajax = TRUE;
//        if ($this->request->method() == Request::POST) {
//            $this->get_act_delivery()->send();
//        }
//    }

    public function action_get_act_excel_suppliers()
    {
        $this->auto_render = FALSE;
        $this->is_ajax = TRUE;
        if ($this->request->method() == Request::POST) {

            if(isset($_POST['ids']))
            {
                $ids = implode(",",($_POST['ids']));
                $query = "SELECT oi.order_id, oi.id as position_id, oi.article, oi.brand, oi.`name`, oi.amount, s.`name`, oi.supplier_id,  oi.purchase_per_unit, s.address FROM orderitems as oi
                    INNER JOIN suppliers as s ON s.id = oi.supplier_id
                    INNER JOIN orders as o ON o.id = oi.order_id
                    WHERE oi.state_id = 13 AND oi.supplier_id IN (".$ids.") AND o.archive = 0
                    ORDER BY oi.supplier_id";

                $results = DB::query(Database::SELECT,$query)->execute('default')->as_array();

                $new_results=[];

                foreach ($results as $result)
                {
                    if(!isset($new_results[$result['supplier_id']]))
                    {
                        $new_results[$result['supplier_id']][] = ['article' => $result['article'], 'amount' => $result['amount'], 'purchase_per_unit' => $result['purchase_per_unit'], 'name' => $result['name'], 'address' => $result['address'], 'name' => $result['name']];
                    }
                    else
                    {
                        $new_results[$result['supplier_id']][] = ['article' => $result['article'], 'amount' => $result['amount'], 'purchase_per_unit' => $result['purchase_per_unit'], 'name' => $result['name'], 'address' => $result['address'], 'name' => $result['name']];
                    }
                }

                $this->get_act_delivery($new_results)->send();
            }
            else
            {
                $this->get_act_delivery()->send();
            }


        }
    }

    public function action_get_act_excel_client()
    {
        $this->auto_render = FALSE;
        $this->is_ajax = TRUE;
        if ($this->request->method() == Request::POST) {

            $query_all = "SELECT  o.id as order_id, oi.id as position_id, oi.article, u.name as manager_name, u.surname as manager_surname, u.phone_number as manager_phone, oi.amount, oi.sale_per_unit*oi.amount as sale_per_unit, oi.brand, o.client_id, c.`name`, c.surname, c.phone, o.delivery_address,  o.manager_comment, o.ready_order FROM
                orderitems as oi
                INNER JOIN orders as o ON o.id = oi.order_id
                INNER JOIN clients as c ON c.id = o.client_id
                INNER JOIN users as u ON o.manager_id = u.id
                WHERE oi.state_id = 3 AND o.delivery_method_id = 6 AND o.ready_order = 2
                ORDER BY o.client_id";

            if(isset($_POST['ids']))
            {
                $ids = implode(",",($_POST['ids']));
                $query = "SELECT  o.id as order_id, oi.id as position_id, oi.article, u.name as manager_name, u.surname as manager_surname, u.phone_number as manager_phone, oi.amount, oi.sale_per_unit*oi.amount as sale_per_unit, oi.brand, o.client_id, c.`name`, c.surname, c.phone, o.delivery_address,  o.manager_comment, o.ready_order FROM
                orderitems as oi
                INNER JOIN orders as o ON o.id = oi.order_id
                INNER JOIN clients as c ON c.id = o.client_id
                INNER JOIN users as u ON o.manager_id = u.id
                WHERE oi.state_id = 3 AND o.delivery_method_id = 6 AND oi.id IN (".$ids.")
                ORDER BY o.client_id";

                $manager_result = DB::query(Database::SELECT,$query)->execute('default')->as_array();
            }

            $results = DB::query(Database::SELECT,$query_all)->execute('default')->as_array();

            $clients_ids = [];

            $new_results=[];

            foreach ($results as $result)
            {
                if(!isset($new_results[$result['client_id']]))
                {
                    $new_results[$result['client_id']][] = ['article' => $result['article'], 'manager_name' => $result['manager_name'], 'manager_surname' => $result['manager_surname'], 'manager_phone' => $result['manager_phone'],  'amount' => $result['amount'], 'sale_per_unit' => $result['sale_per_unit'], 'name' => $result['name'], 'surname' => $result['surname'], 'phone' => $result['phone'], 'delivery_address' => $result['delivery_address'], 'manager_comment' => $result['manager_comment']];
                    $clients_ids[$result['client_id']]=[];
                }
                else
                {
                    $new_results[$result['client_id']][] = ['article' => $result['article'], 'manager_name' => $result['manager_name'], 'manager_surname' => $result['manager_surname'], 'manager_phone' => $result['manager_phone'], 'amount' => $result['amount'], 'sale_per_unit' => $result['sale_per_unit'], 'name' => $result['name'], 'surname' => $result['surname'], 'phone' => $result['phone'], 'delivery_address' => $result['delivery_address'], 'manager_comment' => $result['manager_comment']];
                }
            }

            if(isset($manager_result))
            {
                foreach ($manager_result as $result)
                {
                    if(!isset($new_results[$result['client_id']]))
                    {
                        $new_results[$result['client_id']][] = ['article' => $result['article'], 'manager_name' => $result['manager_name'], 'manager_surname' => $result['manager_surname'], 'manager_phone' => $result['manager_phone'], 'amount' => $result['amount'], 'sale_per_unit' => $result['sale_per_unit'], 'name' => $result['name'], 'surname' => $result['surname'], 'phone' => $result['phone'], 'delivery_address' => $result['delivery_address'], 'manager_comment' => $result['manager_comment']];
                        $clients_ids[$result['client_id']]=[];
                    }
                    else
                    {
                        $new_results[$result['client_id']][] = ['article' => $result['article'], 'manager_name' => $result['manager_name'], 'manager_surname' => $result['manager_surname'], 'manager_phone' => $result['manager_phone'], 'amount' => $result['amount'], 'sale_per_unit' => $result['sale_per_unit'], 'name' => $result['name'], 'surname' => $result['surname'], 'phone' => $result['phone'], 'delivery_address' => $result['delivery_address'], 'manager_comment' => $result['manager_comment']];
                    }
                }
            }

            foreach ($clients_ids as $clients_id=>$key)
            {
                $result_balance = Article::get_real_client_balance((integer)$clients_id);
                $balance = $result_balance[1] - $result_balance[0];
                $clients_ids[$clients_id][] = $balance;
            }
            $this->get_act_delivery_clients($new_results, $clients_ids)->send();
        }
    }

	public function get_act_delivery($new_results = [])
    {

        $path = 'uploads/';
        $spreadsheet = Spreadsheet::factory(array(
            'author'  => 'Kohana-PHPExcel',
            'title'      => 'Report',
            'subject' => 'Subject',
            'description'  => 'Description',
            'path' => $path,
            'name' => ("Suppliers__".date('dmYHis')),
            'format' => 'Excel5',
        ));
        $spreadsheet->set_active_worksheet(0);
        $as = $spreadsheet->get_active_worksheet();
        $as->title("Report");

        $as->getDefaultStyle()->getFont()->setSize(10);

        $as->getColumnDimension('A')->setWidth(15);
        $as->getColumnDimension('B')->setWidth(50);
        $as->getColumnDimension('B')->setWidth(70);
        $as->getColumnDimension('C')->setWidth(25);
        $as->getColumnDimension('D')->setWidth(50);

        $current = 1;

        $today_excel = date("d.m.Y H:i:s");
        $as->mergeCells("A$current:D$current");
        $as->getStyle("A$current")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $as->getStyle("A$current")->getFont()->setBold(true);
        $as->setCellValue("A$current", $today_excel);

        $current = 2;

        $as->mergeCells("A$current:D$current");
        $as->getStyle("A$current")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $as->getStyle("A$current")->getFont()->setBold(true);
        $as->setCellValue("A$current", "ВСЕ ЗАКАЗЫ У ПОСТАВЩИКОВ:");

        $current = 3;

        $as->setCellValue("A$current", "Поставщик:");
        $as->getStyle("A$current")->getFont()->setBold(true)->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);

        $as->setCellValue("B$current", "Articles:");
        $as->getStyle("B$current")->getFont()->setBold(true);

        $as->setCellValue("C$current", "Сумма:");
        $as->getStyle("C$current")->getFont()->setBold(true);

        $as->setCellValue("D$current", "Адресс:");
        $as->getStyle("D$current")->getFont()->setBold(true);


        $all_work_position = ORM::factory('Orderitem')->and_where_open()->where('state_id', '=', 2)->or_where('state_id', '=', 8) ->and_where_close()->order_by('supplier_id')->find_all()->as_array();
//        var_dump($all_work_position); exit();

//        $today = date("d.m.Y");
        $today = new DateTime("now");

        $new_all_work_position = [];

        foreach ($all_work_position as $position => $key)
        {
            $d = new DateTime($key->date_time ? $key->date_time : $key->order->date_time);
            $order_date = $d;
            $delivery_days = $key->delivery_days;
            if ($key->supplier->order_to) {
                $order_to = str_replace('.', ':', $key->supplier->order_to);
                if ($order_date->format('H:i') < date('H:i', strtotime($order_to))) {
                    $delivery_days--;
                }
            }
            $order_date->modify('+' . $delivery_days . 'days');

//            if($order_date <= $today)
//            {
//                echo "Hello \n";
//                echo $key->order->id."\n\n\n";
//            }



            if($key->supplier->our_delivery == 1 AND $order_date->format('d.m.Y') <= $today)
                $new_all_work_position[] = $key;
        }
//        exit();
        unset($all_work_position);

        $current ++;

        if($new_all_work_position)
        {
            $unique_brand = $new_all_work_position[0]->supplier->name;
            $unique_address = $new_all_work_position[0]->supplier->address;
            $sum_curr = 0;

            foreach ($new_all_work_position as $position)
            {
                if($unique_brand == $position->supplier->name)
                {
                    $sum_curr = $sum_curr + (double)($position->purchase_per_unit*$position->amount);
                }
                else
                {
                    $as->setCellValue("A$current", $unique_brand);
                    $as->getStyle("A$current")->getFont()->setBold(true);

                    $as->getRowDimension($current)->setRowHeight(50);

                    $as->setCellValue("C$current", "Сумма: ".round($sum_curr, 2)." ГРН");
                    $as->getStyle("C$current")->getFont()->setBold(false);

                    $as->setCellValue("D$current", $unique_address);
                    $as->getStyle("D$current")->getFont()->setBold(false);

                    $current ++;

                    $unique_brand = $position->supplier->name;
                    $unique_address = $position->supplier->address;
                    $sum_curr = (double)($position->purchase_per_unit*$position->amount);
                }

//                echo "$position->article $position->amount \n";

                $as->setCellValue("B$current", iconv("windows-1251", "utf-8", ($position->article.' ('.$position->amount.'), '.iconv("utf-8", "utf-8",$as->getCell("B$current")->getValue()))));
                $as->getStyle("B$current")->getFont()->setBold(false);
                $as->getStyle("B$current")->getAlignment()->setWrapText(true);


                if($position == end($new_all_work_position)) {
                    $as->setCellValue("A$current", $unique_brand);
                    $as->getStyle("A$current")->getFont()->setBold(true);

                    $as->getRowDimension($current)->setRowHeight(50);

                    $as->setCellValue("C$current", "Сумма: ".round($sum_curr, 2)." ГРН");
                    $as->getStyle("C$current")->getFont()->setBold(false);

                    $as->setCellValue("D$current", $unique_address);
                    $as->getStyle("D$current")->getFont()->setBold(false);

                    $current ++;
                    $current ++;
                    $current ++;
                    $current ++;

                }
            }
        }

//        exit();

        $today = date("Y-m-d");

        $query_tsks = "SELECT * FROM delivery_tasks LEFT JOIN users ON delivery_tasks.user_id = users.id where DATE(data) = '".$today."'";
        $results_tsks = DB::query(Database::SELECT,$query_tsks)->execute('default')->as_array();

        if(!empty($new_results))
        {

            $as->mergeCells("A$current:D$current");
            $as->getStyle("A$current")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $as->getStyle("A$current")->getFont()->setBold(true);
            $as->setCellValue("A$current", "ВСЕ ВОЗВРАТЫ ПОЗИЦИЙ ПОСТАВЩИКАМ:");
            $current ++;

            $as->setCellValue("A$current", "Поставщик:");
            $as->getStyle("A$current")->getFont()->setBold(true)->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);

            $as->setCellValue("B$current", "Articles:");
            $as->getStyle("B$current")->getFont()->setBold(true);

            $as->setCellValue("C$current", "Сумма:");
            $as->getStyle("C$current")->getFont()->setBold(true);

            $as->setCellValue("D$current", "Адресс:");
            $as->getStyle("D$current")->getFont()->setBold(true);
            $current ++;


            foreach ($new_results as $client=>$key)
            {
                $as->setCellValue("A$current", $key[0]['name']);
                $as->getStyle("A$current")->getFont()->setBold(false);
                $as->getStyle("A$current")->getAlignment()->setWrapText(true);

                $as->getRowDimension($current)->setRowHeight(50);

                $as->setCellValue("D$current", $key[0]['address']);
                $as->getStyle("D$current")->getFont()->setBold(false);
                $as->getStyle("D$current")->getAlignment()->setWrapText(true);

                $cash_all = 0;

                foreach ($key as $arr=>$position)
                {
                    $cash_all = $cash_all + $position['purchase_per_unit']*$position['amount'];
                    $as->setCellValue("B$current", iconv("utf-8", "utf-8", ($position['article']."(".$position['amount']."), ".iconv("utf-8", "utf-8",$as->getCell("B$current")->getValue()))));
                }

                $as->getStyle("B$current")->getAlignment()->setWrapText(true);

                $as->setCellValue("C$current", $cash_all." грн.");
                $as->getStyle("C$current")->getFont()->setBold(false);
                $as->getStyle("C$current")->getAlignment()->setWrapText(true);

                $current ++;
            }
        }

        if(!empty($results_tsks))
        {
            $current ++;$current ++;

            $as->mergeCells("A$current:D$current");
            $as->getStyle("A$current")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $as->getStyle("A$current")->getFont()->setBold(true);
            $as->setCellValue("A$current", "ЗАДАНИЯ:");

            $current ++;

            $as->setCellValue("A$current", "Дата:");
            $as->getStyle("A$current")->getFont()->setBold(true)->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);

            $as->setCellValue("B$current", "Комментарий:");
            $as->getStyle("B$current")->getFont()->setBold(true);

            $as->setCellValue("C$current", "Сумма:");
            $as->getStyle("C$current")->getFont()->setBold(true);
            $current ++;

            foreach ($results_tsks as $task)
            {
                $as->setCellValue("A$current", $task['data']);
                $as->getStyle("A$current")->getFont()->setBold(false);
                $as->getStyle("A$current")->getAlignment()->setWrapText(true);

                $as->getRowDimension($current)->setRowHeight(50);

                $as->setCellValue("B$current", $task['text']);
                $as->getStyle("B$current")->getFont()->setBold(false);
                $as->getStyle("B$current")->getAlignment()->setWrapText(true);

                $as->setCellValue("C$current", $task['value']." грн");
                $as->getStyle("C$current")->getFont()->setBold(false);
                $as->getStyle("C$current")->getAlignment()->setWrapText(true);

                $current ++;
            }
        }
        
        return $spreadsheet;
    }

    public function get_act_delivery_clients($array_position, $array_clients)
    {
        $path = 'uploads/';
        $spreadsheet = Spreadsheet::factory(array(
            'author'  => 'Kohana-PHPExcel',
            'title'      => 'Report',
            'subject' => 'Subject',
            'description'  => 'Description',
            'path' => $path,
            'name' => ("Clients__".date('dmYHis')),
            'format' => 'Excel5',
        ));
        $spreadsheet->set_active_worksheet(0);
        $as = $spreadsheet->get_active_worksheet();
        $as->title("Report");

        $as->getDefaultStyle()->getFont()->setSize(10);

        $as->getColumnDimension('A')->setWidth(15);
        $as->getColumnDimension('B')->setWidth(50);
        $as->getColumnDimension('B')->setWidth(50);
        $as->getColumnDimension('C')->setWidth(20);
        $as->getColumnDimension('D')->setWidth(25);
        $as->getColumnDimension('E')->setWidth(20);

        $current = 1;

        $today_excel = date("d.m.Y H:i:s");
        $as->mergeCells("A$current:D$current");
        $as->getStyle("A$current")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $as->getStyle("A$current")->getFont()->setBold(true);
        $as->setCellValue("A$current", $today_excel);

        $current = 2;

        $as->mergeCells("A$current:D$current");
        $as->getStyle("A$current")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $as->getStyle("A$current")->getFont()->setBold(true);
        $as->setCellValue("A$current", "ВСЕ ЗАКАЗЫ КЛИЕНТОВ:");
        $current ++;
        $current ++;

        $as->setCellValue("A$current", "Клиент:");
        $as->getStyle("A$current")->getFont()->setBold(true)->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);

        $as->setCellValue("B$current", "Articles:");
        $as->getStyle("B$current")->getFont()->setBold(true);

        $as->setCellValue("C$current", "Сумма:");
        $as->getStyle("C$current")->getFont()->setBold(true);

        $as->setCellValue("D$current", "Адресс:");
        $as->getStyle("D$current")->getFont()->setBold(true);

        $as->setCellValue("D$current", "Комментарий:");
        $as->getStyle("D$current")->getFont()->setBold(true);

        $as->setCellValue("E$current", "Менеджер:");
        $as->getStyle("E$current")->getFont()->setBold(true);
        $current ++;

        if($array_position)
        {
            foreach ($array_position as $client=>$key)
            {
                $balance_position = 0;
                $unique_comment = "";
                foreach ($key as $arr=>$position)
                {
                    $balance_position = (float)$balance_position + ((float)$position['sale_per_unit']*(integer)$position['amount']);

                    $as->setCellValue("A$current", $position['name']." ".$position['surname']."\n ".$position['phone']);
                    $as->getStyle("A$current")->getFont()->setBold(false);
                    $as->getStyle("A$current")->getAlignment()->setWrapText(true);

                    $as->setCellValue("B$current", iconv("windows-1251", "utf-8", ($position['article'].' ('.$position['amount'].'), '.iconv("utf-8", "utf-8",$as->getCell("B$current")->getValue()))));
                    $as->getStyle("B$current")->getAlignment()->setWrapText(true);

                    $as->setCellValue("D$current", $position['delivery_address']);
                    $as->getStyle("D$current")->getFont()->setBold(false);
                    $as->getStyle("D$current")->getAlignment()->setWrapText(true);

                    $as->setCellValue("E$current", $position['manager_name']." ". $position['manager_surname']." ". $position['manager_phone']);
                    $as->getStyle("E$current")->getFont()->setBold(false);
                    $as->getStyle("E$current")->getAlignment()->setWrapText(true);

                    if($unique_comment != $position['manager_comment'])
                    {
                        $unique_comment = $position['manager_comment'];
                        $as->setCellValue("D$current", iconv("utf-8", "utf-8", ($position['manager_comment']."\n".iconv("utf-8", "utf-8",$as->getCell("D$current")->getValue()))));
                    }
                    $as->getStyle("D$current")->getFont()->setBold(false);
                    $as->getStyle("D$current")->getAlignment()->setWrapText(true);

                    $as->getRowDimension($current)->setRowHeight(80);

                }
//                new
                $client_object = ORM::factory('Client')->where('id', '=', $client)->find();
                $balance_client_debdt = $client_object->get_user_balance();
                $dolg = -$balance_client_debdt['active_balance'];
                if($dolg < $balance_position) {
                    if($dolg <= 0) {
                        $balance_position = 0;
                    } else {
                        $balance_position = $dolg;
                    }
                }
//                end new

//                $balance_position2 = (integer)$balance_position - (integer)$array_clients[$client][0];
//                if($balance_position2<0) $balance_position2 = 0;
                $as->setCellValue("C$current", "Стоимость позиций: ".(integer)$balance_position."грн / \nДолг с учетом баланса:".$balance_position." грн"); //$balance_position2
                $as->getStyle("C$current")->getFont()->setBold(false);
                $as->getStyle("C$current")->getAlignment()->setWrapText(true);
                $current ++;
            }
        }

        return $spreadsheet;
    }
	
	public function action_add() {
		if(!ORM::factory('Permission')->checkPermission('delivery')) Controller::redirect('admin');
		
		$this->template->content = View::factory('admin/delivery/form')
			->bind('permissions', $permissions)
			->bind('errors', $errors)
			->bind('message', $message)
			->bind('data', $data);
			
        $this->template->title = 'Add';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
			
		if (HTTP_Request::POST == $this->request->method()) 
		{			
			try {
				$delivery = ORM::factory('DeliveryMethod');
				$delivery->values($this->request->post(), array(
					'name',
					'price'	
				));
				$delivery->save();
				
				// Reset values so form is not sticky
				$_POST = array();
				
				Controller::redirect('admin/delivery/list');
			} catch (ORM_Validation_Exception $e) {
				$data = $_POST;
				// Set failure message
				$message = 'Исправте ошибки!';
				
				// Set errors using custom messages
				$errors = $e->errors('models');
			}
		}
	}
	
	public function action_edit() {
		if(!ORM::factory('Permission')->checkPermission('delivery')) Controller::redirect('admin');
		
		$id = $this->request->param('id');
		if(empty($id)) Controller::redirect('admin/delivery/list');
		
		$this->template->content = View::factory('admin/delivery/form')
			->bind('permissions', $permissions)
			->bind('errors', $errors)
			->bind('message', $message)
			->bind('data', $data);
			
        $this->template->title = 'Edit';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		$delivery = ORM::factory('DeliveryMethod')->where('id', '=', $id)->find();
		$data = array();
		$data['name'] = $delivery->name;
		$data['price'] = $delivery->price;
		
		if (HTTP_Request::POST == $this->request->method()) 
		{			
			try {
				$delivery->values($this->request->post(), array(
					'name',
					'price'	
				));
				$delivery->save();
				
				// Reset values so form is not sticky
				$_POST = array();
				
				Controller::redirect('admin/delivery/list');
			} catch (ORM_Validation_Exception $e) {
				$data = $_POST;
				// Set failure message
				$message = 'Исправте ошибки!';
				
				// Set errors using custom messages
				$errors = $e->errors('models');
			}
		}
	}
	
	public function action_delete() {
		if(!ORM::factory('Permission')->checkPermission('delivery')) Controller::redirect('admin');
		
        $this->template->title = '';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		$id = $this->request->param('id');
		if(!empty($id)) {
			$delivery = ORM::factory('DeliveryMethod')->where('id', '=', $id)->find();
			
			$delivery->delete();
		}
		
		Controller::redirect('admin/delivery/list');
	}

} // End Admin_User



class Validation_Exception extends Exception {};
