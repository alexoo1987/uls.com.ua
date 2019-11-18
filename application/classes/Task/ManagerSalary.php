<?php defined('SYSPATH') or die('No direct script access.');

/**
 * The penalty for being late
 * Run by cron
 * @throws Kohana_Exception
 */
class Task_ManagerSalary extends Minion_Task
{
    protected function _execute(array $params)
    {
        echo date('Y-m-d H:i:s') . "_____BEGIN\n";

        $managers = ORM::factory('User')->where('status', '=', 1)->find_all()->as_array();
        $finish_day = date('Y-m-8 23:59:59');
        $start_day = date('Y-m-9 00:00:00', strtotime($finish_day."-1 month"));

        $finish_day_penalty = date('Y-m-22 23:59:59');
        $start_day_penalty  = date('Y-m-23 00:00:00', strtotime($finish_day_penalty."-1 month"));

        $managerBalance = [];
        $adminBalance = []; // для подсчета ЗП Димы
        $adminBalance['circulation'] = 0;
        $adminBalance['purchase_per_unit'] = 0;
        $adminBalance['sale_per_unit'] = 0;
        $adminBalance['salary'] = 0;


        foreach ($managers as $manager)
        {
            $managerBalance[$manager->id]['manager'] = $manager;
            $circulation = 0;
            $orderitemsCirculation = ORM::factory('Orderitem')->with('order')->with('order:client')
                ->reset(FALSE)
                ->and_where('order.manager_id', '=', $manager->id)
                ->and_where('order.date_time', '<=', $finish_day)
                ->and_where('state_id', '=', 5)
                ->and_where('order.archive', '=', 0)
                ->and_where('orderitem.salary', '=', 0)
                ->find_all()
                ->as_array();

            foreach ($orderitemsCirculation as $orderitemCirculation)
                $circulation += $orderitemCirculation->sale_per_unit * $orderitemCirculation->amount;

            $managerBalance[$manager->id]['circulation'] = $circulation;

            $query = "SELECT 
				SUM(orderitems.sale_per_unit*orderitems.amount) as prodaj, sum(orderitems.purchase_per_unit*orderitems.amount) as zakup
				FROM orderitems
				INNER JOIN orders ON orderitems.order_id = orders.id
				INNER JOIN orderitems_log ON orderitems_log.orderitem_id = orderitems.id
				WHERE 
				orderitems.state_id = 5
				AND 
				orderitems_log.state_id = 5
				AND manager_id = " . $manager->id . "
				AND orders.date_time <= '" . $finish_day . "'
				AND orderitems.salary = 0
				AND orders.archive = 0
				GROUP BY orders.manager_id"; //AND orders.date_time >= '" . $start_day . "'
            $orderitemsGive = DB::query(Database::SELECT, $query)->execute()->current();

            $queryPenalty = "SELECT 
				SUM(amount) as penalty
				FROM penalty
				WHERE penalty.date >= '" . $start_day_penalty . "'
				AND penalty.date <= '" . $finish_day_penalty . "'
				AND user_id = " . $manager->id . "
				AND status = 0
				GROUP BY penalty.user_id";
            $managerPenalty = DB::query(Database::SELECT, $queryPenalty)->execute()->current();

            $queryUserDebth = "SELECT *
					FROM (SELECT SUM(amount * sale_per_unit) as buy_cash, client_id as ClientId, c.name, c.surname, c.middlename,  c.phone 
					FROM orderitems as oi
					INNER JOIN orders o ON o.id = oi.order_id
					INNER JOIN orderitems_log as ol ON ol.orderitem_id = oi.id
					INNER JOIN clients c ON o.client_id = c.id
					WHERE c.manager_id = " . $manager->id . "
					AND ol.date_time <= '".$finish_day."'
					AND ol.state_id = 5
					AND oi.state_id = 5
					GROUP BY o.client_id) as buy
					
					INNER JOIN (SELECT SUM(`value`) as pay_cash, client_id
					FROM client_payments as cp
					INNER JOIN clients c ON c.id = cp.client_id
					WHERE c.manager_id = " . $manager->id . "
					AND cp.date_time <=  '".$finish_day_penalty."'
					GROUP BY cp.client_id) as pay ON pay.client_id = buy.ClientId";
            $usersPenalty = DB::query(Database::SELECT, $queryUserDebth)->execute()->as_array();

            $queryIrrevocable = "SELECT 
				sum(orderitems.purchase_per_unit*orderitems.amount) as irrevocable_zakup
				FROM orderitems
				INNER JOIN orders ON orderitems.order_id = orders.id
				WHERE 
				orderitems.state_id IN (17, 18)
				AND manager_id = " . $manager->id . "
				AND orders.date_time <= '" . $finish_day . "'
				AND orders.archive = 0
				GROUP BY orders.manager_id"; //AND orders.date_time >= '" . $start_day . "'
            $orderitemsIrrevocable = DB::query(Database::SELECT, $queryIrrevocable)->execute()->current();


            $managerBalance[$manager->id]['prodaj'] = $orderitemsGive['prodaj'];
            $managerBalance[$manager->id]['zakup'] = $orderitemsGive['zakup'];
            $managerBalance[$manager->id]['percent'] = $manager->id == 3 ? 0.3 : ($manager->id == 163 ? 0.25 : $this->getPercent($circulation));
            $managerBalance[$manager->id]['penalty'] = $managerPenalty['penalty'];
            $managerBalance[$manager->id]['irrevocable'] = $orderitemsIrrevocable['irrevocable_zakup'];

            $totlalDebth = 0;
            foreach ($usersPenalty as $userPenalty) {
                if ($userPenalty['pay_cash'] - $userPenalty['buy_cash'] < 0)
                    $totlalDebth += $userPenalty['pay_cash'] - $userPenalty['buy_cash'];
            }
            $managerBalance[$manager->id]['debth'] = $totlalDebth;

            $adminBalance['circulation'] += $managerBalance[$manager->id]['circulation'];
            $adminBalance['purchase_per_unit'] += $orderitemsGive['zakup'];
            $adminBalance['sale_per_unit'] += $orderitemsGive['prodaj'];
            $adminBalance['salary'] += round((round($managerBalance[$manager->id]['prodaj'],2) - round($managerBalance[$manager->id]['zakup'],2)) * (1 - $managerBalance[$manager->id]['percent']),2);

            if($manager->id == 2)
                continue;


            $newPersonalCost = ORM::factory('ManagerSalary');
            $newPersonalCost->manager_id = $manager->id;
            $newPersonalCost->circulation = $managerBalance[$manager->id]['circulation'];
            $newPersonalCost->purchase_per_unit = $orderitemsGive['zakup'];
            $newPersonalCost->sale_per_unit = $orderitemsGive['prodaj'];
            $newPersonalCost->percent = $managerBalance[$manager->id]['percent'] * 100;
            $newPersonalCost->salary = round((round($managerBalance[$manager->id]['prodaj'],2) - round($managerBalance[$manager->id]['zakup'],2)) * $managerBalance[$manager->id]['percent'],2);
            $newPersonalCost->penalty = $managerPenalty['penalty'];
            $newPersonalCost->irrevocable = $managerBalance[$manager->id]['irrevocable'];
            $newPersonalCost->debt = $totlalDebth;
            $newPersonalCost->total = round((round($managerBalance[$manager->id]['prodaj'],2) - round($managerBalance[$manager->id]['zakup'],2)) * $managerBalance[$manager->id]['percent'],2) - round($managerBalance[$manager->id]['penalty'],2)  + round($managerBalance[$manager->id]['debth'],2);
            $newPersonalCost->month = date('m');
            $newPersonalCost->year = date('Y');
            $newPersonalCost->save();

        }

        $newPersonalCost = ORM::factory('ManagerSalary');
        $newPersonalCost->manager_id = 2;
        $newPersonalCost->circulation = $adminBalance['circulation'];
        $newPersonalCost->purchase_per_unit = $adminBalance['purchase_per_unit'];
        $newPersonalCost->sale_per_unit = $adminBalance['sale_per_unit'] ;
        $newPersonalCost->percent = 100;
        $newPersonalCost->salary = $adminBalance['salary'];
        $newPersonalCost->penalty = 0;
        $newPersonalCost->irrevocable = 0;
        $newPersonalCost->debt = 0;
        $newPersonalCost->total = $adminBalance['salary'];
        $newPersonalCost->month = date('m');
        $newPersonalCost->year = date('Y');
        $newPersonalCost->save();

        echo date('Y-m-d H:i:s') . "_____END\n";
    }

    public function getPercent($amount)
    {
        if($amount <= 125000)
            return 0.15;
        elseif ($amount >= 125000 && $amount < 150000)
            return 0.18;
        elseif ($amount >= 150000 && $amount < 200000)
            return 0.20;
        elseif ($amount >= 200000 && $amount < 250000)
            return 0.21;
        elseif ($amount >= 250000 && $amount < 275000)
            return 0.22;
        elseif ($amount >= 275000 && $amount < 300000)
            return 0.23;
        elseif ($amount >= 300000 && $amount < 325000)
            return 0.24;
        elseif ($amount >= 325000 && $amount < 350000)
            return 0.25;
        elseif ($amount >= 350000 && $amount < 375000)
            return 0.26;
        elseif ($amount >= 375000 && $amount < 400000)
            return 0.27;
        elseif ($amount >= 400000 && $amount < 425000)
            return 0.28;
        elseif ($amount >= 425000 && $amount < 450000)
            return 0.29;
        elseif ($amount >= 450000)
            return 0.30;
    }
}