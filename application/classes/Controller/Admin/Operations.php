<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Operations extends Controller_Admin_Application {
	
	public function action_list() {
		if(!ORM::factory('Permission')->checkPermission('manage_operations')) Controller::redirect('admin');
		
		$this->template->content = View::factory('admin/operations/list')
			->bind('filters', $filters)
			->bind('pagination', $pagination)
			->bind('suppliers', $suppliers)
			->bind('operations', $operations);
			
		$this->template->title = 'Операции загрузки';
		$this->template->description = '';
		$this->template->keywords = '';
		$this->template->author = '';
		
		$operations_orm = ORM::factory('Operation');
		$operations_orm->reset(FALSE);
		
		if(!empty($_GET['supplier_id'])) {
			$filters['supplier_id'] = $_GET['supplier_id'];
			$operations_orm = $operations_orm->and_where('supplier_id', '=', $filters['supplier_id']);
		}
		
		$count = $operations_orm->count_all();
		
		$pagination = Pagination::factory(array('total_items' => $count))->route_params(array(
		  'controller' =>  'operations',
		  'action' =>  'list'
		));
		$operations = $operations_orm->limit($pagination->items_per_page)->offset($pagination->offset)
			->order_by('date_time', 'desc')->find_all()->as_array();
		$this->template->scripts[] = 'common/operations_list';
		
		$suppliers = array('' => '---');
		
		foreach(ORM::factory('Supplier')->find_all()->as_array() as $supplier) {
			$suppliers[$supplier->id] = $supplier->name;
		}
	}
	
	public function action_brands() {
		if(!ORM::factory('Permission')->checkPermission('manage_operations')) Controller::redirect('admin');
		
		$this->template->content = View::factory('admin/operations/brands_list')
			->bind('filters', $filters)
			->bind('pagination', $pagination)
			->bind('brands', $brands);
			
		$id = $this->request->param('id');
			
		$this->template->title = 'Брэнды';
		$this->template->description = '';
		$this->template->keywords = '';
		$this->template->author = '';
		
		$trim_charset = " \t\n\r\0.'\"(),";
		$tecdoc = Model::factory('Tecdoc');
		
		if (HTTP_Request::POST == $this->request->method()) 
		{
			// echo "<pre>";
			// print_r($_POST);
			// echo "</pre>";
			
			if(!empty($_POST['change_to'])) foreach($_POST['change_to'] as $key=>$value) {
				$brand_instance = ORM::factory('Brand')->where('id', '=', $key)->find();
				$value = trim($value, $trim_charset);
				if(!empty($value) && $brand_instance->change_to != $value) {
					$brand_instance->change_to = $value;
					
					$brand_long = $value;
                    $brand = Article::get_short_article($brand_long);
					$brand_instance->change_to_short = $brand;
					
					$brand_instance_add = ORM::factory('Brand')->where('brand', '=', $brand)->find();
					if(empty($brand_instance_add->id)) {
						$tecdoc_brand = $tecdoc->get_brand($brand);
						if($tecdoc_brand) {
							$brand = $tecdoc_brand['brand_short'];
							$brand_long = $tecdoc_brand['brand'];
							$brand_instance_add->set('tecdoc_id', $tecdoc_brand['id']);
						}
						
						$brand_instance_add->set('brand', $brand);
						$brand_instance_add->set('brand_long', $brand_long);
						$brand_instance_add->set('operation_id', $id);
						$brand_instance_add->save();
					} else {
						if(!empty($brand_instance_add->change_to)) {
							$brand_long = trim($brand_instance_add->change_to, $trim_charset);
							$brand = Article::get_short_article($brand_long);
						} else {
							$brand_long = trim($brand_instance_add->brand_long, $trim_charset);
						}
					}
					
					/*
					$parts = ORM::factory('Part')->and_where('brand', '=', $brand_instance->brand)->find_all();
					foreach($parts as $part) {
						$part_in_db = ORM::factory('Part')->where('article', '=', $part->article)->and_where('brand', '=', $brand)->find();
						if(!empty($part_in_db->id)) {
							DB::update('priceitems')->set(array('part_id'=>$part_in_db->id))->where('part_id','=',$part->id)->execute();
							DB::update('crosses')->set(array('from_id'=>$part_in_db->id))->where('from_id','=',$part->id)->execute();
							DB::update('crosses')->set(array('to_id'=>$part_in_db->id))->where('to_id','=',$part->id)->execute();
							$part->delete();
						} else {
							$tecdoc_articles = $tecdoc->get_articles($part->article, $brand);
							if($tecdoc_articles) {
								$part->set('article_long', $tecdoc_articles[0]['article_nr']);
								$part->set('name', $tecdoc_articles[0]['description']);
								
								$part->set('tecdoc_id', $tecdoc_articles[0]['id']);
							}
							$part->set('brand', $brand);
							$part->set('brand_long', $brand_long);
							$part->save();
						}
					}*/
					
				} elseif(empty($value)) {
					$brand_instance->change_to = null;
				}
				
				
				$value = isset($_POST['dont_upload'][$key]) ? $_POST['dont_upload'][$key] : 0;
				$value = !empty($value) && $value == 1 ? 1 : 0;
				$brand_instance->dont_upload = $value;

				$brand_instance->save();
				
				if($brand_instance->dont_upload) {
					$parts = ORM::factory('Part')->where('brand', '=', $brand_instance->brand)->find_all();
					foreach($parts as $part) {
						DB::delete('priceitems')->where('part_id', '=', $part->id)->execute();
					}
					DB::delete('parts')->where('brand', '=', $brand_instance->brand)->execute();
				}
			}
			$_POST = array();
		}
		
		$brands_orm = ORM::factory('Brand');
		$brands_orm->reset(FALSE);
		
		if(!empty($id)) {
			$brands_orm = $brands_orm->and_where('operation_id', '=', $id);
		}
		
		if(!empty($_GET['brand'])) {
			$filters['brand'] = $_GET['brand'];
			$brands_orm = $brands_orm->and_where('brand', 'LIKE', Article::get_short_article($filters['brand']).'%')->or_where('change_to_short', 'LIKE', Article::get_short_article($filters['brand']).'%');
		}
		
		$count = $brands_orm->count_all();
		
		$pagination = Pagination::factory(array('total_items' => $count))->route_params(array(
		  'controller' =>  'operations',
		  'action' =>  'brands',
		  'id' => $id
		));
		
		$brands = $brands_orm->limit($pagination->items_per_page)->offset($pagination->offset)
			->order_by('brand_long', 'asc')->find_all()->as_array();
			
	}
	
	public function action_parts() {
		if(!ORM::factory('Permission')->checkPermission('manage_operations')) Controller::redirect('admin');
		
		$this->template->content = View::factory('admin/operations/parts_list')
			->bind('filters', $filters)
			->bind('pagination', $pagination)
			->bind('parts', $parts);
			
		$id = $this->request->param('id');
			
		$this->template->title = 'Запчасти';
		$this->template->description = '';
		$this->template->keywords = '';
		$this->template->author = '';
		
		$trim_charset = " \t\n\r\0.'\"(),";
		
		if (HTTP_Request::POST == $this->request->method()) 
		{
			if(!empty($_POST['name'])) foreach($_POST['name'] as $key=>$value) {
				$value = trim($value);
				$part = ORM::factory('Part')->and_where('id', '=', $key)->find();
				$part->set('name', $value);
				$part->save();
			}
		}
		
		$parts_orm = ORM::factory('Part');
		$parts_orm->reset(FALSE);
		
		if(!empty($id)) {
			$parts_orm = $parts_orm->and_where('operation_id', '=', $id);
		}
		
		if(!empty($_GET['article'])) {
			$filters['article'] = $_GET['article'];
			$parts_orm = $parts_orm->and_where('article', 'LIKE', Article::get_short_article($filters['article']).'%');
		}
		
		if(!empty($_GET['brand'])) {
			$filters['brand'] = $_GET['brand'];
			$parts_orm = $parts_orm->and_where('brand', 'LIKE', Article::get_short_article($filters['brand']).'%');
		}
		
		$count = $parts_orm->count_all();
		
		$pagination = Pagination::factory(array('total_items' => $count))->route_params(array(
		  'controller' =>  'operations',
		  'action' =>  'parts',
		  'id' => $id
		));
		
		$parts = $parts_orm->limit($pagination->items_per_page)->offset($pagination->offset)
			->order_by('article_long', 'asc')->find_all()->as_array();
			
	}
	
	public function action_unmatched() {
		if(!ORM::factory('Permission')->checkPermission('manage_operations')) Controller::redirect('admin');
		
		$this->template->content = View::factory('admin/operations/unmatched_list')
			->bind('filters', $filters)
			->bind('pagination', $pagination)
			->bind('unmatched_list', $unmatched_list)
			->bind('brands_list', $brands_list);
			
		$id = $this->request->param('id');
			
		$this->template->title = 'Несопоставленные';
		$this->template->description = '';
		$this->template->keywords = '';
		$this->template->author = '';
		
		$this->template->scripts[] = 'common/unmatched';
		
		$trim_charset = " \t\n\r\0.'\"(),";

		$query = "
			SELECT COUNT(*) as col, brand FROM unmatched
			WHERE operation_id = '".$id."' GROUP BY brand ORDER BY COUNT(*) DESC
		";
		$brands_tmp = DB::query(Database::SELECT, $query)->execute()->as_array();

		$brands_list = array("" => "---");
		foreach($brands_tmp as $row) {
			$brands_list[strtolower($row['brand'])] = $row['brand'] . "(" . $row['col'] . ")";
		}
		
		if (HTTP_Request::POST == $this->request->method())
		{
			$unmatched_ids = array();
			if(isset($_POST['unmatched_id'])){
				if (is_array($_POST['unmatched_id'])) {
					$unmatched_ids = $_POST['unmatched_id'];
				} else {
					$unmatched_ids[] = $_POST['unmatched_id'];
				}
			}

			//add single brand
			if(!empty($_POST['brand_add'])) {
				$this->add_brand($id);
			}

			if (!empty($_POST['all_brands'])) {
				foreach($brands_tmp as $row) {
					$_POST['brand_add'] = strtolower($row['brand']);
					$this->add_brand($id);
				}
			}

			if(!empty($unmatched_ids)) {
				$unmatched_list = ORM::factory('Unmatched')->where('id', 'IN', $unmatched_ids)->find_all()->as_array();
				
				$proccessed_brands = array();
				$unmatched_to_delete = array();

				foreach($unmatched_list as $unmatched) {
					if($unmatched->reason == 'bad_brand') {
						
						$brand_unmatched_list = ORM::factory('Unmatched')->where('operation_id', '=', $id)->and_where('id', '=', $unmatched->id)->find_all()->as_array();
						$proccessed_brands[] = $unmatched->brand;

						foreach($brand_unmatched_list as $brand_unmatched) {

							$part = ORM::factory('Part')->get_article($brand_unmatched->article, $brand_unmatched->brand, $brand_unmatched->name, $brand_unmatched->operation_id, true, false);

							if($part == 'bad_brand' || $part == 'bad_article') {
								$brand_unmatched->reason = $part;
								$brand_unmatched->save();
							} else {
								$price_item = ORM::factory('Priceitem');
								$price_item->set('part_id', $part->id);
								$price_item->set('price', $brand_unmatched->price);
								$price_item->set('currency_id', $brand_unmatched->currency_id);
								$price_item->set('amount', $brand_unmatched->amount);
								$price_item->set('delivery', $brand_unmatched->delivery);
								$price_item->set('supplier_id', $brand_unmatched->supplier_id);
								$price_item->set('operation_id', $brand_unmatched->operation_id);
								$price_item->save();
								$unmatched_to_delete[] = $brand_unmatched->id;
							}
						}
					} elseif($unmatched->reason == 'bad_article') {
						$part = ORM::factory('Part')->get_article($unmatched->article, $unmatched->brand, $unmatched->name, $unmatched->operation_id, true, true);
						
						if($part == 'bad_brand' || $part == 'bad_article') {
							$unmatched->reason = $part;
							$unmatched->save();
						} else {
							$price_item = ORM::factory('Priceitem');
							$price_item->set('part_id', $part->id);
							$price_item->set('price', $unmatched->price);
							$price_item->set('currency_id', $unmatched->currency_id);
							$price_item->set('amount', $unmatched->amount);
							$price_item->set('delivery', $unmatched->delivery);
							$price_item->set('supplier_id', $unmatched->supplier_id);
							$price_item->set('operation_id', $unmatched->operation_id);
							$price_item->save();
							$unmatched_to_delete[] = $unmatched->id;
						}
					}
				}
				
				if(count($unmatched_to_delete) > 0)
					DB::delete('unmatched')->where('id', 'IN', $unmatched_to_delete)->execute();
			}
		}
		
		$unmatched_orm = ORM::factory('Unmatched');
		$unmatched_orm->reset(FALSE);
		
		if(!empty($id)) {
			$unmatched_orm = $unmatched_orm->and_where('operation_id', '=', $id);
		}
		
		if(!empty($_GET['article'])) {
			$filters['article'] = $_GET['article'];
			$unmatched_orm = $unmatched_orm->and_where('article', 'LIKE', $filters['article'].'%');
		}
		
		if(!empty($_GET['brand_select'])) {
			$filters['brand_select'] = $_GET['brand_select'];
			$unmatched_orm = $unmatched_orm->and_where('brand', 'LIKE', $filters['brand_select']);
		} elseif(!empty($_GET['brand'])) {
			$filters['brand'] = $_GET['brand'];
			$unmatched_orm = $unmatched_orm->and_where('brand', 'LIKE', $filters['brand'].'%');
		}
		
		$count = $unmatched_orm->count_all();
		
		$pagination = Pagination::factory(array('total_items' => $count, 'items_per_page' => 100))->route_params(array(
		  'controller' =>  'operations',
		  'action' =>  'unmatched',
		  'id' => $id,
		));
		
		$unmatched_list = $unmatched_orm->limit($pagination->items_per_page)->offset($pagination->offset)
			->order_by('article', 'asc')->find_all()->as_array();

	}
	
	public function action_delete() {
		if(!ORM::factory('Permission')->checkPermission('manage_operations')) Controller::redirect('admin');
		
        $this->template->title = '';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		$id = $this->request->param('id');
		if(!empty($id)) {
			$operation = ORM::factory('Operation')->where('id', '=', $id)->find();
			
			DB::delete('brands')->where('operation_id', '=', $operation->id)->execute();
			DB::delete('crosses')->where('operation_id', '=', $operation->id)->execute();
			DB::delete('parts')->where('operation_id', '=', $operation->id)->execute();
			DB::delete('priceitems')->where('operation_id', '=', $operation->id)->execute();
			DB::delete('unmatched')->where('operation_id', '=', $operation->id)->execute();
			
			$operation->delete();
		}
		
		Controller::redirect('admin/operations/list');
	}

    public function add_brand($id)
    {
        $items_count = ORM::factory('Unmatched')->where('operation_id', '=', $id)->and_where('brand', '=', $_POST['brand_add'])->group_by('article')->find_all()->count();
        $step = 20000;
        for ($count = 0; $count < $items_count; $count+=$step) {

            if ($_POST['brand_add'] == '') continue;

            $brand_unmatched_list = ORM::factory('Unmatched')->where('operation_id', '=', $id)->and_where('brand', '=', $_POST['brand_add'])->group_by('article')->limit($step)->offset($count)->find_all()->as_array();
            $brands_query = null;
            $parts_query = null;
//            $parts_query = DB::insert('parts', array('operation_id',
//                'tecdoc_id', 'article', 'article_long', 'brand',
//                'brand_long', 'name'));

            $parts_query = "INSERT IGNORE INTO parts (operation_id,
                tecdoc_id, article, article_long, brand,
                brand_long, name) VALUES ";

            $to_delete = array();

            foreach ($brand_unmatched_list as $brand_unmatched) {

                if (!$brand_unmatched->brand OR !$brand_unmatched->article) continue 1;

                if ($brand_unmatched->reason == 'bad_brand') {
                    $find_brand = ORM::factory('Brand')->where('brand', '=', Article::get_short_article($brand_unmatched->brand))->order_by('id', 'desc')->find();
                    $brand_long = mb_strtoupper($brand_unmatched->brand);
                    if (!$find_brand OR $find_brand->brand != Article::get_short_article($brand_unmatched->brand)) {

                        $new_brand = ORM::factory('Brand');
                        $new_brand
                            ->set('brand', Article::get_short_article($brand_unmatched->brand))
                            ->set('brand_long', $brand_long)
                            ->set('operation_id', $id)
                            ->set('tecdoc_id', 0)
                            ->save();
                    }
                    $brand = Article::get_short_article($brand_unmatched->brand);
                    $article = Article::get_short_article($brand_unmatched->article);
                    $article_long = mb_strtoupper($article);

                } else {
                    $brand_factory = ORM::factory('Brand')->where('brand', '=', $brand_unmatched->brand)->order_by('id', 'desc')->find();
                    $brand_long = $brand_factory->brand_long;
                    $brand = $brand_factory->brand;

                    $article_long = mb_strtoupper($brand_unmatched->article);
                    $article = $brand_unmatched->article;
                }

//                $parts_query->values(array($id, 0, $article, $article_long, $brand, $brand_long, $brand_unmatched->name));

                $parts_query .= "($id, 0, '".$article."', '".$article_long."', '".$brand."', '".$brand_long."', '".$brand_unmatched->name."'), ";
                $to_delete[] = $brand_unmatched->id;
//                    $temp_article = $brand_unmatched->article;
//                    $new_part = ORM::factory('Part');
//                    $new_part
//                        ->set('operation_id', $id)
//                        ->set('tecdoc_id', 0)
//                        ->set('article', $article)
//                        ->set('article_long', $article_long)
//                        ->set('brand', $brand)
//                        ->set('brand_long', $brand_long)
//                        ->set('name', $brand_unmatched->name)
//                        ->save();

//                $new_priceitem = ORM::factory('Priceitem');
//                $new_priceitem
//                    ->set('part_id', $new_part->id)
//                    ->set('price', $brand_unmatched->price)
//                    ->set('currency_id', $brand_unmatched->currency_id)
//                    ->set('amount', $brand_unmatched->amount)
//                    ->set('delivery', $brand_unmatched->delivery)
//                    ->set('supplier_id', $brand_unmatched->supplier_id)
//                    ->set('operation_id', $id)
//                    ->save();


            }

            $parts_query = substr($parts_query, 0, -2);
//            print_r($parts_query); exit();
            DB::query(Database::INSERT,$parts_query)->execute();
//            $parts_query->execute();
            DB::delete('unmatched')->where('operation_id', '=', $id)->and_where('id', 'IN', $to_delete)->execute();
        }
    }

}
