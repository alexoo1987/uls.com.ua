<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Created by PhpStorm.
 * User: artem
 * Date: 12.12.16
 * Time: 23:26
 */

class Task_TopProducts extends Minion_Task
{
    protected function _execute(array $params)
    {
        echo date('Y-m-d H:i:s') . "_____BEGIN TOP\n";

        $categoties = ORM::factory('Category')->where('level', '=', 2)->find_all(); //->and_where('id', '=', 888)

        foreach ($categoties as $category)
        {
            echo $category->id."<\n>";
//            $topProductQuery = "
//                SELECT count(p.id) as count, p.id as part FROM parts p
//                INNER JOIN orderitems oi ON oi.article = p.article AND oi.brand = p.brand
//                INNER JOIN group_parts gp ON gp.part_id = p.id
//                INNER JOIN type_category_group tcg ON tcg.id = gp.group_id
//                WHERE tcg.category_id = ".$category->id."
//                GROUP BY p.id
//                ORDER BY count DESC
//                LIMIT 50";
            $topProductQuery = "SELECT count(parts.id) as count, parts.id
                FROM parts
                INNER JOIN orderitems oi ON oi.article = parts.article AND oi.brand = parts.brand
                INNER JOIN group_parts gp ON gp.part_id = parts.id
                INNER JOIN type_category_group tcg ON tcg.id = gp.group_id
                WHERE tcg.category_id = ".$category->id." 
                AND EXISTS(
                             SELECT 1 FROM priceitems 
                             INNER JOIN suppliers ON suppliers.id = priceitems.supplier_id
                             WHERE suppliers.dont_show = 0
                             AND priceitems.part_id = parts.id
                             LIMIT 1
                    )
                GROUP BY parts.id
                ORDER BY count DESC
                LIMIT 15";

            $topProducts = DB::query(Database::SELECT,$topProductQuery)->execute('tecdoc_new')->as_array();

            $count = count($topProducts);
            echo $count."<\n>";

            if($count < 1)
            {
                echo "One <\n>";
                $topProductQuery = "SELECT count(parts.id) as count, parts.id  
                FROM parts
                INNER JOIN group_parts gp ON gp.part_id = parts.id
                INNER JOIN type_category_group tcg ON tcg.id = gp.group_id
                WHERE tcg.category_id = ".$category->id." 
                AND EXISTS(
                             SELECT 1 FROM priceitems 
                             INNER JOIN suppliers ON suppliers.id = priceitems.supplier_id
                             WHERE suppliers.dont_show = 0
                             AND priceitems.part_id = parts.id
                             LIMIT 1
                    )
                GROUP BY parts.id
                ORDER BY count DESC
                LIMIT 15";
                $topProducts = DB::query(Database::SELECT,$topProductQuery)->execute('tecdoc_new')->as_array();

                if(empty(count($topProducts)) || !is_array($topProducts))
                    continue;

                foreach ($topProducts as $topProduct)
                {
                    $newTop = ORM::factory('TopProductsCategory');
                    $newTop->part_id = $topProduct['id'];
                    $newTop->category_id = $category->id;
                    $newTop->save();
                }
            }
            elseif ($count < 12)
            {
                $topProductSecondQuery = "SELECT count(parts.id) as count, parts.id  
                FROM parts
                INNER JOIN group_parts gp ON gp.part_id = parts.id
                INNER JOIN type_category_group tcg ON tcg.id = gp.group_id
                WHERE tcg.category_id = ".$category->id." 
                AND EXISTS(
                             SELECT 1 FROM priceitems 
                             INNER JOIN suppliers ON suppliers.id = priceitems.supplier_id
                             WHERE suppliers.dont_show = 0
                             AND priceitems.part_id = parts.id
                             LIMIT 1
                    )
                GROUP BY parts.id
                ORDER BY count DESC
                LIMIT 15";

                $topProductsSecond = DB::query(Database::SELECT,$topProductSecondQuery)->execute('tecdoc_new')->as_array();

                foreach ($topProducts as $topProduct)
                {
                    $newTop = ORM::factory('TopProductsCategory');
                    $newTop->part_id = $topProduct['id'];
                    $newTop->category_id = $category->id;
                    $newTop->save();
                }

                if(empty(count($topProductsSecond)))
                    continue;

                foreach ($topProductsSecond as $topProduct)
                {
                    $newTop = ORM::factory('TopProductsCategory');
                    $newTop->part_id = $topProduct['id'];
                    $newTop->category_id = $category->id;
                    $newTop->save();
                }

            }
            else{
                foreach ($topProducts as $topProduct)
                {
                    $newTop = ORM::factory('TopProductsCategory');
                    $newTop->part_id = $topProduct['id'];
                    $newTop->category_id = $category->id;
                    $newTop->save();
                }
            }

        }

        echo date('Y-m-d H:i:s') . "_____END\n";

    }
}

