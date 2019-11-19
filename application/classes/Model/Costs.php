<?php defined('SYSPATH') or die('No direct script access.');

class Model_Costs extends ORM {
    protected $_table_name = 'costs';

    protected $_belongs_to = array(
        'supplier'  => array(
            'model'       => 'Supplier',
            'foreign_key' => 'supplier_id',
        ),
        'user'  => array(
            'model'       => 'User',
            'foreign_key' => 'user_id',
        ),

    );

    protected $_has_many = array(
        'supp_order'  => array(
            'model'       => 'SupplierOrder',
            'foreign_key' => 'supp_order_id',
        ),
    );
}