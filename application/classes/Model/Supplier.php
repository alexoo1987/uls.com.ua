<?php defined('SYSPATH') or die('No direct script access.');

class Model_Supplier extends ORM {
    protected $_table_name = 'suppliers';

    protected $_belongs_to = array(
        'currency'  => array(
            'model'       => 'Currency',
            'foreign_key' => 'currency_id',
        ),
    );

    protected $_has_one = array(
        'price'  => array(
            'model'       => 'ImportSetting',
            'foreign_key' => 'supplier_id',
        ),
    );

    protected $_has_many = array(
        'prices'  => array(
            'model'       => 'Operation',
            'foreign_key' => 'supplier_id',
        ),

        'orders' => array(
            'model'       => 'SupplierOrder',
            'foreign_key' => 'supplier_id',
        ),

        'saldos' => array(
            'model'       => 'SupplierSaldo',
            'foreign_key' => 'supplier_id',
        ),
    );

    public function rules()
    {
        return array(
            'name' => array(
                array('not_empty'),
                array('max_length', array(':value', 64)),
            ),
            'delivery_days' => array(
                array('max_length', array(':value', 16)),
            )
        );
    }

    public $_statuses = array('ready' => 'Готов к загрузке', 'error' => 'Ошибка', 'process' => 'Загружается');
    public function get_status() {
        return $this->_statuses[$this->status];
    }
}