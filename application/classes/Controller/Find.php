<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Find extends Controller_Application {

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


		$this->template->content = View::factory('find/list')
			->bind('price_items', $price_items)
			->bind('crosses', $crosses)
			->bind('groups', $groups)
			->bind('parts', $final_parts)
            ->bind('guest', $guest)
            ->bind('top_orderitems', $top_orderitems)
            ->bind('readMoreButton', $readMoreButton)
            ->bind('buyTextButton', $buyTextButton)
			->bind('found', $found);

        $this->template->title = 'Прайс-лист';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		$this->template->scripts[] = 'common/face_find_list';

        $setting = ORM::factory('Setting')->where('code_name', '=', 'tekhnomir_active_site')->find();
        $INFO = Tminfo::instance();
        $INFO->SetLogin('Mir@eparts.kiev.ua');
        $INFO->SetPasswd('9506678d');


        $readMoreButton = [];
        $readMoreButton['enable'] = $settingReadMore = ORM::factory('Setting')->where('code_name', '=', 'read_more_show')->find();
        $readMoreButton['text'] = $settingReadMore = ORM::factory('Setting')->where('code_name', '=', 'read_more')->find();

        $buyTextButton =  $settingReadMore = ORM::factory('Setting')->where('code_name', '=', 'button_buy_text')->find();

		$article = Article::get_short_article($_GET['art']);
		$brand = null;
        $crosses = [];
        $final_parts = [];
		if(!empty($_GET['brand'])) $brand = Article::get_short_article($_GET['brand']);

        if(empty($brand))
        {

            $parts = "SELECT * FROM parts WHERE (article = '".$article."' OR name = '".$article."'OR brand = '".$article."'OR brand_long = '".$article."') AND brand IS NOT NULL  AND brand != '' ";
            $parts = DB::query(Database::SELECT,$parts)->execute('tecdoc_new')->as_array();

            $tm_price = $setting->value != 0 ? $INFO->GetPrice($article, null) : [];
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
                parts.name, parts.images, 
                parts.article, parts.article_long, 
                parts.brand, parts.brand_long, 
                brands.country, brands.original,
                priceitems.amount, priceitems.delivery, 
                priceitems.price * currencies.ratio AS price_start,
                priceitems.price * currencies.ratio * (SELECT discount_limits.percentage / 100 + 1
                     FROM discount_limits
                       LEFT JOIN discounts ON discount_limits.discount_id = discounts.id
                     WHERE discounts.standart = 1
                           AND priceitems.price * currencies.ratio > discount_limits.from
                           AND (priceitems.price * currencies.ratio <= discount_limits.to OR
                                discount_limits.to = 0)
                     LIMIT 1) AS price
                FROM priceitems
                INNER JOIN currencies ON currencies.id = priceitems.currency_id
                INNER JOIN suppliers ON suppliers.id = priceitems.supplier_id
                INNER JOIN parts ON parts.id = priceitems.part_id
                INNER JOIN brands ON brands.id = parts.brand_id
                WHERE parts.article = '".$final_sum_parts_new[0]['article']."' AND parts.brand = '".$final_sum_parts_new[0]['brand']."'
                      AND suppliers.dont_show = 0 AND priceitems.price <> 0
                ORDER BY priceitems.part_id, IF(priceitems.delivery = 1, priceitems.delivery, price_start),
                             IF(priceitems.delivery = 1, price_start, priceitems.delivery)
                LIMIT 1";

                $parts = DB::query(Database::SELECT,$parts)->execute('tecdoc_new')->current();

                $change_brand = ORM::factory('ChangeTmBrand')->where('replace_to_short', '=', Article::get_short_article($final_sum_parts_new[0]['brand']))->find_all()->as_array();
                if(!empty($change_brand) and !empty($change_brand[0]->replace_to )) {
                    $tm_price = [];
                    foreach ($change_brand as $brand_replace_one) {
                        $tm_items_time = $INFO->GetPrice($final_sum_parts_new[0]['article'], $brand_replace_one->replace_from_short);
                        if(!empty($tm_items_time))
                        {
                            $tm_price = array_merge($tm_price, $tm_items_time);
                        }
                    }
                }
                else
                {
                    $tm_price = $setting->value != 0 ? $INFO->GetPrice($final_sum_parts_new[0]['article'], $final_sum_parts_new[0]['brand']) : [];
                    if(empty($tm_price))
                    {
                        $brands = "SELECT brand_long FROM brands where brand = '".Article::get_short_article($final_sum_parts_new[0]['brand'])."'";
                        $brands = DB::query(Database::SELECT,$brands)->execute('tecdoc_new')->current();
                        $tm_price = $setting->value != 0 ? $INFO->GetPrice($final_sum_parts_new[0]['article'], $brands['brand_long']) : [];
                    }
                }


                if(!empty($tm_price) AND !empty($parts))
                {
                    $tm_price = $this->proccess_tekhnomir_as_prices_new($tm_price)[0];
                    $final_parts = $this->best_of_array([$parts, $tm_price]);
                }
                elseif(empty($tm_price) AND !empty($parts))
                {
                    $final_parts = $this->best_of_array([$parts]);
                }
                elseif(!empty($tm_price) AND empty($parts))
                {
                    $tm_price = $this->proccess_tekhnomir_as_prices_new($tm_price)[0];
                    $final_parts = $this->best_of_array([$tm_price]);
                }
                $crosses = NewTecdocQuery::getCrossesSite($final_sum_parts_new[0]['article'], $final_sum_parts_new[0]['brand']);

            }
            else
            {
                $final_parts = $final_sum_parts;
            }
        }
        else
        {
            $parts = "SELECT parts.id, priceitems.id as price_id, 
                parts.name, parts.images, parts.article, parts.article_long, parts.brand, 
                parts.brand_long, priceitems.amount, priceitems.delivery, 
                brands.country, brands.original,
                priceitems.price * currencies.ratio AS price_start,
                priceitems.price * currencies.ratio * (SELECT discount_limits.percentage / 100 + 1
                     FROM discount_limits
                       LEFT JOIN discounts ON discount_limits.discount_id = discounts.id
                     WHERE discounts.standart = 1
                           AND priceitems.price * currencies.ratio > discount_limits.from
                           AND (priceitems.price * currencies.ratio <= discount_limits.to OR
                                discount_limits.to = 0)
                     LIMIT 1) AS price
                FROM priceitems
                  INNER JOIN currencies ON currencies.id = priceitems.currency_id
                  INNER JOIN suppliers ON suppliers.id = priceitems.supplier_id
                INNER JOIN parts ON parts.id = priceitems.part_id
                INNER JOIN brands ON brands.id = parts.brand_id
                WHERE parts.article = '".$article."' AND parts.brand = '".$brand."'
                      AND suppliers.dont_show = 0 AND priceitems.price <> 0
                ORDER BY priceitems.part_id, IF(priceitems.delivery = 1, priceitems.delivery, price_start),IF(priceitems.delivery = 1, price_start, priceitems.delivery)
                LIMIT 1";

//            print_r($parts); exit();
            $parts = DB::query(Database::SELECT,$parts)->execute('tecdoc_new')->current();

            $tm_price = [];

            $change_brand = ORM::factory('ChangeTmBrand')->where('replace_to_short', '=', Article::get_short_article($brand))->find_all()->as_array();
            if(!empty($change_brand) and !empty($change_brand[0]->replace_to )) {

                foreach ($change_brand as $brand_replace_one) {
                    $tm_items_time = $setting->value != 0 ? $INFO->GetPrice($article, $brand_replace_one->replace_from_short) : [];
                    if(!empty($tm_items_time))
                    {
                        $tm_price = array_merge($tm_price, $tm_items_time);
                    }
                }
            }
            else
            {
                $tm_price = $setting->value != 0 ? $INFO->GetPrice($article, $brand) : [];
                if(empty($tm_price))
                {
                    $tm_price = $setting->value != 0 ? $INFO->GetPrice($article, $parts['brand_long']) : [];
                }
            }

            if(!empty($tm_price) AND !empty($parts))
            {
                $tm_price = $this->proccess_tekhnomir_as_prices_new($tm_price)[0];
                //                print_r($parts);
//                print_r($tm_price); exit();
                $final_parts = $this->best_of_array([$parts, $tm_price]);

            }
            elseif(empty($tm_price) AND !empty($parts))
            {
                $final_parts = $this->best_of_array([$parts]);
            }
            elseif(!empty($tm_price) AND empty($parts))
            {
                $tm_price = $this->proccess_tekhnomir_as_prices_new($tm_price)[0];
                $final_parts = $this->best_of_array([$tm_price]);
            }


            $crosses = NewTecdocQuery::getCrossesSite($article, $brand);


        }
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

            $change_brand = ORM::factory('ChangeTmBrand')->where('replace_from', '=', Article::get_short_article($key['Brand']))->find();
            if(!empty($change_brand->replace_to))
            {
                $key['Brand'] = $change_brand->replace_to;
            }

            if (isset($key['DeliveryType'])) {
                if (!in_array($key['DeliveryType'], array_keys($delivery_setting))) continue;
                else $key['Price'] = $key['Price'] + $delivery_setting[$key['DeliveryType']] * $key['Weight'];
            }

            if(!isset($new_array_tehnomir[Article::get_short_article($key['Brand'])]))
            {
                $new_array_tehnomir[Article::get_short_article($key['Brand'])] = $key;
            }
            else
            {
                if($new_array_tehnomir[Article::get_short_article($key['Brand'])]['Price']>$key['Price'])
                {
                    $new_array_tehnomir[Article::get_short_article($key['Brand'])] = $key;
                }
            }
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
            $price = 0;
            $price = round($item['Price'], 2)*$usd_ratio;
            $price_final = Article::get_price_for_client_by_namber($price);
            $country = "SELECT country FROM brands WHERE brand = '".Article::get_short_article($item['Brand'])."' ";
            $country = DB::query(Database::SELECT,$country)->execute('tecdoc_new')->current();

            $price_tm[] = ['id'=>!empty($part)?$part['id']:$id, 'country' =>!empty($country)?$country['country']:'', 'name'=>!empty($part)?$part['name']:$item['Name'], 'price_id'=>$id, 'images' => $part['images'], 'article'=>Article::get_short_article($item['Number']), 'article_long'=>$item['Number'], 'brand'=>Article::get_short_article($item['Brand']), 'brand_long'=>$item['Brand'], 'amount'=>$item['Quantity'] == 0 ? "" : $item['Quantity'], 'delivery'=>$item['DeliveryTime']+1, 'price_start'=>$price, 'price'=>$price_final ];
        }

        return $price_tm;
    }
    private function best_of_array($array)
    {
        $new_array_tehnomir = [];
//        print_r($array); exit();
        foreach ($array as $value => $key)
        {
            if(!isset($new_array_tehnomir[$key['brand']]))
            {
                $new_array_tehnomir[$key['brand']] = $key;
            }
            else
            {
                if((float)$new_array_tehnomir[$key['brand']]['price'] > (float)$key['price'])
                {
//                    if($new_array_tehnomir[$key['brand']]['delivery'] == 1)
                    if($key['delivery'] != 1 && $new_array_tehnomir[$key['brand']]['delivery'] == 1)
                        continue;
                    else
                        $new_array_tehnomir[$key['brand']] = $key;
                }
                else
                {
                    if($key['delivery'] == 1 && $new_array_tehnomir[$key['brand']]['delivery'] != 1)
                    {
                        $new_array_tehnomir[$key['brand']] = $key;
                    }
                }
            }
        }
//        var_dump($new_array_tehnomir); exit();
        return $new_array_tehnomir;
    }

	private function proccess_tekhnomir_as_prices($tm_cross_prices_tmp, $article, $brand, $cross = false, $brand_replace = false ) {
		$items = array();
		$uah_currency = ORM::factory('Currency')->get_by_code('UAH');
		$usd_currency = ORM::factory('Currency')->get_by_code('USD');
		$currency_id = $usd_currency->id;
        $flag = false;
		$usd_ratio = ORM::factory('Currency')->get_by_code('USD')->ratio;
		$eur_ratio = ORM::factory('Currency')->get_by_code('EUR')->ratio;
		$uah_ratio = $uah_currency->ratio;
		$part_factory = ORM::factory('Part');

		$setting = ORM::factory('Setting')->where('code_name', '=', 'tekhnomir_percentage')->find();
		$tekhnomir_percentage = !empty($setting->id) && !empty($setting->value) ? $setting->value : 0;



		if(!empty($tm_cross_prices_tmp)) foreach ( $tm_cross_prices_tmp as $row ) {
			$json_array = array();
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
//            echo $row['Brand']."<br>";
            $price = (double) $row['Price'];
            if ($row['Currency'] == 'EUR') $price = $price * ($eur_ratio / $usd_ratio);
            else if ($row['Currency'] == 'UAH') $price = $price / $usd_ratio;

			$price = $price * ((100+$tekhnomir_percentage)/100);

			if($row['DeliveryTime'] == 0) $row['DeliveryTime'] = 1;

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

			$price_item = ORM::factory('Priceitem');
			$price_item->set('price', $price);
			$price_item->set('currency_id', $currency_id);
			$price_item->set('amount', ($row['Quantity'] == 0 ? "" : $row['Quantity']));
			$price_item->set('delivery', $row['DeliveryTime']);
			$price_item->set('supplier_id', 38);
			$price_item->part = $part;

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
		return $items;
	}

	/**
	 * Подбирает наиболие выгодную позицию среди всех поставщиков
	 * @param $items
	 * @return array
	 */
	public function get_best_match($items){

		$temp = array();

		foreach ($items AS $item){
			if ($item->price > 0)
			$temp[$item->part_id][] = array(
				'id' => $item->id,
				'price' => $item->get_price_for_client(),
				'delivery' => $item->delivery,
				'amount' => $item->amount,
				'tecdoc_id' => $item->part->tecdoc_id,
			);

 		}

		$result = array();
		foreach ($temp AS $part_id => $array){
			$result[] = $this->smart_sort($array);
		}
//		echo "Hello<br>";
//        var_dump($result);
//        echo "Hello2<br>";
		foreach ($items AS $key => $item){
			if (!in_array($item->id, $result)) unset($items[$key]);
		}
//		var_dump($items);
		return $items;
	}

	/**
	 * Ищет позицию с минимальным сроком доставки и минимальной стоимостью
	 * @param $array
	 * @return mixed
	 */
	public function smart_sort($array){
		$min_delivery = $array[0]['delivery'];
		$min_price = $array[0]['price'];
        $min_price_one_delivery = $array[0]['price']*10000;

		$id = 0;
        $id_delivery = -1;

		foreach ($array AS $key => $value){
            if($value['delivery']==1 or $value['delivery']==0)
            {
                if($value['price']<$min_price_one_delivery)
                {
                    $min_price_one_delivery = $value['price'];
                    $id_delivery = $key;
                    continue;
                }
            }
			else{//$value['delivery'] <= $min_delivery AND
                if ($value['price'] < $min_price) {
                    $min_delivery = $value['delivery'];
                    $min_price = $value['price'];
                    $id = $key;
                }
			}
		}
		if($id_delivery != -1)
        {
            return $array[$id_delivery]['id'];
        }
        else
        {
            return $array[$id]['id'];
        }


	}

}

function sort_objects_by_price($a, $b) {
	if($a->get_price_for_client() == $b->get_price_for_client() && $a->delivery == $b->delivery){ return 0 ; }
	if($a->get_price_for_client() > 0) {
		if($a->delivery == 1 && $b->delivery > 1) return -1;
		if($a->delivery > 1 && $b->delivery == 1) return 1;
		if($a->get_price_for_client() < $b->get_price_for_client() || ($a->get_price_for_client() == $b->get_price_for_client() && $a->delivery < $b->delivery)) return -1;
		return 1;
	}
}
