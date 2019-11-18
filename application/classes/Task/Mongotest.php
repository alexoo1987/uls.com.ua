<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: artem
 * Date: 12.12.16
 * Time: 23:26
 */
class Task_Mongotest extends Minion_Task
{

    public $step = 100000;

    public function _execute(array $params)
    {

        $mongo = new MongoClient(); // соединение
        $collection = $mongo->selectDB('eparts')->selectCollection('main');
        //количество
        $start = microtime(true);
        $result = $collection->count(['model_id' => 500]);
        var_dump(microtime(true) - $start);
        var_dump($result);exit();

        $mongo->close();
    }


    public function log($message)
    {
        fwrite(STDOUT,  date('Y-m-d H:i:s') . "___________" . $message . "\n");
    }
}
