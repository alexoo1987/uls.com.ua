<?php defined('SYSPATH') or die('No direct script access.');

class Task_NovaPoshta extends Minion_Task {

    protected function _execute(array $params)
    {
        $query ="SELECT np_cities.id, np_cities.`name` as city_name,  np_cities.`ref` as city_ref FROM np_cities";

        $results = DB::query(Database::SELECT,$query)->execute('tecdoc_new')->as_array();

        foreach ($results as $result)
        {
            $this->get_address($result['city_ref'], $result['id']);
            sleep(5);
        }
    }

    protected function get_city($name)
    {
        $np = new LisDev\Delivery\NovaPoshtaApi2(
            '6a8ca3163492bb644bc33dde1265f6cd',
            'ua',
            FALSE,
            'curl'
        );

        $cities = $np->getCity(0, $name);
        return $cities['data'][0];
    }
    protected function get_address($cityRef, $cityId)
    {
        $np = new LisDev\Delivery\NovaPoshtaApi2(
            '6a8ca3163492bb644bc33dde1265f6cd',
            'ua',
            FALSE,
            'curl'
        );

        $results = $np
            ->model('Address')
            ->method('getStreet')
            ->params([
                "CityRef" => $cityRef,
            ])
            ->execute();

        foreach ($results['data'] as $result) {
            $create_city = ORM::factory('NpAddress');
            $create_city
                ->set('city_id', $cityId)
                ->set('name', $result['Description'])
                ->set('ref', $result['Ref'])
                ->save();
        }

        return true;
    }

    protected function get_warehouse($nameCity, $nameArea)
    {
        $np = new LisDev\Delivery\NovaPoshtaApi2(
            '6a8ca3163492bb644bc33dde1265f6cd',
            'ua',
            FALSE,
            'curl'
        );

        $warehouses = $np->getCity($nameCity, $nameArea);

        if(isset($warehouses['data'][0][0]['Ref']))
        {
            $result_warehouse = $np->getWarehouses($warehouses['data'][0][0]['Ref']);
        }
        else{
            $result_warehouse = $np->getWarehouses($warehouses['data'][0]['Ref']);

        }

        return $result_warehouse['data'];
    }
}

?>
