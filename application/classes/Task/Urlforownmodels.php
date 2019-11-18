<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Created by PhpStorm.
 * User: artem
 * Date: 12.12.16
 * Time: 23:26
 */

class Task_Urlforownmodels extends Minion_Task //Забивает url в own_models и откидает все < 1980 года, own_manufacture забивал вручную
{
    protected function _execute(array $params)
    {
        echo date('Y-m-d H:i:s') . "_____BEGIN cashe \n";

        $query = "
			SELECT	MOD_ID,	MOD_MFA_ID,	TEX_TEXT AS MOD_CDS_TEXT, MANUFACTURERS.MFA_BRAND, MODELS.MOD_PCON_END as END_DATA
            FROM MODELS
            INNER JOIN MANUFACTURERS ON MANUFACTURERS.MFA_ID = MODELS.MOD_MFA_ID
            INNER JOIN COUNTRY_DESIGNATIONS ON CDS_ID = MOD_CDS_ID
            INNER JOIN DES_TEXTS ON TEX_ID = CDS_TEX_ID
            WHERE	MOD_MFA_ID = MANUFACTURERS.MFA_ID  AND	CDS_LNG_ID = 16
            ORDER BY MFA_BRAND
		";
        $result = DB::query(Database::SELECT,$query)->execute('tecdoc')->as_array();
        foreach ($result as $value)
        {
            if($value['END_DATA'] < 198000 AND $value['END_DATA'] != NULL)
            {
                $short_name = trim($value['MOD_CDS_TEXT']);
                $short_name = preg_replace('/(\sc\s{1})|([а-яА-Я\/]*)|\(.*\)/u', '',$short_name);
                $short_name = trim($short_name);
                $url = Article::get_short_article($short_name);
                DB::insert('own_models', array('tecdoc_id', 'short_name', 'url', 'active', 'tecdoc_manufacture_id'))
                    ->values(array($value['MOD_ID'], $short_name, $url, 0, $value['MOD_MFA_ID']))->execute();

            }
            else
            {
                $short_name = trim($value['MOD_CDS_TEXT']);
                $short_name = preg_replace('/(\sc\s{1})|([а-яА-Я\/]*)|\(.*\)/u', '',$short_name);
                $short_name = trim($short_name);
                $url = Article::get_short_article($short_name);
                DB::insert('own_models', array('tecdoc_id', 'short_name', 'url', 'active', 'tecdoc_manufacture_id'))
                    ->values(array($value['MOD_ID'], $short_name, $url, 1, $value['MOD_MFA_ID']))->execute();
            }

        }

        echo date('Y-m-d H:i:s') . "_____END\n";
    }
}