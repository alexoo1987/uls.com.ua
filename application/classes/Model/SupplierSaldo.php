<?php defined('SYSPATH') or die('No direct script access.');

class Model_SupplierSaldo extends ORM {
    protected $_table_name = 'supplier_saldo';

    protected $_belongs_to = array(
        'currency'  => array(
            'model'       => 'Currency',
            'foreign_key' => 'currency_id',
        ),

        'supplier'  => array(
            'model'       => 'Supplier',
            'foreign_key' => 'supplier_id',
        ),

    );
}