<?php defined('SYSPATH') or die('No direct script access.');
ini_set('memory_limit', '512M');

class Controller_Admin_Suppliers extends Controller_Admin_Application {
	
	public function action_list() {
		if(!ORM::factory('Permission')->checkPermission('suppliers')) Controller::redirect('admin');
		
		$this->template->content = View::factory('admin/suppliers/list')
            ->bind('admin', $admin)
			->bind('suppliers', $suppliers);
		$this->template->title = 'Поставщики';
		$this->template->description = '';
		$this->template->keywords = '';
		$this->template->author = '';
		
		$suppliers = ORM::factory('Supplier')->where('dont_show','=',0)->find_all()->as_array();

        $var_id = Auth::instance()->get_user()->id;
        if(($var_id == 2)||($var_id == 74))
        {
            $admin = true;
        }
        else{
            $admin = false;
        }

		
		$this->template->scripts[] = "common/suppliers_list";
	}

    public function action_unlist() {
        if(!ORM::factory('Permission')->checkPermission('suppliers')) Controller::redirect('admin');

        $this->template->content = View::factory('admin/suppliers/list')
            ->bind('admin', $admin)
            ->bind('suppliers', $suppliers);
        $this->template->title = 'Поставщики';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $suppliers = ORM::factory('Supplier')->where('dont_show','=',1)->find_all()->as_array();

        $var_id = Auth::instance()->get_user()->id;
        if(($var_id == 2)||($var_id == 74))
        {
            $admin = true;
        }
        else{
            $admin = false;
        }


        $this->template->scripts[] = "common/suppliers_list";
    }
	
	public function action_add() {
		if(!ORM::factory('Permission')->checkPermission('suppliers')) Controller::redirect('admin');
		
		$this->template->content = View::factory('admin/suppliers/form')
			->bind('permissions', $permissions)
			->bind('errors', $errors)
			->bind('message', $message)
			->bind('currencies', $currencies)
			->bind('data', $data);
			
        $this->template->title = 'Добавить поставщика';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
			
		if (HTTP_Request::POST == $this->request->method()) 
		{
			try {
				$supplier = ORM::factory('Supplier');
				$supplier->values($this->request->post(), array(
					'name',
					'phone',
					'delivery_days',
					'сomment_text',
					'price_source',
					'notice',
					'currency_id',
					'order_to',
				));
				$supplier->dont_show = !empty($_POST['dont_show']) && $_POST['dont_show'] == 1 ? 1 : 0;
				$supplier->save();
				
				// Reset values so form is not sticky
				$_POST = array();
				
				Controller::redirect('admin/suppliers/list');
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
	
	public function action_edit() {
		if(!ORM::factory('Permission')->checkPermission('suppliers')) Controller::redirect('admin');
		
		$id = $this->request->param('id');
		if(empty($id)) Controller::redirect('admin/suppliers/list');
		
		$this->template->content = View::factory('admin/suppliers/form')
			->bind('permissions', $permissions)
			->bind('errors', $errors)
			->bind('message', $message)
			->bind('currencies', $currencies)
			->bind('data', $data);
			
        $this->template->title = 'Редактирование поставщика';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		$supplier = ORM::factory('Supplier')->where('id', '=', $id)->find();
		$data = array();
		$data['name'] = $supplier->name;
		$data['phone'] = $supplier->phone;
		$data['delivery_days'] = $supplier->delivery_days;
		$data['сomment_text'] = $supplier->сomment_text;
		$data['price_source'] = $supplier->price_source;
		$data['notice'] = $supplier->notice;
		$data['currency_id'] = $supplier->currency_id;
		$data['dont_show'] = $supplier->dont_show;
		$data['status'] = $supplier->status;
		$data['order_to'] = $supplier->order_to;
        $data['address'] = $supplier->address;
        $data['our_delivery'] = $supplier->our_delivery;

		if (HTTP_Request::POST == $this->request->method()) 
		{			
			try {
				$supplier->values($this->request->post(), array(
					'name',
					'phone',
					'delivery_days',
					'сomment_text',
					'price_source',
					'notice',
					'currency_id',
					'status',
                    'address',
					'order_to',
				));
				$supplier->dont_show = !empty($_POST['dont_show']) && $_POST['dont_show'] == 1 ? 1 : 0;
                $supplier->our_delivery = !empty($_POST['our_delivery']) && $_POST['our_delivery'] == 1 ? 1 : 0;
				$supplier->save();
				
				// Reset values so form is not sticky
				$_POST = array();
				
				Controller::redirect('admin/suppliers/list');
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
		if(!ORM::factory('Permission')->checkPermission('suppliers')) Controller::redirect('admin');
		
        $this->template->title = '';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		$id = $this->request->param('id');
		if(!empty($id)) {
			$supplier = ORM::factory('Supplier')->where('id', '=', $id)->find();
			
			DB::delete('priceitems')->where('supplier_id', '=', $id)->execute();
			$supplier->delete();
		}
		
		Controller::redirect('admin/suppliers/list');
	}
	
	public function action_update() {
		if(!ORM::factory('Permission')->checkPermission('suppliers')) Controller::redirect('admin');
		
		$this->template->title = 'Обновление прайсов :: Шаг 1';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		$this->template->content = View::factory('admin/suppliers/update')
			->bind('suppliers', $suppliers)
			->bind('data', $data);
		
		$this->template->scripts[] = 'bootstrap.validate';
		$this->template->scripts[] = 'bootstrap.validate.ru';
		$this->template->scripts[] = 'common/supplier_update';
		
		
		$suppliers = array('' => '---');
		
		foreach(ORM::factory('Supplier')->find_all()->as_array() as $supplier) {
			$suppliers[$supplier->id] = $supplier->name;
		}
	}
	
	public function action_update_step2() {
		if(!ORM::factory('Permission')->checkPermission('suppliers')) Controller::redirect('admin');
		
		$this->template->content = View::factory('admin/suppliers/update_step2')
			->bind('permissions', $permissions)
			->bind('supplier_id', $supplier_id)
			->bind('filepath', $filepath)
			->bind('columns', $columns)
			->bind('data', $data)
			->bind('process', $process);
			
        $this->template->title = 'Обновление прайсов :: Шаг 2';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		$this->template->scripts[] = 'bootstrap.validate';
		$this->template->scripts[] = 'bootstrap.validate.ru';
		$this->template->scripts[] = 'common/supplier_update_step2';
			
		if (HTTP_Request::POST == $this->request->method()) 
		{
			if($this->request->post('file_type') == 'xls') {
				throw new Exception("Can't find EXCEL module.");
			}
			$supplier_id = $this->request->post('supplier_id');
			$supplier = ORM::factory('Supplier')->where('id', '=', $supplier_id)->find();
			$process = $supplier->status == "process";
			$filepath = Upload::save($_FILES['filename'], "price_".$supplier_id.".csv", "uploads");
			
			
			$f = fopen('php://memory', 'w+');
			try {
				fwrite($f, iconv('CP1251', 'UTF-8', file_get_contents($filepath)));
			} catch (Exception $e) {
				try {
					fwrite($f, iconv('CP1251', 'UTF-8//IGNORE', file_get_contents($filepath)));
				} catch (Exception $e) {
					fwrite($f, mb_convert_encoding(file_get_contents($filepath), 'UTF-8'));
				}
			}
			rewind($f);
			$columns = fgetcsv($f, 0, ';', '"');
			fclose($f);
			
			$columns = array('' => '---') + $columns;
			
			$supplier = ORM::factory('Supplier')->where('id', '=', $supplier_id)->find();
			
			$data['delivery'] = $supplier->delivery_days;
			$data['ratio'] = 1;
	
			$tpl_obj = ORM::factory('Pricetemplate')->where('supplier_id', '=', $supplier_id)->find();
			if(!empty($tpl_obj->id)) {
				$data_from_json = json_decode($tpl_obj->json_data, true);
				$data['ratio'] = $data_from_json['ratio'];
				$data['stores'] = $data_from_json['stores'];
				$data['remove_first'] = $data_from_json['remove_first'];
				
				$data['article_column'] = $data_from_json['columns']['article'];
				$data['brand_column'] = $data_from_json['columns']['brand'];
				$data['price_column'] = $data_from_json['columns']['price'];
				$data['name_column'] = $data_from_json['columns']['name'];
			}
			
		} else Controller::redirect('admin/suppliers/update');
		
	}
	
	public function action_update_step3() {
		if(!ORM::factory('Permission')->checkPermission('suppliers')) Controller::redirect('admin');
		
		$this->template->content = View::factory('admin/suppliers/update_step3')
			->bind('permissions', $permissions)
			->bind('lines_count', $lines_count)
			->bind('data', $data);
			
        $this->template->title = 'Обновление прайсов :: Шаг 3';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		$this->template->scripts[] = 'common/supplier_update_step3';
			
		if (HTTP_Request::POST == $this->request->method()) 
		{
			$operation = ORM::factory('Operation');
			$operation->description = "Обновление прайсов";
			$operation->supplier_id = $this->request->post('supplier_id');
			$operation->save();
		
			$columns = array();
			
			$columns['article'] = $this->request->post('article_column');
			$columns['brand'] = $this->request->post('brand_column');
			$columns['price'] = $this->request->post('price_column');
			$columns['name'] = $this->request->post('name_column');
			
			$stores = array();
			foreach($_POST['amount_column'] as $key=>$val) {
				$values = array();
				$values['amount_column'] = $_POST['amount_column'][$key];
				$values['delivery_column'] = $_POST['delivery_column'][$key];
				$values['delivery'] = $_POST['delivery'][$key];
				
				$stores[] = $values;
			}
			
			$sess_data = array();
			$sess_data['filepath'] = $this->request->post('filepath');
			$sess_data['supplier_id'] = $this->request->post('supplier_id');
			$sess_data['ratio'] = $this->request->post('ratio');
			$sess_data['columns'] = $columns;
			$sess_data['stores'] = $stores;
			$sess_data['lines_processed'] = 1;
			$sess_data['remove_first'] = $this->request->post('remove_first');
			$sess_data['operation_id'] = $operation->id;
			
			$data_to_json = array();
			$data_to_json['ratio'] = $this->request->post('ratio');
			$data_to_json['columns'] = $columns;
			$data_to_json['stores'] = $stores;
			$data_to_json['remove_first'] = $this->request->post('remove_first');
			
			$json_str = json_encode($data_to_json);
			
			$tpl = ORM::factory('Pricetemplate')->where('supplier_id', '=', $sess_data['supplier_id'])->find();
			if(empty($tpl->id)) {
				$tpl = ORM::factory('Pricetemplate');
				$tpl->supplier_id = $sess_data['supplier_id'];
			}
			
			$tpl->json_data = $json_str;
			$tpl->save();
			
			$lines_count = 0;
			
			$f = fopen('php://memory', 'w+');
			try {
				fwrite($f, iconv('CP1251', 'UTF-8', file_get_contents($sess_data['filepath'])));
			} catch (Exception $e) {
				try {
					fwrite($f, iconv('CP1251', 'UTF-8//IGNORE', file_get_contents($sess_data['filepath'])));
				} catch (Exception $e) {
					fwrite($f, mb_convert_encoding(file_get_contents($sess_data['filepath']), 'UTF-8'));
				}
			}
			rewind($f);
			
			while (fgetcsv($f, 0, ';', '"') !== false) $lines_count++;
			
			fclose($f);
			
			$sess_data['lines_count'] = $lines_count;
			
			//ORM::factory('Priceitem')->where('supplier_id', '=', $sess_data['supplier_id'])->find_all()->delete();
			DB::delete('priceitems')->where('supplier_id', '=', $sess_data['supplier_id'])->execute();
			DB::delete('unmatched')->where('supplier_id', '=', $sess_data['supplier_id'])->execute();
Kohana::$log->add(Log::DEBUG, "Before set client job");
			$client = new GearmanClient();
			$client->addServer();
			$result = $client->doBackground("processfile", json_encode($sess_data));
Kohana::$log->add(Log::DEBUG, "After set client job");
			
			Controller::redirect('admin/suppliers/list');
			
			
		} else Controller::redirect('admin/suppliers/update');
	}
	
	public function action_gearmanworker(){
Kohana::$log->add(Log::DEBUG, "action_gearmanworker()");
		#session_write_close();
		#ignore_user_abort(false);
		#if (ob_get_level() == 0) ob_start();
		#echo "<script language='JavaScript'>location.href='".URL::site('admin/suppliers/list')."'</script>"; 
		#ob_flush();
		#flush();
		#ob_end_flush();

        // if ($this->request->method() == Request::POST){
			$worker= new GearmanWorker();
			$worker->addServer();

			$worker->addFunction("processfile", array($this, "process_file"));
			while ($worker->work());
			#$ret = $worker->work();
			#if ($worker->returnCode() != GEARMAN_SUCCESS) echo "GEARMAN RETURN CODE: False";
        // }
    }
 
    function process_file($job)
    {

        $workload = $job->workload();
        $sess_data = json_decode($workload, true);
	
		$supplier = ORM::factory('Supplier')->where('id', '=', $sess_data['supplier_id'])->find();
		$supplier->status = "process";
		$supplier->error = "";
		$supplier->total_processed = 0;
		$supplier->total_upload = $sess_data['lines_count'];
		
		$supplier->save();

		$returned_data = array();
		do {
			try {
				$tmp = $this->action_proccess($sess_data);
				
				$sess_data = $tmp[0];
				$returned_data = $tmp[1];
				
				$supplier->total_processed = $sess_data['lines_processed'];
				$supplier->save();
			} catch (Exception $e) {
				$supplier->status = "error";
				$supplier->error = $e->getMessage();
				$supplier->save();
				break;
			}
		} while(isset($returned_data['status']) && $returned_data['status'] == 'continue');
    }
} // End Admin_User



class Validation_Exception extends Exception {};
