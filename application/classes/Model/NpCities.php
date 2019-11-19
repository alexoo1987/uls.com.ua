<?php defined('SYSPATH') or die('No direct script access.');

class Model_NpCities extends ORM {
    protected $_table_name = 'np_cities';
    public $salary_arr = array();

    protected $_belongs_to = array(
        'area'  => array(
            'model'       => 'NpAreas',
            'foreign_key' => 'area_id',
        ),
    );

    protected $_has_many = array(
        'warehouses'  => array(
            'model'       => 'NpWarehouse',
            'foreign_key' => 'city_id',
        ),
    );
}