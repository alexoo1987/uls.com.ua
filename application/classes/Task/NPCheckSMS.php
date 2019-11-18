<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Created by PhpStorm.
 * User: artem
 * Date: 12.12.16
 * Time: 23:26
 */

class Task_NPCheckSMS extends Minion_Task
{
    protected function _execute(array $params)
    {
        echo date('Y-m-d H:i:s') . "_____BEGIN Synchronization\n";

//        echo date('Y-m-d', strtotime('-3 days')); exit();

        $allTtnResult = $this->returnTtns();

        $NPResult = $this->checkTtns($allTtnResult);

        $this->sendSMS($allTtnResult, $NPResult);

        echo date('Y-m-d H:i:s') . "_____END\n";

    }


    protected function returnTtns ()
    {
        $maxDateCreated = date('Y-m-d 00:00:00', strtotime('-15 days'));

        $allTtnQuery = "SELECT np_ttns.ttn, c.phone FROM np_ttns
            INNER JOIN orderitems oi ON oi.id = np_ttns.orderitem_id
            INNER JOIN orders o ON o.id = oi.order_id
            INNER JOIN clients c ON c.id = o.client_id
            WHERE time >= '".$maxDateCreated."'
            GROUP BY ttn";

        $allTtnResult = DB::query(Database::SELECT,$allTtnQuery)->execute('tecdoc_new')->as_array();

        return $allTtnResult;
    }
    
    
    protected function checkTtns (array $ttns)
    {
        $np = new LisDev\Delivery\NovaPoshtaApi2(
            '6a8ca3163492bb644bc33dde1265f6cd',
            'ru',
            FALSE,
            'curl'
        );

        $query = [];

        foreach ($ttns as $ttn)
        {
            $phone = str_replace('+', '',str_replace('(', '', str_replace(')', '', str_replace('-', '', $ttn['phone']))));
            $query[] = [
                "DocumentNumber" => $ttn['ttn'],
                "Phone" => $phone
            ];
        }

        $allResult = [];

        if(count($query > 100))
        {
            for($i = 0; $i < count($query); $i += 50)
            {
                $result = $np
                    ->model('TrackingDocument')
                    ->method('getStatusDocuments')
                    ->params([
                        'Documents' => array_slice($query, $i, 50),
                    ])
                    ->execute();

                $allResult = array_merge($allResult, $result['data']);
            }
        }

        return $allResult;
    }


    protected function sendSMS ($arrayPhones, $arrayResults)
    {
        foreach ($arrayResults as $key => $arrayResult)
        {
            if(in_array($arrayResult['StatusCode'],  [7,8]) &&  $arrayResult['ScheduledDeliveryDate'] == date('d-m-Y', strtotime('-3 days')))
            {
//                if($arrayPhones[$key]['ttn'] == $arrayResult['Number'] && $arrayPhones[$key]['ttn'] == '20400102121791')
//                {
//                    if($arrayResult['ScheduledDeliveryDate'] == date('d-m-Y', strtotime('-3 days')))
//                    {
//                        echo "yes";
//                    }
                    $text = 'Ваш заказ 3-й день на Новой почте. На 5-й день возвращается обратно';
                    $phone = $arrayPhones[$key]['phone'];
                    $subject = 'Eparts';

                    Sms::send($text, $subject, $phone);
//                }
            }
        }

        return true;
    }
}