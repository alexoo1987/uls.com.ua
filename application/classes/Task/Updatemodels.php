<?php defined('SYSPATH') or die('No direct script access.');
class Task_Updatemodels extends Minion_Task {
    protected function _execute(array $params)
    {
        $tecdoc = Model::factory('Tecdoc');
        $tecdoc->update_models_names();
    }
}

?>
