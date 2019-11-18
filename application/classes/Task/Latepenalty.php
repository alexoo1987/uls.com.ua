<?php defined('SYSPATH') or die('No direct script access.');

/**
 * The penalty for being late
 * Run by cron
 * @throws Kohana_Exception
 */
class Task_Latepenalty extends Minion_Task
{
    protected function _execute(array $params)
    {
        $run_time = ORM::factory('Setting')->where('code_name', '=', 'late_penalty_time')->find()->value;

        $now = new DateTime();

        if ($run_time != $now->format('H:i')) exit();

        echo date('Y-m-d H:i:s') . "_____BEGIN\n";

        $office_ip = ORM::factory('Setting')->where('code_name', '=', 'office_ip')->find()->value;
        $amount = ORM::factory('Setting')->where('code_name', '=', 'late_penalty')->find()->value;

        $office_ips = explode(",", $office_ip);

        foreach(ORM::factory('User')->where('status','=','1')->find_all()->as_array() as $user) {
            if (!ORM::factory('Permission')->checkPermissionByUser($user->id, 'late_penalty')) continue;

            $last_activity = new DateTime($user->last_activity);

            if ($last_activity->format('Y-m-d') != $now->format('Y-m-d') OR !in_array($user->last_ip, $office_ips)) { //$user->last_ip != $office_ip

                echo "Penalty to $user->surname\n";

                $penalty = ORM::factory('Penalty');
//                if($user->id == 59)
//                {
//                    $penalty
//                        ->set('user_id', $user->id)
//                        ->set('date', $now->format('Y-m-d H:i:s'))
//                        ->set('amount', 100)
//                        ->set('description', "Штраф за опоздание")
//                        ->save();
//                }
//                else
//                {
                    $penalty
                        ->set('user_id', $user->id)
                        ->set('date', $now->format('Y-m-d H:i:s'))
                        ->set('amount', $amount)
                        ->set('description', "Штраф за опоздание")
                        ->save();
//                }

            }
        }

        echo date('Y-m-d H:i:s') . "_____END\n";
    }
}