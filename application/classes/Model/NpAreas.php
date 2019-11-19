<?php defined('SYSPATH') or die('No direct script access.');

class Model_NpAreas extends ORM {
    protected $_table_name = 'np_areas';
    public $salary_arr = array();

    protected $_has_many = array(
        'cities'  => array(
            'model'       => 'NpCities',
            'foreign_key' => 'area_id',
        ),
    );
}