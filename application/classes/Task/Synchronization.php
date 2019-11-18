<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Created by PhpStorm.
 * User: artem
 * Date: 12.12.16
 * Time: 23:26
 */

class Task_Synchronization extends Minion_Task
{
    protected function _execute(array $params)
    {
        echo date('Y-m-d H:i:s') . "_____BEGIN Synchronization\n";

        $order_tm = ORM::factory('OrderEpartsTm')->where('status','=', 1)->find_all()->as_array();

        $order_tm_array = array();
        $INFO = Tminfo::instance();
        $INFO->SetLogin('Mir@eparts.kiev.ua');
        $INFO->SetPasswd('9506678d');
        $array_states['tm_states'] =  array(1, 2, 3,4,5,6, 7,8,9,10,11,12,13,14,15,16,17,18,19,20,21);
        $array_states['our_states'] = array(34,7,34,2,2,6,31,6,6, 6, 6,32,34,34,15,15,14,35,15,15,15);

        foreach ($order_tm as $key=>$value)
        {
            echo $value->order_tm_id."\n";
            $tm_create_order = $INFO->GetOrderPositions($value->order_tm_id);
            $order_tm_array[$value->order_id] = $tm_create_order;
        }

        foreach ($order_tm_array as $key=>$value) {
            foreach ($value as $item=>$var) {
                $position = ORM::factory('Orderitem')->where('id','=', $key)->and_where('supplier_id','=',38)->find();
                echo $position->id."<br>";
                for ($i =0; $i<count($array_states['tm_states']); $i++)
                {
                    if($var['StateId']==$array_states['tm_states'][$i])
                    {
                        if($position->state_id != $array_states['our_states'][$i])
                        {
                            if($position->state_id == 18)
                            {
                                if($array_states['tm_states'][$i]!=12)
                                {
                                    $order_tm_position = ORM::factory('OrderEpartsTm')->where('order_id','=', $position->id)->find();
                                    $order_tm_position->status = 0;
                                    $order_tm_position ->save();

                                    $position->state_id = $array_states['our_states'][$i];
                                    $position ->save();
                                    $log = ORM::factory('OrderitemLog');

                                    $log
                                        ->set('orderitem_id', $position->id)
                                        ->set('state_id', $array_states['our_states'][$i])
                                        ->set('tehnomir', 1)
                                        ->set('date_time', date('Y-m-d H:i:s'))
                                        ->set('user_id', 94 ) //Auth::instance()->get_user()->id
                                        ->save();
                                    break;
                                }
                                else
                                {
                                    continue;
                                }
                            }
                            else
                            {
                                if($array_states['tm_states'][$i] == 12 OR $array_states['tm_states'][$i] == 21 OR $array_states['tm_states'][$i] == 20 OR $array_states['tm_states'][$i] == 16 OR $array_states['tm_states'][$i] == 15 )
                                {
                                    $order_tm_position = ORM::factory('OrderEpartsTm')->where('order_id','=', $position->id)->find();
                                    $order_tm_position->status = 0;
                                    $order_tm_position ->save();

                                    $position->state_id = $array_states['our_states'][$i];
                                    $position ->save();
                                }
                                else
                                {
                                    $position->state_id = $array_states['our_states'][$i];
                                    $position ->save();
                                }


                                $log = ORM::factory('OrderitemLog');

                                $log
                                    ->set('orderitem_id', $position->id)
                                    ->set('state_id', $array_states['our_states'][$i])
                                    ->set('tehnomir', 1)
                                    ->set('date_time', date('Y-m-d H:i:s'))
                                    ->set('user_id', 94)
                                    ->save();
                                break;
                            }

                        }
                    }
                }
            }
        }
        echo date('Y-m-d H:i:s') . "_____END\n";

    }
}