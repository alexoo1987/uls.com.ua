<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Distribute costs between orders.
 * Run by cron
 * @throws Kohana_Exception
 */
class Task_Distributecosts extends Minion_Task
{
    protected function _execute(array $params)
    {
        $costs = DB::select(DB::expr('SUM(costs.amount) AS total'), 'costs.date')->from('costs')
            ->join('costs_type', 'LEFT')
            ->on('costs.type', '=', 'costs_type.id')
            ->where(DB::expr('DATE(created)'), '=', date('Y-m-d'))
            ->and_where('costs.amount', '>', 0)
            ->and_where('costs_type.subtract', '=', 1)
            ->group_by('costs.date')
            ->order_by('costs.date')
            ->execute()->as_array();

        if (empty($costs)) exit(date('Y-m-d H:i:s') . "_____no costs\n");

        foreach ($costs AS $key => $one) {
            //get orders by date
            $orders = ORM::factory('Order')->where(DB::expr('DATE(date_time)'), '=', $one['date'])->find_all()->as_array();
            if (!empty($orders)) {
                //calculate sum
                $sum = 0;
                foreach ($orders AS $order) {
                    foreach ($order->orderitems->find_all()->as_array() AS $item) {
                        if (!in_array($item->state_id, array(1, 4, 7, 11, 12, 13, 14, 15, 17, 35, 34)))
                            $sum += $item->purchase_per_unit;
                    }
                }
                //skip if no orders
                if ($sum == 0) continue;
                //distribute cost
                foreach ($orders AS $order) {
                    foreach ($order->orderitems->find_all()->as_array() AS $item) {
                        $percent = $item->purchase_per_unit / $sum;
                        if (!in_array($item->state_id, array(1, 4, 7, 11, 12, 13, 14, 15, 17, 35, 34))) {
                            $item->delivery_price += round($percent * $one['total'], 2);
                            $item->save();
                        }
                    }
                }
            }
        }
        exit(date('Y-m-d H:i:s') . "_____done\n");
    }
}