<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Ajax extends Controller_Application {
	
	public function action_index()
	{
	
	}


    public function action_close_modal()
    {
        $this->auto_render = FALSE;
        $this->is_ajax = TRUE;
        header('content-type: application/json');

        Cookie::set('close_modal_info' , true , 120*3) ;

        echo json_encode(array("status" => "success" ));
    }

    public  function action_show_modal_buy_one_click(){
	    $priceitem = ORM::factory('Priceitem', $this->request->param('id'));
        $items = [
            [
                'id' => 1,
                'priceitem' => $priceitem,
                'qty' => 1,
                'number' => 0
            ]
        ];
        echo View::factory('common/modal_buy_one_click')
            ->bind('items', $items)
            ->render();
        exit();
    }
	
    public function action_show_modal_cart()
    {
        $this->auto_render = FALSE;
        $this->is_ajax = TRUE;
//        print "Привет вася <div class='col-sm-4'></div><br>";
        $items = array();

        foreach(Cart::instance()->content as $item) {

            if(is_numeric($item['id'])) {
                $priceitem = ORM::factory('Priceitem', $item['id']);
            } else {
                $json_array = json_decode(base64_decode(str_replace('_','=',$item['id'])), true);
                $priceitem = ORM::factory('Priceitem')->get_from_arr($json_array);
            }
            $items[] = array(
                'id' => $item['id'],
                'priceitem' => $priceitem,
                'qty' => $item['qty'],
                'number' => $item['number']
            );
        }
        echo View::factory('common/modal_cart_new')->set('items', $items)->render();
        exit();
    }
    public function action_modal_cart_remove() {
        $this->auto_render = FALSE;
        $this->template->title = 'Корзина';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        if(!empty($_GET['cart_id'])) {
            $cart_id = $_GET['cart_id'];
            Cart::instance()->delete($cart_id);
        }

        $cart_result = Cart::instance()->get_count();
        $items = array();

        foreach(Cart::instance()->content as $item) {

            if(is_numeric($item['id'])) {
                $priceitem = ORM::factory('Priceitem', $item['id']);
            } else {
                $json_array = json_decode(base64_decode(str_replace('_','=',$item['id'])), true);
                $priceitem = ORM::factory('Priceitem')->get_from_arr($json_array);
            }
            $items[] = array(
                'id' => $item['id'],
                'priceitem' => $priceitem,
                'qty' => $item['qty'],
                'number' => $item['number']
            );
        }
//        $cart_result = Cart::instance()->get_count();
//        'cart_count' => $cart_result['qty'];
        echo View::factory('common/modal_cart_new')->set('items', $items)->render();
        exit();
    }

    public function action_get_number_cart()
    {
        // return Controller::redirect(html_entity_decode($this->request->post('redirect_to')));
        $cart_result = Cart::instance()->get_count();
        echo json_encode(array('status' => 'success', 'cart_count' => $cart_result['qty']));
        exit();
    }
	
	public function action_upload_image()
	{
		$this->auto_render = FALSE;
		$this->is_ajax = TRUE;
		
		if(!Auth::instance()->logged_in('admin')) return;
		
		$json = array();
		if ($this->request->method() == Request::POST)
        {
            if (isset($_FILES['filename']))
            {
                $json['img'] = 'uploads/'.$this->_save_image($_FILES['filename']);
            }
        }
		
		echo $json['img'];
	}
	
	public function action_check_client_phone()
	{
		$this->auto_render = FALSE;
		$this->is_ajax = TRUE;
		header('content-type: application/json');
		
		if(!empty($_POST['id'])) $id = $_POST['id'];
		if(!empty($_POST['phone'])) $phone = $_POST['phone'];
		
		$json = array();
		
		$client = ORM::factory('Client');
		
		$client->where('phone', '=', $phone);
		if(!empty($id)) $client->and_where('id', '!=', $id);

		$json['result'] = ($client->count_all() == 0);

		echo json_encode($json);
	}

	public function action_check_client_email()
	{
		$this->auto_render = FALSE;
		$this->is_ajax = TRUE;
		header('content-type: application/json');

		if(!empty($_POST['id'])) $id = $_POST['id'];
		if(!empty($_POST['email'])) $email= $_POST['email'];

		$json = array();

		$client = ORM::factory('Client');

		$client->where('email', '=', $email);
		if(!empty($id)) $client->and_where('id', '!=', $id);

		$json['result'] = ($client->count_all() == 0);

		echo json_encode($json);
	}
	
	protected function _save_image($image)
    {
        if (
            ! Upload::valid($image) OR
            ! Upload::not_empty($image) OR
            ! Upload::type($image, array('jpg', 'jpeg', 'png', 'gif')))
        {
            return FALSE;
        }
 
        $directory = DOCROOT.'uploads/';
 
        if ($file = Upload::save($image, NULL, $directory))
        {
            $filename = date('YmdHis').strtolower(Text::random('alnum', 20)).'.jpg';
 
            Image::factory($file)
                ->save($directory.$filename);
 
            // Delete the temporary file
            unlink($file);
 
            return $filename;
        }
 
        return FALSE;
    }

	public function action_rating()
	{

		$this->auto_render = FALSE;
		$this->is_ajax = TRUE;

		$this->response->headers('Content-Type', 'application/json');

		$value = Request::current()->post('value');
		$id = Request::current()->post('id');
		$user_id = Request::$client_ip;

        $factory = Request::current()->post('factory');

		$object = ORM::factory($factory)->where('id', '=', $id)->find();

        if ($object->ip) {
			$ip = explode(',', $object->ip);
			if (in_array($user_id, $ip)) {
				echo '0';
				exit();

			} else {
				$ip[] = $user_id;
				$object->rating = ($object->rating * $object->votes + $value) / ($object->votes + 1);
				$object->votes++;

			}
		} else {
			$ip[] = $user_id;
			$object->rating = ($object->rating * $object->votes + $value) / ($object->votes + 1);
			$object->votes++;
		}

		$object->ip = implode(',', $ip);
		$object->save();

		$temp = array(
			'votes' => $object->votes,
			'rating' => $object->rating
		);

		echo json_encode($temp);
		exit();


//		echo json_encode($json);
	}

	/**
	 * Render top menu by json
	 * @throws View_Exception
	 */
	public function action_render_menu()
	{
		$this->auto_render = FALSE;
		$this->is_ajax = TRUE;

        echo View::factory('common/categories_menu')->render();
		exit();
	}

    public function action_render_menu_horizontal()
    {
        $this->auto_render = FALSE;
        $this->is_ajax = TRUE;

        echo View::factory('common/categories_menu_horizontal')->render();
        exit();
    }

//	active_filter

    public function action_render_filters()
    {
        $model_slug = $this->request->post('model');
        $manufacturer_slug = $this->request->post('manufacturer');
        $category_id = $this->request->post('category_id');
        $linkreal = $this->request->post('linkreal');
        $active_filter = $this->request->post('active_filter');

        $brand_ids = explode("-", $active_filter);
        unset($brand_ids[0]);

        $guest = $this->guest;
//        $query = "SELECT DISTINCT parts.brand_id, parts.brand_long, parts.brand
//        FROM parts
//          INNER JOIN group_parts ON group_parts.part_id = parts.id
//          INNER JOIN type_category_group ON group_parts.group_id = type_category_group.id
//          INNER JOIN own_types ON type_category_group.type_id = own_types.tecdoc_id
//          INNER JOIN own_models ON own_types.tecdoc_models_id = own_models.tecdoc_id
//          INNER JOIN own_manufactures
//            ON own_models.tecdoc_manufacture_id = own_manufactures.tecdoc_id
//        WHERE EXISTS(SELECT 1
//                     FROM priceitems
//                       INNER JOIN suppliers ON suppliers.id = priceitems.supplier_id
//                     WHERE suppliers.dont_show = 0 AND priceitems.part_id = group_parts.part_id
//                     LIMIT 1) AND type_category_group.category_id = ".$category_id."
//              AND own_models.url = '".$model_slug."'
//              AND own_manufactures.url = '".$manufacturer_slug."'";

        $selectPartsQuery = "SELECT DISTINCT GROUP_CONCAT(group_parts.part_id) as parts_id
                                 FROM group_parts
                                 INNER JOIN type_category_group ON group_parts.group_id = type_category_group.id
                                 INNER JOIN own_types ON type_category_group.type_id = own_types.tecdoc_id
                                 INNER JOIN own_models ON own_types.tecdoc_models_id = own_models.tecdoc_id
                                 INNER JOIN own_manufactures ON own_models.tecdoc_manufacture_id = own_manufactures.tecdoc_id
                                 WHERE EXISTS(SELECT 1
                                                        FROM priceitems
                                                        INNER JOIN suppliers ON suppliers.id = priceitems.supplier_id
                                                        WHERE suppliers.dont_show = 0 AND priceitems.part_id = group_parts.part_id
                                                        LIMIT 1)
                                AND type_category_group.category_id = ".$category_id."
                                AND own_models.url = '".$model_slug."'
                                AND own_manufactures.url = '".$manufacturer_slug."' ";

        $selectPartsIds = DB::query(Database::SELECT,$selectPartsQuery)->execute('tecdoc_new')->get('parts_id',0);



        if(substr($selectPartsIds, -1) == ',')
            $selectPartsIds = mb_substr($selectPartsIds, 0, -1);


        $selectCrosses = "  SELECT DISTINCT parts.brand_id, parts.brand_long, parts.brand
                            FROM (
                                SELECT crosses_td_mod.to_id  as id
                                FROM crosses_td_mod
                                WHERE crosses_td_mod.from_id IN (".$selectPartsIds.")
                                
                                
                                UNION ALL
                                SELECT crosses_td_mod.from_id  as id
                                FROM crosses_td_mod
                                WHERE crosses_td_mod.to_id IN (".$selectPartsIds.")
                                
                                
                                UNION ALL
                                SELECT crosses.to_id as id
                               FROM crosses
                                WHERE crosses.from_id IN (".$selectPartsIds.")
                                
                                
                                UNION ALL
                                SELECT crosses.from_id  as id
                                FROM crosses
                                WHERE crosses.to_id IN (".$selectPartsIds.")
                                
                                
                                UNION ALL
                                SELECT parts.id  as id
                                FROM parts
                                WHERE parts.id IN (".$selectPartsIds.")
        
                            ) AS crosses_all
                            INNER JOIN parts ON crosses_all.id = parts.id
                            WHERE EXISTS(SELECT 1
                                FROM priceitems
                                INNER JOIN suppliers ON suppliers.id = priceitems.supplier_id
                                WHERE suppliers.dont_show = 0 AND priceitems.part_id = parts.id
                            LIMIT 1) ORDER BY parts.brand";

        $results = DB::query(Database::SELECT,$selectCrosses)->execute('tecdoc_new')->as_array();
        echo View::factory('catalog/parts_filters')->set('filters', $results)->set('linkreal', $linkreal)->set('brand_ids', $brand_ids)->render();
        exit();
    }

    public function action_render_filters_by_type()
    {
        $category_id = $this->request->post('category_id');
        $type_id = $this->request->post('type');
        $linkreal = $this->request->post('linkreal');
        $active_filter = $this->request->post('active_filter');

        $brand_ids = explode("-", $active_filter);
        unset($brand_ids[0]);

        $guest = $this->guest;
//        $query = "SELECT DISTINCT parts.brand_id, parts.brand_long, parts.brand
//        FROM parts
//          INNER JOIN group_parts ON group_parts.part_id = parts.id
//          INNER JOIN type_category_group ON group_parts.group_id = type_category_group.id
//          INNER JOIN own_types ON type_category_group.type_id = own_types.tecdoc_id
//        WHERE EXISTS(SELECT 1
//                     FROM priceitems
//                       INNER JOIN suppliers ON suppliers.id = priceitems.supplier_id
//                     WHERE suppliers.dont_show = 0 AND priceitems.part_id = group_parts.part_id
//                     LIMIT 1) AND type_category_group.category_id = ".$category_id."
//              AND own_types.tecdoc_id = '".$type_id."'";

        $selectPartsQuery = "SELECT DISTINCT GROUP_CONCAT(group_parts.part_id) as parts_id
                                 FROM group_parts
                                 INNER JOIN type_category_group ON group_parts.group_id = type_category_group.id
                                 INNER JOIN own_types ON type_category_group.type_id = own_types.tecdoc_id
                                 INNER JOIN own_models ON own_types.tecdoc_models_id = own_models.tecdoc_id
                                 INNER JOIN own_manufactures ON own_models.tecdoc_manufacture_id = own_manufactures.tecdoc_id
                                 WHERE EXISTS(SELECT 1
                                                        FROM priceitems
                                                        INNER JOIN suppliers ON suppliers.id = priceitems.supplier_id
                                                        WHERE suppliers.dont_show = 0 AND priceitems.part_id = group_parts.part_id
                                                        LIMIT 1)
                                AND type_category_group.category_id = ".$category_id."
                                AND own_types.tecdoc_id = ".$type_id." ";

        $selectPartsIds = DB::query(Database::SELECT,$selectPartsQuery)->execute('tecdoc_new')->get('parts_id',0);

        if(substr($selectPartsIds, -1) == ',')
            $selectPartsIds = mb_substr($selectPartsIds, 0, -1);


        $selectCrosses = "  SELECT DISTINCT parts.brand_id, parts.brand_long, parts.brand
                            FROM (
                                SELECT crosses_td_mod.to_id  as id
                                FROM crosses_td_mod
                                WHERE crosses_td_mod.from_id IN (".$selectPartsIds.")
                                
                                
                                UNION ALL
                                SELECT crosses_td_mod.from_id  as id
                                FROM crosses_td_mod
                                WHERE crosses_td_mod.to_id IN (".$selectPartsIds.")
                                
                                
                                UNION ALL
                                SELECT crosses.to_id as id
                               FROM crosses
                                WHERE crosses.from_id IN (".$selectPartsIds.")
                                
                                
                                UNION ALL
                                SELECT crosses.from_id  as id
                                FROM crosses
                                WHERE crosses.to_id IN (".$selectPartsIds.")
                                
                                
                                UNION ALL
                                SELECT parts.id  as id
                                FROM parts
                                WHERE parts.id IN (".$selectPartsIds.")
        
                            ) AS crosses_all
                            INNER JOIN parts ON crosses_all.id = parts.id
                            WHERE EXISTS(SELECT 1
                                FROM priceitems
                                INNER JOIN suppliers ON suppliers.id = priceitems.supplier_id
                                WHERE suppliers.dont_show = 0 AND priceitems.part_id = parts.id
                            LIMIT 1) ORDER BY parts.brand";


        $results = DB::query(Database::SELECT,$selectCrosses)->execute('tecdoc_new')->as_array();
        echo View::factory('catalog/parts_filters')->set('filters', $results)->set('linkreal', $linkreal)->set('brand_ids', $brand_ids)->render();
        exit();
    }

	public function action_add_to_cart()
    {
        $this->template->title = 'Корзина покупок';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        //Cart::instance()->add($priceitem_id, $qty/*, $number*/);
        if (HTTP_Request::POST == $this->request->method())
        {
            $priceitem_id = $this->request->post('priceitem_id');
            $qty = $this->request->post('qty');

            Cart::instance()->add($priceitem_id, $qty/*, $number*/);
            $cart_result = Cart::instance()->get_count();
            echo json_encode(array('status' => 'success', 'cart_count' => $cart_result['qty'], 'cart_price' => $cart_result['price']));
            return;
        }

        //Cart::instance()->add();
    }

    public function action_delete_from_cart()
    {
        $this->template->title = 'Корзина покупок';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        //Cart::instance()->add($priceitem_id, $qty/*, $number*/);
        if (HTTP_Request::POST == $this->request->method())
        {
            $priceitem_id = $this->request->post('priceitem_id');
            $qty = $this->request->post('qty');

            Cart::instance()->unadd($priceitem_id, $qty/*, $number*/);
            $cart_result = Cart::instance()->get_count();
            echo json_encode(array('status' => 'success', 'cart_count' => $cart_result['qty'], 'cart_price' => $cart_result['price']));
            return;
        }

        //Cart::instance()->add();
    }
	
} // End Admin_Pages
