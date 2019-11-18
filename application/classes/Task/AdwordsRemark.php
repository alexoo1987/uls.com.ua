<?php defined('SYSPATH') or die('No direct script access.');

class Task_AdwordsRemark extends Minion_Task {

    protected function _execute(array $params)
    {
        $query ="SELECT *
                FROM (
                SELECT
                parts.tecdoc_id,
                parts.brand,
                parts.article,
                parts.brand_long,
                parts.article_long,
                parts.`name`,
                parts.images,
                priceitems.id as price_id,
                priceitems.part_id,
                priceitems.price * currencies.ratio * (SELECT discount_limits.percentage / 100 + 1
                FROM discount_limits
                LEFT JOIN discounts ON discount_limits.discount_id = discounts.id
                WHERE discounts.standart = 1
                AND priceitems.price * currencies.ratio > discount_limits.from
                AND (priceitems.price * currencies.ratio <= discount_limits.to OR
                discount_limits.to = 0)
                LIMIT 1) AS price_final, priceitems.currency_id
                FROM priceitems
                INNER JOIN currencies ON currencies.id = priceitems.currency_id
                INNER JOIN suppliers ON priceitems.supplier_id = suppliers.id
                
                
                INNER JOIN (SELECT DISTINCT parts.*
                FROM parts
                WHERE EXISTS(SELECT 1
                FROM priceitems
                INNER JOIN suppliers ON suppliers.id = priceitems.supplier_id
                WHERE suppliers.dont_show = 0
                LIMIT 1) )
                AS parts ON priceitems.part_id = parts.id
                WHERE suppliers.dont_show = 0
                ORDER BY priceitems.part_id, price_final ASC
                ) AS temp
                GROUP BY part_id";
        $results = DB::query(Database::SELECT,$query)->execute('tecdoc_new')->as_array();

        header('Content-Type: text/csv; charset=windows-1251');

        $file = fopen('/var/www/media/google_files/feed_prices.csv', 'w');  /* записываем в файл */

        $array = [];

        $array[] = ["id","title","description","link","image_​link","availability","price","google_​product_​category","brand","gtin", ];

        foreach ($results as $result)
        {
            $title = substr($result['name'], 0, 50) . (strlen($result['name']) > 50 ? "..." : "")." ".$result['brand_long']." ".$result['article_long'];
            $url = $this->getPartUrl($result);
            $imgUrl = $this->getImageUrl($result['images']);
            $price = round($result['price_final'], 0)." UAH";
            $array[] = [
                $result['price_id'],
                $title,
                $result['name'],
                $url,
                $imgUrl,
                'in stock',
                $price,
                "Vehicles & Parts > Vehicle Parts & Accessories > Motor Vehicle Parts",
                $result['brand'],
                $result['article']
            ];
        }

        foreach ($array as $fields) {
            fputcsv($file, $fields, ",");   /* записываем строку в csv-файл */
        }

        fclose($file);

    }

    public function getPartUrl($part)
    {
        $partId = !empty($part['part_id']) ? $part['part_id'] : $part['id'];
        $uri = Htmlparser::transliterate($part['brand'] . '-' . $part['article'] .
                '-' . substr($part['name'], 0, 50)) . '-' . $partId;
        return "https://ulc.com.ua/katalog/produkt/".$uri;
    }

    public function getImageUrl($url)
    {
        $static = "https://ulc.com.ua/image/tecdoc_images";
        $empty = "https://ulc.com.ua/media/img/no-image.png";

        if(!empty($url))
            return $static.$url;
        else
            return $empty;
    }
}

?>
