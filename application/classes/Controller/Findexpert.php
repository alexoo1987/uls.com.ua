<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Findexpert extends Controller_Application {

	public function action_index()
	{
		//if(!Auth::instance()->logged_in('login')) Controller::redirect('admin');
		
		$this->template->content = View::factory('find/list_expert')
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
			
        $this->template->title = 'Поиск';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

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

                $crosses_original = NewTecdocQuery::get_crosses($final_sum_parts_new[0]['article'], $final_sum_parts_new[0]['brand'], 1);

                $final_crosses_original = [];
                foreach ($crosses_original as $cross)
                {
                    if(!isset($final_crosses_original[(integer)$cross['id']]))
                    {
                        $final_crosses_original[(integer)$cross['id']]['priceitems'][] = $cross;
                    }
                    else
                    {
                        $final_crosses_original[(integer)$cross['id']]['priceitems'][] = $cross;
                    }
                }

                $crosses_analog = NewTecdocQuery::get_crosses($final_sum_parts_new[0]['article'], $final_sum_parts_new[0]['brand'], 0 );

                $final_crosses_analog = [];
                foreach ($crosses_analog as $cross)
                {
                    if(!isset($final_crosses_analog[(integer)$cross['id']]))
                    {
                        $final_crosses_analog[(integer)$cross['id']]['priceitems'][] = $cross;
                    }
                    else {
                        $final_crosses_analog[(integer)$cross['id']]['priceitems'][] = $cross;
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
                }
                else
                {
                    $final_crosses_original[(integer)$cross['id']]['priceitems'][] = $cross;
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
                }
                else
                {
                    $final_crosses_analog[(integer)$cross['id']]['priceitems'][] = $cross;
                }
            }
        }

		$this->template->scripts[] = 'bootstrap-tooltip';
		$this->template->scripts[] = 'bootstrap-popover';
		$this->template->scripts[] = 'common/find_list';

	}
	
	private $_tm_parts = array();

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
function sort_objects_by_price_one_day($a, $b) {
    if (($a->delivery == $b->delivery) and ($b->delivery == 1 or $b->delivery==0)){
        if ($a->get_price() == $b->get_price()) return 0;
        return $a->get_price() < $b->get_price() ? -1 : 1;
    }
    return ($a->delivery == 0 || $a->delivery ==1) ? -1 : 1;
}