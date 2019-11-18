<?php defined('SYSPATH') or die('No direct script access.');

class Task_Addpartsfromtd extends Minion_Task
{

    protected function _execute(array $params)
    {
        echo date('Y-m-d H:i:s') . "_____BEGIN\n";

        $query_parts = "INSERT INTO parts (tecdoc_id, article_long, brand_long, article, brand, name)
            SELECT
                ART_ID AS tecdoc_id,
                ART_ARTICLE_NR AS article_long,
                SUP_BRAND AS brand_long,
                LOWER(
                    regex_replace (
                        '[^0-9a-zA-Z]',
                        '',
                        ART_ARTICLE_NR
                    )
                ) AS article,
                LOWER(
                    regex_replace (
                        '[^0-9a-zA-Z]',
                        '',
                        SUP_BRAND
                    )
                ) AS brand,
            TEX_TEXT AS name
            FROM
                ARTICLES
            LEFT JOIN SUPPLIERS ON ART_SUP_ID = SUP_ID
            LEFT JOIN DESIGNATIONS ON ART_DES_ID = DES_ID
            LEFT JOIN DES_TEXTS ON DES_TEX_ID = TEX_ID
            WHERE
                DES_LNG_ID = 16
            AND ART_ID NOT IN (SELECT tecdoc_id FROM parts WHERE tecdoc_id IS NOT NULL)
            ";

        $tecdoc_art_ids = DB::query(Database::INSERT,$query_parts)->execute('tecdoc');

        echo date('Y-m-d H:i:s') . "_____END\n";
    }


}
