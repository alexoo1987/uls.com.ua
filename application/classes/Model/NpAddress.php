<?php defined('SYSPATH') or die('No direct script access.');

class Model_NpAddress extends ORM {
    protected $_table_name = 'np_address';
    public $salary_arr = array();

   protected $_belongs_to = array(
                'area'  => array(
                        'model'       => 'NpCities',
                        'foreign_key' => 'city_id',
                    ),
            );
}