<?php defined('SYSPATH') or die('No direct script access.');

class Model_SupplierOrder extends ORM {
    protected $_table_name = 'supplier_order';

    protected $_belongs_to = array(
        'supplier'  => array(
            'model'       => 'Supplier',
            'foreign_key' => 'supplier_id',
        ),

        'supplierpay'  => array(
            'model'       => 'SupplierPayment',
            'foreign_key' => 'supp_order_id',
        ),

        'supplierdelivery'  => array(
            'model'       => 'Costs',
            'foreign_key' => 'supp_order_id',
        ),
    );

    protected $_has_many = array(
        'orderitemssupplier'  => array(
            'model'       => 'OrderitemToSupplier',
            'foreign_key' => 'order_supplier_id',
        ),
    );
}