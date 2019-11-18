<?php defined('SYSPATH') or die('No direct script access.');

class Model_Orderitem extends ORM {
    protected $_table_name = 'orderitems';
    public $salary_arr = array();

    /*protected $_has_many = array(
        'orderitems'  => array(
            'model'       => 'Orderitems',
            'foreign_key' => 'order_id',
        ),
    );*/

    protected $_has_one = array(
        'ttn' => array(
            'model' => 'NpTtns',
            'foreign_key' => 'orderitem_id'
        ),
        'supp_order' => array(
            'model' => 'OrderitemToSupplier',
            'foreign_key' => 'orderitem_id'
        )
    );

    protected $_has_many = array(
        'logs' => array(
            'model' => 'OrderitemLog',
            'foreign_key' => 'orderitem_id'
        ),
        'comments' => array(
            'model' => 'Orderitemcomment',
            'foreign_key' => 'order_item_id'
        ),
    );

    protected $_belongs_to = array(
        'order'  => array(
            'model'       => 'Order',
            'foreign_key' => 'order_id',
        ),
//        'ordertm'  => array(
//            'model'       => 'OrderEpartsTm',
//            'foreign_key' => 'order_id',
//        ),
        'supplier'  => array(
            'model'       => 'Supplier',
            'foreign_key' => 'supplier_id',
        ),
        'state'  => array(
            'model'       => 'State',
            'foreign_key' => 'state_id',
        ),
        'discount'  => array(
            'model'       => 'Discount',
            'foreign_key' => 'discount_id',
        ),
        'currency'  => array(
            'model'       => 'Currency',
            'foreign_key' => 'currency_id',
        ),
    );

    public $val = null;
    public $val_in_curr = null;

    public function get_greater_than_delivered() {
        return ORM::factory('Orderitem')->with('order')->with('state')->where('order.date_time', '<', 'NOW() - INTERVAL CAST(delivery_days AS SIGNED) DAY')
            ->and_where('order.archive', '=', '0')
            ->and_where('state.text_id', '=', 'in_work');
    }
}