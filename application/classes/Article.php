<?php defined('SYSPATH') or die('No direct script access.');

class Article {
	public static function get_short_article($article) {
		$article = strtolower($article);
		$article = str_replace(" ", "", $article);
		$article = str_replace("-", "", $article);
		$article = str_replace("/", "", $article);
		$article = str_replace("_", "", $article);
		$article = str_replace(".", "", $article);
		$article = str_replace("=", "", $article);
		$article = str_replace("'", "", $article);
		$article = str_replace("\"", "", $article);
		$article = str_replace(",", "", $article);
		$article = str_replace("?", "", $article);
		$article = str_replace("\\", "", $article);
		$article = str_replace("*", "", $article);
		$article = str_replace("#", "", $article);
		$article = str_replace("(", "", $article);
		$article = str_replace(")", "", $article);
		return $article;
	}

	public static function get_real_client_balance($client_id)
    {
        $query_sum_position = "SELECT 
            SUM(oi.sale_per_unit*oi.amount ) as value
            from orderitems as oi 
            LEFT JOIN orders as o ON o.id = oi.order_id
            WHERE oi.state_id = 5 AND o. client_id = ".$client_id."";

        (integer)$results_position = DB::query(Database::SELECT,$query_sum_position)->execute('tecdoc')->get('value', 0);

        $query_all_payments = "SELECT SUM(`value`) as payments FROM client_payments WHERE client_id = ".$client_id."";

        (integer)$results_payments = DB::query(Database::SELECT,$query_all_payments)->execute('tecdoc')->get('payments', 0);

        return [$results_position, $results_payments];
    }

    public static function get_supplier_balance ($supplier_id)
    {
//        $supplier_id = 10;
        $get_order_balance = "
            SELECT 
            SUM(t.SUMM) as sum_get
            FROM
            (
            SELECT o.article, o.brand, o.amount, 
            IF(s.currency_id=2, o.purchase_per_unit, o.purchase_per_unit_in_currency) as curr, s.id, s.`name`, c.`code`, 
            IF(s.currency_id=2, o.amount*o.purchase_per_unit, o.amount*o.purchase_per_unit_in_currency) as SUMM
            FROM orderitems_log as ol
            JOIN orderitems as o ON o.id = ol.orderitem_id
            JOIN suppliers as s ON s.id = o.supplier_id
            JOIN currencies as c ON o.currency_id = c.id AND o.currency_id = s.currency_id
            AND DATE(ol.date_time) >= '2016-08-08'
            AND ol.state_id IN (3,5,13,14,17)

            AND o.supplier_id = ".$supplier_id."
            GROUP BY ol.orderitem_id
            ORDER BY ol.date_time DESC
            ) as t
            GROUP BY t.id
            ";

        $order_balance = DB::query(Database::SELECT,$get_order_balance)->execute('tecdoc')->get('sum_get', 0);
        (double)$order_balance = round($order_balance,2);

        $get_return_balance = "SELECT 
            SUM(t.purchase_per_unit_in_currency*t.amount) as sum_return
            FROM
            (
            SELECT o.article, o.brand, o.amount, o.purchase_per_unit_in_currency, s.id, s.`name`, c.`code`
            FROM orderitems_log as ol
            JOIN orderitems as o ON o.id = ol.orderitem_id
            JOIN currencies as c ON o.currency_id = c.id
            JOIN suppliers as s ON s.id = o.supplier_id
            AND DATE(ol.date_time) >= '2016-08-08' 
            AND ol.state_id = 14
            AND o.state_id = 14
            AND o.supplier_id = ".$supplier_id."
            GROUP BY ol.orderitem_id
            ORDER BY ol.date_time DESC
            ) as t
            GROUP BY t.id";

        (double)$return_balance = DB::query(Database::SELECT,$get_return_balance)->execute('tecdoc')->get('sum_return', 0);
        (double)$return_balance = round($return_balance,2);

        $get_payments = "SELECT DISTINCT
            SUM(sp.`value`)as summ_payments
            FROM
            supplier_payments as sp
            WHERE supplier_id = ".$supplier_id." 
            AND date_time > '2016-08-07' 
            GROUP BY supplier_id";

        (double)$payments = DB::query(Database::SELECT,$get_payments)->execute('tecdoc')->get('summ_payments', 0);
        (double)$payments = round($payments,2);

//        echo $payments."<br>".$order_balance."<br>".$return_balance;
//        exit();
//
//        var_dump($get_order_balance);
//        echo "<br><br><br><br><br>";
//        var_dump($get_return_balance);
//        echo "<br><br><br><br><br>";
//        var_dump($get_payments);
//        echo "<br><br><br><br><br>";
//        exit();

        (double)$balance = round($payments-$order_balance+$return_balance, 2);

        return $balance;

    }
	
	public static function shorten_string($oldstring, $wordsreturned)
	{
		$string = preg_replace('/(?<=\S,)(?=\S)/', ' ', $oldstring);
		$string = str_replace("\n", " ", $string);
		$array = explode(" ", $string);
		if (count($array)<=$wordsreturned)
		{
			$retval = $string;
		}
		else
		{
			array_splice($array, $wordsreturned);
			$retval = implode(" ", $array);//." ...";
		}
		return $retval;
	}
    

    public static function get_price_for_client_by_namber($price, $discount_id = false)
    {
        if(!$discount_id)
        {
            if(!ORM::factory('Client')->logged_in()) {
                $discount = ORM::factory('Discount')->getStandart();
            } else {
                $discount = ORM::factory('Client')->get_client()->discount;
            }

            foreach($discount->discount_limits->find_all()->as_array() as $dl) {
                if($price > $dl->from && ($price <= $dl->to || $dl->to == 0)) {
                    $price_final = round(($price * (100 + $dl->percentage) / 100), 0);
                    break;
                }
            }
        }
        else
        {
            $discount = ORM::factory('Discount')->where('id', '=', $discount_id)->find();
            {
                foreach($discount->discount_limits->find_all()->as_array() as $dl) {
                    if($price > $dl->from && ($price <= $dl->to || $dl->to == 0)) {
                        $price_final = round(($price * (100 + $dl->percentage) / 100), 0);
                        break;
                    }
                }
            }
        }
        return isset($price_final) ? $price_final : $price;
    }

//    public static function get_price_for_client_by_namber($price, $discount_id = false)
//    {
//        if(!$discount_id)
//        {
//            if(!ORM::factory('Client')->logged_in()) {
//                $discount = ORM::factory('Discount')->getStandart();
//            } else {
//                $discount = ORM::factory('Client')->get_client()->discount;
//            }
//
//            foreach($discount->discount_limits->find_all()->as_array() as $dl) {
//                if($price > $dl->from && ($price <= $dl->to || $dl->to == 0)) {
//                    $price_final = round(($price * (100 + $dl->percentage) / 100), 0);
//                    break;
//                }
//            }
//        }
//        else
//        {
//            $discount = ORM::factory('Discount')->where('id', '=', $discount_id)->find();
//            {
//                foreach($discount->discount_limits->find_all()->as_array() as $dl) {
//                    if($price > $dl->from && ($price <= $dl->to || $dl->to == 0)) {
//                        $price_final = round(($price * (100 + $dl->percentage) / 100), 0);
//                        break;
//                    }
//                }
//            }
//        }
//        return $price_final;
//    }
}