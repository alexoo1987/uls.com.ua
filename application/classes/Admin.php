<?php
/**
 * Created by PhpStorm.
 * User: artem
 * Date: 04.10.17
 * Time: 12:22
 */

defined('SYSPATH') or die('No direct script access.');

class Admin {
    public static function check_ready_order($order_id)
    {
        $order_items_in_order = ORM::factory('Orderitem')->where('order_id', '=', $order_id)->find_all()->as_array();
        $count_ready = 0;
        $un_work_number = 0;
        $count_packaging = 0;
        foreach ($order_items_in_order AS $order_items)
        {

            if(in_array($order_items->state_id, [1,4,5,13,14,15,18,35,39,41]))
            {
                $un_work_number ++;
            }

            if($order_items->state_id == 3)
            {
                $count_ready ++;
            }

            if($order_items->state_id == 37)
            {
                $count_packaging ++;
            }
        }

        $order_change = ORM::factory('Order')->where('id', '=', $order_id)->find();
        if($count_ready > 0 AND $un_work_number != count($order_items_in_order) AND $count_ready + $un_work_number == count($order_items_in_order))
        {
            if($order_change->delivery_method_id == 3){
                $order_change->ready_order = 1;
            }
            else{
                $order_change->ready_order = 2;
                if($order_change->delivery_method_id == 1)
                    self::sendSMSReady($order_change->client->phone, $order_change->id, 1);
                elseif($order_change->delivery_method_id == 2)
                    self::sendSMSReady($order_change->client->phone, $order_change->id, 2);

            }

        }
        elseif($count_packaging > 0 AND $un_work_number != count($order_items_in_order) AND $count_packaging + $un_work_number == count($order_items_in_order))
        {
            $order_change->ready_order = 2;
            if($order_change->delivery_method_id == 1)
                self::sendSMSReady($order_change->client->phone, $order_change->id, 1);
            elseif($order_change->delivery_method_id == 2)
                self::sendSMSReady($order_change->client->phone, $order_change->id, 2);
        }
        elseif($count_packaging > 0 AND $un_work_number != count($order_items_in_order) AND $count_packaging + $count_ready + $un_work_number == count($order_items_in_order))
        {
            $order_change->ready_order = 1;
        }
        else
        {
            $order_change->ready_order = 0;
        }

        $order_change->save();
    }

    public static function sendSMSReady($phone, $orderNumber, $deliveryMethod)
    {
        $subText = $deliveryMethod == 1 ? "бул. Вацлава Гавела 18" : "ул. Соборная 28";
        $text = "Заказ №".$orderNumber." собран и ждет по адресу ".$subText." ulc.com.ua" ;
        $subject = 'ULC';

        Sms::send($text, $subject, $phone);
        return true;
    }
}