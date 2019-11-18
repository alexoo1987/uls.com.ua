<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Created by PhpStorm.
 * User: artem
 * Date: 12.12.16
 * Time: 23:26
 */

class Task_Botforcategory extends Minion_Task
{
    protected function _execute(array $params)
    {
        echo date('Y-m-d H:i:s') . "_____BEGIN cashe \n";

        $ch = curl_init('https://ulc.com.ua/katalog/tormoznye-kolodki-613');
        curl_exec($ch);
        curl_close($ch);

        $ch = curl_init('https://ulc.com.ua/katalog/tormoznye-diski-614');
        curl_exec($ch);
        curl_close($ch);

        $ch = curl_init('https://ulc.com.ua/katalog/tormoznye-kolodki-602');
        curl_exec($ch);
        curl_close($ch);

        $ch = curl_init('https://ulc.com.ua/katalog/tormoznye-diski-603');
        curl_exec($ch);
        curl_close($ch);

        echo date('Y-m-d H:i:s') . "_____END\n";
    }
}