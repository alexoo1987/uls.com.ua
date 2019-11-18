<?php defined('SYSPATH') or die('No direct script access.');
ini_set('memory_limit', '512M');

class Controller_Admin_Pricedownload extends Controller_Admin_Application {
	
	
	public function action_get() {
		if(!ORM::factory('Permission')->checkPermission('pricedownload')) Controller::redirect('admin');
		
		$this->template->title = 'Выгрузка прайсов :: Шаг 1';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		$this->template->content = View::factory('admin/pricedownload/get_step1')
			->bind('discounts', $discounts)
			->bind('suppliers', $suppliers)
			->bind('data', $data);
		
		$this->template->scripts[] = 'bootstrap.validate';
		$this->template->scripts[] = 'bootstrap.validate.ru';
		$this->template->scripts[] = 'common/pricedownload_get_step1';
		
		$discounts = array("" => "---");
		
		foreach(ORM::factory('Discount')->order_by('id', 'asc')->find_all()->as_array() as $discount) {
			$discounts[$discount->id] = $discount->name;
		}

		$suppliers = array('0' => 'Все поставщики');
		
		foreach(ORM::factory('Supplier')->find_all()->as_array() as $supplier) {
			$suppliers[$supplier->id] = $supplier->name;
		}
	}
	
	public function action_get_step2() {
		if(!ORM::factory('Permission')->checkPermission('pricedownload')) Controller::redirect('admin');
		
		$this->template->content = View::factory('admin/pricedownload/get_step2')
			->bind('permissions', $permissions)
			->bind('lines_count', $lines_count)
			->bind('data', $data);
			
        $this->template->title = 'Выгрузка прайсов :: Шаг 2';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		$this->template->scripts[] = 'common/pricedownload_get_step2';
			
		if (HTTP_Request::POST == $this->request->method()) 
		{			
			$sess_data = array();
			$sess_data['last_id'] = 0;
			$sess_data['discount_id'] = $this->request->post('discount_id');
			$sess_data['lines_processed'] = 1;
			$sess_data['file_prefix'] = 1;
			$sess_data['suppliers'] = $_POST['suppliers'];
			
			$this->init_new_file($sess_data['file_prefix']);
			
			$priceitems = ORM::factory('Priceitem');
			$priceitems->reset(FALSE);
			
			if(!in_array('0', $sess_data['suppliers'])) {
				$priceitems = $priceitems->where('supplier_id', 'IN', $sess_data['suppliers']);
			}
			$lines_count = $priceitems->count_all();
			$sess_data['lines_count'] = $lines_count;
			
			Session::instance()->set("parser", $sess_data);
			
		} else Controller::redirect('admin/pricedownload/get');
	}
	
	private function init_new_file($file_prefix = "0") {
		$fp = fopen('uploads/eparts_price_'.$file_prefix.'.csv', 'w');
		$fields = array("Номер запчасти", "Производитель", "Описание", "Цена (грн)", "Количество", "Срок поставки (дней)");

		array_walk($fields, 'encodeCSV');
		fputcsv($fp, $fields, ';');

		fclose($fp);
	}
	
	public function action_proccess() {
        $this->auto_render = false;
		$json = array();
		
		$sess_data = Session::instance()->get('parser');
		$counter = 0;
		$lines_count = $sess_data['lines_count'];
		
		$f = fopen('uploads/eparts_price_'.$sess_data['file_prefix'].'.csv', 'a');
		$priceitems = ORM::factory('Priceitem');
		$priceitems->reset(FALSE);
		
		$priceitems = $priceitems->order_by("id", "asc")
								 ->where('id', '>', $sess_data['last_id']);
		
		$suppliers = $sess_data['suppliers'];
		
		if(!in_array('0', $suppliers)) {
			$priceitems = $priceitems->where('supplier_id', 'IN', $suppliers);
		}
		
		$priceitems = $priceitems->limit(2500)
								 ->find_all()
								 ->as_array();
		$priceitem = null;
		foreach($priceitems as $priceitem) {
			if($sess_data['lines_processed'] > 0 && $sess_data['lines_processed'] % 500000 == 0) {
				fclose($f);
				$sess_data['file_prefix']++;
				$this->init_new_file($sess_data['file_prefix']);
				$f = fopen('uploads/eparts_price_'.$sess_data['file_prefix'].'.csv', 'a');
			}
			$fields = array($priceitem->part->article_long, 
				            $priceitem->part->get_brand(),
							Article::shorten_string($priceitem->part->name, 3),
							$priceitem->get_price_for_client(false, true, $sess_data['discount_id']),
							(!empty($priceitem->amount) ? $priceitem->amount : '+'),
							$priceitem->delivery
							);
							
			array_walk($fields, 'encodeCSV');
			if(empty($fields[0]) || empty($fields[1]) || empty($fields[3])) {
				$sess_data['lines_processed']++;
				continue;
			}
			fputcsv($f, $fields, ';');
			
			$sess_data['lines_processed']++;
		}
		if($priceitem) {
			$sess_data['last_id'] = $priceitem->id;
		}
		
		Session::instance()->set("parser", $sess_data);
		
		$json['current'] = $sess_data['lines_processed'];
		$json['total'] = $lines_count;
		$json['status'] = ($sess_data['lines_processed'] >= $lines_count || count($priceitems) == 0) ? "complete" : "continue";
		fclose($f);
		
		if($json['status'] == "complete") {
			$zip = new ZipArchive();
			$filename = "uploads/eparts_price.zip";
			if(file_exists($filename)) @unlink($filename);

			if ($zip->open($filename, ZIPARCHIVE::CREATE)!==TRUE) {
				exit("Невозможно открыть <$filename>\n");
			}
			
			for($i = 1; $i <= $sess_data['file_prefix']; $i++) {
				$zip->addFile('uploads/eparts_price_'.$i.'.csv');
			}
			$zip->close();
		}
		
		$this->response->body(json_encode($json));
	}

} // End Admin_User



class Validation_Exception extends Exception {};
function encodeCSV(&$value, $key){
	try {
		$value = iconv('UTF-8', 'Windows-1251', $value);
	} catch(Exception $e) {}
}