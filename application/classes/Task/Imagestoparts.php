<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Created by PhpStorm.
 * User: artem
 * Date: 12.12.16
 * Time: 23:26
 */

class Task_Imagestoparts extends Minion_Task
{
    protected function _execute(array $params)
    {
        echo date('Y-m-d H:i:s') . "_____BEGIN cashe \n";

        $items = ORM::factory('Part')->where('images', '=', NULL)->and_where('tecdoc_id', 'IS NOT', NULL)->and_where('tecdoc_id', '<>', '0')->find_all()->as_array();

        foreach ($items as $item=>$key)
        {
            $id = $key->tecdoc_id;
            if ($id) {
                $query = "SELECT image FROM tof_graphics 
                WHERE article_id = \"{$id}\" ";
                $result = DB::query(Database::SELECT,$query)->execute(/*'tecdoc'*/)->as_array();
                if(!empty($result))
                {
                    $key->images = $result[0];
                    $key->save();
                }
                else
                {
                    continue;
                }
            }
        }

        echo date('Y-m-d H:i:s') . "_____END\n";
    }
}