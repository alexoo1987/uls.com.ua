<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Find extends Controller_Admin_Application {

    public function action_index()
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

        $this->template->content = View::factory('admin/find/list')
            ->bind('price_items', $price_items)
            ->bind('final_crosses_original', $final_crosses_original)
            ->bind('final_crosses_analog', $final_crosses_analog)
            ->bind('discounts', $discounts)
            ->bind('deliverys', $deliverys)
            ->bind('delivery_type', $delivery_type)
            ->bind('discount_id', $discount_id)
            ->bind('order_by', $order_by)
            ->bind('order_by_str', $order_by_str)
            ->bind('groups', $groups)
            ->bind('parts', $final_parts)
            ->bind('discount_str', $discount_str)
            ->bind('guest', $guest)
            ->bind('top_orderitems', $top_orderitems)
            ->bind('found', $found);

        $this->template->title = 'Прайс-лист';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
        $this->template->scripts[] = 'common/face_find_list';

        $discounts = array();
        $deliverys = [];
        $order_by = [];
        $order_by['price_start'] = 'По цене';
        $order_by['delivery'] = 'По сроку доставки';


        foreach(ORM::factory('Discount')->order_by('id', 'asc')->find_all()->as_array() as $discount) {
            $discounts[$discount->id] = $discount->name;
        }

        if(!ORM::factory('Permission')->checkPermission('vip_price'))
            unset($discounts[11]);

        $deliverys[1] = 'все';
        $deliverys[2] = '1 день';
        $deliverys[3] = '2-3 дня';
        $deliverys[4] = 'больше 3х дней';

        //сортировка
        if(isset($_GET['order_by']) && !empty($_GET['order_by'])) {
            $order_by_str = $_GET['order_by'];
        } else {
            $order_by_str = "price_start";
        }

        //уровень цен
        if(isset($_GET['discount_id']) && !empty($_GET['discount_id'])) {
            $discount_id = $_GET['discount_id'];
            $discount_str = "&discount_id=".$discount_id;
        } else {
            $discount_tmp = ORM::factory('Discount')->where('admin_default', '=', '1')->find();
            if($discount_tmp->id)
                $discount_id = $discount_tmp->id;
            else
                $discount_id = 1;
            $discount_str = "";
        }

        if(isset($_GET['delivery_type']) && !empty($_GET['delivery_type'])) {
            $delivery_type = $_GET['delivery_type'];
            $delivery_str = "&delivery_type=".$delivery_type;
        } else {
            $delivery_type = 1;
            $delivery_str = "";
        }

        $INFO = Tminfo::instance();
        $INFO->SetLogin('Mir@eparts.kiev.ua');
        $INFO->SetPasswd('9506678d');

        $article = Article::get_short_article($_GET['art']);
        $brand = null;
        $crosses = [];
        $final_parts = [];
        if(!empty($_GET['brand'])) $brand = Article::get_short_article($_GET['brand']);

        if(empty($brand))
        {

            $parts = "SELECT * FROM parts WHERE article = '".$article."' AND brand IS NOT NULL  AND brand != '' ";
            $parts = DB::query(Database::SELECT,$parts)->execute('tecdoc_new')->as_array();

            $tm_price = $INFO->GetPrice($article, null);
            if(!empty($tm_price))
            {
                $tm_price = $this->proccess_tekhnomir_as_prices_new($tm_price);
                $sum_parts = array_merge($parts, $tm_price);
            }
            else
            {
                $sum_parts = $parts;
            }

            $final_sum_parts = [];
            foreach ($sum_parts as $value => $key)
            {
                if(!isset($final_sum_parts[$key['article'].$key['brand']]))
                {
                    $final_sum_parts[$key['article'].$key['brand']] = $key;
                }
            }


            if(count($final_sum_parts) == 0)
            {
                $final_sum_parts = [];
            }
            elseif (count($final_sum_parts) == 1)
            {
                $final_sum_parts_new = [];
                foreach ($final_sum_parts as $part_final)
                {
                    $final_sum_parts_new[] = $part_final;
                    unset($final_sum_parts);
                    break;
                }

                $parts = "SELECT parts.id, priceitems.id as price_id, 
                parts.name, parts.images, parts.article, 
                parts.article_long, parts.brand, parts.brand_long, suppliers.id as supplier_id, suppliers.notice, suppliers.name as supplier_name,
                brands.country,
                priceitems.amount, priceitems.delivery, priceitems.price * currencies.ratio AS price_start
                FROM priceitems
                INNER JOIN currencies ON currencies.id = priceitems.currency_id
                INNER JOIN suppliers ON suppliers.id = priceitems.supplier_id
                INNER JOIN parts ON parts.id = priceitems.part_id
                LEFT JOIN brands ON brands.id = parts.brand_id
                WHERE parts.article = '".$final_sum_parts_new[0]['article']."' AND parts.brand = '".$final_sum_parts_new[0]['brand']."'
                      AND suppliers.dont_show = 0 AND priceitems.price <> 0
                ORDER BY priceitems.part_id, IF(priceitems.delivery = 1, priceitems.delivery, price_start),
                             IF(priceitems.delivery = 1, price_start, priceitems.delivery)";

                $parts = DB::query(Database::SELECT,$parts)->execute('tecdoc_new')->as_array();

                $change_brand = ORM::factory('ChangeTmBrand')->where('replace_to_short', '=', Article::get_short_article($final_sum_parts_new[0]['brand']))->find_all()->as_array();

                if(!empty($change_brand) and !empty($change_brand[0]->replace_to )) {
                    $tm_price =  [];
                    foreach ($change_brand as $brand_replace_one) {
                        $tm_items_time = $INFO->GetPrice($final_sum_parts_new[0]['article'], $brand_replace_one->replace_from_short);
                        try{
                            $tm_price = array_merge($tm_price, $tm_items_time);
                        }
                        catch (Exception $e)
                        {
                            continue;
                        }
                    }
                }
                else
                {
                    $tm_price = $INFO->GetPrice($final_sum_parts_new[0]['article'], $final_sum_parts_new[0]['brand']);
                    if(empty($tm_price))
                    {
                        $brands = "SELECT brand_long FROM brands where brand = '".Article::get_short_article($final_sum_parts_new[0]['brand'])."'";
                        $brands = DB::query(Database::SELECT,$brands)->execute('tecdoc_new')->current();
                        $tm_price = $INFO->GetPrice($final_sum_parts_new[0]['article'], $brands['brand_long']);
                    }
                }

                if(!empty($tm_price) AND !empty($parts))
                {

                    $tm_price = $this->proccess_tekhnomir_as_prices_new($tm_price);

                    $part = "SELECT parts.*, brands.country, brands.original FROM parts LEFT JOIN brands ON brands.id = parts.brand_id WHERE article = '".$final_sum_parts_new[0]['article']."' AND parts.brand = '".$final_sum_parts_new[0]['brand']."'";
                    $part = DB::query(Database::SELECT,$part)->execute('tecdoc_new')->current();
                    $tm_price = array_merge($parts, $tm_price);

                    $final_parts[$part['id']] = ['part' => $part, 'priceitems' => $tm_price ];

                }
                elseif(empty($tm_price) AND !empty($parts))
                {
                    $part = "SELECT parts.*, brands.country, brands.original FROM parts LEFT JOIN brands ON brands.id = parts.brand_id WHERE article = '".$final_sum_parts_new[0]['article']."' AND parts.brand = '".$final_sum_parts_new[0]['brand']."'";
                    $part = DB::query(Database::SELECT,$part)->execute('tecdoc_new')->current();

                    $final_parts[$part['id']] = ['part' => $part, 'priceitems' => $parts ];

                }
                elseif(!empty($tm_price) AND empty($parts))
                {
                    $tm_price = $this->proccess_tekhnomir_as_prices_new($tm_price);
                    $part = "SELECT parts.*, brands.country, brands.original FROM parts LEFT JOIN brands ON brands.id = parts.brand_id WHERE article = '".$final_sum_parts_new[0]['article']."' AND parts.brand = '".$final_sum_parts_new[0]['brand']."'";
                    $part = DB::query(Database::SELECT,$part)->execute('tecdoc_new')->current();
                    if(empty($part))
                    {
                        $part = ['id' => $tm_price[0]['price_id'], 'country' => '', 'name' => $tm_price[0]['name'], 'tecdoc_id' => '', 'images' => '', 'article' => $tm_price[0]['article'], 'article_long' => $tm_price[0]['article_long'], 'brand' => $tm_price[0]['brand'], 'brand_long' => $tm_price[0]['brand_long'],];
                    }

                    $final_parts[$part['id']] = ['part' => $part, 'priceitems' => $tm_price ];
                }

//                = '".$final_sum_parts_new[0]['brand']."'

                if($delivery_type != 1)
                {
                    $crosses_original = NewTecdocQuery::get_crosses($final_sum_parts_new[0]['article'], $final_sum_parts_new[0]['brand'], 1, $delivery_type);
                }
                else
                {
                    $crosses_original = NewTecdocQuery::get_crosses($final_sum_parts_new[0]['article'], $final_sum_parts_new[0]['brand'], 1);
                }

                $final_crosses_original = [];
                foreach ($crosses_original as $cross)
                {
                    if(!isset($final_crosses_original[(integer)$cross['id']]))
                    {
                        $final_crosses_original[(integer)$cross['id']]['priceitems'][] = $cross;
                        $final_crosses_original[(integer)$cross['id']]['flags'] = ['quicle'=>0, 'midle' => 0, 'slow'=> 0];
                        if($cross['delivery']<2)
                        {
                            $final_crosses_original[(integer)$cross['id']]['flags']['quicle'] += 1;
                        }
                        elseif ($cross['delivery']>1 AND $cross['delivery']<4)
                        {
                            $final_crosses_original[(integer)$cross['id']]['flags']['midle'] += 1;
                        }
                        else
                        {
                            $final_crosses_original[(integer)$cross['id']]['flags']['slow'] += 1;
                        }
                    }
                    else
                    {
                        $final_crosses_original[(integer)$cross['id']]['priceitems'][] = $cross;
                        if($cross['delivery']<2)
                        {
                            $final_crosses_original[(integer)$cross['id']]['flags']['quicle'] += 1;
                        }
                        elseif ($cross['delivery']>1 AND $cross['delivery']<4)
                        {
                            $final_crosses_original[(integer)$cross['id']]['flags']['midle'] += 1;
                        }
                        else
                        {
                            $final_crosses_original[(integer)$cross['id']]['flags']['slow'] += 1;
                        }
                    }
                }

                if($delivery_type != 1)
                {
                    $crosses_analog = NewTecdocQuery::get_crosses($final_sum_parts_new[0]['article'], $final_sum_parts_new[0]['brand'], 0, $delivery_type);
                }
                else
                {
                    $crosses_analog = NewTecdocQuery::get_crosses($final_sum_parts_new[0]['article'], $final_sum_parts_new[0]['brand'], 0 );
                }


                $final_crosses_analog = [];
                foreach ($crosses_analog as $cross)
                {
                    if(!isset($final_crosses_analog[(integer)$cross['id']]))
                    {
                        $final_crosses_analog[(integer)$cross['id']]['priceitems'][] = $cross;
                        $final_crosses_analog[(integer)$cross['id']]['flags'] = ['quicle'=>0, 'midle' => 0, 'slow'=> 0];
                        if($cross['delivery']<2)
                        {
                            $final_crosses_analog[(integer)$cross['id']]['flags']['quicle'] += 1;
                        }
                        elseif ($cross['delivery']>1 AND $cross['delivery']<4)
                        {
                            $final_crosses_analog[(integer)$cross['id']]['flags']['midle'] += 1;
                        }
                        else
                        {
                            $final_crosses_analog[(integer)$cross['id']]['flags']['slow'] += 1;
                        }
                    }
                    else
                    {
                        $final_crosses_analog[(integer)$cross['id']]['priceitems'][] = $cross;
                        if($cross['delivery']<2)
                        {
                            $final_crosses_analog[(integer)$cross['id']]['flags']['quicle'] += 1;
                        }
                        elseif ($cross['delivery']>1 AND $cross['delivery']<4)
                        {
                            $final_crosses_analog[(integer)$cross['id']]['flags']['midle'] += 1;
                        }
                        else
                        {
                            $final_crosses_analog[(integer)$cross['id']]['flags']['slow'] += 1;
                        }
                    }
                }

            }
            else
            {
                $final_parts = $final_sum_parts;
            }
        }
        else
        {
            $parts = "SELECT parts.id, suppliers.id as supplier_id, 
                suppliers.name as supplier_name, 
                priceitems.id as price_id, parts.name, 
                suppliers.notice,
                parts.images, parts.article, 
                parts.article_long, 
                brands.country,
                brands.original,
                parts.brand, parts.brand_long, 
                priceitems.amount, 
                priceitems.delivery, 
                priceitems.price * currencies.ratio AS price_start
                FROM priceitems
                  INNER JOIN currencies ON currencies.id = priceitems.currency_id
                  INNER JOIN suppliers ON suppliers.id = priceitems.supplier_id
                INNER JOIN parts ON parts.id = priceitems.part_id
                LEFT JOIN brands ON brands.id = parts.brand_id
                WHERE parts.article = '".$article."' AND parts.brand = '".$brand."'
                      AND suppliers.dont_show = 0 AND priceitems.price <> 0
                ORDER BY priceitems.part_id, IF(priceitems.delivery = 1, priceitems.delivery, price_start),IF(priceitems.delivery = 1, price_start, priceitems.delivery)";

            $parts = DB::query(Database::SELECT,$parts)->execute('tecdoc_new')->as_array();

            $tm_price = [];

            $change_brand = ORM::factory('ChangeTmBrand')->where('replace_to_short', '=', Article::get_short_article($brand))->find_all()->as_array();
            if(!empty($change_brand) and !empty($change_brand[0]->replace_to )) {

                foreach ($change_brand as $brand_replace_one) {
                    $tm_items_time = $INFO->GetPrice($article, $brand_replace_one->replace_from_short);
                    if(!empty($tm_items_time))
                    {
                        $tm_price = array_merge($tm_price, $tm_items_time);
                    }
                }
            }
            else
            {
                $tm_price = $INFO->GetPrice($article, $brand);
                if(empty($tm_price))
                {
                    $brands = "SELECT brand_long FROM brands where brand = '".$brand."'";
                    $brands = DB::query(Database::SELECT,$brands)->execute('tecdoc_new')->current();
                    $tm_price = $INFO->GetPrice($article, $brands['brand_long']);
                }
            }

            if(!empty($tm_price) AND !empty($parts))
            {

                $tm_price = $this->proccess_tekhnomir_as_prices_new($tm_price);
                $part = "SELECT parts.*, brands.country FROM parts LEFT JOIN brands ON brands.id = parts.brand_id WHERE parts.article = '".$article."' AND parts.brand = '".$brand."'";
//                print_r($part); exit();
                $part = DB::query(Database::SELECT,$part)->execute('tecdoc_new')->current();
                $final_parts[$part['id']] = ['part' => $part, 'priceitems' => array_merge($parts,$tm_price) ];


            }
            elseif(empty($tm_price) AND !empty($parts))
            {

                $part = "SELECT parts.*, brands.country FROM parts LEFT JOIN brands ON brands.id = parts.brand_id WHERE parts.article = '".$article."' AND parts.brand = '".$brand."' ";
//                print_r($part); exit();
                $part = DB::query(Database::SELECT,$part)->execute('tecdoc_new')->current();

                $final_parts[$part['id']] = ['part' => $part, 'priceitems' => $parts ];

            }
            elseif(!empty($tm_price) AND empty($parts))
            {
                $tm_price = $this->proccess_tekhnomir_as_prices_new($tm_price);
                $part = "SELECT parts.*, brands.country FROM parts LEFT JOIN brands ON brands.id = parts.brand_id WHERE parts.article = '".$article."' AND parts.brand = '".$brand."'";

                $part = DB::query(Database::SELECT,$part)->execute('tecdoc_new')->current();
                if(empty($part))
                {
                    $part = ['id' => $tm_price[0]['price_id'], 'country' => '', 'name' => $tm_price[0]['name'], 'tecdoc_id' => '', 'images' => '', 'article' => $tm_price[0]['article'], 'article_long' => $tm_price[0]['article_long'], 'brand' => $tm_price[0]['brand'], 'brand_long' => $tm_price[0]['brand_long'],];
                }

                $final_parts[$part['id']] = ['part' => $part, 'priceitems' => $tm_price ];

            }



            if($delivery_type != 1)
            {
                $crosses_original = NewTecdocQuery::get_crosses($article, $brand, 1, $delivery_type);
            }
            else
            {
                $crosses_original = NewTecdocQuery::get_crosses($article, $brand, 1);
            }
            $final_crosses_original = [];
            foreach ($crosses_original as $cross)
            {
                if(!isset($final_crosses_original[(integer)$cross['id']]))
                {
                    $final_crosses_original[(integer)$cross['id']]['priceitems'][] = $cross;
                    $final_crosses_original[(integer)$cross['id']]['flags'] = ['quicle'=>0, 'midle' => 0, 'slow'=> 0];
                    if($cross['delivery']<2)
                    {
                        $final_crosses_original[(integer)$cross['id']]['flags']['quicle'] += 1;
                    }
                    elseif ($cross['delivery']>1 AND $cross['delivery']<4)
                    {
                        $final_crosses_original[(integer)$cross['id']]['flags']['midle'] += 1;
                    }
                    else
                    {
                        $final_crosses_original[(integer)$cross['id']]['flags']['slow'] += 1;
                    }
                }
                else
                {
                    $final_crosses_original[(integer)$cross['id']]['priceitems'][] = $cross;
                    if($cross['delivery']<2)
                    {
                        $final_crosses_original[(integer)$cross['id']]['flags']['quicle'] += 1;
                    }
                    elseif ($cross['delivery']>1 AND $cross['delivery']<4)
                    {
                        $final_crosses_original[(integer)$cross['id']]['flags']['midle'] += 1;
                    }
                    else
                    {
                        $final_crosses_original[(integer)$cross['id']]['flags']['slow'] += 1;
                    }
                }
            }

            if($delivery_type != 1)
            {
                $crosses_analog = NewTecdocQuery::get_crosses($article, $brand, 0, $delivery_type);
            }
            else
            {
                $crosses_analog = NewTecdocQuery::get_crosses($article, $brand, 0 );
            }

            $final_crosses_analog = [];
            foreach ($crosses_analog as $cross)
            {
                if(!isset($final_crosses_analog[(integer)$cross['id']]))
                {
                    $final_crosses_analog[(integer)$cross['id']]['priceitems'][] = $cross;
                    $final_crosses_analog[(integer)$cross['id']]['flags'] = ['quicle'=>0, 'midle' => 0, 'slow'=> 0];
                    if($cross['delivery']<2)
                    {
                        $final_crosses_analog[(integer)$cross['id']]['flags']['quicle'] += 1;
                    }
                    elseif ($cross['delivery']>1 AND $cross['delivery']<4)
                    {
                        $final_crosses_analog[(integer)$cross['id']]['flags']['midle'] += 1;
                    }
                    else
                    {
                        $final_crosses_analog[(integer)$cross['id']]['flags']['slow'] += 1;
                    }
                }
                else
                {
                    $final_crosses_analog[(integer)$cross['id']]['priceitems'][] = $cross;
                    if($cross['delivery']<2)
                    {
                        $final_crosses_analog[(integer)$cross['id']]['flags']['quicle'] += 1;
                    }
                    elseif ($cross['delivery']>1 AND $cross['delivery']<4)
                    {
                        $final_crosses_analog[(integer)$cross['id']]['flags']['midle'] += 1;
                    }
                    else
                    {
                        $final_crosses_analog[(integer)$cross['id']]['flags']['slow'] += 1;
                    }
                }
            }
//            print_r($final_crosses_analog); exit();
        }
        $this->template->scripts[] = 'bootstrap-tooltip';
        $this->template->scripts[] = 'bootstrap-popover';
        $this->template->scripts[] = 'common/find_list';
    }

    private function best_of_array($array)
    {
        $new_array_tehnomir = [];
        foreach ($array as $value => $key)
        {
            if(!isset($new_array_tehnomir[$key['brand']]))
            {
                $new_array_tehnomir[$key['brand']] = $key;
            }
            else
            {
                if($new_array_tehnomir[$key['brand']]['price']>$key['price'])
                {
                    $new_array_tehnomir[$key['brand']] = $key;
                }
            }
        }
        return $new_array_tehnomir;
    }

    private function proccess_tekhnomir_as_prices_new($array_tehnomir)
    {
        $new_array_tehnomir = [];
        $setting = ORM::factory('Setting')->where('code_name', '=', 'tekhnomir_percentage')->find();
        $tekhnomir_percentage = !empty($setting->id) && !empty($setting->value) ? $setting->value : 0;


        foreach ($array_tehnomir as $value => $key)
        {
            $key['Price'] = $key['Price'] * ((100+$tekhnomir_percentage)/100);
            if ($key['DeliveryTime'] == 0) $key['DeliveryTime'] = 1;
            $delivery_setting = array(
                'LOCAL' => 0,
                'AIR' => 5,
                'CONTAINER' => 3.2,
            );

//            $key['DeliveryTime'] +=1;

            $change_brand = ORM::factory('ChangeTmBrand')->where('replace_from', '=', Article::get_short_article($key['Brand']))->find();
            if(!empty($change_brand->replace_to))
            {
                $key['Brand'] = $change_brand->replace_to;
            }

            if (isset($key['DeliveryType'])) {
                if (!in_array($key['DeliveryType'], array_keys($delivery_setting))) continue;
                else $key['Price'] = $key['Price'] + $delivery_setting[$key['DeliveryType']] * $key['Weight'];
            }

            $new_array_tehnomir[] =  $key;
        }


        $usd_currency = ORM::factory('Currency')->get_by_code('USD');
        $currency_id = $usd_currency->id;
        $usd_ratio = $usd_currency->ratio;
        $price_tm = [];

        foreach ($new_array_tehnomir as $item)
        {
            $json_array['price'] = $item['Price'];
            $json_array['currency_id'] = 1;
            $json_array['amount'] = $item['Quantity'];
            $json_array['delivery'] = $item['DeliveryTime'];
            $json_array['supplier_code'] = $item['SupplierCode'];
            $json_array['supplier_id'] = 38;

//            var_dump($json_array); exit();

            $part = "SELECT * FROM parts WHERE article = '".Article::get_short_article($item['Number'])."' AND brand = '".Article::get_short_article($item['Brand'])."' ";
            $part = DB::query(Database::SELECT,$part)->execute('tecdoc_new')->current();

            if(!empty ($part))
            {
                $json_array['part_id'] = $part['id'];
                $json_array['article'] = $part['article_long'];
                $json_array['brand'] = $part['brand_long'];
                $json_array['name'] = $part['name'];
            }
            else
            {
                $json_array['part_id'] = 0;
                $json_array['article'] = $item['Number'];
                $json_array['brand'] = $item['Brand'];
                $json_array['name'] = $item['Name'];
            }
            try {
                $id = str_replace('=', '_', base64_encode(json_encode($json_array)));
            } catch (Exception $e) {
                $json_array['name'] = iconv('WINDOWS-1251', 'UTF-8//IGNORE', $json_array['name']);
                $id = str_replace('=', '_', base64_encode(json_encode($json_array)));
            }

//            echo $id; exit();
            $price = 0;
            $price = round($item['Price'], 2)*$usd_ratio;

            $price_tm[] = ['id'=>!empty($part)?$part['id']:$id, 'country'=>'', 'delivery_type'=>$item['DeliveryType'] == 'AIR' ? 1 : 0, 'weight'=>$item['Weight'], 'supplier_id'=>38, 'return_flag'=>$item['ReturnFlag'], 'notice'=>'', 'supplier_name'=>'Техномир', 'name'=>!empty($part)?$part['name']:$item['Name'], 'price_id'=>$id, 'image' => '', 'article'=>Article::get_short_article($item['Number']), 'article_long'=>$item['Number'], 'brand'=>Article::get_short_article($item['Brand']), 'brand_long'=>$item['Brand'], 'amount'=>$item['Quantity'] == 0 ? "" : $item['Quantity'], 'delivery'=>$item['DeliveryTime']+1, 'price_start'=>$price ];
        }

        return $price_tm;
    }
	
	private $_tm_parts = array();
	private function proccess_tekhnomir_as_prices($tm_cross_prices_tmp, $article, $brand, $cross = false, $brand_replace = false ) {
		$items = array();
		$uah_currency = ORM::factory('Currency')->get_by_code('UAH');
		$usd_currency = ORM::factory('Currency')->get_by_code('USD');
		$currency_id = $usd_currency->id;

		$usd_ratio = ORM::factory('Currency')->get_by_code('USD')->ratio;
		$eur_ratio = ORM::factory('Currency')->get_by_code('EUR')->ratio;
		$uah_ratio = $uah_currency->ratio;
		$part_factory = ORM::factory('Part');
        $flag = false;

		$setting = ORM::factory('Setting')->where('code_name', '=', 'tekhnomir_percentage')->find();
		$tekhnomir_percentage = !empty($setting->id) && !empty($setting->value) ? $setting->value : 0;

		if(!empty($tm_cross_prices_tmp)) foreach ( $tm_cross_prices_tmp as $row ) {
			$json_array = array();
			//if($row['SupplierCode'] != 'STOK') continue;
			$short_article = Article::get_short_article($row['Number']);
            if($brand_replace)
            {
                for ($i=0; $i<count($brand_replace['to']); $i++) {
                    if (strnatcasecmp($row['Brand'], $brand_replace['from1'][$i]) == 0 or strnatcasecmp($row['Brand'], $brand_replace['from2'][$i]) == 0) {
                        $row['Brand'] = $brand_replace['to'][$i];
                    }
                }
                for ($i=0; $i<count($brand_replace['vag_from']); $i++) {
                    if (strnatcasecmp($row['Brand'], $brand_replace['vag_from'][$i]) == 0) {
                        $row['Brand'] = $brand_replace['vag_to'][0];
                    }
                }
            }
			if(((strnatcasecmp($article, $short_article) != 0) ||
				(strnatcasecmp(Article::get_short_article($row['Brand']), $brand) != 0 && $brand != '')) && !$cross)
				continue;
				
			if(strnatcasecmp($article, $short_article) == 0 && 
				(strnatcasecmp(Article::get_short_article($row['Brand']), $brand) == 0 || $brand == '') && $cross)
				continue;

			$short_brand = Article::get_short_article($row['Brand']);

			
			$price = (double) $row['Price'];
			if ($row['Currency'] == 'EUR') $price = $price * ($eur_ratio / $usd_ratio);
			else if ($row['Currency'] == 'UAH') $price = $price / $usd_ratio;

			$price = $price * ((100+$tekhnomir_percentage)/100);
			
			if($row['DeliveryTime'] == 0) $row['DeliveryTime'] = 1;
//			elseif ($row['DeliveryTime'] >= 8) $price = $price + 8 * $row['Weight'];

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

			if(isset($this->_tm_parts[$short_brand]) && isset($this->_tm_parts[$short_brand][$short_article])) {
				$part = $this->_tm_parts[$short_brand][$short_article];
			} else {
				if(!isset($this->_tm_parts[$short_brand])) $this->_tm_parts[$short_brand] = array();
				$part = $part_factory->get_part($row['Number'], $row['Brand'], $row['Name'], $short_article, $short_brand);
				$this->_tm_parts[$short_brand][$short_article] = $part;
			}
//			var_dump($row);
//            echo "<br><br><br>";
		
			$price_item = ORM::factory('Priceitem');
			$price_item->set('price', $price);
			$price_item->set('currency_id', $currency_id);
			$price_item->set('amount', ($row['Quantity'] == 0 ? "" : $row['Quantity']));
			$price_item->set('delivery', $row['DeliveryTime']);
//            $price_item->set('suplier_code_tehnomir', $row['SupplierCode']);
            //$price_item->set('return_flag', $row['ReturnFlag']);
			$price_item->set('supplier_id', 38);
			$price_item->part = $part;
			$price_item->volume = $row['DeliveryType'] == 'AIR' ? 1 : 0;
			$price_item->weight = $row['Weight'];
            $price_item->return_flag = $row['ReturnFlag'];

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
				$price_item->id = str_replace('=','_',base64_encode(json_encode($json_array)));
			} catch (Exception $e) {
				$json_array['name'] = iconv('WINDOWS-1251', 'UTF-8//IGNORE', $json_array['name']);
				$price_item->id = str_replace('=','_',base64_encode(json_encode($json_array)));
			}
			
			$items[] = $price_item;
		}
//        var_dump($items);
//        echo "<br><br><br>";
		return $items;
	}

	public function action_hide_purchase() {
		$this->auto_render = FALSE;
		$this->is_ajax = TRUE;

		$checked = !empty($_POST['checked']) && $_POST['checked'] == '1' ? '1' : '0';
		Cookie::set('hide_purchase', $checked);
		echo json_encode(array('status' => 'success'));
	}
} // End Admin_Pages

function sort_objects_by_price($a, $b) {
	if ($a->get_price() == $b->get_price()){
	    if ($a->delivery == $b->delivery) return 0;
	    return $a->delivery < $b->delivery ? -1 : 1;
    }
	return ($a->get_price() > 0 && $a->get_price() < $b->get_price()) ? -1 : 1;
}
