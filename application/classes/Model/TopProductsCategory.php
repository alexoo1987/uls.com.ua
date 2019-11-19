<?php defined('SYSPATH') or die('No direct script access.');

class Model_TopProductsCategory extends ORM {
    protected $_table_name = 'top_products_category';

    protected $_belongs_to = array(
        'part'  => array(
            'model'       => 'Parts',
            'foreign_key' => 'part_id',
        ),

        'cat'  => array(
            'model'       => 'Category',
            'foreign_key' => 'category_id',
        ),
    );
}