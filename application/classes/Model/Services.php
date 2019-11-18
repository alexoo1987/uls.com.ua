<?php

class Model_Services
{
    const activeStates = [2,3,5,6,8,16,19,31,32,34,36,37,38];
    const disableStates = [1,4,7,13,14,15,17,18,33,35,39,41];
    const inWork = [2, 6, 8, 19, 31, 32];
    const purchasedStates = [3, 5, 13, 14, 17];

    public function create_order($id_pos, $count, $phone)
    {
        $new = false;
        if(!ORM::factory('Client')->logged_in())
        {
            $client = ORM::factory('Client', array('phone' => $phone));
            if (!$client->loaded())
            {
                $last_order = ORM::factory('Order')->order_by('id', 'DESC')->limit(1)->find();

                //default query
                $query = DB::select('id')
                    ->from('users')
                    ->join('roles_users', 'LEFT')
                    ->on('users.id', '=', 'roles_users.user_id')
                    ->where('roles_users.role_id', 'IN', array(3,10,17))
                    ->and_where('users.status', '=', 1);

                //all managers
                $managers = $query->execute()->as_array();
                $temp = array();
                foreach ($managers AS $one) {
                    $temp[] = $one['id'];
                }
                $managers = $temp;

                //active managers
                $active_managers = $query
                    ->and_where('last_activity', '>', DB::expr('DATE_ADD(NOW(), INTERVAL -15 MINUTE)'))
                    ->execute()
                    ->as_array();
                $temp = array();
                foreach ($active_managers AS $one) {
                    $temp[] = $one['id'];
                }
                $active_managers = $temp;

                $last_order_manager = $last_order->manager_id;

                if (empty($active_managers)) {
                    //add nonactive managers to queue
                    if (in_array($last_order_manager, $managers)) {
                        foreach ($managers AS $manager) {
                            if ($last_order_manager == $manager)
                                $man = current($managers);
                        }
                    } else {
                        $man = $managers[array_rand($managers)];
                    }
                } elseif (count($active_managers) == 1) {
                    //take lone manager
                    $man = $active_managers[0];
                } else {

                    $man = $active_managers[array_rand($active_managers)];

                    //add active managers to queue
                    if (in_array($last_order_manager, $managers)){
                        $success = 0;
                        while ($success != 1) {
                            foreach ($managers AS $manager) {
                                if ($last_order_manager == $manager) {
                                    if (in_array(current($managers), $active_managers)) {
                                        $man = current($managers);
                                        $success = 1;
                                        break;
                                    } else {
                                        $last_order_manager = current($managers);
                                    }
                                }
                            }
                        }
                    }
                }

                $client = ORM::factory('Client');

                $client->name = 'Гость';
                $client->surname = 'Гость';
                $client->client_type = Model_Client::TYPE_FIZ;
                $client->edrpoy = '';
                $client->name_organization = '';
                $client->phone = $phone;
                $client->password = $this->generateRandomString();
                $client->email = '';
                $client->manager_id = $man;
                $client->discount_id = ORM::factory('Discount')->getClient_standart();
                $client->save();
            }
            $new = true;
        }

        $order = ORM::factory('Order');
        $order->delivery_address = $client->delivery_address ? $client->delivery_address : '';
        $order->delivery_method_id = $client->delivery_method_id ? $client->delivery_method_id : 0;
        $order->state = 0;
        $order->manager_id = $client->manager_id;
        $order->confirmation = 1;
        $order->client_id = $client->id;
        $order->set('archive', 0);
        $order->set('online', 1);
        $order->save();


        $sms_text = "Заказ №".$order->get_order_number()." оформлен.";
        $sms_text .= "\nulc.com.ua\nulc.com.ua@gmail.com\n(098) 092-82-08";

        Sms::send($sms_text, "Оформление заказа", $phone);

        $this->add_priceitem_to_order($id_pos, $order, $count);
    }

    public function add_priceitem_to_order($priceitem_id, $order, $amount) {
        if(is_numeric($priceitem_id)) {
            $priceitem = ORM::factory('Priceitem')->where('id', '=', $priceitem_id)->find();
        } else {
            $json_array = json_decode(base64_decode(str_replace('_','=',$priceitem_id)), true);
            $priceitem = ORM::factory('Priceitem')->get_from_arr($json_array);
        }

        $discount = $priceitem->get_discount_for_client();


        $orderitem = ORM::factory('Orderitem');
        $orderitem->set('order_id', $order->id);
        $orderitem->set('article', $priceitem->part->article_long);
        $orderitem->set('brand', $priceitem->part->brand_long);
        $orderitem->set('name', $priceitem->part->name);
        $orderitem->set('suplier_code_tehnomir', $priceitem->suplier_code_tehnomir);
        $orderitem->set('purchase_per_unit', $priceitem->get_price());
        $orderitem->set('purchase_per_unit_in_currency', $priceitem->price);
        $orderitem->set('currency_id', $priceitem->currency_id);
        $orderitem->set('state_id', ORM::factory('State')->get_state_by_text_id('order_accept')->id);
        $orderitem->set('amount', $amount);
        $orderitem->set('delivery_days', $priceitem->delivery);

        $orderitem->set('sale_per_unit', $priceitem->get_price_for_client());

        $orderitem->set('discount_id', $discount->id);
        $orderitem->set('supplier_id', $priceitem->supplier_id);
        $orderitem->save();


    }

    public function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
