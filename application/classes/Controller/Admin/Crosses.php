<?php defined('SYSPATH') or die('No direct script access.');
ini_set('memory_limit', '512M');
class Controller_Admin_Crosses extends Controller_Admin_Application {
	public function action_update() {
		if(!ORM::factory('Permission')->checkPermission('crosses')) Controller::redirect('admin');
		
		$this->template->title = 'Обновление кроссов :: Шаг 1';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		$this->template->content = View::factory('admin/crosses/update')
			->bind('data', $data);
		
		$this->template->scripts[] = 'bootstrap.validate';
		$this->template->scripts[] = 'bootstrap.validate.ru';
		$this->template->scripts[] = 'common/crosses_update';
	}
	
	public function action_update_step2() {
		if(!ORM::factory('Permission')->checkPermission('crosses')) Controller::redirect('admin');
		
		$this->template->content = View::factory('admin/crosses/update_step2')
			->bind('permissions', $permissions)
			->bind('filepath', $filepath)
			->bind('columns', $columns)
			->bind('data', $data);
			
        $this->template->title = 'Обновление кроссов :: Шаг 2';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		
		$this->template->scripts[] = 'bootstrap.validate';
		$this->template->scripts[] = 'bootstrap.validate.ru';
		$this->template->scripts[] = 'common/crosses_update_step2';
			
		if (HTTP_Request::POST == $this->request->method()) 
		{
			$filepath = Upload::save($_FILES['filename'], "price_".date("YmdHis").".csv", "uploads");
			
			$f = fopen('php://memory', 'w+');
			fwrite($f, iconv('CP1251', 'UTF-8', file_get_contents($filepath)));
			rewind($f);
			$columns = fgetcsv($f, 0, ';', '"');
			fclose($f);
			
			$columns = array('' => '---') + $columns;
			
		} else Controller::redirect('admin/crosses/update');
	}
	
	public function action_update_step3() {
		if(!ORM::factory('Permission')->checkPermission('crosses')) Controller::redirect('admin');
		
		$this->template->content = View::factory('admin/crosses/update_step3')
			->bind('permissions', $permissions)
			->bind('lines_count', $lines_count)
			->bind('data', $data);
			
        $this->template->title = 'Обновление кроссов :: Шаг 3';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		$this->template->scripts[] = 'common/crosses_update_step3';
			
		if (HTTP_Request::POST == $this->request->method()) 
		{
			$operation = ORM::factory('Operation');
			$operation->description = "Обновление кроссов";
			$operation->save();

			$crosses = array();
			foreach($_POST['article'] as $key=>$val) {
				$values = array();
				$values['article'] = $_POST['article'][$key];
				$values['brand'] = $_POST['brand'][$key];
				$values['brand_text'] = $_POST['brand_text'][$key];
				$crosses[] = $values;
			}
			
			$sess_data = array();
			$sess_data['filepath'] = $this->request->post('filepath');
			$sess_data['crosses'] = $crosses;
			$sess_data['lines_processed'] = 1;
			$sess_data['operation_id'] = $operation->id;

			$f = fopen('php://memory', 'w+');
			fwrite($f, iconv('CP1251', 'UTF-8', file_get_contents($sess_data['filepath'])));
			rewind($f);
            $lines_count = 0;
			while (fgetcsv($f, 0, ';', '"') !== false)
                $lines_count++;

			fclose($f);
			$sess_data['lines_count'] = $lines_count;
			Session::instance()->set("cross_parser", $sess_data);
			
		}
		else
		    Controller::redirect('admin/crosses/update');
	}
	
	public function action_proccess() {
        $this->auto_render = false;
		$json = array();
		
		$sess_data = Session::instance()->get('cross_parser');
		$counter = 0;
		$lines_count = $sess_data['lines_count'];
		
		$f = fopen('php://memory', 'w+');
		fwrite($f, iconv('CP1251', 'UTF-8', file_get_contents($sess_data['filepath'])));
		rewind($f);
		
		$trim_charset = " \t\n\r\0.'\"(),";
		
		for($i = 0; $data = fgetcsv($f, 0, ';', '"'); $i++) {
			if($i < $sess_data['lines_processed'])
			    continue;

			if($counter >= 1000)
			    break;
			
			$crosses = array();
			foreach($sess_data['crosses'] as $item) {
				//Set brand
				if(empty($item['brand']) && $item['brand'] !== '0')
					$brand_long = trim($item['brand_text'], $trim_charset);
				else
					$brand_long = trim($data[$item['brand']], $trim_charset);
					
				//Set article
				$article_long = trim($data[$item['article']], $trim_charset);
				
				//Get part
				$part = ORM::factory('Part')->get_article($article_long, $brand_long, "", $sess_data['operation_id']);

				if(!$part)
				    continue;

				if(empty($part->id))
				{
					$crosses[] = [
						'part' => null,
						'art' => Article::get_short_article($article_long),
						'brand' => Article::get_short_article($brand_long),
					];
				}
				else
                {
					$crosses[] = [
						'part' => $part,
						'art' => $part->article,
						'brand' => $part->brand,
					];
				}
			}
			
			if(count($crosses) > 1) {

				for($from_ind = 0; $from_ind < count($crosses)-1; $from_ind++)
				{
					for($to_ind = ($from_ind+1); $to_ind < count($crosses); $to_ind++)
					{
						if($crosses[$from_ind]['art'] == $crosses[$to_ind]['art'] and $crosses[$from_ind]['brand'] == $crosses[$to_ind]['brand'])
						    continue;

                        $query = "INSERT IGNORE INTO crosses (from_id, from_art, from_brand, to_id, to_art, to_brand, operation_id) VALUE (";

                        if($crosses[$from_ind]['part'] ==  null)
                            $query .= "null, ";
                        else
                            $query .= "".$crosses[$from_ind]['part']->id.", ";

                        $query .= "'".$crosses[$from_ind]['art']."', '".$crosses[$from_ind]['brand']."', ";

                        if($crosses[$to_ind]['part'] ==  null)
                            $query .= "null, ";
                        else
                            $query .= "".$crosses[$to_ind]['part']->id.", ";

                        $query .= "'".$crosses[$to_ind]['art']."', '".$crosses[$to_ind]['brand']."', ".$sess_data['operation_id'].") ";
                        DB::query(Database::INSERT,$query)->execute('tecdoc');
					}
				}
			}
			
			$sess_data['lines_processed']++;
			$counter++;
		}
		
		Session::instance()->set("cross_parser", $sess_data);
		
		$json['current'] = $sess_data['lines_processed'];
		$json['status'] = ($sess_data['lines_processed'] >= $lines_count) ? "complete" : "continue";
		fclose($f);
		$this->response->body(json_encode($json));
	}

} // End Admin_User



class Validation_Exception extends Exception {};
