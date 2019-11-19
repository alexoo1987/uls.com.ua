<?php defined('SYSPATH') or die('No direct script access.');

class Model_ClientDocument extends ORM {
    protected $_table_name = 'clients_documents';

    protected $_belongs_to = array(
        'client'  => array(
            'model'       => 'Client',
            'foreign_key' => 'client_id',
        ),

    );
}