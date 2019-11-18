<?php defined('SYSPATH') or die('No direct script access.');

class Model_Order extends ORM {
    protected $_table_name = 'orders';
    public $ready = null;

    protected $_has_many = array(
        'orderitems'  => array(
            'model'       => 'Orderitem',
            'foreign_key' => 'order_id',
        ),

        'np_ttns'  => array(
            'model'       => 'NpTtns',
            'foreign_key' => 'order_id',
        ),
    );

    protected $_belongs_to = array(
        'area'  => array(
            'model'       => 'NpAreas',
            'foreign_key' => 'np_area_id',
        ),
        'city'  => array(
            'model'       => 'NpCities',
            'foreign_key' => 'np_city_id',
        ),
        'warehouse'  => array(
            'model'       => 'NpWarehouse',
            'foreign_key' => 'np_warehouse_id',
        ),


        'client'  => array(
            'model'       => 'Client',
            'foreign_key' => 'client_id',
        ),
        'currency'  => array(
            'model'       => 'Currency',
            'foreign_key' => 'currency_id',
        ),
        'manager'  => array(
            'model'       => 'User',
            'foreign_key' => 'manager_id',
        ),
        'agent'  => array(
            'model'       => 'User',
            'foreign_key' => 'id_purchasing_agent',
        ),
        'delivery_method'  => array(
            'model'       => 'DeliveryMethod',
            'foreign_key' => 'delivery_method_id',
        ),
        'OrderState'  => array(
            'model'       => 'OrderState',
            'foreign_key' => 'state',
        ),
    );
    
    
    public function getDeliveryNpDetails()
    {
        if($this->delivery_method->id == 3)
        {
            if(!empty($this->np_city_id) AND !empty($this->np_warehouse_id) AND !empty($this->np_area_id))
            {
                return $this->area->name." обл, місто ".$this->city->name.", ".$this->warehouse->name;
            }
            else
            {
                if(!empty($this->np_city) && !empty($this->np_warehouse) && !empty($this->np_area))
                {
                    return $this->np_area." обл, місто ".$this->np_city.", ".$this->np_warehouse;
                }
            }
        }
        
        return false;
    }
    

    public function get_order_number() {
        return str_pad($this->id, 10, "0", STR_PAD_LEFT);
    }

    public function get_balance() {
        $order_details = array();

        $order_details['all_sale'] = 0;
        $order_details['all_in'] = 0;
        $order_details['balance'] = 0;

        $order_disallow = Model_Services::disableStates;

        $client_payments = ORM::factory('ClientPayment')->where('order_id', '=', $this->id)->order_by('date_time')->find_all()->as_array();


        foreach($client_payments as $cp) {
            $order_details['all_in'] += $cp->value;
        }

        foreach($this->orderitems->find_all()->as_array() as $oi) {
            if(in_array($oi->state_id, $order_disallow)) continue;
            $order_details['all_sale'] += $oi->sale_per_unit*$oi->amount;
        }

        $order_details['balance'] = $order_details['all_in'] - $order_details['all_sale'];
        if($order_details['balance'] > 0) $order_details['balance'] = 0;

        return $order_details;
    }

    public function get_work_day($count, $order_date)
    {
        $date              = $order_date;
        $day_week          = date( 'N', strtotime( $date ) );
        $day_count         = $count + $day_week;
        $week_count        = floor($day_count/5);
        $holiday_count     = ( $day_count % 5 > 0 ) ? 0 : 2;
        $week_day          = $week_count * 7 - $day_week + ( $day_count % 5 ) - $holiday_count;
        $date_end          = date( "d-m-Y", strtotime( $date . " + $week_day day " ) );
        $date_end_count    = date( 'N', strtotime( $date_end ) );
        $holiday_shift     = $date_end_count > 5 ? 7 - $date_end_count + 1 : 0;
        return date("d-m-Y", strtotime($date_end . " + $holiday_shift day "));
    }
}