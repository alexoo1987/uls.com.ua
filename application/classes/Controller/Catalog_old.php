<?php defined('SYSPATH') or die('No direct script access.');

class Controller_CatalogOld extends Controller_Application {
	
	public function action_index()
	{
		$this->template->content = View::factory('catalog/manufacturers')
			->bind('manufacturers', $manufacturers);
			
        $this->template->title = 'Каталог (производители)';
        $this->template->h1 = 'Каталог (производители)';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

		$tecdoc = Model::factory('Tecdoc');
		
		$manufacturers = $tecdoc->get_manufacturers(false, false, false, true);
	}
	
	public function action_car()
	{
		$category = ORM::factory('Category')->where('slug', '=', $this->request->param('manufacturer'))->find();
		if(!empty($category->id)) {
			$this->action_parts();
			return;
		}

		$this->template->content = View::factory('catalog/cars')
			->bind('cars', $cars)
			->bind('manufacturer', $manufacturer)
			->bind('h1', $h1)
			->bind('content_text', $content_text);
		
		$slug = $this->request->param('manufacturer');
		
		$tecdoc = Model::factory('Tecdoc');
		$manufacturers = $tecdoc->get_manufacturers($slug);
		$manufacturer = $manufacturers[0];

		if(strpos($this->request->uri(), 'katalog/car') !== false) {
			return Controller::redirect('katalog/'.$manufacturer['slug'], 301);
		}
		
		$cars = $tecdoc->get_cars(false, $manufacturer['id']);

		// $models_array = array();
		// foreach(array_rand($cars, 5) as $key) {
		// 	$models_array[] = $cars[$key]['description'];
		// }
		
			
		$seo_identifier = $this->request->uri()/*.URL::query()*/;
		$seo_data = ORM::factory('Seodata')->where('seo_identifier', '=', $seo_identifier)->find();
		if(!empty($seo_data->id)) {
	        $this->template->title = $seo_data->title;
	        $this->template->description = $seo_data->description;
	        $this->template->keywords = $seo_data->keywords;
	        $this->template->author = '';
            $this->template->noindex =  $seo_data->noindex;
	        $h1 = $seo_data->h1;
	        $content_text = $seo_data->content;
		} else {
	        $this->template->title = "Купить запчасти на ".$manufacturer['brand']." в магазине eparts, в Киеве, Харькове, Одесса, Днепропетровск";
	        $this->template->description = "";
	        $this->template->keywords = "";
	        $this->template->author = '';
	        $h1 = "Автозапчасти для ".$manufacturer['brand'].":";
	        $content_text = "";
		}
	}
	
	public function action_model()
	{
		$this->template->content = View::factory('catalog/tree')
			->bind('tree_list', $tree_list)
			->bind('manufacturer', $manufacturer)
			->bind('model', $model)
			->bind('modification', $modification)
			->bind('h1', $h1)
            ->bind('css_select_cat_block', $css_select_cat_block)
			->bind('content_text', $content_text);
			
		$css_select_cat_block = true;
		$manufacturer = $this->request->param('manufacturer');
		$model = $this->request->param('model');

		$tecdoc = Model::factory('Tecdoc');

		$manufacturers = $tecdoc->get_manufacturers($manufacturer);
		$manufacturer = $manufacturers[0];

		$models = $tecdoc->get_cars($model);
		$model = $models[0];

		$modification = false;
		
		$tree_list = ORM::factory('Category')->where('level', '=', 0)->order_by('id')->find_all()->as_array();

		$seo_identifier = $this->request->uri()/*.URL::query()*/;
		$seo_data = ORM::factory('Seodata')->where('seo_identifier', '=', $seo_identifier)->find();
        $this->template->styles[] = 'dist/select_left';
        $this->template->scripts[] = 'dist/left_block';

		if(!empty($seo_data->id)) {
	        $this->template->title = $seo_data->title;
	        $this->template->description = $seo_data->description;
	        $this->template->keywords = $seo_data->keywords;
	        $this->template->author = '';
            $this->template->noindex =  $seo_data->noindex;
	        $h1 = $seo_data->h1;
	        $content_text = $seo_data->content;
		} else {
	        $this->template->title = "Купить запчасти на ".$manufacturer['brand']." ".$model['short_description']." в магазине eparts, в Киеве, Харькове, Одесса, Днепропетровск";
	        $this->template->description = "";
	        $this->template->keywords = "";
	        $this->template->author = '';
	        $h1 = "Автозапчасти для ".$manufacturer['brand']." ".$model['short_description'].":";
	        $content_text = "";
		}
	}
	
	public function action_types()
	{
		//temp redirect from old pages
		$manufacturer = $this->request->param('manufacturer');
		if (in_array($manufacturer, array('types', 'model', 'parts'))) HTTP::redirect(URL::base(), 301);

		$category = ORM::factory('Category')->where('slug', '=', $this->request->param('modification'))->find();
		if(!empty($category->id)) {
			$this->action_parts();
			return;
		}
		$this->template->content = View::factory('catalog/tree')
			->bind('tree_list', $tree_list)
			->bind	('manufacturer', $manufacturer)
			->bind('model', $model)
			->bind('modification', $modification)
			->bind('h1', $h1)
			->bind('content_text', $content_text);
		
		$manufacturer = $this->request->param('manufacturer');
		$model = $this->request->param('model');
		$modification = $this->request->param('modification');

		$tecdoc = Model::factory('Tecdoc');


		$manufacturers = $tecdoc->get_manufacturers($manufacturer);
		$manufacturer = $manufacturers[0];

		$models = $tecdoc->get_cars($model);
		$model = $models[0];

		$modifications = $tecdoc->get_types($modification);
		$modification = $modifications[0];
		
		$tree_list = ORM::factory('Category')->where('level', '=', 0)->order_by('id')->find_all()->as_array();

		$seo_identifier = $this->request->uri()/*.URL::query()*/;
		$seo_data = ORM::factory('Seodata')->where('seo_identifier', '=', $seo_identifier)->find();
		if(!empty($seo_data->id)) {
	        $this->template->title = $seo_data->title;
	        $this->template->description = $seo_data->description;
	        $this->template->keywords = $seo_data->keywords;
	        $this->template->author = '';
            $this->template->noindex =  $seo_data->noindex;
	        $h1 = $seo_data->h1;
	        $content_text = $seo_data->content;
		} else {
	        $this->template->title = "Купить запчасти на ".$manufacturer['brand']." ".$model['short_description']." ".$modification['description']." в магазине eparts, в Киеве, Харькове, Одесса, Днепропетровск";
	        $this->template->description = "";
	        $this->template->keywords = "";
	        $this->template->author = '';
	        $h1 = "Автозапчасти для ".$manufacturer['brand']." ".$model['short_description']." ".$modification['description'].":";
	        $content_text = "";
		}
	}
	
	public function action_parts()
	{
        if(ORM::factory('Client')->logged_in()) {
            //return Controller::redirect('authorization/login?order_add=true');
            $guest = false;

        }else{

            $guest = true;

        }
        //TOP products
        $top_items = ORM::factory('TopOrderitem')
            ->find_all()
            ->as_array();
        $top_orderitems = array();
        foreach ($top_items as $top_item=>$key)
        {
            $top_orderitems[] = Article::get_short_article($key->article);
        }
		$this->template->content = View::factory('catalog/parts')
			->bind('pagination', $pagination)
			->bind('category', $category)
			->bind('parts', $parts)
			->bind('prices', $prices)
			->bind('modification', $modification)
			->bind('model', $model)
			->bind('manufacturer', $manufacturer)
			->bind('h1', $h1)
			->bind('content_text', $content_text)
			->bind('cars_block', $cars_block)
			->bind('filter', $filter)
            ->bind('guest', $guest)
            ->bind('top_orderitems', $top_orderitems)
			->bind('manufacturers_block', $manufacturers_block);

		$modification = false;
		$model = false;
		$manufacturer = false;
		$breadcrumbs = array();

		$tecdoc = Model::factory('Tecdoc');

        //марка авто
		$manufacturer_slug = $this->request->param('manufacturer', false);
        //модель авто
		$model_slug = $this->request->param('model', false);
        //модификация авто
		$modification_slug = $this->request->param('modification', false);
        //категория
		$category_slug = $this->request->param('category', false);
		$filter_current['brand'] = $this->request->query('brand') ? $this->request->query('brand') : array();
		$filter_current['location'] = $this->request->query('location') ? $this->request->query('location') : array();



//        echo $manufacturer_slug."<br>"; //bmw
//        echo $model_slug."<br>"; //5
//        echo $modification_slug."<br>"; //523-i-33006
//        echo $category_slug; //tormoznye-kolodki-613
//        exit();

		if(!empty($model_slug)) {
            $models = $tecdoc->get_cars($model_slug);
            $model = $models[0];

            $manufacturers = $tecdoc->get_manufacturers($manufacturer_slug);
            $manufacturer = $manufacturers[0];
		}

		$modification_ids = array();
		if(empty($category_slug)) {
			if (!empty($modification_slug)) {
				$category_slug = $modification_slug;

				$modifications = $tecdoc->get_types(false, $model['id']);
				foreach ($modifications as $modification_tmp) {
					$modification_ids[] = $modification_tmp['id'];
				}
			} else {
				$category_slug = $manufacturer_slug;
			}
		} else {
			$modifications = $tecdoc->get_types($modification_slug);
			$modification = $modifications[0];
			$modification_ids[] = $modification['id'];
		}

//		check if category is root
		$is_root = ORM::factory('Category')->where('level', '=', 0)->and_where('slug', '=', $category_slug)->find_all()->count();
		if ($is_root) $this->action_category($category_slug);

		if(count($modification_ids) == 0) {
			$cookie_modification_id = Cookie::get('car_modification', NULL);

			if(!empty($cookie_modification_id)) {
				$modification = $tecdoc->get_type($cookie_modification_id);

				return Controller::redirect('katalog/'.$modification['full_slug'].'/'.$category_slug);
			}
		}


		if(!ORM::factory('Catalogfromip')->check_ip()) {
			throw new HTTP_Exception_503( 'Превышено число запросов' );
			return false;
		}

		$model_id = !$modification && $model ? $model['id'] : false;

		$tree_id = false;
		$category = ORM::factory('Category')->where('slug', '=', $category_slug)->find();
		$breadcrumbs['category'] = $category;
		if($category && $category->tecdoc_ids) {
			$tree_id = explode(",", $category->tecdoc_ids);
		}

		if($model_id) {
//			$this->template->noindex = true;
			$cars = $tecdoc->get_cars(false, $manufacturer['id']);
			$cars_block = View::factory('catalog/cars_block')
				->set('manufacturer', $manufacturer)
				->set('cars', $cars)
				->set('category', $category)
				->render();
		} else $cars_block = "";

		if(!$model_id && !$modification) {
			$manufacturers = $tecdoc->get_manufacturers(false, false, false, true);
			$manufacturers_block = View::factory('catalog/manufacturers_block')
				->set('manufacturers', $manufacturers)
				->set('category', $category)
				->render();
		} else $manufacturers_block = "";

		$page  = (int) $this->request->query('page');
		if($page > 0) $page -= 1;

		$items_per_page = 18;
		$offset = $page * $items_per_page;
		$limit = 5 * $items_per_page;

		$article_ids = false;
		if($tree_id) {
			//get all parts
			$article_ids = $tecdoc->get_parts_ids($modification_ids, $model_id, $tree_id, $limit, $offset, true); //$limit, $offset

			//find articles by filters
			$temp_article_ids = empty(array_filter($filter_current)) ? $article_ids : $tecdoc->get_parts_ids($modification_ids, $model_id, $tree_id, $limit, $offset, true, $filter_current);

			//brand filter
			$filter['brand'] = $tecdoc->get_brands(empty($filter_current['brand']) ? $temp_article_ids: $article_ids);

			//other filters
			$filter['location'] = $tecdoc->get_filters((empty($filter_current['location']) ? $temp_article_ids: $article_ids),100);

			//add current filter to all filters if not exists
			foreach ($filter_current AS $name => $array) {
				foreach ($array AS $value) {
					if (!in_array($value, $filter[$name]))
						$filter[$name][] = $value;
				}
				//sort every array
				if ($filter[$name])	asort($filter[$name]);
			}

			$article_ids = $temp_article_ids;

			$count = count($article_ids);
			$count = $offset + intval($count);
			$article_ids = array_slice($article_ids, 0, $items_per_page);
		} else $count = 0;

		$route_params = array('controller' => 'catalog');

		$title = $category->name;
		$h1 = $category->name;

		if($manufacturer) {
			$route_params['manufacturer'] = $manufacturer['slug'];
			$title .= " на ".$manufacturer['brand'];
			$h1 .= " на ".$manufacturer['brand'];
		}

		if($model) {
			$route_params['model'] = $model['slug'];
			$title .= " ".$model['short_description'];
			$h1 .= " ".$model['short_description'];
		}

		if($modification) {
			$route_params['modification'] = $modification['slug'];
			$title .= " ".$modification['description'];
			$h1 .= " ".$modification['description'];
		}

		if($manufacturer && $model && $modification) {
			$route_params['category'] = $category_slug;
		} elseif($manufacturer && $model) {
			$route_params['modification'] = $category_slug;
		} else {
			$route_params['manufacturer'] = $category_slug;
		}

		$title .= " купить в Eparts.kiev.ua";

		$pagination = Pagination::factory(
			array(
				'total_items' => $count,
				'items_per_page' => $items_per_page,
				'view' => 'pagination/floating_modified',
			)
		)->route_params($route_params);

		if($article_ids) {
			$parts = $tecdoc->get_parts($article_ids);
		} else $parts = array();

		$prices = array();
		$part_ids = array();
		if(!empty($parts)) {
			foreach ($parts as $part) {
				$part_ids[] = $part['id'];
			}

			$price_items = ORM::factory('Priceitem')
				->with('supplier')
				->with('part')
				->where('part.tecdoc_id', 'IN', $part_ids)
				->and_where('supplier.dont_show', '=', 0)
				->find_all()->as_array();

			usort($price_items, 'sort_objects_by_price');

			foreach ($price_items as $price_item) {
				$tecdoc_id = $price_item->part->tecdoc_id;
				$price = $price_item->get_price_for_client();
				if(!array_key_exists($tecdoc_id, $prices)) {
					$prices[$tecdoc_id] = array();
					$prices[$tecdoc_id]['price'] = $price;
					$prices[$tecdoc_id]['price_item'] = $price_item;
				}
			}
		}

		$seo_identifier = $this->request->uri()/*.URL::query()*/;
		$seo_data = ORM::factory('Seodata')->where('seo_identifier', '=', $seo_identifier)->find();
		if(!empty($seo_data->id)) {
	        $this->template->title = $seo_data->title;
	        $this->template->description = $seo_data->description;
	        $this->template->keywords = $seo_data->keywords;
	        $this->template->author = '';
            $this->template->noindex =  $seo_data->noindex;
	        $h1 = $seo_data->h1;
	        $content_text = $seo_data->content;
		} else {
	        $this->template->title = $title;
	        $this->template->description = "";
	        $this->template->keywords = "";
	        $this->template->author = '';
	        $h1 = $h1;
	        $content_text = "";
		}
	}
	
	public function action_product()
    {
        if (!ORM::factory('Catalogfromip')->check_ip()) {
            throw new HTTP_Exception_503('Превышено число запросов');
            return false;
        }
        if(ORM::factory('Client')->logged_in()) {
            //return Controller::redirect('authorization/login?order_add=true');
            $guest = false;
        }else{
            $guest = true;
        }
        //TOP products
        $top_items = ORM::factory('TopOrderitem')
            ->find_all()
            ->as_array();
        $top_orderitems = array();
        foreach ($top_items as $top_item=>$key)
        {
            $top_orderitems[] = Article::get_short_article($key->article);
        }
        //        if (base64_decode(str_replace('_','=',$id) == true)) {
        $slug = $this->request->param('id');

        // Корзина
        $items_cart = array();
        foreach(Cart::instance()->content as $item) {

            if(is_numeric($item['id'])) {
                $priceitem_cart = ORM::factory('Priceitem', $item['id']);
            } else {
                $json_array = json_decode(base64_decode(str_replace('_','=',$item['id'])), true);
                $priceitem_cart = ORM::factory('Priceitem')->get_from_arr($json_array);
            }
            $items_cart[] = array(
                'id' => $item['id'],
                'priceitem' => $priceitem_cart,
                'qty' => $item['qty'],
                'number' => $item['number']
            );
        }


        $this->template->content = View::factory('catalog/article')
            ->bind('criterias', $criterias)
            ->bind('graphics', $graphics)
            ->bind('applied_to', $applied_to)
            ->bind('part', $part)
            ->bind('part_obj', $part_obj)
            ->bind('price_item', $price_item)
            ->bind('category', $category)
            ->bind('guest', $guest)
            ->bind('items', $items_cart)
            ->bind('top_orderitems', $top_orderitems)
            ->bind('groups', $groups);

        $price_item = false;
        $groups = array();


        $tecdoc = Model::factory('Tecdoc');
        $brand_replace['from1'] = array('kia', 'citroen', 'acura', 'nissan', 'lexus');
        $brand_replace['from2'] = array('hyundai', 'peugeot', 'honda', 'infiniti', 'toyota');
        $brand_replace['to'] = array('hyundai/kia', 'citroen/peugeot', 'honda/acura', 'nissan/infiniti', 'toyota/lexus');
        $brand_replace['vag_from'] = array( 'audi', 'vw', 'seat', 'skoda', 'vag');
        $brand_replace['vag_to'] = array('vag');
        $id_key = false;
        $id_key_vag = false;
        $id_key_value = 0;

        if (!is_object(json_decode(base64_decode(str_replace('_', '=', $slug))))) {
            $flag = true;
            $slug = explode('-', $slug);
            $id = end($slug);
            $tecdoc_id = NULL;


            if (empty($id)) {
                $tecdoc_id = $this->request->query('id');
                $part_obj = ORM::factory('Part')->where('tecdoc_id', '=', $tecdoc_id)->find();
                if (!empty($part_obj->id)) {
                    $this->auto_render = FALSE;
                    return Controller::redirect('katalog/article/' . $part_obj->id, 301);
                }/* else {
                    return Controller::redirect('/');
                }*/
            } else {
                $part_obj = ORM::factory('Part')->where('id', '=', $id)->find();
                if (empty($part_obj->id)) {
                    /*$this->auto_render = FALSE;
                    return Controller::redirect('/');*/
                } else {
                    $tecdoc_id = $part_obj->tecdoc_id;
                }
            }

            $category = $this->get_category_by_tecdoc_id($tecdoc_id);
            $this->template->title = ($category ? $category->name . ' ' : '') . $part_obj->get_brand() . ' ' . $part_obj->article_long . ' Купить по лучшей цене с доставкой по Украине Eparts';
            $this->template->description = 'Лучшая цена на ⚙ ' . ($category ? $category->name . ' ' : '') . ' ' . $part_obj->get_brand() . ' ' . $part_obj->article_long . ', купить онлайн в каталоге запчастей для автомобиля с доставкой по всей Украине. Интернет магазин автозапчастей Eparts.';
            $this->template->keywords = '';
            $this->template->author = '';



            if (!empty($tecdoc_id)) {
                $criterias = $tecdoc->get_criterias($tecdoc_id);
                $graphics = $tecdoc->get_graphics($tecdoc_id);
                $applied_to = $tecdoc->get_types_by_art_id($tecdoc_id);
                $part = $tecdoc->get_part($tecdoc_id);
            } else {
                $criterias = false;
                $graphics = false;
                $applied_to = false;
                $part = false;
            }

            if (!empty($part_obj->id)) {
                $price_items = ORM::factory('Priceitem')->with('supplier')
                    ->where('part_id', '=', $part_obj->id)
                    ->and_where('supplier.dont_show', '=', 0)
                    ->find_all()
                    ->as_array();

                $setting = ORM::factory('Setting')->where('code_name', '=', 'tekhnomir_active_site')->find();
                if (ORM::factory('Findfromip')->check_ip() AND $setting) {
                    $INFO = Tminfo::instance();
                    $INFO->SetLogin('eparts');
                    $INFO->SetPasswd('950667817282kda');
                    for ($i = 0; $i < count($brand_replace['to']); $i++) {
                        if (strnatcasecmp(Article::get_short_article($brand_replace['to'][$i]), $part_obj->brand) == 0) {
                            $id_key_value = $i;
                            $id_key = true;
                            break;
                        }
                    }
                    for ($i = 0; $i < count($brand_replace['vag_from']); $i++) {
                        if (strnatcasecmp(Article::get_short_article($brand_replace['vag_from'][$i]), $part_obj->brand) == 0) {
                            $id_key_vag = true;
                            break;
                        }
                    }
                    if ($id_key) {
                        $tm_items1 = $INFO->GetPrice($part_obj->article, $brand_replace['from1'][$id_key_value]);
                        $tm_items2 = $INFO->GetPrice($part_obj->article, $brand_replace['from2'][$id_key_value]);
                        $tm_items = array_merge($tm_items1, $tm_items2);
                    } else {
                        if($id_key_vag)
                        {
                            $tm_items1 = $INFO->GetPrice($part_obj->article, $brand_replace['vag_from'][0]);
                            $tm_items2 = $INFO->GetPrice($part_obj->article, $brand_replace['vag_from'][1]);
                            $tm_items3 = $INFO->GetPrice($part_obj->article, $brand_replace['vag_from'][2]);
                            $tm_items4 = $INFO->GetPrice($part_obj->article, $brand_replace['vag_from'][3]);
                            $tm_items5 = $INFO->GetPrice($part_obj->article, $brand_replace['vag_from'][4]);
                            $tm_items = array_merge($tm_items1, $tm_items2, $tm_items3, $tm_items4, $tm_items5);

                        }
                        else
                        {
                            $tm_items = $INFO->GetPrice($part_obj->article, $part_obj->brand);
                            if(!$tm_items)
                            {
                                $tm_items = $INFO->GetPrice($part_obj->article, $part_obj->brand_long);
                            }
                        }

                    }

                } else {
                    $tm_items = array();
                }

                $items = array();
                if ($tm_items) {
                    $usd_currency = ORM::factory('Currency')->get_by_code('USD');
                    $currency_id = $usd_currency->id;

                    $usd_ratio = ORM::factory('Currency')->get_by_code('USD')->ratio;
                    $eur_ratio = ORM::factory('Currency')->get_by_code('EUR')->ratio;

                    $setting = ORM::factory('Setting')->where('code_name', '=', 'tekhnomir_percentage')->find();
                    $tekhnomir_percentage = !empty($setting->id) && !empty($setting->value) ? $setting->value : 0;

                    foreach ($tm_items AS $key => $row) {
                        $item = $row;


                        $price = (double)$row['Price'];
                        if ($row['Currency'] == 'EUR') $price = $price * ($eur_ratio / $usd_ratio);
                        else if ($row['Currency'] == 'UAH') $price = $price / $usd_ratio;

                        $price = $price * ((100 + $tekhnomir_percentage) / 100);

                        if ($row['DeliveryTime'] == 0) $row['DeliveryTime'] = 1;

                        $delivery_setting = array(
                            'LOCAL' => 0,
                            'AIR' => 5,
                            'CONTAINER' => 3.2,
                        );

                        if (isset($row['DeliveryType'])) {
                            if (!in_array($row['DeliveryType'], array_keys($delivery_setting))) continue;
                            else $price = $price + $delivery_setting[$row['DeliveryType']] * $row['Weight'];
                        }

                        $price = round($price, 2);

                        $row['DeliveryTime'] += 1;

                        $price_item = ORM::factory('Priceitem');
                        $price_item->set('price', $price);
                        $price_item->set('currency_id', $currency_id);
                        $price_item->set('amount', ($row['Quantity'] == 0 ? "" : $row['Quantity']));
                        $price_item->set('delivery', $row['DeliveryTime']);
//                        $price_item->set('suplier_code_tehnomir', $row['SupplierCode']);
                        $price_item->set('supplier_id', 38);
                        $price_item->part = $part_obj;

                        $json_array['price'] = $price;
                        $json_array['currency_id'] = $currency_id;
                        $json_array['amount'] = $row['Quantity'];
                        $json_array['delivery'] = $row['DeliveryTime'];
                        $json_array['supplier_code'] = $row['SupplierCode'];
                        $json_array['supplier_id'] = 38;
                        $json_array['part_id'] = $price_item->part->id;
                        $json_array['article'] = $price_item->part->article_long;
                        $json_array['brand'] = $price_item->part->brand_long;
                        $json_array['name'] = $price_item->part->name;

                        try {
                            $price_item->id = str_replace('=', '_', base64_encode(json_encode($json_array)));
                        } catch (Exception $e) {
                            $json_array['name'] = iconv('WINDOWS-1251', 'UTF-8//IGNORE', $json_array['name']);
                            $price_item->id = str_replace('=', '_', base64_encode(json_encode($json_array)));
                        }

                        $items[] = $price_item;
                    }
                }


                $price_items = array_merge($price_items, $items);


                $price_item = $this->get_best_match($price_items);

                //$price_item = usort($price_item, 'delete_by_delivery');
                $price_item = array_values($price_item);


                if (!isset($price_item[1]) AND isset($price_item[0])) {
                    $price_item = $price_item[0];
                } else {
                    if (isset($price_item[1]) AND isset($price_item[0])) {
                        if ($price_item[0]->delivery == $price_item[1]->delivery) {
                            $price_item = $price_item[0]->get_price_for_client() >= $price_item[1]->get_price_for_client() ? $price_item[1] : $price_item[0];
                        }
                    }
                }
                $cross_art = $part_obj->article;
                $cross_brand = $part_obj->brand;
            } else {
                $cross_art = $part['art'];
                $cross_brand = $part['brand_short'];
            }
        }
        else
        {
            $json_price = (json_decode(base64_decode(str_replace('_','=',$slug))));
            $part_obj = $json_price;

            $this->template->title = ($category ? $category->name . ' ' : '') . $json_price->brand . ' ' . $json_price->article . ' Купить по лучшей цене с доставкой по Украине Eparts';
            $this->template->description = 'Лучшая цена на ⚙ ' . ($category ? $category->name . ' ' : '') . ' ' . $json_price->brand . ' ' . $json_price->article . ', купить онлайн в каталоге запчастей для автомобиля с доставкой по всей Украине. Интернет магазин автозапчастей Eparts.';
            $this->template->keywords = '';
            $this->template->author = '';

            for ($i = 0; $i < count($brand_replace['to']); $i++) {
                if (strnatcasecmp(Article::get_short_article($brand_replace['from1'][$i]), $json_price->brand) == 0 or strnatcasecmp(Article::get_short_article($brand_replace['from2'][$i]), $json_price->brand) == 0) {
                    $json_price->brand = $brand_replace['to'][$i];
                    break;
                }
            }
            for ($i = 0; $i < count($brand_replace['vag_from']); $i++) {
                if (strnatcasecmp(Article::get_short_article($brand_replace['vag_from'][$i]), $json_price->brand) == 0) {
                    $json_price->brand = $brand_replace['vag_to'][0];
                    break;
                }
            }

            $setting = ORM::factory('Setting')->where('code_name', '=', 'tekhnomir_active_site')->find();
            if (ORM::factory('Findfromip')->check_ip() AND $setting) {
                $INFO = Tminfo::instance();
                $INFO->SetLogin('eparts');
                $INFO->SetPasswd('950667817282kda');
                for ($i = 0; $i < count($brand_replace['to']); $i++) {
                    if (strnatcasecmp($brand_replace['to'][$i], $json_price->brand) == 0) {
                        $id_key_value = $i;
                        $id_key = true;
                        break;
                    }
                }
                for ($i = 0; $i < count($brand_replace['vag_from']); $i++) {
                    if (strnatcasecmp(Article::get_short_article($brand_replace['vag_from'][$i]), $part_obj->brand) == 0) {
                        $id_key_vag = true;
                        break;
                    }
                }
                if ($id_key) {

                    $tm_items1 = $INFO->GetPrice($json_price->article, $brand_replace['from1'][$id_key_value]);
                    $tm_items2 = $INFO->GetPrice($json_price->article, $brand_replace['from2'][$id_key_value]);
                    $tm_items = array_merge($tm_items1, $tm_items2);
                } else {
                    if($id_key_vag)
                    {
                        $tm_items = $INFO->GetPrice($part_obj->article, $brand_replace['vag_to'][0]);
                    }
                    else
                    {
                        $tm_items = $INFO->GetPrice($part_obj->article, $part_obj->brand);
                    }
                }

            } else {
                $tm_items = array();
            }

            $items = array();
            if ($tm_items) {
                $usd_currency = ORM::factory('Currency')->get_by_code('USD');
                $currency_id = $usd_currency->id;

                $usd_ratio = ORM::factory('Currency')->get_by_code('USD')->ratio;
                $eur_ratio = ORM::factory('Currency')->get_by_code('EUR')->ratio;

                $setting = ORM::factory('Setting')->where('code_name', '=', 'tekhnomir_percentage')->find();
                $tekhnomir_percentage = !empty($setting->id) && !empty($setting->value) ? $setting->value : 0;

                foreach ($tm_items AS $key => $row) {
                    $item = $row;


                    $price = (double)$row['Price'];
                    if ($row['Currency'] == 'EUR') $price = $price * ($eur_ratio / $usd_ratio);
                    else if ($row['Currency'] == 'UAH') $price = $price / $usd_ratio;

                    $price = $price * ((100 + $tekhnomir_percentage) / 100);

                    if ($row['DeliveryTime'] == 0) $row['DeliveryTime'] = 1;

                    $delivery_setting = array(
                        'LOCAL' => 0,
                        'AIR' => 5,
                        'CONTAINER' => 3.2,
                    );

                    if (isset($row['DeliveryType'])) {
                        if (!in_array($row['DeliveryType'], array_keys($delivery_setting))) continue;
                        else $price = $price + $delivery_setting[$row['DeliveryType']] * $row['Weight'];
                    }

                    $price = round($price, 2);

                    $row['DeliveryTime'] += 1;

                    $price_item = ORM::factory('Priceitem');
                    $price_item->set('price', $price);
                    $price_item->set('currency_id', $currency_id);
                    $price_item->set('amount', ($row['Quantity'] == 0 ? "" : $row['Quantity']));
                    $price_item->set('delivery', $row['DeliveryTime']);
                    $price_item->set('supplier_id', 38);
                    $price_item->part = $json_price;

                    $json_array['price'] = $price;
                    $json_array['currency_id'] = $currency_id;
                    $json_array['amount'] = $row['Quantity'];
                    $json_array['delivery'] = $row['DeliveryTime'];
                    $json_array['supplier_code'] = $row['SupplierCode'];
                    $json_array['supplier_id'] = 38;
                    $json_array['part_id'] = 0;
                    $json_array['article'] = $json_price->article;
                    $json_array['brand'] = $json_price->brand;
                    $json_array['name'] = $json_price->name;

                    try {
                        $price_item->id = str_replace('=', '_', base64_encode(json_encode($json_array)));
                    } catch (Exception $e) {
                        $json_array['name'] = iconv('WINDOWS-1251', 'UTF-8//IGNORE', $json_array['name']);
                        $price_item->id = str_replace('=', '_', base64_encode(json_encode($json_array)));
                    }

                    $items[] = $price_item;
                }
            }


            $price_items = $items;

            $price_item = $this->get_best_match($price_items);
            //$price_item = usort($price_item, 'delete_by_delivery');
            $price_item = array_values($price_item);



            if (!isset($price_item[1]) AND isset($price_item[0])) {
                $price_item = $price_item[0];
            } else {
                if (isset($price_item[1]) AND isset($price_item[0])) {
                    if ($price_item[0]->delivery == $price_item[1]->delivery) {
                        $price_item = $price_item[0]->get_price_for_client() >= $price_item[1]->get_price_for_client() ? $price_item[1] : $price_item[0];
                    }
                }
            }
            $cross_art = $json_price->article;
            $cross_brand =  $json_price->brand;
//            var_dump($price_item);
//            exit();

        }

		$crosses = array();
		$crosses_original = array();

		$uah_currency = ORM::factory('Currency')->get_by_code('UAH');
		$fake_supplier = ORM::factory('Supplier');
		$fake_supplier->notice = '---';
		$fake_supplier->phone = '---';
		$fake_supplier->сomment_text = '---';
		$fake_supplier->name = '---';

		foreach($tecdoc->get_crosses($cross_art, $cross_brand) as $cross) {
			$part_tmp = ORM::factory('Part')
				->where('article', '=', $cross['art']);
			if($cross['brand_short']) {
				$part_tmp = $part_tmp->and_where('brand', '=', $cross['brand_short']);
			}
			$part_tmp = $part_tmp->find();
			if($part_tmp->loaded()) {
				if($part_tmp->priceitems->with('supplier')->where('supplier.dont_show', '=', 0)->count_all() == 0) continue;
				$cross_data = $part_tmp->priceitems->with('supplier')->where('supplier.dont_show', '=', 0)->find_all()->as_array();
				if($cross_data) {
					if($cross['article_type'] == '3') {
						$crosses_original = array_merge($crosses_original, $cross_data);
					} else {
						$crosses = array_merge($crosses, $cross_data);
					}
				}
			}
		}

		if(!empty($part_obj->id)) {
			$crosses_tmp = ORM::factory('Cross')->where('from_id', '=', $part_obj->id)->and_where('to_id', 'IS NOT', NULL)->find_all()->as_array();
				
			foreach($crosses_tmp as $cross) {
				if($cross->to_part->priceitems->with('supplier')->where('supplier.dont_show', '=', 0)->count_all() == 0) continue;
				$cross_data = $cross->to_part->priceitems->with('supplier')->where('supplier.dont_show', '=', 0)->find_all()->as_array();
				if($cross_data)
					$crosses = array_merge($crosses, $cross_data);
			}
		}
		usort($crosses, 'sort_objects_by_price');
		usort($crosses_original, 'sort_objects_by_price');

		$original_crosses_group = array();
		foreach($crosses_original as $cross) {
			if(!array_key_exists($cross->part->id , $original_crosses_group)) {
				$original_crosses_group[$cross->part->id] = array(
					'part' => $cross->part,
					'price_items' => array()
				);
			}
			
			$original_crosses_group[$cross->part->id]['price_items'][] = $cross;
		}

		$crosses_group = array();
		foreach($crosses as $cross) {
			if(!array_key_exists($cross->part->id , $crosses_group)) {
				$crosses_group[$cross->part->id] = array(
					'part' => $cross->part,
					'price_items' => array()
				);
			}

			$crosses_group[$cross->part->id]['price_items'][] = $cross;
		}
		if(count($original_crosses_group) > 0)
			$groups[] = array(0 => "Оригинальные заменители", 1 => $original_crosses_group);
		if(count($crosses_group) > 0)
			$groups[] = array(0 => "Аналоги и заменители", 1 => $crosses_group);
		
		//$this->template->scripts[] = 'jquery-ui-1.10.4.custom.min';
		$this->template->scripts[] = 'jquery.fancybox.pack';
		$this->template->scripts[] = 'common/catalog_article';
		$this->template->styles[] = 'fancybox/jquery.fancybox';

		//OPEN GRAPH BLOCK//

//		$open_graph = array();
//		$open_graph['title'] = ($category ? $category->name . ' ' : '') . $part_obj->get_brand() . ' ' . $part_obj->article_long;
//		$open_graph['type'] = $part_obj->article_long;
//		$open_graph['url'] = URL::base() . Request::current()->uri();
//		$this->template->open_graph = $open_graph;

		////////////////////
	}

	public function action_article()
	{
		$id = $this->request->param('id');


		if (!empty($id)) {
		    if(!is_object(json_decode(base64_decode(str_replace('_','=',$id)))))
            {
                $part = ORM::factory('Part')->where('id', '=', $id)->find()->as_array();
                if (empty($part['id'])) {
                    HTTP::redirect(URL::base(), 301);
                } elseif ($part['tecdoc_id']) {
                    $tecdoc = ORM::factory('Tecdoc');
                    $part_tecdoc = $tecdoc->get_part($part['tecdoc_id']);
                    $url = Htmlparser::transliterate($part_tecdoc['brand'] . "-" . $part_tecdoc['article_nr'] . "-" . substr($part_tecdoc['description'], 0, 50)) . "-" . $part['id'];
                } else {
                    $url = Htmlparser::transliterate($part['brand'] . "-" . $part['article_long'] . "-" . substr($part['name'], 0, 50)) . "-" . $part['id'];
                }
                HTTP::redirect(URL::base() . 'katalog/produkt/' . $url, 301);
            }
            else
            {
//                $id = (substr($id, 3));
//                echo $id;
//                exit();

                HTTP::redirect(URL::base() . 'katalog/produkt/' . $id, 301);
            }

        } else {
			HTTP::redirect(URL::base(), 301);
		}
	}
	
	public function action_image()
	{
		$this->auto_render = FALSE;
		$this->response->headers('Content-Type', 'image/png');
		if(empty($_GET['path'])) return;
		
		$path = $_SERVER['DOCUMENT_ROOT']."/media/tecdoc_images/".$_GET['path'];
		//echo file_get_contents($path);
//echo $path;
        try {
            $img = Image::factory($path, 'Imagick');
        } catch (Exception $e){
            $path = $_SERVER['DOCUMENT_ROOT']."/media/img/no-image.png";
            $img = Image::factory($path);
        }

		
		if(!empty($_GET['small'])) $img->resize(300, 200, NULL);
		
		echo $img->render('png');
	}

	public function action_car_choose() {
		$this->auto_render = FALSE;
		$this->is_ajax = TRUE;

		$year = !empty($_POST['year']) ? $_POST['year'] : false;
		$manuf = !empty($_POST['manuf']) ? $_POST['manuf'] : false;
		$model = !empty($_POST['model']) ? $_POST['model'] : false;
		$body_type = !empty($_POST['body_type']) ? $_POST['body_type'] : false;
		$liters_fuel = !empty($_POST['liters_fuel']) ? $_POST['liters_fuel'] : false;
		$car_mod = !empty($_POST['car_mod']) ? $_POST['car_mod'] : false;

		if($year) {
			if(!$manuf) {
				$this->car_choose_manuf();
			} elseif (!$model) {
				$this->car_choose_model($manuf, $year);
			} elseif (!$body_type) {
				$this->car_choose_body_type($model, $year);
			} elseif (!$liters_fuel) {
				$this->car_choose_liters_fuel($model, $year, $body_type);
			} elseif (!$car_mod) {
				$this->car_choose_types($model, $year, $body_type, $liters_fuel);
			}
		}
	}

	public function car_choose_manuf()
	{
		$tecdoc = Model::factory('Tecdoc');

		$result = $tecdoc->get_manufacturers(false, false, false, true);

		$items = array();
		foreach ($result as $row) {
			$items[] = array('id' => $row['id'], 'name' => strtoupper($row['brand']));
		}

		echo View::factory('car_select_form/manuf')->set('items', $items)->render();
	}

	public function car_choose_model($manufacturer_id, $year)
	{
		$tecdoc = Model::factory('Tecdoc');

		$result = $tecdoc->get_cars(false, $manufacturer_id, false, $year);

		$items = array();
		if($result) {
			foreach ($result as $row) {
				$items[] = array('id' => $row['id'], 'name' => strtoupper($row['short_description']));
			}
		}

		echo View::factory('car_select_form/model')->set('items', $items)->render();
	}

	public function car_choose_body_type($model_id, $year)
	{
		$tecdoc = Model::factory('Tecdoc');

		$result = $tecdoc->get_body_types($model_id, $year);

		$items = array();
		if($result) {
			foreach ($result as $row) {
				$items[] = array('id' => $row['body_type'], 'name' => $row['body_type']);
			}
		}

		echo View::factory('car_select_form/body_type')->set('items', $items)->render();
	}

	public function car_choose_liters_fuel($model_id, $year, $body_type)
	{
		$tecdoc = Model::factory('Tecdoc');

		$result = $tecdoc->get_liters_fuel($model_id, $year, $body_type);

		$items = array();
		if($result) {
			foreach ($result as $row) {
				if(!isset($items[$row['engine_type']])) $items[$row['engine_type']] = array();
				$items[$row['engine_type']][] = array('id' => $row['engine_type'].'-'.$row['capacity'], 'name' => $row['capacity']);
			}
		}

		echo View::factory('car_select_form/liters_fuel')->set('items', $items)->render();
	}

	public function car_choose_types($model_id, $year, $body_type=false, $liters_fuel=false)
	{
		$tecdoc = Model::factory('Tecdoc');
		
		if($liters_fuel) {
			$liters_fuel = explode('-', $liters_fuel);
			$engine_type = $liters_fuel[0];
			$capacity = $liters_fuel[1];
		} else {
			$engine_type = false;
			$capacity = false;
		}
		$result = $tecdoc->get_types(false, $model_id, false, $year, $body_type, $engine_type, $capacity);

		$items = array();
		if($result) {
			foreach ($result as $row) {
				$start_year = substr($row['start_date'], 0, 4);
				$start_month = substr($row['start_date'], 4);
				if(!empty($row['end_date'])) {
					$end_year = substr($row['end_date'], 0, 4);
					$end_month = substr($row['end_date'], 4);
				} else {
					$end_year = '.';
					$end_month = '.';
				}
				$date_txt = $start_month.".".$start_year." - ".$end_month.".".$end_year;
				$items[] = array('id' => $row['id'], 'name' => $row['description'].", ".$row['capacity_hp_from']." л.с. (".$date_txt.")");
			}
		}

		echo View::factory('car_select_form/types')->set('items', $items)->render();
	}

	public function action_set_car_mod()
	{
		$this->auto_render = FALSE;
		$this->is_ajax = TRUE;
		$this->response->headers('Content-Type', 'application/json');

		$tecdoc = Model::factory('Tecdoc');

		$data = "";
		$status = "fail";

		$car_modification = !empty($_POST['car_modification']) ? $_POST['car_modification'] : false;
		$category_slug = !empty($_POST['category_slug']) ? $_POST['category_slug'] : false;
		if(!empty($car_modification)) {
			Cookie::set('car_modification', $car_modification);

			$view = View::factory('common/car_selected')
				->bind('car_mod', $car_mod);
			$car_mod = $car_modification;
			$data = $view ->render();
			$status = "success";

			if($category_slug) {
				$modification = $tecdoc->get_type($car_modification);
				$redirect = URL::site('katalog/'.$modification['full_slug'].'/'.$category_slug);
			} else {
				$redirect = false;
			}
		}

		$json = array('data' => $data, 'status' => $status);
		if($redirect) {
			$json['redirect'] = $redirect;
		}

		echo json_encode($json);
	}

	public function action_select_another()
	{
		$this->auto_render = FALSE;
		$this->is_ajax = TRUE;
		$this->response->headers('Content-Type', 'application/json');

		Cookie::delete('car_modification');
		$data = View::factory('common/car_select_form')->render();
		$status = "success";

		echo json_encode(array('data' => $data, 'status' => $status));
	}

	public function action_get_images()
	{
		$this->auto_render = FALSE;
		$this->is_ajax = TRUE;
		$this->response->headers('Content-Type', 'application/json');
		$status = "success";

		$tecdoc = Model::factory('Tecdoc');

		$tecdoc_ids = !empty($_POST['tecdoc_ids']) ? $_POST['tecdoc_ids'] : array();

		$images = array();
		$result = $tecdoc->get_images($tecdoc_ids);
		foreach ($result as $row) {
			if(array_key_exists($row['article_id'], $images)) continue;
			$images[$row['article_id']] = URL::site('katalog/image')."?path=".$row['image'];
		}

		echo json_encode(array('status' => $status, 'images' => $images));
	}

	public function action_manufacturer_category()
	{

        if(ORM::factory('Client')->logged_in()) {
            //return Controller::redirect('authorization/login?order_add=true');
            $guest = false;
        }else{
            $guest = true;
        }
        //TOP products
        $top_items = ORM::factory('TopOrderitem')
            ->find_all()
            ->as_array();
        $top_orderitems = array();
        foreach ($top_items as $top_item=>$key)
        {
            $top_orderitems[] = Article::get_short_article($key->article);
        }

		$this->template->content = View::factory('catalog/parts')
			->bind('pagination', $pagination)
			->bind('category', $category)
			->bind('parts', $parts)
			->bind('prices', $prices)
			->bind('modification', $modification)
			->bind('model', $model)
			->bind('manufacturer', $manufacturer)
			->bind('h1', $h1)
			->bind('content_text', $content_text)
			->bind('cars_block', $cars_block)
			->bind('filter', $filter)
            ->bind('guest', $guest)
            ->bind('top_orderitems', $top_orderitems)
			->bind('manufacturers_block', $manufacturers_block);


		$tecdoc = Model::factory('Tecdoc');

		$manufacturer_slug = $this->request->param('manufacturer', false);
		$category_slug = $this->request->param('category', false);

		$manufacturers = $tecdoc->get_manufacturers($manufacturer_slug);
		$manufacturer = $manufacturers[0];

		$filter_current['brand'] = $this->request->query('brand') ? $this->request->query('brand') : array();
		$filter_current['location'] = $this->request->query('location') ? $this->request->query('location') : array();

		$models = $tecdoc->get_cars(false, $manufacturer['id'], false, false, 1);


		$modification_ids = array();

		if ($models) {
			foreach ($models AS $value) {
				$modifications = $tecdoc->get_types(false, $value['id']);
				foreach ($modifications AS $item) {
					$modification_ids[] = $item['id'];
				}
			}
		}

		if(count($modification_ids) == 0) {
			$cookie_modification_id = Cookie::get('car_modification', NULL);

			if(!empty($cookie_modification_id)) {
				$modification = $tecdoc->get_type($cookie_modification_id);

				return Controller::redirect('katalog/'.$modification['full_slug'].'/'.$category_slug);
			}
		}


		if(!ORM::factory('Catalogfromip')->check_ip()) {
			throw new HTTP_Exception_503( 'Превышено число запросов' );
			return false;
		}

		$tree_id = false;
		$category = ORM::factory('Category')->where('slug', '=', $category_slug)->find();

		if ($category->tecdoc_ids) {
			$current_tree = explode(",", $category->tecdoc_ids);
			foreach ($current_tree as $one) {
				$tree_id[] = $one;
			}
		}

		if($manufacturer['id']) {
			$cars = $tecdoc->get_cars(false, $manufacturer['id']);
			$cars_block = View::factory('catalog/cars_block')
				->set('manufacturer', $manufacturer)
				->set('cars', $cars)
				->set('category', $category)
				->render();
		} else $cars_block = "";

//		if(!$model_id && !$modification) {
//			$manufacturers = $tecdoc->get_manufacturers(false, false, false, true);
//			$manufacturers_block = View::factory('catalog/manufacturers_block')
//				->set('manufacturers', $manufacturers)
//				->set('category', $category)
//				->render();
//		} else $manufacturers_block = "";

		$page  = (int) $this->request->query('page');
		if($page > 0) $page -= 1;

		$items_per_page = 18;
		$offset = $page * $items_per_page;
		$limit = 5 * $items_per_page; //get 5 pages for best speed

		$article_ids = false;
		if($tree_id) {
			//get all parts
			$article_ids = $tecdoc->get_parts_ids(array_slice($modification_ids, 0, 30), false, $tree_id, $limit, $offset, true); //

			//find articles by filters
			$temp_article_ids = empty(array_filter($filter_current)) ? $article_ids : $tecdoc->get_parts_ids(array_slice($modification_ids, 0, 30), false, $tree_id, $limit, $offset, true, $filter_current);

			//brand filter
			$filter['brand'] = $tecdoc->get_brands(empty($filter_current['brand']) ? $temp_article_ids: $article_ids);

			//other filters
			$filter['location'] = $tecdoc->get_filters((empty($filter_current['location']) ? $temp_article_ids: $article_ids),100);

			//set current filter to all filters
			foreach ($filter_current AS $name => $array) {
				foreach ($array AS $value) {
					if (!in_array($value, $filter[$name]))
						$filter[$name][] = $value;
				}
				//sort every array
				if ($filter[$name]) asort($filter[$name]);
			}

			$article_ids = $temp_article_ids;

			$count = count($article_ids);
			$count = $offset + intval($count);
			$article_ids = array_slice($article_ids, 0, $items_per_page);
		} else $count = 0;
		$route_params = array('controller' => 'catalog');

		$title = $category->name;
		$h1 = $category->name;

		if($manufacturer) {
			$route_params['manufacturer'] = $manufacturer['slug'];
			$title .= " на ".$manufacturer['brand'];
			$h1 .= " на ".$manufacturer['brand'];
		}

		$route_params['category'] = $category_slug;

		$title .= " купить в Eparts.kiev.ua";

		$pagination = Pagination::factory(
			array(
				'total_items' => $count,
				'items_per_page' => $items_per_page,
				'view' => 'pagination/floating_modified',
			)
		)->route_params($route_params);

		if($article_ids) {
			$parts = $tecdoc->get_parts($article_ids);
		} else $parts = array();

		$prices = array();
		$part_ids = array();
		if(!empty($parts)) {
			foreach ($parts as $part) {
				$part_ids[] = $part['id'];
			}

			$price_items = ORM::factory('Priceitem')
				->with('supplier')
				->with('part')
				->where('part.tecdoc_id', 'IN', $part_ids)
				->and_where('supplier.dont_show', '=', 0)
				->order_by('price','DESC')
				->find_all()->as_array();

			usort($price_items, 'sort_objects_by_price');

			foreach ($price_items as $price_item) {
				$tecdoc_id = $price_item->part->tecdoc_id;
				$price = $price_item->get_price_for_client();
				if(!array_key_exists($tecdoc_id, $prices)) {
					$prices[$tecdoc_id] = array();
					$prices[$tecdoc_id]['price'] = $price;
					$prices[$tecdoc_id]['price_item'] = $price_item;
				}
			}
		}
		$seo_identifier = $this->request->uri()/*.URL::query()*/;
		$seo_data = ORM::factory('Seodata')->where('seo_identifier', '=', $seo_identifier)->find();
		if(!empty($seo_data->id)) {
			$this->template->title = $seo_data->title;
			$this->template->description = $seo_data->description;
			$this->template->keywords = $seo_data->keywords;
			$this->template->author = '';
            $this->template->noindex =  $seo_data->noindex;
			$h1 = $seo_data->h1;
			$content_text = $seo_data->content;
		} else {
			$this->template->title = $title;
			$this->template->description = "";
			$this->template->keywords = "";
			$this->template->author = '';
			$h1 = $h1;
			$content_text = "";
		}
	}

	public function action_category($slug)
	{
		$category = ORM::factory('Category')->where('level', '=', 0)->and_where('slug', '=', $slug)->find_all()->as_array();
		$this->template->content = View::factory('catalog/category')
		->bind('category', $category[0]);
//        $this->template->noindex = true;

    }
	
	/*public function action_update()
	{
		set_time_limit(0);
		$id = $this->request->param('id');
		$this->auto_render = FALSE;
		$tecdoc = Model::factory('Tecdoc');
		foreach(ORM::factory('Part')->where('id', '>=', $id)->and_where('id', '<', intval($id)+20000)->and_where('tecdoc_id', 'IS NOT', NULL)->find_all()->as_array() as $part) {
			if(empty($part->tecdoc_id)) continue;
			$tecdoc_articles = $tecdoc->get_articles($part->article, $part->brand);
			if($tecdoc_articles) {				
				$part->tecdoc_id = $tecdoc_articles[0]['id'];
				$part->save();
			}
		}
		echo "DONE ".$id;
	}*/
	/**
	 * Подбирает наиболие выгодные позиции среди всех поставщиков
	 * @param $items
	 * @return array
	 */
	public function get_best_match($items){

		$temp = array();


		foreach ($items AS $item){
		    if ($item->delivery == 0) $item->delivery = 1;
            if ($item->amount < 0){
                unset($item);
                continue;
            };
			$temp[$item->part_id][] = array(
				'id' => $item->id,
				'price' => $item->get_price_for_client(),
				'delivery' => $item->delivery,
				'amount' => $item->amount,
			);
		}

		$result = array();
		foreach ($temp AS $part_id => $array){
			$result = $this->smart_sort($array);
		}

		foreach ($items AS $key => $item){
			if (!in_array($item->id, $result)) unset($items[$key]);
		}
		return $items;
	}

	/**
	 * Ищет позицию с минимальным сроком доставки и минимальной стоимостью
	 * @param $array
	 * @return mixed
	 */
	public function smart_sort($array){

        usort($array, 'sort_by_delivery');
        $delivery = $array[0]['id'];

        usort($array, 'sort_by_price');
        $price = $array[0]['id'];

        $export = array(
            $delivery,
            $price
        );

		return $export;
	}




	/**
	 * Finds category object by tecdoc id
	 * $tecdoc_id
	 */
	public function get_category_by_tecdoc_id($tecdoc_id)
	{
		$link = DB::select()
			->from('tof_link_article')
			->where('article_id', '=', $tecdoc_id)
			//->limit(1)
			->execute()
			->as_array();

        if (empty($link)) return null;

        //$category = ORM::factory('Category')->where('tecdoc_ids', 'LIKE', '%' . $link[0]['generic_article_id'] . '%')->find();
		$category = ORM::factory('Category')->where('tecdoc_ids', 'LIKE', '%' . $link[0]['generic_article_id'] . '%')->find_all()->as_array();

        $temps_cat = array();

        foreach ($category as $cat=>$key)
        {
            $temps_cat[$key->id] = explode(",", $key->tecdoc_ids);
        }
        $keys_cat = 0;
        foreach ($temps_cat as $temp_cat=>$key)
        {
            foreach ($key as $keys=>$var)
            {
                if($var==$link[0]['generic_article_id'])
                {
                    $keys_cat = $temp_cat;
                    break;
                }
            }
        }
        $category = ORM::factory('Category')->where('id', '=' , $keys_cat)->find();
//        foreach ($category as $cat=>$key)
//        {
//            if($key->id != $keys_cat)
//            {
//                unset($category[$cat]);
//            }
//        }

		return $category->id ? $category : null;
	}

} // End Admin_Pages

function sort_objects_by_price($a, $b) {
    $a->delivery = $a->delivery==0 ? 1 : $a->delivery;
    $b->delivery = $b->delivery==0 ? 1 : $b->delivery;
	if($a->get_price_for_client() == $b->get_price_for_client() && $a->delivery == $b->delivery){ return 0 ; }
	if($a->get_price_for_client() > 0) {
		if($a->delivery == 1 && $b->delivery > 1) return -1;
		if($a->delivery > 1 && $b->delivery == 1) return 1;
		if($a->get_price_for_client() < $b->get_price_for_client() || ($a->get_price_for_client() == $b->get_price_for_client() && $a->delivery < $b->delivery)) return -1;
		return 1;
	}
}

function sort_by_price($a, $b)
{
    $a['delivery'] = $a['delivery']==0 ? 1 : $a['delivery'];
    $b['delivery'] = $b['delivery']==0 ? 1 : $b['delivery'];
    if ($a['price'] == $b['price'] AND $a['delivery'] == $b['delivery']) return 0;
    elseif ($a['price'] == $b['price'])  return ($a['delivery'] > $b['delivery']) ? 1:-1;
    return ($a['price'] > $b['price']) ? 1:-1;

}

function sort_by_delivery($a, $b)
{
    $a['delivery'] = $a['delivery']==0 ? 1 : $a['delivery'];
    $b['delivery'] = $b['delivery']==0 ? 1 : $b['delivery'];
    if ($a['delivery'] == $b['delivery'] AND $a['price'] == $b['price']) return 0;
    elseif ($a['delivery'] == $b['delivery'])  return ($a['price'] > $b['price']) ? 1:-1;
    return ($a['delivery'] > $b['delivery']) ? 1:-1;

}

//function delete_by_delivery($a, $b)
//{
//    $a['delivery'] = $a['delivery']==0 ? 1 : $a['delivery'];
//    $b['delivery'] = $b['delivery']==0 ? 1 : $b['delivery'];
//    if ($a['delivery'] == $b['delivery'] AND $a['price'] == $b['price']) unset($a);
//    elseif ($a['delivery'] == $b['delivery'] AND $a['price'] > $b['price']) unset($a);
//    elseif ($a['delivery'] == $b['delivery'] AND $a['price'] < $b['price']) unset($b);
//}