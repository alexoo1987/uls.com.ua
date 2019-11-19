<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Created by PhpStorm.
 * User: artem
 * Date: 12.12.16
 * Time: 23:26
 */

class Task_Urlsforowntypes extends Minion_Task // забивает url для own_types
{
    protected function _execute(array $params)
    {
        echo date('Y-m-d H:i:s') . "_____BEGIN cashe \n";

        $query = "
			SELECT TYP_ID AS id, TEX_TEXT AS name, TYP_MOD_ID AS model_id
            FROM TYPES
            LEFT JOIN COUNTRY_DESIGNATIONS ON TYP_CDS_ID = CDS_ID
            LEFT JOIN DES_TEXTS ON CDS_TEX_ID = TEX_ID
            WHERE CDS_LNG_ID = 16
		";
        $result = DB::query(Database::SELECT,$query)->execute('tecdoc')->as_array();
        foreach ($result as $value)
        {
            $short_name = trim($value['name']);
            $short_name = preg_replace('/(\sc\s{1})|([а-яА-Я\/]*)|\(.*\)/u', '',$short_name);
            $short_name = trim($short_name);
            $url = Article::get_short_article($short_name);
            $url = mb_strtolower($url);
            $url = $url."-".$value['id'];
            echo $url."\n";
            DB::insert('own_types', array('tecdoc_id', 'tecdoc_models_id', 'url'))
                ->values(array($value['id'], $value['model_id'], $url))->execute();
        }

        echo date('Y-m-d H:i:s') . "_____END\n";
    }
}