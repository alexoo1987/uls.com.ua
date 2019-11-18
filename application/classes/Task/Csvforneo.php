<?php defined('SYSPATH') or die('No direct script access.');

class Task_Csvforneo extends Minion_Task
{

    protected function _execute(array $params)
    {
        echo date('Y-m-d H:i:s') . "_____BEGIN\n";

        $query = "SELECT SUM(amount) as summ, article, brand from orderitems
            WHERE supplier_id = 32
            GROUP BY article, brand
            ORDER BY summ DESC
            INTO OUTFILE '/var/lib/mysql-files/best-intercars.csv'
            FIELDS TERMINATED BY ','
            ENCLOSED BY '\"'
            LINES TERMINATED BY '\n';";
//        $query = 'LOAD DATA LOCAL INFILE "/tmp/own_manuf.csv"
//        INTO TABLE own_manufactures
//        FIELDS TERMINATED BY ","
//            ENCLOSED BY ""
//        LINES TERMINATED BY "\n" ';

//        $query = 'SELECT MFA_ID as tecdoc_manuf_id,
//        LOWER( REPLACE (regex_replace ( "[/(\sc\s{1})|([а-яА-Я\/]*)|\(.*\)/u]", "", MFA_BRAND ) , " ","-") ) AS url,
//        MFA_BRAND as brand
//        FROM MANUFACTURERS
//        ORDER BY MFA_BRAND
//        INTO OUTFILE "/tmp/own_manuf.csv"
//        FIELDS TERMINATED BY ","
//        OPTIONALLY ENCLOSED BY ""
//        LINES TERMINATED BY "\n"';

        //cat_td-to-parts
//        $query = 'SELECT DISTINCT LA_GA_ID as ga_tecdoc_id, parts.id AS part_id
//        FROM parts
//        INNER JOIN LINK_ART  ON tecdoc_id = LA_ART_ID
//        INTO OUTFILE "/tmp/cat_td-parts.csv"
//        FIELDS TERMINATED BY ","
//        OPTIONALLY ENCLOSED BY ""
//        LINES TERMINATED BY "\n"';


//        //cat_td-to-our_cat
//        $query = 'SELECT category_id, ga_tecdoc_id from category_to_tecdoc
//        INTO OUTFILE "/tmp/our_cat-to-cat_td.csv"
//        FIELDS TERMINATED BY ","
//        OPTIONALLY ENCLOSED BY ""
//        LINES TERMINATED BY "\n"';

        //    suppliers
//        $query = 'SELECT id, dont_show FROM suppliers
//        INTO OUTFILE "/tmp/suppliers.csv"
//        FIELDS TERMINATED BY ","
//        OPTIONALLY ENCLOSED BY ""
//        LINES TERMINATED BY "\n"';

////    currencies
//        $query = 'SELECT id, ratio, `code` FROM currencies
//        INTO OUTFILE "/tmp/currency.csv"
//        FIELDS TERMINATED BY ","
//        OPTIONALLY ENCLOSED BY ""
//        LINES TERMINATED BY "\n"';

        //      priceitem
//        $query = 'SELECT id, part_id,delivery,currency_id,price,supplier_id FROM priceitems
//        INTO OUTFILE "/tmp/priceitem.csv"
//        FIELDS TERMINATED BY ","
//        OPTIONALLY ENCLOSED BY ""
//        LINES TERMINATED BY "\n"';

////      model
//        $query = 'SELECT tecdoc_id, url FROM own_models WHERE active = 1
//        INTO OUTFILE "/tmp/model.csv"
//        FIELDS TERMINATED BY ","
//        OPTIONALLY ENCLOSED BY ""
//        LINES TERMINATED BY "\n"';


//        type
//        $query = 'SELECT tecdoc_id, tecdoc_models_id FROM own_types
//        INTO OUTFILE "/tmp/type.csv"
//        FIELDS TERMINATED BY ","
//        OPTIONALLY ENCLOSED BY ""
//        LINES TERMINATED BY "\n"';

        //part
//        $query = '
//        SELECT id, brand, article from parts
//        INTO OUTFILE "/tmp/part.csv"
//        FIELDS TERMINATED BY ","
//        OPTIONALLY ENCLOSED BY ""
//        LINES TERMINATED BY "\n"
//        ';

//          categories
//        $query = '
//        SELECT id, slug from categories WHERE `level`=2
//        INTO OUTFILE "/tmp/categories.csv"
//        FIELDS TERMINATED BY ","
//        OPTIONALLY ENCLOSED BY ""
//        LINES TERMINATED BY "\n"
//        ';

        //priceitems
//        $query = '
//        SELECT p.id, part_id, supplier_id, delivery, price, currency_id, c.ratio, c.`code`
//        FROM priceitems AS p
//        LEFT JOIN currencies AS c ON c.id = p.currency_id
//        INTO OUTFILE "/tmp/priceitems.csv"
//        FIELDS TERMINATED BY ","
//        OPTIONALLY ENCLOSED BY ""
//        LINES TERMINATED BY "\n"
//        ';

//        category-part
//        $query = '
//        SELECT DISTINCT category_id, parts.id AS part_id
//        FROM parts
//        LEFT JOIN LINK_ART  ON tecdoc_id = LA_ART_ID
//        INNER JOIN category_to_tecdoc ON LA_GA_ID = ga_tecdoc_id
//        INTO OUTFILE "/tmp/category-part.csv"
//        FIELDS TERMINATED BY ","
//        OPTIONALLY ENCLOSED BY ""
//        LINES TERMINATED BY "\n"
//        ';

//      MODELS-TYPES
//        $query = '
//        SELECT
//            TYP_MOD_ID AS model_id,
//            TYP_ID AS type_id
//        FROM
//            TYPES
//        INNER JOIN own_models ON TYP_MOD_ID = own_models.tecdoc_id
//        WHERE
//            own_models.active = 1
//        INTO OUTFILE "/tmp/types-models.csv"
//        FIELDS TERMINATED BY ","
//        OPTIONALLY ENCLOSED BY ""
//        LINES TERMINATED BY "\n"
//        ';



//        TYPES-PARTS
//        $query = '
//        SELECT LAT_TYP_ID as type_id, parts.id as part_id
//                FROM parts
//                LEFT JOIN LINK_ART  ON parts.tecdoc_id = LA_ART_ID
//                LEFT JOIN LINK_LA_TYP  ON LAT_LA_ID = LA_ID
//                LEFT JOIN TYPES  ON LAT_TYP_ID  = TYP_ID
//                WHERE parts.tecdoc_id IS NOT NULL
//                AND parts.tecdoc_id <> 0
//                AND LA_GA_ID IN (SELECT ga_tecdoc_id FROM category_to_tecdoc)
//                AND TYP_MOD_ID IN (SELECT tecdoc_id FROM own_models)
//        INTO OUTFILE "/tmp/types-parts.csv"
//        FIELDS TERMINATED BY ","
//        OPTIONALLY ENCLOSED BY ""
//        LINES TERMINATED BY "\n"
//        ';

        $all_categories = DB::query(null, $query)->execute('tecdoc'); // вытягиваем все категории

        exit();

//        header('Content-Type: text/csv; charset=windows-1251');
//
//        $file = fopen('/tmp/types-parts.csv', 'w');  /* записываем в файл */
//
//        fputcsv($file, ['type_id', 'part_id'], ",");
//
//        $i = 0;
//        while(true)
//        {
//            $query_all_category = "
//                SELECT LAT_TYP_ID as type_id, parts.id as part_id
//                FROM parts
//                LEFT JOIN LINK_ART  ON parts.tecdoc_id = LA_ART_ID
//                LEFT JOIN LINK_LA_TYP  ON LAT_LA_ID = LA_ID
//                LEFT JOIN TYPES  ON LAT_TYP_ID  = TYP_ID
//                WHERE parts.tecdoc_id IS NOT NULL
//                AND parts.tecdoc_id <> 0
//                AND LA_GA_ID IN (SELECT ga_tecdoc_id FROM category_to_tecdoc)
//                AND TYP_MOD_ID IN (SELECT tecdoc_id FROM own_models)
//                LIMIT {$i}, 10000
//            ";
//            $all_categories = DB::query(Database::SELECT, $query_all_category)->execute('tecdoc')->as_array(); // вытягиваем все категории
//
//            if(!$all_categories)
//                break;
//
//            foreach ($all_categories as $all_category)
//            {
//                fputcsv($file, [$all_category['type_id'], $all_category['part_id']], ",");
//            }
//
//            $i = $i + 10000;
//
//        }
//
//        fclose($file);


        echo date('Y-m-d H:i:s') . "_____END\n";
    }


}
