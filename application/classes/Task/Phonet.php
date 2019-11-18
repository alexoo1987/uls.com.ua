<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Distribute costs between orders.
 * Run by cron
 * @throws Kohana_Exception
 */
class Task_Phonet extends Minion_Task
{
    protected function _execute(array $params)
    {
        echo date('Y-m-d H:i:s') . "_____BEGIN\n";

        // 3 month is max
        $stepTime = 90 * 24 * 3600 * 1000;

        $startTime = strtotime("01.12.2017") * 1000;
        $endTime = $startTime + $stepTime;

        $phonet = Libs_Phonet::getInstance($startTime, $endTime, 0, 50);

        while ($endTime < ((time() * 1000) + $stepTime + 1000)) {
            $step = 1;
            while ($calls = $phonet->getCallsCompany()) {
                foreach ($calls as $call) {
                    $model = ORM::factory('PhonetCompanyCalls');
                    $model->values([
                        'parent_uuid'    => $call->parentUuid,
                        'uuid​'           => $call->uuid,
                        'end_at'         => $call->endAt/1000,
                        'lg_direction'   => $call->lgDirection,
                        'leg_id'         =>
                            !is_null($call->leg) && !is_null($call->leg->id)
                                ? $call->leg->id : NULL,
                        'leg_second_id'  =>
                            !is_null($call->leg2) && !is_null($call->leg2->id)
                                ? $call->leg2->id : NULL,
                        'other_leg_num'  => $call->otherLegNum,
                        'other_leg_name' => mb_substr($call->otherLegName, 0, 50),
                        'disposition'    => $call->disposition,
                        'trunk'          => $call->trunk,
                        'bill_secs'      => $call->billSecs,
                        'duration'       => $call->duration,
                        'audio_rec_url'  => $call->audioRecUrl,
                        'transfer_history​' => !is_null($call->transferHistory) ? $call->transferHistory : null,
                    ]);
                    $model->save();
                }
                $phonet->setPage(++$step);
            }
            $phonet->setStartTime($startTime = $endTime + 1);
            $phonet->setEndTime($endTime = $endTime + $stepTime);
            $phonet->setPage(1);
        }

        echo date('Y-m-d H:i:s') . "_____END\n";
    }
}