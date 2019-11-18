<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Created by PhpStorm.
 * User: artem
 * Date: 12.12.16
 * Time: 23:26
 */



class Task_Changesupp extends Minion_Task
{
    protected function _execute(array $params)
    {
        echo date('Y-m-d H:i:s') . "_____BEGIN cashe \n";

//        $cross = ORM::factory('Cross')->where('to_brand','=', 'kia')->find_all()->as_array();
//        foreach ($cross as $cros=>$key)
//        {
//            if($key->to_brand == 'kia')
//            {
//                $key->to_brand = 'hyundaikia';
//                $key->save();
//            }
//        }

        $articles_count = Model::factory('Priceitem')->where('supplier_id', '=', 143)->count_all();
        for ($i = 0; $i < $articles_count; $i += 5000) {
            $articles = Model::factory('Priceitem')->where('supplier_id', '=', 143)->limit(5000)->offset($i)->find_all()->as_array();
            //var_dump($articles);
            foreach ($articles as $user_penalty=> $row_penalty)
            {
                $row_penalty->delivery = 8;
                //echo "<br>";
                $row_penalty->save();
//                echo $row_penalty->salary;
            }
        }
        echo date('Y-m-d H:i:s') . "_____END\n";
        $articles_count = Model::factory('Priceitem')->where('supplier_id', '=', 146)->count_all();
        for ($i = 0; $i < $articles_count; $i += 5000) {
            $articles = Model::factory('Priceitem')->where('supplier_id', '=', 146)->limit(5000)->offset($i)->find_all()->as_array();
            //var_dump($articles);
            foreach ($articles as $user_penalty=> $row_penalty)
            {
                $row_penalty->delivery = 8;
                //echo "<br>";
                $row_penalty->save();
//                echo $row_penalty->salary;
            }
        }
        echo date('Y-m-d H:i:s') . "_____END\n";
        $articles_count = Model::factory('Priceitem')->where('supplier_id', '=', 153)->count_all();
        for ($i = 0; $i < $articles_count; $i += 5000) {
            $articles = Model::factory('Priceitem')->where('supplier_id', '=', 153)->limit(5000)->offset($i)->find_all()->as_array();
            //var_dump($articles);
            foreach ($articles as $user_penalty=> $row_penalty)
            {
                $row_penalty->delivery = 8;
                //echo "<br>";
                $row_penalty->save();
//                echo $row_penalty->salary;
            }
        }
        echo date('Y-m-d H:i:s') . "_____END\n";
        $articles_count = Model::factory('Priceitem')->where('supplier_id', '=', 154)->count_all();
        for ($i = 0; $i < $articles_count; $i += 5000) {
            $articles = Model::factory('Priceitem')->where('supplier_id', '=', 154)->limit(5000)->offset($i)->find_all()->as_array();
            //var_dump($articles);
            foreach ($articles as $user_penalty=> $row_penalty)
            {
                $row_penalty->delivery = 8;
                //echo "<br>";
                $row_penalty->save();
//                echo $row_penalty->salary;
            }
        }
        echo date('Y-m-d H:i:s') . "_____END\n";
        $articles_count = Model::factory('Priceitem')->where('supplier_id', '=', 155)->count_all();
        for ($i = 0; $i < $articles_count; $i += 5000) {
            $articles = Model::factory('Priceitem')->where('supplier_id', '=', 155)->limit(5000)->offset($i)->find_all()->as_array();
            //var_dump($articles);
            foreach ($articles as $user_penalty=> $row_penalty)
            {
                $row_penalty->delivery = 8;
                //echo "<br>";
                $row_penalty->save();
//                echo $row_penalty->salary;
            }
        }
        echo date('Y-m-d H:i:s') . "_____END\n";
        $articles_count = Model::factory('Priceitem')->where('supplier_id', '=', 156)->count_all();
        for ($i = 0; $i < $articles_count; $i += 5000) {
            $articles = Model::factory('Priceitem')->where('supplier_id', '=', 156)->limit(5000)->offset($i)->find_all()->as_array();
            //var_dump($articles);
            foreach ($articles as $user_penalty=> $row_penalty)
            {
                $row_penalty->delivery = 8;
                //echo "<br>";
                $row_penalty->save();
//                echo $row_penalty->salary;
            }
        }
        echo date('Y-m-d H:i:s') . "_____END\n";
        $articles_count = Model::factory('Priceitem')->where('supplier_id', '=', 157)->count_all();
        for ($i = 0; $i < $articles_count; $i += 5000) {
            $articles = Model::factory('Priceitem')->where('supplier_id', '=', 157)->limit(5000)->offset($i)->find_all()->as_array();
            //var_dump($articles);
            foreach ($articles as $user_penalty=> $row_penalty)
            {
                $row_penalty->delivery = 8;
                //echo "<br>";
                $row_penalty->save();
//                echo $row_penalty->salary;
            }
        }
        echo date('Y-m-d H:i:s') . "_____END\n";
        $articles_count = Model::factory('Priceitem')->where('supplier_id', '=', 158)->count_all();
        for ($i = 0; $i < $articles_count; $i += 5000) {
            $articles = Model::factory('Priceitem')->where('supplier_id', '=', 158)->limit(5000)->offset($i)->find_all()->as_array();
            //var_dump($articles);
            foreach ($articles as $user_penalty=> $row_penalty)
            {
                $row_penalty->delivery = 8;
                //echo "<br>";
                $row_penalty->save();
//                echo $row_penalty->salary;
            }
        }
    }
}
