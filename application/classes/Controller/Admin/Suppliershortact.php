<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Suppliershortact extends Controller_Admin_Application {
	
	public function action_index()
	{
		if(!ORM::factory('Permission')->checkPermission('supplier_payments')) Controller::redirect('admin');
        $this->template->title = 'Короткая статистика по поставщикам';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		$this->template->content = View::factory('admin/suppliershortact/form')
			->bind('data', $data)
			->bind('message', $message);
		
		/* // $this->template->scripts[] = 'jquery-ui-1.10.4.custom.min'; */
		$this->template->scripts[] = 'bootstrap.validate';
		$this->template->scripts[] = 'bootstrap.validate.ru';
		$this->template->scripts[] = 'common/suppliershortact';
		
	}
	
	public function action_get_act() {
		if(!ORM::factory('Permission')->checkPermission('supplier_payments')) Controller::redirect('admin');
		
		$this->auto_render = FALSE;
		$this->is_ajax = TRUE;
		
		$return_data = array();

        $allowed_states = Model_Services::purchasedStates;

        if ($this->request->method() == Request::POST)
        {
			foreach(ORM::factory('Supplier')->find_all()->as_array() as $supplier) {

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

                $filters['supplier_id'] = $supplier->id;
                $data['supplier_id'] = $filters['supplier_id'];
                $supplier_payments = $supplier_payments->and_where('supplier_id', '=', $filters['supplier_id']);
                $orderitems_tmp = $orderitems_tmp->and_where('orderitems.supplier_id', '=', $filters['supplier_id']);

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

                $temp_id = 0;

                $dont_show = array('irretrievable');

                foreach ($states as $state) {

                    if ($state->state->text_id == 'return_to_supplier' AND !in_array($state->orderitem->state->text_id, $dont_show)) 
                    {
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
                $data['supplier'] = $supplier;

                if (!empty($_POST['date_from'])) $data['date_from'] = $_POST['date_from'];
                if (!empty($_POST['date_to'])) $data['date_to'] = $_POST['date_to'];
                $return_data[] = $data;
			}
        }

        $this->create_act($return_data)->send();
	}
	
	private function create_act($data = array(), $path = 'uploads/') {
		if(!ORM::factory('Permission')->checkPermission('supplier_payments')) Controller::redirect('admin');
		
		$spreadsheet = Spreadsheet::factory(array(
			'author'  => 'Kohana-PHPExcel',
			'title'      => 'Report',
			'subject' => 'Subject',
			'description'  => 'Description',
			'path' => $path,
			'name' => ("supplier_act_".date('dmYHis')),
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

		
		$current = 1;

		$as->setCellValue("A$current", " ID");
		$as->getStyle("A$current")->getFont()->setBold(true);

		$as->setCellValue("B$current", "Название");
		$as->getStyle("B$current")->getFont()->setBold(true);

        $as->setCellValue("C$current", "Валюта");
        $as->getStyle("C$current")->getFont()->setBold(true);

		$as->setCellValue("D$current", "Сумма до");
		$as->getStyle("D$current")->getFont()->setBold(true);
		
		$as->setCellValue("E$current", "Сумма за период");
		$as->getStyle("E$current")->getFont()->setBold(true);
		
		$as->setCellValue("F$current", "Сумма всего");
		$as->getStyle("F$current")->getFont()->setBold(true);

        $as->setCellValue("G$current", "Принято товаров на сумму");
        $as->getStyle("G$current")->getFont()->setBold(true);

        $as->setCellValue("H$current", "Возврат товаров на сумму");
        $as->getStyle("H$current")->getFont()->setBold(true);

		$as->getStyle("A$current:F$current")->applyFromArray($styleArray);
		
		$current += 1;

        foreach ($data AS $key => $item) {
            $sum_cost = 0;
            $sum_return = 0;
            $sum_payment = 0;
            $sum_result = 0;
            $before = 0;
            foreach ($item['data'] AS $k => $row) {

            switch ($row['type']){

                case 'payment':
                    $cost = 0;
                    $payment = $row['item']->value;
                    $return = 0;
                    $sum_payment += $payment;
                    break;
                case 'purchase':
                    $cost = round(($row['item']->currency->code == 'UAH' ? $row['item']->purchase_per_unit : $row['item']->purchase_per_unit_in_currency) * $row['item']->amount, 2);
                    $payment = 0;
                    $return = 0;
                    $sum_cost += $cost;
                    break;
                case 'return':
                    $payment = 0;
                    $cost = 0;
                    $return = round(($row['item']->currency->code == 'UAH' ? $row['item']->purchase_per_unit : $row['item']->purchase_per_unit_in_currency) * $row['item']->amount, 2);
                    $sum_return += $return;
                    break;
            }

            $datefrom = date('Y-m-d', strtotime($item['date_from']));
            $item_date = date('Y-m-d', strtotime($row['date_time']));

            if ($datefrom > $item_date) {
                $before = $before + $payment + $return - $cost;
                continue 1;
            }
            $sum_result = $sum_result + $payment + $return - $cost;

            }
            $as->setCellValue("A$current", $item['supplier']->id);
            $as->setCellValue("B$current", $item['supplier']->name);
            $as->setCellValue("C$current", $item['supplier']->currency->code);
            $as->setCellValue("D$current", $before);
            $as->setCellValue("E$current", $sum_result);
            $as->setCellValue("F$current", $before + $sum_result);
            $as->getStyle("A$current:F$current")->applyFromArray($styleArray);

            $current++;

        }
        return $spreadsheet;
	}
}

function sort_objects_by_date($a, $b) {
	if($a['date_time'] == $b['date_time']){ return 0 ; }
	return ($a['date_time'] < $b['date_time']) ? -1 : 1;
}