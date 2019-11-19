<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Distribute costs between orders.
 * Run by cron
 * @throws Kohana_Exception
 */
class Task_Checkexpired extends Minion_Task
{
    protected function _execute(array $params)
    {
        echo date('Y-m-d H:i:s') . "_____BEGIN\n";

        $orderitems = ORM::factory('Orderitem')->where('state_id', 'IN', array(2, 32, 31, 6))->find_all()->as_array();

        $setting = ORM::factory('Setting')->where('code_name', '=', 'buyer_penalty')->find();

        foreach ($orderitems AS $orderitem) {
            $order_date = new DateTime($orderitem->date_time ? $orderitem->date_time : $orderitem->order->date_time);

            $delivery_days = $orderitem->delivery_days;


            if ($orderitem->supplier->order_to) {
                $order_to = str_replace('.', ':', $orderitem->supplier->order_to);
                if ($order_date->format('H:i') < date('H:i', strtotime($order_to))) {
                    $delivery_days--;
                }
            }

            $order_date
                ->modify('+' . $delivery_days . 'days')
                ->setTime(20,00);

            $now = new DateTime();

            if ($order_date->format('Y-m-d') == $now->format('Y-m-d') AND ($now > $order_date)) {

                $user = DB::select('user_id')->from('orderitems_log')->where('orderitem_id', '=', $orderitem->id)->and_where('state_id', 'IN', array(2, 32, 31, 6))->execute()->get('user_id', 0);

                $object = ORM::factory('User')->where('id', '=', $user)->find();
                if ($object->status)
                {
                    if ($object->status == 1) {
                        $penalty = ORM::factory('Penalty');

                        if ($user) {
                            $penalty->set('user_id', $user);
                        } else {
                            $penalty->set('role_id', 13);
                        }

                        $penalty
                            ->set('date', $now->format('Y-m-d H:i:s'))
                            ->set('amount', $setting->value)
                            ->set('order_id', $orderitem->order->id)
                            ->set('orderitem_id', $orderitem->id)
                            ->set('description', "Штраф. Позиция просроченна")
                            ->save();

                        echo date('Y-m-d H:i:s') . "_____Penalty " . $setting->value . " uah\n";
                    }
                }



            }
        }

        echo date('Y-m-d H:i:s') . "_____END\n";
    }
}