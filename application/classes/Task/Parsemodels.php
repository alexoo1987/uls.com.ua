<?php defined('SYSPATH') or die('No direct script access.');
class Task_Parsemodels extends Minion_Task {
    protected function _execute(array $params)
    {
        $page = Htmlparser::htmlToXml(Htmlparser::http("http://ukrparts.com.ua/"));

        $models = $page->xpath("//div[contains(@class, 'models-list')]/*/div[contains(@class,'model-brand') or contains(@class ,'model-name')]");
        $current_brand = "";
        $result = array();
        $manufacturers = array();

        foreach($models as $model) {
        	if($model->attributes()->{'class'} == "model-brand") {
        		$current_brand = (string)$model->a[0];
        		$current_brand = str_replace("Запчасти на ", "", $current_brand);
        		$result[$current_brand] = array();
        		$manufacturers[] = $current_brand;
        		// Minion_CLI::write($current_brand);
        	} elseif($model->attributes()->{'class'} == "model-name") {
        		$current_model = (string)$model->a[0];
        		$current_model = str_replace($current_brand." ", "", $current_model);
        		$result[$current_brand][] = $current_model;
        	}
        }

        foreach ($manufacturers as $manufacturer) {
			usort($result[$manufacturer], 'sortfunc');
        }

		$tecdoc = Model::factory('Tecdoc');

        foreach ($result as $manufacturer => $models_arr) {
        	$manufacturers = $tecdoc->get_manufacturers(false, false, $manufacturer);
        	$manuf_obj = $manufacturers[0];

        	foreach ($models_arr as $model) {
        		$models_db = $tecdoc->get_cars(false, $manuf_obj['id'], $model, false, false);
        		if(!$models_db) Minion_CLI::write($model);
        		else {
        			$first_model = $models_db[0];

        			$tecdoc->update_car(array('short_description' => $model, 'modified' => 1), $first_model['id']);

        			$models_ids = array();
        			foreach($models_db as $model_db) {
        				if($model_db['id'] != $first_model['id'])
        					$models_ids[] = $model_db['id'];
        			}

        			if(count($models_ids) > 0) {
	        			$tecdoc->update_types(array('model_id' => $first_model['id'], 'modified' => 1), $models_ids);

	        			$tecdoc->delete_cars_where($models_ids);
	        		}
        		}
        	}
        }

        foreach ($tecdoc->get_duplicated_cars() as $dupl_model) {
        	$model = $dupl_model['short_description'];
    		$models_db = $tecdoc->get_cars(false, $dupl_model['manufacturer_id'], $model, false, false);
    		if(!$models_db) Minion_CLI::write($model);
    		else {
    			$first_model = $models_db[0];

    			$tecdoc->update_car(array('short_description' => $model, 'modified' => 1), $first_model['id']);

    			$models_ids = array();
    			foreach($models_db as $model_db) {
    				if($model_db['id'] != $first_model['id'])
    					$models_ids[] = $model_db['id'];
    			}

    			if(count($models_ids) > 0) {
        			$tecdoc->update_types(array('model_id' => $first_model['id'], 'modified' => 1), $models_ids);

        			$tecdoc->delete_cars_where($models_ids);
        		}
    		}
        }

        $query = "UPDATE `tof_models` SET `slug`=transliterate(short_description) WHERE modified = 1";
		DB::query(Database::UPDATE,$query)->execute('tecdoc')->as_array();

  //       ob_start();
		// var_dump($result);
		// $output = ob_get_clean();

		// Minion_CLI::write($output);
    }
}

function sortfunc($a,$b){
    return strlen($b)-strlen($a);
}

?>
