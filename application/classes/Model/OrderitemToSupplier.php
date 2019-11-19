<?php defined('SYSPATH') or die('No direct script access.');

class Model_OrderitemToSupplier extends ORM {
    protected $_table_name = 'orderitem_to_supplier_order';

    protected $_belongs_to = array(
        'orderitem'  => array(
            'model'       => 'Orderitem',
            'foreign_key' => 'orderitem_id',
        ),

        'supplier_order'  => array(
            'model'       => 'SupplierOrder',
            'foreign_key' => 'order_supplier_id',
        ),
    );
}