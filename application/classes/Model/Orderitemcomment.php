<?php defined('SYSPATH') or die('No direct script access.');

class Model_Orderitemcomment extends ORM {
    protected $_table_name = 'orderitemscomments';
    protected $_belongs_to = array(
        'order'  => array(
            'model'       => 'Order',
            'foreign_key' => 'order_id',
        ),
        'author'  => array(
            'model'       => 'Users',
            'foreign_key' => 'author_id',
        ),
    );
}