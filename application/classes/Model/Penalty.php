<?php defined('SYSPATH') or die('No direct script access.');
 
class Model_Penalty extends ORM {
    protected $_table_name = 'penalty';

    protected $_belongs_to = array(
        'orderitem'  => array(
            'model'       => 'Orderitem',
            'foreign_key' => 'orderitem_id',
        )
    );
}