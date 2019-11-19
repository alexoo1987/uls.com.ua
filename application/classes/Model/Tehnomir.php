<?php
/**
 * Created by PhpStorm.
 * User: artem
 * Date: 04.06.17
 * Time: 15:17
 */
class Model_Tehnomir extends Model {
    public function process_tm_in_product_page($tm_items, $part)
    {
        $usd_currency = ORM::factory('Currency')->get_by_code('USD');
        $currency_id = $usd_currency->id;
        $usd_ratio = $usd_currency->ratio;

        $setting = ORM::factory('Setting')->where('code_name', '=', 'tekhnomir_percentage')->find();
        $tekhnomir_percentage = !empty($setting->id) && !empty($setting->value) ? $setting->value : 0;

        foreach ($tm_items AS $key => $row) {
            $item = $row;
            $price = (double)$row['Price'];
            $price = $price * ((100 + $tekhnomir_percentage) / 100);

            if ($row['DeliveryTime'] == 0) $row['DeliveryTime'] = 1;

            $delivery_setting = array(
                'LOCAL' => 0,
                'AIR' => 5,
                'CONTAINER' => 3.2,
            );

            $change_brand = ORM::factory('ChangeTmBrand')->where('replace_from', '=', Article::get_short_article($row['Brand']))->find();
            if(!empty($change_brand->replace_to))
            {
                $row['Brand'] = $change_brand->replace_to;
            }

            if (isset($row['DeliveryType'])) {
                if (!in_array($row['DeliveryType'], array_keys($delivery_setting))) continue;
                else $price = $price + $delivery_setting[$row['DeliveryType']] * $row['Weight'];
            }

            $price = round($price, 2);

            $json_array['price'] = $price;
            $json_array['currency_id'] = $currency_id;
            $json_array['amount'] = $row['Quantity'];
            $json_array['delivery'] = $row['DeliveryTime'];
            $json_array['supplier_code'] = $row['SupplierCode'];
            $json_array['supplier_id'] = 38;
            if(!empty ($part))
            {
                $json_array['part_id'] = $part['id'];
                $json_array['article'] = $part['article_long'];
                $json_array['brand'] = $part['brand_long'];
                $json_array['name'] = $part['name'];
            }


            try {
                $id = str_replace('=', '_', base64_encode(json_encode($json_array)));
            } catch (Exception $e) {
                $json_array['name'] = iconv('WINDOWS-1251', 'UTF-8//IGNORE', $json_array['name']);
                $id = str_replace('=', '_', base64_encode(json_encode($json_array)));
            }
            $price = $price*$usd_ratio;
            $price_final = Article::get_price_for_client_by_namber($price);
            $price_tm[] = ['id'=>$id, 'amount'=>$row['Quantity'] == 0 ? "" : $row['Quantity'], 'delivery'=>$row['DeliveryTime']+1, 'price_start'=>$price, 'price'=>$price_final ];
        }
        return $price_tm;
    }
}