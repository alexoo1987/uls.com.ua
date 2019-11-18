<?php defined('SYSPATH') or die('No direct script access.');

class Model_NpWarehouse extends ORM {
    protected $_table_name = 'np_warehouse';
    public $salary_arr = array();

    protected $_belongs_to = array(
        'area'  => array(
            'model'       => 'NpCities',
            'foreign_key' => 'city_id',
        ),
    );
}