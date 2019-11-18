<?php defined('SYSPATH') or die('No direct script access.');

class NewTecdocQuery {
    public static function getCrossesSite($article, $brand) {
           $crosses = "SELECT *
                    FROM (
                           SELECT
                             priceitems.id,
                             priceitems.part_id,
                             priceitems.amount,
                             priceitems.price * currencies.ratio             AS price_start,
                             parts.article_long,
                             parts.article,
                             parts.brand,
                             brands.country,
                             brands.original,
                             parts.brand_long,
                             parts.images,
                             parts.`name`,
                             priceitems.price * currencies.ratio * (SELECT discount_limits.percentage / 100 + 1
                                                                    FROM discount_limits
                                                                      LEFT JOIN discounts ON discount_limits.discount_id = discounts.id
                                                                    WHERE discounts.standart = 1
                                                                          AND priceitems.price * currencies.ratio > discount_limits.from
                                                                          AND (priceitems.price * currencies.ratio <= discount_limits.to OR
                                                                               discount_limits.to = 0)
                                                                    LIMIT 1) AS price_final,
                             priceitems.delivery
                           FROM priceitems
                             INNER JOIN currencies ON currencies.id = priceitems.currency_id
                             INNER JOIN suppliers ON priceitems.supplier_id = suppliers.id
                             INNER JOIN (SELECT DISTINCT
                                           parts.*
                                         FROM (
                                                SELECT crosses_td_mod.to_id as id
                                                FROM parts
                                                  INNER JOIN crosses_td_mod ON parts.id = crosses_td_mod.from_id
                                                WHERE article = '".$article."'
                                                AND brand = '".$brand."'
                                                
                                                UNION ALL
                                                SELECT crosses_td_mod.from_id
                                                FROM parts
                                                  INNER JOIN crosses_td_mod ON parts.id = crosses_td_mod.to_id
                                                WHERE article = '".$article."'
                                                AND brand = '".$brand."'
                                                
                                                UNION ALL
                                                SELECT crosses.to_id
                                                FROM parts
                                                  INNER JOIN crosses ON parts.id = crosses.from_id
                                                WHERE article = '".$article."'
                                                AND brand = '".$brand."'
                                                
                                                UNION ALL
                                                SELECT crosses.from_id
                                                FROM parts
                                                  INNER JOIN crosses ON parts.id = crosses.to_id
                                                WHERE article = '".$article."'
                                                AND brand = '".$brand."'
                                                
                                                UNION ALL
                                                SELECT crosses2.to_id
                                                FROM parts
                                                  INNER JOIN crosses AS crosses1 ON parts.id = crosses1.from_id
                                                  INNER JOIN crosses AS crosses2 ON crosses1.to_id = crosses2.from_id
                                                WHERE article = '".$article."'
                                                AND brand = '".$brand."'
                                        
                                                UNION ALL
                                                SELECT crosses2.from_id
                                                FROM parts
                                                  INNER JOIN crosses AS crosses1 ON parts.id = crosses1.to_id
                                                  INNER JOIN crosses AS crosses2 ON crosses1.from_id = crosses2.to_id
                                                WHERE article = '".$article."'
                                                AND brand = '".$brand."'
                                                
                                                UNION ALL
                                                SELECT crosses2.from_id
                                                FROM parts
                                                  INNER JOIN crosses AS crosses1 ON parts.id = crosses1.from_id
                                                  INNER JOIN crosses AS crosses2 ON crosses1.to_id = crosses2.to_id
                                                WHERE article = '".$article."'
                                                AND brand = '".$brand."'
                                                
                                                UNION ALL
                                                SELECT crosses2.to_id
                                                FROM parts
                                                  INNER JOIN crosses AS crosses1 ON parts.id = crosses1.to_id
                                                  INNER JOIN crosses AS crosses2 ON crosses1.from_id = crosses2.from_id
                                                WHERE article = '".$article."'
                                                AND brand = '".$brand."'
                                                
                                                
                                                
                                                
                                               /* UNION ALL
                                                SELECT crosses3.to_id
                                                FROM parts
                                                  INNER JOIN crosses AS crosses1 ON parts.id = crosses1.from_id
                                                  INNER JOIN crosses AS crosses2 ON crosses1.to_id = crosses2.to_id
                                                  INNER JOIN crosses AS crosses3 ON crosses2.from_id = crosses3.from_id
                                                WHERE article = '".$article."'
                                                AND brand = '".$brand."'
                                                
                                                UNION ALL
                                                SELECT crosses3.from_id
                                                FROM parts
                                                  INNER JOIN crosses AS crosses1 ON parts.id = crosses1.from_id
                                                  INNER JOIN crosses AS crosses2 ON crosses1.to_id = crosses2.from_id
                                                  INNER JOIN crosses AS crosses3 ON crosses2.to_id = crosses3.to_id
                                                WHERE article = '".$article."'
                                                AND brand = '".$brand."'
                                                
                                                UNION ALL
                                                SELECT crosses3.from_id
                                                FROM parts
                                                  INNER JOIN crosses AS crosses1 ON parts.id = crosses1.from_id
                                                  INNER JOIN crosses AS crosses2 ON crosses1.to_id = crosses2.to_id
                                                  INNER JOIN crosses AS crosses3 ON crosses2.from_id = crosses3.to_id
                                                WHERE article = '".$article."'
                                                AND brand = '".$brand."'
                                                
                                                UNION ALL
                                                SELECT crosses3.to_id
                                                FROM parts
                                                  INNER JOIN crosses AS crosses1 ON parts.id = crosses1.from_id
                                                  INNER JOIN crosses AS crosses2 ON crosses1.to_id = crosses2.from_id
                                                  INNER JOIN crosses AS crosses3 ON crosses2.to_id = crosses3.from_id
                                                WHERE article = '".$article."'
                                                AND brand = '".$brand."'
                                                
                                
                                                UNION ALL
                                                SELECT crosses3.from_id
                                                FROM parts
                                                  INNER JOIN crosses AS crosses1 ON parts.id = crosses1.to_id
                                                  INNER JOIN crosses AS crosses2 ON crosses1.from_id = crosses2.from_id
                                                  INNER JOIN crosses AS crosses3 ON crosses2.to_id = crosses3.to_id
                                                WHERE article = '".$article."'
                                                AND brand = '".$brand."'
                                                
                                                UNION ALL
                                                SELECT crosses3.to_id
                                                FROM parts
                                                  INNER JOIN crosses AS crosses1 ON parts.id = crosses1.to_id
                                                  INNER JOIN crosses AS crosses2 ON crosses1.from_id = crosses2.to_id
                                                  INNER JOIN crosses AS crosses3 ON crosses2.from_id = crosses3.from_id
                                                WHERE article = '".$article."'
                                                AND brand = '".$brand."'
                                              
                                                
                                                UNION ALL
                                                SELECT crosses3.to_id
                                                FROM parts
                                                  INNER JOIN crosses AS crosses1 ON parts.id = crosses1.to_id
                                                  INNER JOIN crosses AS crosses2 ON crosses1.from_id = crosses2.from_id
                                                  INNER JOIN crosses AS crosses3 ON crosses2.to_id = crosses3.from_id
                                                WHERE article = '".$article."'
                                                AND brand = '".$brand."'
                                              
                                                
                                                UNION ALL
                                                SELECT crosses3.from_id
                                                FROM parts
                                                  INNER JOIN crosses AS crosses1 ON parts.id = crosses1.to_id
                                                  INNER JOIN crosses AS crosses2 ON crosses1.from_id = crosses2.to_id
                                                  INNER JOIN crosses AS crosses3 ON crosses2.from_id = crosses3.to_id
                                                WHERE article = '".$article."'
                                                AND brand = '".$brand."' */

                                                
                                              ) AS crosses_all
                                           INNER JOIN parts ON crosses_all.id = parts.id) AS parts ON priceitems.part_id = parts.id
                                           INNER JOIN brands ON brands.id = parts.brand_id
                                           WHERE suppliers.dont_show = 0
                           ORDER BY priceitems.part_id, IF(priceitems.delivery = 1, priceitems.delivery, price_final),
                             IF(priceitems.delivery = 1, price_final, priceitems.delivery)
                         ) AS temp
                    GROUP BY part_id ORDER BY price_final";
          $crosses = DB::query(Database::SELECT,$crosses)->execute('tecdoc_new')->as_array();
          return $crosses;
    }

    public static function get_crosses($article, $brand, $original, $delivery_type = false) {

        $crosses = "SELECT *
                    FROM (
                           SELECT
                             priceitems.id as price_id,
                             priceitems.part_id as id,
                             priceitems.amount,
                             brands.country,
                             brands.original,
                             suppliers.id as supplier_id, 
                             suppliers.name as supplier_name,
                             suppliers.notice,
                             priceitems.price * currencies.ratio             AS price_start,
                             parts.article_long,
                             parts.article,
                             parts.brand,
                             parts.tecdoc_id,
                             parts.brand_long,
                             parts.images,
                             parts.`name`,
                             priceitems.delivery
                           FROM priceitems
                             INNER JOIN currencies ON currencies.id = priceitems.currency_id
                             INNER JOIN suppliers ON priceitems.supplier_id = suppliers.id
                             
                             INNER JOIN (SELECT DISTINCT
                                           parts.*
                                         FROM (
                                                SELECT crosses_td_mod.to_id as id
                                                FROM parts
                                                  INNER JOIN crosses_td_mod ON parts.id = crosses_td_mod.from_id
                                                WHERE article = '".$article."'
                                                AND brand = '".$brand."'
                                                
                                                UNION ALL
                                                SELECT crosses_td_mod.from_id
                                                FROM parts
                                                  INNER JOIN crosses_td_mod ON parts.id = crosses_td_mod.to_id
                                                WHERE article = '".$article."'
                                                AND brand = '".$brand."'
                                                
                                                UNION ALL
                                                SELECT crosses.to_id
                                                FROM parts
                                                  INNER JOIN crosses ON parts.id = crosses.from_id
                                                WHERE article = '".$article."'
                                                AND brand = '".$brand."'
                                                
                                                UNION ALL
                                                SELECT crosses.from_id
                                                FROM parts
                                                  INNER JOIN crosses ON parts.id = crosses.to_id
                                                WHERE article = '".$article."'
                                                AND brand = '".$brand."'
                                                
                                                
                                                
                                                
                                                UNION ALL
                                                SELECT crosses2.to_id
                                                FROM parts
                                                  INNER JOIN crosses AS crosses1 ON parts.id = crosses1.from_id
                                                  INNER JOIN crosses AS crosses2 ON crosses1.to_id = crosses2.from_id
                                                WHERE article = '".$article."'
                                                AND brand = '".$brand."'
                                        
                                                UNION ALL
                                                SELECT crosses2.from_id
                                                FROM parts
                                                  INNER JOIN crosses AS crosses1 ON parts.id = crosses1.to_id
                                                  INNER JOIN crosses AS crosses2 ON crosses1.from_id = crosses2.to_id
                                                WHERE article = '".$article."'
                                                AND brand = '".$brand."'
                                                
                                                UNION ALL
                                                SELECT crosses2.from_id
                                                FROM parts
                                                  INNER JOIN crosses AS crosses1 ON parts.id = crosses1.from_id
                                                  INNER JOIN crosses AS crosses2 ON crosses1.to_id = crosses2.to_id
                                                WHERE article = '".$article."'
                                                AND brand = '".$brand."'
                                                
                                                UNION ALL
                                                SELECT crosses2.to_id
                                                FROM parts
                                                  INNER JOIN crosses AS crosses1 ON parts.id = crosses1.to_id
                                                  INNER JOIN crosses AS crosses2 ON crosses1.from_id = crosses2.from_id
                                                WHERE article = '".$article."'
                                                AND brand = '".$brand."'
                                                
                                                
                                                
                                                
                                             /*  UNION ALL
                                                SELECT crosses3.to_id
                                                FROM parts
                                                  INNER JOIN crosses AS crosses1 ON parts.id = crosses1.from_id
                                                  INNER JOIN crosses AS crosses2 ON crosses1.to_id = crosses2.to_id
                                                  INNER JOIN crosses AS crosses3 ON crosses2.from_id = crosses3.from_id
                                                WHERE article = '".$article."'
                                                AND brand = '".$brand."'

                                                UNION ALL
                                                SELECT crosses3.from_id
                                                FROM parts
                                                  INNER JOIN crosses AS crosses1 ON parts.id = crosses1.from_id
                                                  INNER JOIN crosses AS crosses2 ON crosses1.to_id = crosses2.from_id
                                                  INNER JOIN crosses AS crosses3 ON crosses2.to_id = crosses3.to_id
                                                WHERE article = '".$article."'
                                                AND brand = '".$brand."'

                                                UNION ALL
                                                SELECT crosses3.from_id
                                                FROM parts
                                                  INNER JOIN crosses AS crosses1 ON parts.id = crosses1.from_id
                                                  INNER JOIN crosses AS crosses2 ON crosses1.to_id = crosses2.to_id
                                                  INNER JOIN crosses AS crosses3 ON crosses2.from_id = crosses3.to_id
                                                WHERE article = '".$article."'
                                                AND brand = '".$brand."'

                                                UNION ALL
                                                SELECT crosses3.to_id
                                                FROM parts
                                                  INNER JOIN crosses AS crosses1 ON parts.id = crosses1.from_id
                                                  INNER JOIN crosses AS crosses2 ON crosses1.to_id = crosses2.from_id
                                                  INNER JOIN crosses AS crosses3 ON crosses2.to_id = crosses3.from_id
                                                WHERE article = '".$article."'
                                                AND brand = '".$brand."'


                                                UNION ALL
                                                SELECT crosses3.from_id
                                                FROM parts
                                                  INNER JOIN crosses AS crosses1 ON parts.id = crosses1.to_id
                                                  INNER JOIN crosses AS crosses2 ON crosses1.from_id = crosses2.from_id
                                                  INNER JOIN crosses AS crosses3 ON crosses2.to_id = crosses3.to_id
                                                WHERE article = '".$article."'
                                                AND brand = '".$brand."'

                                                UNION ALL
                                                SELECT crosses3.to_id
                                                FROM parts
                                                  INNER JOIN crosses AS crosses1 ON parts.id = crosses1.to_id
                                                  INNER JOIN crosses AS crosses2 ON crosses1.from_id = crosses2.to_id
                                                  INNER JOIN crosses AS crosses3 ON crosses2.from_id = crosses3.from_id
                                                WHERE article = '".$article."'
                                                AND brand = '".$brand."'*/


                                                
                                                

                                              ) AS crosses_all
                                           INNER JOIN parts ON crosses_all.id = parts.id) AS parts ON priceitems.part_id = parts.id
                                           INNER JOIN brands ON brands.id = parts.brand_id
                                           WHERE suppliers.dont_show = 0 AND brands.original = ".$original."";

        if($delivery_type)
        {
            if($delivery_type == 2)
            {
                $crosses .= " AND priceitems.delivery = 1";
            }
            elseif ($delivery_type == 3)
            {
                $crosses .= " AND priceitems.delivery BETWEEN 2 AND 3 ";
            }
            else
            {
                $crosses .= " AND priceitems.delivery > 3";
            }
        }

        $crosses .= " ORDER BY price_start ASC ) AS temp";

        $crosses = DB::query(Database::SELECT,$crosses)->execute('default')->as_array();
        return $crosses;
    }
}




//1st level (with tehdoc)
//SELECT crosses_td_mod.to_id as id
//                                                FROM parts
//                                                  INNER JOIN crosses_td_mod ON parts.id = crosses_td_mod.from_id
//                                                WHERE article = '".$article."'
//AND brand = '".$brand."'
//
//                                                UNION ALL
//                                                SELECT crosses_td_mod.from_id
//                                                FROM parts
//                                                  INNER JOIN crosses_td_mod ON parts.id = crosses_td_mod.to_id
//                                                WHERE article = '".$article."'
//AND brand = '".$brand."'
//
//                                                UNION ALL
//                                                SELECT crosses.to_id
//                                                FROM parts
//                                                  INNER JOIN crosses ON parts.id = crosses.from_id
//                                                WHERE article = '".$article."'
//AND brand = '".$brand."'
//
//                                                UNION ALL
//                                                SELECT crosses.from_id
//                                                FROM parts
//                                                  INNER JOIN crosses ON parts.id = crosses.to_id
//                                                WHERE article = '".$article."'
//AND brand = '".$brand."'



//2nd level

//UNION ALL
//                                                SELECT crosses2.to_id
//                                                FROM parts
//                                                  INNER JOIN crosses AS crosses1 ON parts.id = crosses1.from_id
//                                                  INNER JOIN crosses AS crosses2 ON crosses1.to_id = crosses2.from_id
//                                                WHERE article = '".$article."'
//AND brand = '".$brand."'
//
//                                                UNION ALL
//                                                SELECT crosses2.from_id
//                                                FROM parts
//                                                  INNER JOIN crosses AS crosses1 ON parts.id = crosses1.to_id
//                                                  INNER JOIN crosses AS crosses2 ON crosses1.from_id = crosses2.to_id
//                                                WHERE article = '".$article."'
//AND brand = '".$brand."'
//
//                                                UNION ALL
//                                                SELECT crosses2.from_id
//                                                FROM parts
//                                                  INNER JOIN crosses AS crosses1 ON parts.id = crosses1.from_id
//                                                  INNER JOIN crosses AS crosses2 ON crosses1.to_id = crosses2.to_id
//                                                WHERE article = '".$article."'
//AND brand = '".$brand."'
//
//                                                UNION ALL
//                                                SELECT crosses2.to_id
//                                                FROM parts
//                                                  INNER JOIN crosses AS crosses1 ON parts.id = crosses1.to_id
//                                                  INNER JOIN crosses AS crosses2 ON crosses1.from_id = crosses2.from_id
//                                                WHERE article = '".$article."'
//AND brand = '".$brand."'
//


//3rd level

//
//UNION ALL
//                                                SELECT crosses3.to_id
//                                                FROM parts
//                                                  INNER JOIN crosses AS crosses1 ON parts.id = crosses1.from_id
//                                                  INNER JOIN crosses AS crosses2 ON crosses1.to_id = crosses2.to_id
//                                                  INNER JOIN crosses AS crosses3 ON crosses2.from_id = crosses3.from_id
//                                                WHERE article = '".$article."'
//AND brand = '".$brand."'
//
//                                                UNION ALL
//                                                SELECT crosses3.from_id
//                                                FROM parts
//                                                  INNER JOIN crosses AS crosses1 ON parts.id = crosses1.from_id
//                                                  INNER JOIN crosses AS crosses2 ON crosses1.to_id = crosses2.from_id
//                                                  INNER JOIN crosses AS crosses3 ON crosses2.to_id = crosses3.to_id
//                                                WHERE article = '".$article."'
//AND brand = '".$brand."'
//
//                                                UNION ALL
//                                                SELECT crosses3.from_id
//                                                FROM parts
//                                                  INNER JOIN crosses AS crosses1 ON parts.id = crosses1.from_id
//                                                  INNER JOIN crosses AS crosses2 ON crosses1.to_id = crosses2.to_id
//                                                  INNER JOIN crosses AS crosses3 ON crosses2.from_id = crosses3.to_id
//                                                WHERE article = '".$article."'
//AND brand = '".$brand."'
//
//                                                UNION ALL
//                                                SELECT crosses3.to_id
//                                                FROM parts
//                                                  INNER JOIN crosses AS crosses1 ON parts.id = crosses1.from_id
//                                                  INNER JOIN crosses AS crosses2 ON crosses1.to_id = crosses2.from_id
//                                                  INNER JOIN crosses AS crosses3 ON crosses2.to_id = crosses3.from_id
//                                                WHERE article = '".$article."'
//AND brand = '".$brand."'
//
//
//                                                UNION ALL
//                                                SELECT crosses3.from_id
//                                                FROM parts
//                                                  INNER JOIN crosses AS crosses1 ON parts.id = crosses1.to_id
//                                                  INNER JOIN crosses AS crosses2 ON crosses1.from_id = crosses2.from_id
//                                                  INNER JOIN crosses AS crosses3 ON crosses2.to_id = crosses3.to_id
//                                                WHERE article = '".$article."'
//AND brand = '".$brand."'
//
//                                                UNION ALL
//                                                SELECT crosses3.to_id
//                                                FROM parts
//                                                  INNER JOIN crosses AS crosses1 ON parts.id = crosses1.to_id
//                                                  INNER JOIN crosses AS crosses2 ON crosses1.from_id = crosses2.to_id
//                                                  INNER JOIN crosses AS crosses3 ON crosses2.from_id = crosses3.from_id
//                                                WHERE article = '".$article."'
//AND brand = '".$brand."'
//
//
//                                                UNION ALL
//                                                SELECT crosses3.to_id
//                                                FROM parts
//                                                  INNER JOIN crosses AS crosses1 ON parts.id = crosses1.to_id
//                                                  INNER JOIN crosses AS crosses2 ON crosses1.from_id = crosses2.from_id
//                                                  INNER JOIN crosses AS crosses3 ON crosses2.to_id = crosses3.from_id
//                                                WHERE article = '".$article."'
//AND brand = '".$brand."'
//
//
//                                                UNION ALL
//                                                SELECT crosses3.from_id
//                                                FROM parts
//                                                  INNER JOIN crosses AS crosses1 ON parts.id = crosses1.to_id
//                                                  INNER JOIN crosses AS crosses2 ON crosses1.from_id = crosses2.to_id
//                                                  INNER JOIN crosses AS crosses3 ON crosses2.from_id = crosses3.to_id
//                                                WHERE article = '".$article."'
//AND brand = '".$brand."'