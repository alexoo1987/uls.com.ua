<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Supplieract extends Controller_Admin_Application {
	
	public function action_index()
	{
		if(!ORM::factory('Permission')->checkPermission('supplieract')) Controller::redirect('admin');
        $this->template->title = 'Акт сверки поставщика';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		$this->template->content = View::factory('admin/supplieract/form')
			->bind('data', $data)
			->bind('message', $message)
			->bind('suppliers', $suppliers);
		
		$suppliers = array('' => '---');
		
		foreach(ORM::factory('Supplier')->find_all()->as_array() as $supplier) {
			$suppliers[$supplier->id] = $supplier->name;
		}
		
		/* // $this->template->scripts[] = 'jquery-ui-1.10.4.custom.min'; */
		$this->template->scripts[] = 'bootstrap.validate';
		$this->template->scripts[] = 'bootstrap.validate.ru';
		$this->template->scripts[] = 'common/supplieract';
		
	}
	
	public function action_get_act() {
		if(!ORM::factory('Permission')->checkPermission('supplieract')) Controller::redirect('admin');
		
		$this->auto_render = FALSE;
		$this->is_ajax = TRUE;
		
		$data = array();
		$data['items_to_print'] = array();

		if ($this->request->method() == Request::POST) {

            $total = array('payments' => 0, 'purchase' => array(), 'return' => array(), 'returns' => array(), 'orderitems' => 0, 'before' => 0, 'oi_in_currency' => array(), 'payments_table' => array());

            $allowed_states = array(3, 5, 13, 14, 17);

            $supplier_payments = ORM::factory('SupplierPayment');
            $supplier_payments->reset(FALSE);
            $orderitems_tmp = ORM::factory('OrderitemLog');
            $orderitems_tmp->reset(FALSE);

            $orderitems_tmp = $orderitems_tmp->join('orderitems')->on('orderitemlog.orderitem_id', '=', 'orderitems.id')
                ->where('orderitemlog.state_id', 'IN', $allowed_states);


            //Module work only from this date!!!
            if (!isset($_POST['date_from'])) $_POST['date_from'] = '08.08.2016';

            if (!empty($_POST['date_from'])) {
                $filters['date_from'] = $_POST['date_from'];
                $supplier_payments = $supplier_payments->and_where('date_time', '>=', date('Y-m-d 00:00:00', strtotime('08.08.2016')));
                $orderitems_tmp = $orderitems_tmp->and_where('orderitemlog.date_time', '>=', date('Y-m-d 00:00:00', strtotime('08.08.2016')));
            }

            if (!empty($_POST['date_to'])) {
                $filters['date_to'] = $_POST['date_to'];
                $supplier_payments = $supplier_payments->and_where('date_time', '<=', date('Y-m-d 23:59:59', strtotime($filters['date_to'])));
                $orderitems_tmp = $orderitems_tmp->and_where('orderitemlog.date_time', '<=', date('Y-m-d 23:59:59', strtotime($filters['date_to'])));
            }
            if (!empty($_POST['supplier_id'])) {
                $filters['supplier_id'] = $_POST['supplier_id'];
                $data['supplier_id'] = $filters['supplier_id'];
                $supplier_payments = $supplier_payments->and_where('supplier_id', '=', $filters['supplier_id']);
                $orderitems_tmp = $orderitems_tmp->and_where('orderitems.supplier_id', '=', $filters['supplier_id']);
            }

            if (!empty($_POST['user_id'])) {
                $filters['user_id'] = $_POST['user_id'];
                $supplier_payments = $supplier_payments->and_where('user_id', '=', $filters['user_id']);
            }

            if (!empty($_POST['state_id'])) {
                $filters['state_id'] = $_POST['state_id'];
                $orderitems_tmp = $orderitems_tmp->and_where('orderitems.state_id', '=', $filters['state_id']);
            }

            $supplier_payments = $supplier_payments->order_by('date_time', "desc")->find_all()->as_array();


            $data = array();


            //Write payments
            foreach ($supplier_payments as $sp) {
                $data[] = array(
                    'type' => 'payment',
                    'date_time' => $sp->date_time,
                    'item' => $sp
                );
            }

            $states = $orderitems_tmp
                ->order_by('orderitemlog.orderitem_id', "asc")->order_by('orderitemlog.date_time', "asc")->find_all()->as_array();

            $purchase = array();
            $return = array();
            $temp_id = 0;

            $dont_show = array('irretrievable');

            foreach ($states as $state) {

                if ($state->state->text_id == 'return_to_supplier' AND !in_array($state->orderitem->state->text_id, $dont_show) AND !in_array($state, $return)) {
                    $data[] = array(
                        'type' => 'return',
                        'date_time' => $state->date_time,
                        'item' => $state->orderitem
                    );
                }

                if ($temp_id != $state->orderitem_id) {
                    $temp_id = $state->orderitem_id;

                    $orderitem = $state->orderitem;
                    $firstState = $orderitem->logs->where('orderitemlog.state_id', 'IN', $allowed_states)->find();
                    if ($firstState->id != $state->id) continue;

                    $data[] = array(
                        'type' => 'purchase',
                        'date_time' => $state->date_time,
                        'item' => $state->orderitem
                    );
                }


            }

            usort($data, 'sort_objects_by_date');

            $data['data'] = $data;
            $data['supplier_id'] = $_POST['supplier_id'];
            $data['supplier'] = ORM::factory('Supplier')->where('id', '=', $data['supplier_id'])->find();

            if (!empty($_POST['date_from'])) $data['date_from'] = $_POST['date_from'];
            if (!empty($_POST['date_to'])) $data['date_to'] = $_POST['date_to'];

            $this->create_act($data)->send();
        }
/*
			$orderitems = ORM::factory('Orderitem')->with('order')->with('state');
			$orderitems_before = ORM::factory('Orderitem')->with('order')->with('state');
			
			$supplier_payments = ORM::factory('SupplierPayment');
			$supplier_payments_before = ORM::factory('SupplierPayment');
			
			$data['supplier_id'] = $_POST['supplier_id'];
			$data['supplier'] = ORM::factory('Supplier')->where('id', '=', $data['supplier_id'])->find();
			
			$orderitems = $orderitems->and_where('supplier_id', '=', $data['supplier_id']);
			$orderitems_before = $orderitems_before->and_where('supplier_id', '=', $data['supplier_id']);
			
			$orderitems = $orderitems->and_where('state.text_id', 'IN', $allowed_states_with_return);
			$orderitems_before = $orderitems_before->and_where('state.text_id', 'IN', $allowed_states);
			
			$supplier_payments = $supplier_payments->and_where('supplier_id', '=', $data['supplier_id']);
			$supplier_payments_before = $supplier_payments_before->and_where('supplier_id', '=', $data['supplier_id']);
			
			if(!empty($_POST['date_from'])) {
				$data['date_from'] = $_POST['date_from'];
				$orderitems_before = $orderitems_before->and_where('order.date_time', '<', date('Y-m-d 00:00:00', strtotime($data['date_from'])));
				$orderitems = $orderitems->and_where('order.date_time', '>=', date('Y-m-d 00:00:00', strtotime($data['date_from'])));
				
				$supplier_payments_before = $supplier_payments_before->and_where('date_time', '<', date('Y-m-d 00:00:00', strtotime($data['date_from'])));
				$supplier_payments = $supplier_payments->and_where('date_time', '>=', date('Y-m-d 00:00:00', strtotime($data['date_from'])));

				$orderitems_before = $orderitems_before->find_all()->as_array();
				$supplier_payments_before = $supplier_payments_before->find_all()->as_array();
			} else {
				$orderitems_before = false;
				$supplier_payments_before = false;
			}
			if(!empty($_POST['date_to'])) {
				$data['date_to'] = $_POST['date_to'];
				$orderitems = $orderitems->and_where('order.date_time', '<=', date('Y-m-d 23:59:59', strtotime($data['date_to'])));
				$supplier_payments = $supplier_payments->and_where('date_time', '<=', date('Y-m-d 23:59:59', strtotime($data['date_to'])));
			}
			
			$orderitems = $orderitems->order_by("order.date_time", "asc")->find_all()->as_array();
			$supplier_payments = $supplier_payments->order_by("date_time", "asc")->find_all()->as_array();
			
			$items_to_print = array();
			foreach($supplier_payments as $sp) {
				$item = array();
				$item['date_time'] = $sp->date_time;
				$item['type'] = "payment";
				$item['item'] = $sp;
				$items_to_print[] = $item;
			}
			
			foreach($orderitems as $orderitem) {
				$item = array();
				$item['date_time'] = $orderitem->order->date_time;
				$item['type'] = "orderitem";
				$item['item'] = $orderitem;
				$items_to_print[] = $item;
			}
			usort($items_to_print, 'sort_objects_by_date');

			$data['items_to_print'] = $items_to_print;
			
			$data['total_before'] = 0;
			
			if($orderitems_before && $supplier_payments_before) {
				foreach($supplier_payments_before as $sp) {
					$data['total_before'] += $sp->value;
				}
				
				foreach($orderitems_before as $orderitem) {
					$val = $orderitem->purchase_per_unit*$orderitem->amount;
					$data['total_before'] -= $val;
				}
			}
        }
		
		if(count($data['items_to_print'] > 0)) {
			$this->create_act($data)->send();
		}*/
	}
	
	private function create_act($data = array(), $path = 'uploads/') {

		if(!ORM::factory('Permission')->checkPermission('supplieract')) Controller::redirect('admin');
		
		$spreadsheet = Spreadsheet::factory(array(
			'author'  => 'Kohana-PHPExcel',
			'title'      => 'Report',
			'subject' => 'Subject',
			'description'  => 'Description',
			'path' => $path,
			'name' => ("supplier_".$data['supplier_id']."_".date('dmYHis')),
			'format' => 'Excel5',
		));
		$spreadsheet->set_active_worksheet(0);
		$as = $spreadsheet->get_active_worksheet();
		$as->title("Report");

		$as->getDefaultStyle()->getFont()->setSize(10);

		$as->getColumnDimension('A')->setWidth(15);
		$as->getColumnDimension('B')->setWidth(30);
		$as->getColumnDimension('C')->setWidth(10);
		$as->getColumnDimension('D')->setWidth(10);
		$as->getColumnDimension('E')->setWidth(10);
		$as->getColumnDimension('F')->setWidth(10);
		
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
		
		$as->mergeCells("E$current:F$current");
		$as->getStyle("E$current")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$as->setCellValue("E$current", "Сумма кредит");
		$as->getStyle("E$current")->getFont()->setBold(true);
		
		$as->setCellValue("E".($current+1), "Платеж");
		$as->getStyle("E".($current+1))->getFont()->setBold(true);
		$as->setCellValue("F".($current+1), "Возврат");
		$as->getStyle("F".($current+1))->getFont()->setBold(true);
		
		$as->mergeCells("G$current:G".($current+1));
		$as->setCellValue("G$current", "Текущее сальдо");
		$as->getStyle("G$current")->getFont()->setBold(true);
		$as->getStyle("G$current")->getAlignment()->setWrapText(true);
		
		$as->getStyle("A$current:G$current")->applyFromArray($styleArray);

        $current +=2;

        $as->mergeCells("A$current:B$current");
		$as->getStyle("A$current")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

		$as->setCellValue("A$current", "Сальдо до:");
		$as->getStyle("A$current")->getFont()->setBold(true);

        $as->getStyle("A$current:G$current")->applyFromArray($styleArray);


        $sum_cost = 0;
		$sum_return = 0;
		$sum_cash_return = 0;
		$sum_payment = 0;
        $sum_result = 0;

        $flag = true;

        foreach ($data['data'] AS $key => $row) {


            switch ($row['type']){

                case 'payment':

                    $cost = 0;
                    $payment = $row['item']->value;
                    $return = 0;

                    if ($payment >= 0) {
                        $name = 'Проплата (' . $row['item']->comment_text . ')';
                        $sum_payment += $payment;
                        $cash_return = 0;
                    } else {
                        $name = 'Возврат денег (' . $row['item']->comment_text . ')';
                        $cash_return = $payment;
                        $sum_cash_return += $cash_return;
                        $payment = 0;
                    }
                    break;
                case 'purchase':
                    $name = "Заказ: ". $row['item']->brand." ".$row['item']->article." (".$row['item']->amount."шт.)";

                    $cost = round(($row['item']->currency->code == 'UAH' ? $row['item']->purchase_per_unit : $row['item']->purchase_per_unit_in_currency) * $row['item']->amount, 2);
                    $payment = 0;
                    $return = 0;
                    $cash_return = 0;
                    $sum_cost += $cost;
                    break;
                case 'return':
                    $name = "Возврат: ". $row['item']->brand." ".$row['item']->article." (".$row['item']->amount."шт.)";
                    $payment = 0;
                    $cost = 0;
                    $cash_return = 0;
                    $return = round(($row['item']->currency->code == 'UAH' ? $row['item']->purchase_per_unit : $row['item']->purchase_per_unit_in_currency) * $row['item']->amount, 2);
                    $sum_return += $return;
                    break;
            }

            $datefrom = date('Y-m-d', strtotime($data['date_from']));
            $item_date = date('Y-m-d', strtotime($row['date_time']));

            if ($datefrom > $item_date) {
                $sum_result = $sum_result + $payment + $return - $cost + $cash_return;
                continue;
            } elseif ($flag AND $datefrom <= $item_date) {
                $sum_cost = $cost;
                $sum_return = $return;
                $sum_payment = $payment;
                $sum_cash_return = $cash_return;
                $as->setCellValue("G$current", $sum_result);
                $flag = false;
                $current++;
            }

            $sum_result = $sum_result + $payment + $return - $cost + $cash_return;

            $as->setCellValue("A$current", date('d.m.Y H:i:s', strtotime($row['date_time'])));
            $as->setCellValue("B$current", $name);
            $as->setCellValue("C$current", $cost);
            $as->setCellValue("D$current", -$cash_return);
            $as->setCellValue("E$current", $payment);
            $as->setCellValue("F$current", $return);
            $as->setCellValue("G$current", $sum_result);
            $as->getStyle("A$current:G$current")->applyFromArray($styleArray);

            $current++;

        }

		
		$as->mergeCells("A$current:B$current");
		$as->getStyle("A$current")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$as->setCellValue("A$current", "Общая сумма:");
		$as->getStyle("A$current")->getFont()->setBold(true);

		$as->setCellValue("C$current", $sum_cost);
		$as->getStyle("C$current")->applyFromArray($styleArray);

        $as->setCellValue("D$current", -$sum_cash_return);
        $as->getStyle("D$current")->applyFromArray($styleArray);
//
		$as->setCellValue("E$current", $sum_payment);
		$as->getStyle("E$current")->applyFromArray($styleArray);

		$as->setCellValue("F$current", $sum_return);
		$as->getStyle("F$current")->applyFromArray($styleArray);

		$as->setCellValue("G$current", $sum_result);
		$as->getStyle("G$current")->applyFromArray($styleArray);

        $current++;

        $as->setCellValue("A$current",  "Общая сумма доставок:");
        $as->getStyle("A$current")->getFont()->setBold(true);

        if(!empty($data['date_to'])){

            $from = date("Y-m-d", strtotime($data["date_from"]));
            $to = date("Y-m-d", strtotime($data["date_to"]));

            $delivery_costs = "SELECT * FROM costs where type = 3 AND supplier_id = ".$data['supplier_id']." AND DATE(date)>= '".$from."'  AND DATE(date) <= '".$to."'";
        }
        else{
            $from = date("Y-m-d", strtotime($data["date_from"]));
            $delivery_costs = "SELECT * FROM costs where type = 3 AND supplier_id = ".$data['supplier_id']." AND DATE(date)>= '".$from."'";
        }

//
//        var_dump($delivery_costs); exit();
        $delivery_costs = DB::query(Database::SELECT, $delivery_costs)->execute('tecdoc')->as_array();
        $all_supplier_cost = 0;

        if(!empty($delivery_costs)){
            foreach ($delivery_costs as $supp_cost){
                $all_supplier_cost += $supp_cost['amount'];
            }
        }

        $as->setCellValue("B$current",  $all_supplier_cost." грн");
        $as->getStyle("B$current")->getFont()->setBold(true);

		return $spreadsheet;
	}
}

function sort_objects_by_date($a, $b) {
	if($a['date_time'] == $b['date_time']){ return 0 ; }
	return ($a['date_time'] < $b['date_time']) ? -1 : 1;
}