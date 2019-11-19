<?php defined('SYSPATH') or die('No direct script access.');

error_reporting( E_ERROR );
use LisDev\Delivery\NovaPoshtaApi2;

class Controller_Admin_Ajax extends Controller {

    public function action_test()
    {

//        $np = new LisDev\Delivery\NovaPoshtaApi2(
//            '6a8ca3163492bb644bc33dde1265f6cd',
//            'ru',
//            FALSE,
//            'curl'
//        );
//        $result = $np
//            ->model('TrackingDocument')
//            ->method('getStatusDocuments')
//            ->params([
//                'Documents' => [[
//                    "DocumentNumber" => "20400098587325",
//                    "Phone" => ""
//                ]],
//            ])
//            ->execute();




//        $maxDateCreated = date('Y-m-d 00:00:00', strtotime('-15 days'));
//
//        $allTtnQuery = "SELECT np_ttns.ttn, c.phone FROM np_ttns
//            INNER JOIN orderitems oi ON oi.id = np_ttns.orderitem_id
//            INNER JOIN orders o ON o.id = oi.order_id
//            INNER JOIN clients c ON c.id = o.client_id
//            WHERE time >= '".$maxDateCreated."'
//            GROUP BY ttn";
//
//        $allTtnResult = DB::query(Database::SELECT,$allTtnQuery)->execute('tecdoc_new')->as_array();
//
//        $np = new LisDev\Delivery\NovaPoshtaApi2(
//            '6a8ca3163492bb644bc33dde1265f6cd',
//            'ru',
//            FALSE,
//            'curl'
//        );
//
//        $query = [];
//
//        $ttns = $allTtnResult;
//
//        foreach ($ttns as $ttn)
//        {
//            $phone = str_replace('+', '',str_replace('(', '', str_replace(')', '', str_replace('-', '', $ttn['phone']))));
//            $query[] = [
//                "DocumentNumber" => $ttn['ttn'],
//                "Phone" => $phone
//            ];
//        }
//
//        $allResult = [];
//
//        if(count($query > 100))
//        {
//            for($i = 0; $i < count($query); $i += 50)
//            {
//                $result = $np
//                    ->model('TrackingDocument')
//                    ->method('getStatusDocuments')
//                    ->params([
//                        'Documents' => array_slice($query, $i, 50),
//                    ])
//                    ->execute();
//
//                $allResult = array_merge($allResult, $result['data']);
//            }
//        }
//
//        print_r($allResult);
//        exit();
    }

    public function action_index()
    {
        if(!empty($_REQUEST['number']) AND $_SERVER['REMOTE_ADDR'] == '91.194.251.209')
        {
            $phone_client = $_REQUEST['number'];
            if(strlen($phone_client) == 10)
            {
                $new_phone_client = '+38('.$phone_client[0].$phone_client[1].$phone_client[2].')'.$phone_client[3].$phone_client[4].$phone_client[5].'-'.$phone_client[6].$phone_client[7].'-'.$phone_client[8].$phone_client[9];
                $client = ORM::factory('Client')->where('phone', '=', $new_phone_client)->find();
                if(!$client)
                    return false;
                else
                {
                    echo $client->manager->inside_code;
                }
            }
            else
                return false;
        }
        else
            return false;
//
//        echo 'API'. PHP_EOL . '==='. PHP_EOL;
//        echo 'Request Time: ' . time() . PHP_EOL;
//        echo 'Request Method: ' . print_r($_SERVER['REQUEST_METHOD'], true) . PHP_EOL;
//
//        if(FALSE === empty($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
//            echo 'Request Header Method: ' . print_r($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'], true) . PHP_EOL;
//        }
//
//        echo 'Server Data: ' . print_r($_SERVER, true) . PHP_EOL;
//        echo 'Request Files: ' . print_r($_FILES, true) . PHP_EOL;
//        echo 'Request Data: ' . PHP_EOL;
//        echo 'GET/POST: ' . print_r($_REQUEST, true) . PHP_EOL;
//        parse_str(file_get_contents('php://input'), $_DELETE);
//        echo 'DELETE/PUT: ' . print_r($_DELETE, true) . PHP_EOL;
    }

    public function action_send_msg()
    {
        $this->auto_render = FALSE;
        $this->is_ajax = TRUE;
        header('content-type: application/json');

        $user_id = Auth::instance()->get_user()->id;
        if(!empty($_POST['message'])) $message = $_POST['message'];

        if(empty($message)) {
            echo json_encode(array("status" => "fail"));
            return false;
        }

        $msg = ORM::factory('Message');

        $msg->user_id = $user_id;
        $msg->message = $message;
        $msg->save();

        echo json_encode(array("status" => "success", 'surname' => Auth::instance()->get_user()->surname, 'time' => date("d.m H:i:s")));
    }

    public function action_display_birthday_session()
    {
        $this->auto_render = FALSE;
        $this->is_ajax = TRUE;
        header('content-type: application/json');

        $session = Session::instance();
        $current_user = $session->get('auth_user');
        $current_user_id = $current_user->id;

        $user_data = ORM::factory('BirthdayDisplay')->where('user_id', '=', $current_user_id)->find();

        if(!$user_data->loaded()){
            $user_data = ORM::factory('BirthdayDisplay');
        }

        $user_data ->user_id = (int)$current_user_id;
        $user_data ->date = date('Y-m-d');
        $user_data ->value = 0;
        $user_data ->save();

        echo json_encode(array("status" => "success" ));
    }

    public function action_display_birthday_congrad_cookie()
    {
        $this->auto_render = FALSE;
        $this->is_ajax = TRUE;
        header('content-type: application/json');

        Cookie::set('birthday_congratulations_close_event' , true , 60*60*24) ;
        $session = Session::instance();
        $current_user = $session->get('auth_user');
        $current_user_id = $current_user->id;

        $user_data = ORM::factory('BirthdayDisplay')->where('user_id', '=', $current_user_id)->find();

        if(!$user_data->loaded()){
            $user_data = ORM::factory('BirthdayDisplay');
        }

        $user_data ->user_id = (int)$current_user_id;
        $user_data ->date = date('Y-m-d');
        $user_data ->value = 0;
        $user_data ->save();

        echo json_encode(array("status" => "success" ));
    }

//    предупреждение, что срок заказа заказнчивается
    public function action_warning_order_in_suppliers()
    {
        $this->auto_render = FALSE;
        $this->is_ajax = TRUE;
        header('content-type: application/json');

        Cookie::set('warning_order' , true , 60*3) ;

        echo json_encode(array("status" => "success" ));
    }

    //    предупреждение новой почты
    public function action_warning_np()
    {
        $this->auto_render = FALSE;
        $this->is_ajax = TRUE;
        header('content-type: application/json');

        Cookie::set('warning_np' , true , 60*60*4) ;

        echo json_encode(array("status" => "success" ));
    }


    public function action_get_msgs()
    {
        $this->auto_render = FALSE;
        $this->is_ajax = TRUE;
        header('content-type: application/json');

        $user_id = Auth::instance()->get_user()->id;
        if(!empty($_POST['last_id'])) $last_id = $_POST['last_id'];


        if(empty($last_id)) {
            echo json_encode(array("status" => "fail"));
            return false;
        }

        $msgs = ORM::factory('Message')->order_by('timestamp', 'asc')->where('id', '>', $last_id)->and_where('user_id', '!=', $user_id)->find_all()->as_array();

        $messages = array();

        if(count($msgs) == 0) {
            echo json_encode(array("status" => "fail"));
            return false;
        }

        foreach($msgs as $msg) {
            $d = new DateTime($msg->timestamp);
            $messages[] = array(
                'surname' => $msg->user->surname,
                'time' => $d->format('d.m H:i:s'),
                'message' => $msg->message,
                'type' => ($msg->user_id == $user_id ? 'left' : 'right')
            );
        }

        echo json_encode(array("status" => "success", 'msgs' => $messages, 'last_id' => $msg->id));
    }

    /**
     * Send SMS to number
     */
    public function action_send_sms()
    {
        $this->auto_render = FALSE;
        $this->is_ajax = TRUE;

        $text = Request::current()->post('text');
        $phone = Request::current()->post('phone');
        $subject = Request::current()->post('subject');

        Sms::send($text, $subject, $phone);
    }

    /**
     * Update order parameter
     */
    public function action_update_order()
    {
        $this->auto_render = FALSE;
        $this->is_ajax = TRUE;
        $order_id = $_POST['order_id'];
        $parameter = $_POST['parameter'];
        $value = $_POST['value'];

        $order = ORM::factory('Order')->where('id', '=', $order_id)->find();
        $order->$parameter = $value;
        $order->save();
    }

    /**
     * Update orderitem parameter
     */
    public function action_update_orderitem()
    {
        $this->auto_render = FALSE;
        $this->is_ajax = TRUE;
        $id = $_POST['orderitem_id'];
        $parameter = $_POST['parameter'];
        $value = $_POST['value'];
        $item = ORM::factory('Orderitem')->where('id', '=', $id)->find();
        $item->$parameter = $value;
        $item->save();
    }


    /**
     * Get value by param
     *
     */
    public function action_get_currency_by_supplier()
    {
        $this->auto_render = FALSE;
        $this->is_ajax = TRUE;
        header('content-type: application/json');

        $supplier_id = $_POST['supplier_id'];

        $supplier = ORM::factory('Supplier')->where('id', '=', $supplier_id)->find();

        $temp = array(
            'id' => $supplier->currency->id,
            'ratio' => $supplier->currency->ratio
        );

        echo json_encode($temp);
        exit();
    }

    /**
     * Change supplier
     *
     */
    public function action_change_supplier()
    {
        $this->auto_render = FALSE;
        $this->is_ajax = TRUE;
        header('content-type: application/json');

        $article = Request::current()->post('article');
        $brand = Request::current()->post('brand');
        $supplier_id = Request::current()->post('supplier_id');
        $amount = Request::current()->post('amount');
        $delivery_days = Request::current()->post('delivery_days');

        $amount = (int)$amount;
        $delivery_days = (int)$delivery_days;

        $article = Article::get_short_article($article);
        $brand = Article::get_short_article($brand);

        $var_id = Auth::instance()->get_user()->id;
        $admin = false;
        if(($supplier_id != 48)&&($var_id == 3)){
            echo json_encode(array('status' => 1, 'admin' => $admin));
            exit();
        }

        //техномир
        if ($supplier_id == 38)
        {
            $part_obj = ORM::factory('Part')->where('brand', '=', $brand)->and_where('article', '=', $article)->find();
            $setting = ORM::factory('Setting')->where('code_name', '=', 'tekhnomir_active_site')->find();
            if (ORM::factory('Findfromip')->check_ip() AND $setting) {
                $INFO = Tminfo::instance();
                $INFO->SetLogin('eparts');
                $INFO->SetPasswd('950667817282kda');
                $tm_items = $INFO->GetPrice($article, $brand);
            } else {
                $tm_items = array();
            }

            $items = array();
            if ($tm_items) {
                $usd_currency = ORM::factory('Currency')->get_by_code('USD');
                $currency_id = $usd_currency->id;

                $usd_ratio = ORM::factory('Currency')->get_by_code('USD')->ratio;
                $eur_ratio = ORM::factory('Currency')->get_by_code('EUR')->ratio;

                $setting = ORM::factory('Setting')->where('code_name', '=', 'tekhnomir_percentage')->find();
                $tekhnomir_percentage = !empty($setting->id) && !empty($setting->value) ? $setting->value : 0;

                foreach ($tm_items AS $key => $row) {
                    $item = $row;


                    $price = (double) $row['Price'];
                    if ($row['Currency'] == 'EUR') $price = $price * ($eur_ratio / $usd_ratio);
                    else if ($row['Currency'] == 'UAH') $price = $price / $usd_ratio;

                    $price = $price * ((100+$tekhnomir_percentage)/100);

                    $delivery_setting = array(
                        'LOCAL' => 0,
                        'AIR' => 5,
                        'CONTAINER' => 3.2,
                    );

                    if (isset($row['DeliveryType'])) {
                        if (!in_array($row['DeliveryType'], array_keys($delivery_setting))) continue;
                        else $price = $price + $delivery_setting[$row['DeliveryType']] * $row['Weight'];
                    }

                    $price = round($price, 2);

                    $row['DeliveryTime'] += 1;

                    $price_item = ORM::factory('Priceitem');
                    $price_item->set('price', $price);
                    $price_item->set('currency_id', $currency_id);
                    $price_item->set('amount', ($row['Quantity'] == 0 ? 15 : $row['Quantity']));
                    $price_item->set('delivery', ($row['DeliveryTime'] == 0 ? 1 : $row['DeliveryTime'])); //($row['DeliveryTime'] == 0 ? 1 : $row['Quantity']+1)
//                    $price_item->set('suplier_code_tehnomir', $row['SupplierCode']);
                    $price_item->set('supplier_id', 38);
                    $price_item->part = $part_obj;

                    $json_array['price'] = $price;
                    $json_array['currency_id'] = $currency_id;
                    $json_array['amount'] = $row['Quantity'];
                    $json_array['delivery'] = $row['DeliveryTime'];
                    $json_array['supplier_code'] = $row['SupplierCode'];
                    $json_array['supplier_id'] = 38;
                    $json_array['part_id'] = $price_item->part->id;
                    $json_array['article'] = $price_item->part->article_long;
                    $json_array['brand'] = $price_item->part->brand_long;
                    $json_array['name'] = $price_item->part->name;

                    try {
                        $price_item->id = str_replace('=','_',base64_encode(json_encode($json_array)));
                    } catch (Exception $e) {
                        $json_array['name'] = iconv('WINDOWS-1251', 'UTF-8//IGNORE', $json_array['name']);
                        $price_item->id = str_replace('=','_',base64_encode(json_encode($json_array)));
                    }

                    $items[] = $price_item;
                }
            }
            else{
                //Если не нашло ничего у техномира
                if(($var_id == 2)||($var_id == 74))
                {
                    $suplier = ORM::factory('Supplier')
                        ->where('id', '=', $supplier_id)
                        ->find();
                    $current = $suplier->currency->ratio;
                    $admin = true;
                    $temp = array(
                        'currency_id' => $suplier->currency_id,
                        'ratio' => $current,
                        'admin' => $admin,
                        'status' => 1
                    );
                    echo json_encode($temp);
                    exit();
                }
                else
                {
                    echo json_encode(array('status' => 1, 'admin' => $admin));
                    exit();
                }

            }

            $items = $this->get_best_match_item_tehnomir($items, $delivery_days, $amount);
            $items = array_values($items);
            //Если у техномира нашло - выбираем самое дешовое
            //Если есть подходящий варик:
            if($items){
                if(($var_id == 2)||($var_id == 74)) {
                    $suplier = ORM::factory('Supplier')
                        ->where('id', '=', $supplier_id)
                        ->find();
                    $current = $suplier->currency->ratio;
                    $admin = true;
                    $temp = array(
                        'price_in_currency' => $items[0]->price,
                        'price' => round($items[0]->price * $items[0]->currency->ratio, 2)/*ceil($items[0]->price * $items[0]->currency->ratio/*)*/,
                        'currency_id' => $suplier->currency_id,
                        'ratio' => $current,
                        'delivery' => $items[0]->delivery,
                        'status' => 2,
                        'admin' => $admin
                    );
                    //var_dump($temp);
                    echo json_encode($temp);
                    exit();
                }
                else{
                    $suplier = ORM::factory('Supplier')
                        ->where('id', '=', $supplier_id)
                        ->find();
                    $temp = array(
                        'price_in_currency' => $items[0]->price,
                        'price' => round($items[0]->price * $items[0]->currency->ratio, 2)/*ceil($items[0]->price * $items[0]->currency->ratio/*)*/,
                        'currency_id' => $suplier->currency_id,
                        'delivery' => $items[0]->delivery,
                        'status' => 2,
                        'admin' => $admin
                    );
                    //var_dump($temp);
                    echo json_encode($temp);
                    exit();
                }
            }
            //Если нет подходящего варика:
            else{
                if(($var_id == 2)||($var_id == 74))
                {
                    $suplier = ORM::factory('Supplier')
                        ->where('id', '=', $supplier_id)
                        ->find();
                    $current = $suplier->currency->ratio;
                    $admin = true;
                    $temp = array(
                        'currency_id' => $suplier->currency_id,
                        'ratio' => $current,
                        'admin' => $admin,
                        'status' => 1
                    );
                    echo json_encode($temp);
                    exit();
                }
                else{
                    echo json_encode(array('status' => 1));
                    exit();
                }

            }

        }
        if($supplier_id == 48){
            // если поставщик неизвестный - Белявский, дмитрий, артем - могут изменить
            if(($var_id == 3)||($var_id == 2)||($var_id == 74))
            {
                $admin = true;
                echo json_encode(array('status' => 0, 'admin' => $admin,));
                exit();
            }
            // остальные - не могут изменить
            else{
                echo json_encode(array('status' => 0, 'admin' => $admin,));
                exit();
            }
        }
        // остальные
        $part = ORM::factory('Part')
            ->where('article', '=', $article)
            ->and_where('brand', '=', $brand)
            ->find();
        if (!$part->id) {
            //если не нашло в таблице партс
            if(($var_id == 2)||($var_id == 74))
            {
                $suplier = ORM::factory('Supplier')
                    ->where('id', '=', $supplier_id)
                    ->find();
                $current = $suplier->currency->ratio;
                $admin = true;
                $temp = array(
                    'currency_id' => $suplier->currency_id,
                    'ratio' => $current,
                    'admin' => $admin,
                    'status' => 1
                );
                echo json_encode($temp);
                exit();

            }
            else{
                echo json_encode(array('status' => 0, 'admin' => $admin));
                exit();

            }

        }


        $priceitem = ORM::factory('Priceitem')
            ->where('part_id', '=', $part->id)
            ->and_where('supplier_id', '=', $supplier_id)
            ->and_where('amount', '>=', $amount)
            ->order_by('price', 'ASC')
            ->order_by('delivery', 'ASC')
            ->find();

        if (!$priceitem->id) {
            if(($var_id == 2)||($var_id == 74)){
                $suplier = ORM::factory('Supplier')
                    ->where('id', '=', $supplier_id)
                    ->find();
                $current = $suplier->currency->ratio;
                $admin = true;
                $temp = array(
                    'currency_id' => $suplier->currency_id,
                    'ratio' => $current,
                    'admin' => $admin,
                    'status' => 1
                );
                echo json_encode($temp);
                exit();
            }
            else{
                //cant find this priceitem
                echo json_encode(array('status' => 1, 'admin' => $admin));
                exit();
            }
        }


        if(($var_id == 2)||($var_id == 74)||($var_id == 59))
        {
            $suplier = ORM::factory('Supplier')
                ->where('id', '=', $supplier_id)
                ->find();
            $current = $suplier->currency->ratio;
            $admin = true;
            $temp = array(
                'price_in_currency' => $priceitem->price,
                'price' => round($priceitem->price * $priceitem->currency->ratio, 2)/*ceil($priceitem->price * $priceitem->currency->ratio/*)*/,
                'currency_id' => $priceitem->currency_id,
                'delivery' => $priceitem->delivery,
                'admin' => $admin,
                'ratio' => $current,
                'status' => 2,
            );
        }
        else{
            $temp = array(
                'price_in_currency' => $priceitem->price,
                'price' => round($priceitem->price * $priceitem->currency->ratio, 2)/*ceil($priceitem->price * $priceitem->currency->ratio/*)*/,
                'currency_id' => $priceitem->currency_id,
                'delivery' => $priceitem->delivery,
                'admin' => $admin,
                'status' => 2,
            );
        }


        echo json_encode($temp);
        exit();
    }

    /**
     * Get orders by manager id
     *
     */
    public function action_get_orders()
    {
        $this->auto_render = FALSE;
        $this->is_ajax = TRUE;
        header('content-type: application/json');

        $manager_id = $_POST['manager_id'];

        $orders = ORM::factory('Order')->where('manager_id', '=', $manager_id)->order_by('id', 'DESC')->limit(150)->find_all()->as_array();

        $data = array();

        foreach ($orders AS $order) {
            $data[] = array(
                'id' => $order->id,
                'client' => $order->client->surname
            );
        }

        echo json_encode($data);
        exit();
    }

    public function action_get_region()
    {
        $this->auto_render = FALSE;
        $this->is_ajax = TRUE;

        $np = new LisDev\Delivery\NovaPoshtaApi2(
            '6a8ca3163492bb644bc33dde1265f6cd',
            'ua',
            FALSE,
            'curl'
        );

        $areas = $np->getAreas();
        $result = [];

        foreach ($areas['data'] as $data)
        {
            $result[] = $data['Description'];
        }

        echo json_encode($areas);
        exit();
    }

    public function action_select_city()
    {
        $this->auto_render = FALSE;
        $this->is_ajax = TRUE;

        $area_id = $_POST['region_id'];
        $cities = ORM::factory('NpCities')->where('area_id', '=', $area_id)->find_all()->as_array();
        $new_cities = [];
        $new_cities[0] = '-----';

        foreach ($cities as $city) {
            $new_cities[$city->id] = $city->name;
        }

        echo json_encode($new_cities);
        exit();
    }

    public function action_select_warehous()
    {
        $this->auto_render = FALSE;
        $this->is_ajax = TRUE;

        $city_id = $_POST['city_id'];
        $area_id = $_POST['area_id'];
        $new_warehous = [];

        $warehouses = ORM::factory('NpWarehouse')->where('city_id', '=', $city_id)->find_all()->as_array();
        $new_warehous[0] = '-----';
        foreach ($warehouses as $warehouse) {
            $new_warehous[$warehouse->id] = $warehouse->name;
        }

        echo json_encode($new_warehous);
        exit();
    }

    public function action_create_express()
    {

        $this->auto_render = FALSE;
        $this->is_ajax = TRUE;

        $id = $_POST['id'];
        $name = $_POST['name'];
        $surname = $_POST['surname'];
        $phone = $_POST['phone'];
        $warehouse_from = $_POST['warehouse_from'];
        $region = $_POST['region'];
        $city = $_POST['city'];
        $warehous = $_POST['warehous'];
        $weight = $_POST['weight'];
        $width = $_POST['width'];
        $height = $_POST['height'];
        $lenght = $_POST['lenght'];
        $cost = $_POST['cost'];
        $platezh= $_POST['platezh'];

//        $id = 183415;
//        $name = "Артем";
//        $surname = "Мандзюк";
//        $phone = "380682130880";
//        $warehouse_from = "47402e8b-e1c2-11e3-8c4a-0050568002cf";
//        $region = "71508135-9b87-11de-822f-000c2965ae0e";
//        $city = "db5c888c-391c-11dd-90d9-001a92567626";
//        $warehous = "1ec09d96-e1c2-11e3-8c4a-0050568002cf";
//        $weight = "0.2";
//        $width = "0.01";
//        $height = "0.1";
//        $lenght = "0.1";
//        $cost = "200";
//        $platezh= "200";


        $np = new NovaPoshtaApi2(
            '6a8ca3163492bb644bc33dde1265f6cd',
            'ua',
            FALSE,
            'curl'
        );

        $contactPerson = ORM::factory('Users')->where('ref_np', 'IS NOT', NULL)->and_where('phone_np', 'IS NOT', NULL)->and_where('status', '=', 1)->order_by('total_ttns_sum', 'ASC')->limit(1)->find();

        $orderitem = ORM::factory('Orderitem')->where('id', '=', $id)->find();

        $senderInfo = $np->getCounterparties('Sender', 1, '', '');
        $sender = $senderInfo['data'][0];
        $senderWarehouses = $np->getWarehouses($sender['City']);

        $sender_info = [
            'FirstName' => $sender['FirstName'],
            'MiddleName' => $sender['MiddleName'],
            'LastName' => $sender['LastName'],
            'CitySender' => $sender['City'],
            'SenderAddress' => $warehouse_from,
            'SendersPhone' => $contactPerson->phone_np,
            'ContactSender' => $contactPerson->ref_np
        ];

        $recipient = [
            'FirstName' => $name,
            'MiddleName' => '',
            'LastName' => $surname,
            'Phone' => $phone,
//            'City' => $city,
//            'Region' => $region,
//            'Warehouse' => $warehous,
            'CityRecipient' => $city,
            'RecipientAddress' => $warehous,
        ];

        $params = [
            // Дата отправления
            'DateTime' => date('d.m.Y'),
            // Тип доставки, дополнительно - getServiceTypes()
            'ServiceType' => 'WarehouseWarehouse',
            // Тип оплаты, дополнительно - getPaymentForms()
            'PaymentMethod' => 'Cash',
            // Кто оплачивает за доставку
            'PayerType' => 'Recipient',
            // Кол-во мест
            'SeatsAmount' => '1',
            // Описание груза
            'Description' => 'Запчасти',
            // Тип доставки, дополнительно - getCargoTypes
            'CargoType' => 'Cargo',
            // Стоимость груза в грн
            'Cost' => $cost,
            // Вес груза
            'Weight' => $weight,
//            // Объем груза в куб.м.
//            'VolumeGeneral' => $volume,
//            // Ширина
//            'volumetricWidth' => $width,
//            // Высота
//            'volumetricHeight' => $height,
//            // Глубина
//            'volumetricLength' => $lenght,
        ];

        $params['OptionsSeat'] = [
            [
                // Стоимость груза в грн
                'Cost' => $cost,
                // Описание груза
                'Description' => 'Запчасти',
                "weight" => $weight,
                "volumetricVolume" => $width * $height* $lenght,
                "volumetricWidth" => $width,
                "volumetricHeight" => $height,
                "volumetricLength" => $lenght
            ]
        ];

        if($platezh > 0)
        {
            $params['BackwardDeliveryData'] = [
                [
                    // Кто оплачивает обратную доставку
                    'PayerType' => 'Recipient',
                    // Тип доставки
                    'CargoType' => 'Money',
                    // Значение обратной доставки
                    'RedeliveryString' => $platezh,
                ]
            ];

            $contactPerson->total_ttns_sum = $contactPerson->total_ttns_sum + $platezh;
            $contactPerson->save();
        }

        if($warehouse_from == '10dc7a89-ba83-11e7-becf-005056881c6b')
        {
            $params['ServiceType'] = 'DoorsWarehouse';
        }

        $result = $np->newInternetDocument($sender_info, $recipient, $params);

        if(isset($result['data'][0]['IntDocNumber']) and $result['data'][0]['IntDocNumber'])
        {
            $orderitem->state_id = 5;
            $orderitem->save();

            $log = ORM::factory('OrderitemLog');
            $log
                ->set('orderitem_id', $orderitem->id)
                ->set('state_id', 5)
                ->set('date_time', date('Y-m-d H:i:s'))
                ->set('user_id', Auth::instance()->get_user()->id)
                ->save();

            Admin::check_ready_order($orderitem->order->id);

            $ttn = ORM::factory('NpTtns');
            $ttn
                ->set('orderitem_id', $orderitem->id)
                ->set('summ', $platezh)
                ->set('ttn', $result['data'][0]['IntDocNumber'])
                ->set('ref', $result['data'][0]['Ref'])
                ->set('time', date('Y-m-d H:i:s'))
                ->save();

            $text = $result['data'][0]['IntDocNumber']." Новая почта \nulc.com.ua\nulc.com.ua@gmail.com\n(098) 092-82-08";
            Sms::send($text, 'Доставка', $phone);

            $url = $np->printMarkings($result['data'][0]['Ref'], 'pdf_link');
            $link = $url['data'][0];

            $data_result = [];
            $data_result['url'] = $link;
            $data_result['error'] = 0;

            echo json_encode($data_result);
            exit();
        }
        elseif (isset($result['errors'][0]) AND $result['errors'][0])
        {
            $data_result = [];
            $data_result['url'] = 0;
            $data_result['error'] = $result['errors'][0];

            echo json_encode($data_result);
            exit();
        }
    }

    public function action_test_np(){

//        $users = ORM::factory('Users')->where('status', '=', 1)->and_where('ref_np', '=', NULL)->find_all();

        $np = new NovaPoshtaApi2(
            '6a8ca3163492bb644bc33dde1265f6cd',
            'ua',
            FALSE,
            'curl'
        );
     //   print_r($np->printMarkings(20400111005203, 'pdf_link'));
     //   exit();


//        foreach ($users as $user)
//        {
//            $result = $np
//                ->model('ContactPerson')
//                ->method('save')
//                ->params(array(
//                    'CounterpartyRef' => '4c5a4736-12e9-11e7-95d1-005056887b8d',
//                    'FirstName' => $user->name,
//                    'LastName' => $user->surname,
//                    'MiddleName' => $user->middle_name,
//                    'Phone' => '+380672247730',
//                ))
//                ->execute();
//
//            if(isset($result['data'][0]['Ref'])) {
//                $user->ref_np = $result['data'][0]['Ref'];
//                $user->save();
//            }
//        }


        print_r($np->getCounterpartyContactPersons('4c5a4736-12e9-11e7-95d1-005056887b8d'));

    }

    public function action_create_express_for_order(){

        $this->auto_render = FALSE;
        $this->is_ajax = TRUE;


        $id = $_POST['id'];
        $name = $_POST['name'];
        $surname = $_POST['surname'];
        $phone = $_POST['phone'];
        $warehouse_from = $_POST['warehouse_from'];
        $region = $_POST['region'];
        $city = $_POST['city'];
        $warehous = $_POST['warehous'];
        $weight = $_POST['weight'];
//        $width = $_POST['width'];
//        $height = $_POST['height'];
//        $lenght = $_POST['lenght'];
        $cost = $_POST['cost'];
        $platezh= $_POST['platezh'];
        $places = $_POST['places'];

//        $id = 30108;
//        $name = "Евгений Александрович";
//        $surname = "Ляшенко";
//        $phone = "380506044039";
//        $warehouse_from = "47402e8b-e1c2-11e3-8c4a-0050568002cf";
//        $region = "Полтавська";
//        $city = "Лубны";
//        $warehous = "Отделение №1: ул. Фабричная, 7";
//        $weight = "10";
//        $cost = 1399;
//        $platezh= 100;
//        $places = 2;

        $contactPerson = ORM::factory('Users')->where('ref_np', 'IS NOT', NULL)->and_where('phone_np', 'IS NOT', NULL)->and_where('status', '=', 1)->order_by('total_ttns_sum', 'ASC')->limit(1)->find();

        $np = new NovaPoshtaApi2(
            '6a8ca3163492bb644bc33dde1265f6cd',
            'ua',
            FALSE,
            'curl'
        );

        $order = ORM::factory('Order')->where('id', '=', $id)->find();
        $data['orderitems'] = ORM::factory('Orderitem')->where('order_id', '=', $id)->and_where('state_id', '=', 37)->find_all()->as_array();

        $senderInfo = $np->getCounterparties('Sender', 1, '', '');
        $sender = $senderInfo['data'][0];


        $sender_info = [
            'FirstName' => $sender['FirstName'],
            'MiddleName' => $sender['MiddleName'],
            'LastName' => $sender['LastName'],
            'CitySender' => $sender['City'],
            'SenderAddress' => $warehouse_from,
            'SendersPhone' => $contactPerson->phone_np,
            'ContactSender' => $contactPerson->ref_np
        ];

        $recipient = [
            'FirstName' => $name,
            'MiddleName' => '',
            'LastName' => $surname,
            'Phone' => $phone,
//            'City' => $city,
//            'Region' => $region,
//            'Warehouse' => $warehous,
            'CityRecipient' => $city,
            'RecipientAddress' => $warehous,
        ];

        $params = [
            // Дата отправления
            'DateTime' => date('d.m.Y'),
            // Тип доставки, дополнительно - getServiceTypes()
            'ServiceType' => 'WarehouseWarehouse',
            // Тип оплаты, дополнительно - getPaymentForms()
            'PaymentMethod' => 'Cash',
            // Кто оплачивает за доставку
            'PayerType' => 'Recipient',
            // Кол-во мест
            'SeatsAmount' => $places,
            // Описание груза
            'Description' => 'Запчасти',
            // Тип доставки, дополнительно - getCargoTypes
            'CargoType' => 'Cargo',
            // Стоимость груза в грн
            'Cost' => $cost,
            // Вес груза
            'Weight' => $weight,
        ];

        $optionSeats = [];

        for($i = 0; $i < $places; $i++)
        {
            $optionSeats[] = [
                'Cost' => $cost / $places,
                'Description' => 'Запчасти',
                "weight" => $weight / $places,
                "volumetricVolume" => 0.1 * 0.1 * 0.1,
                "volumetricWidth" => 0.1,
                "volumetricHeight" => 0.1,
                "volumetricLength" => 0.1
            ];
        }

        $params['OptionsSeat'] = $optionSeats;

        if($platezh > 0)
        {
            $params['BackwardDeliveryData'] = [
                [
                    // Кто оплачивает обратную доставку
                    'PayerType' => 'Recipient',
                    // Тип доставки
                    'CargoType' => 'Money',
                    // Значение обратной доставки
                    'RedeliveryString' => $platezh,
                ]
            ];

            $contactPerson->total_ttns_sum = $contactPerson->total_ttns_sum + $platezh;
            $contactPerson->save();
        }

        if($warehouse_from == '10dc7a89-ba83-11e7-becf-005056881c6b')
        {
            $params['ServiceType'] = 'DoorsWarehouse';
        }

//        print_r($sender_info);
//        echo "<br />---------------<br />";
//        print_r($recipient);
//        echo "<br />---------------<br />";
//        print_r($params);
//        echo "<br />---------------<br />";

        $result = $np->newInternetDocument($sender_info, $recipient, $params);
//        print_r($result);
//        echo "<br />---------------<br />";
//        exit();

        if(isset($result['data'][0]['IntDocNumber']) and $result['data'][0]['IntDocNumber'])
        {
            foreach ($data['orderitems'] as $item)
            {
                $item->state_id = 5;
                $item->save();

                $log = ORM::factory('OrderitemLog');
                $log
                    ->set('orderitem_id', $item->id)
                    ->set('state_id', 5)
                    ->set('date_time', date('Y-m-d H:i:s'))
                    ->set('user_id', Auth::instance()->get_user()->id)
                    ->save();

                $ttn = ORM::factory('NpTtns');
                $ttn
                    ->set('orderitem_id', $item->id)
                    ->set('summ', $platezh)
                    ->set('ttn', $result['data'][0]['IntDocNumber'])
                    ->set('ref', $result['data'][0]['Ref'])
                    ->set('time', date('Y-m-d H:i:s'))
                    ->save();
            }

//            ready order
            Admin::check_ready_order($order->id);

            $text = $result['data'][0]['IntDocNumber']." Новая почта \nulc.com.ua\nulc.com.ua@gmail.com\n(098) 092-82-08";
            Sms::send($text, 'Доставка', $phone);

            $url = $np->printMarkings($result['data'][0]['Ref'], 'pdf_link');
            $link = $url['data'][0];

            $data_result = [];
            $data_result['url'] = $link;
            $data_result['error'] = 0;

            echo json_encode($data_result);
            exit();
        }

        elseif (isset($result['errors'][0]) AND $result['errors'][0])
        {
            $data_result = [];
            $data_result['url'] = 0;
            $data_result['error'] = $result['errors'][0];

            echo json_encode($data_result);
            exit();
        }
    }

    public function action_create_ttn_doc(){

        $this->auto_render = FALSE;
        $this->is_ajax = TRUE;

        $id = $_POST['id'];

        $np = new NovaPoshtaApi2(
            '6a8ca3163492bb644bc33dde1265f6cd',
            'ua',
            FALSE,
            'curl'
        );

        $orderitem = ORM::factory('Orderitem')->where('id', '=', $id)->find();

        if($orderitem->ttn->ref)
        {
            $url = $np->printMarkings($orderitem->ttn->ref, 'pdf_link');
            $link = $url['data'][0];

            $data_result = [];
            $data_result['url'] = $link;
            $data_result['error'] = 0;
        }

        else{
            $data_result = [];
            $data_result['url'] = 0;
            $data_result['error'] = "Ошибка, ттн нет";
        }



        echo json_encode($data_result);
        exit();
    }

    public function action_create_cities()
    {
        $query ="SELECT * FROM np_areas";
        $results = DB::query(Database::SELECT,$query)->execute('tecdoc_new')->as_array();

        $np = new LisDev\Delivery\NovaPoshtaApi2(
            '6a8ca3163492bb644bc33dde1265f6cd',
            'ua',
            FALSE,
            'curl'
        );

        foreach ($results as $result)
        {
            $cities = $this->get_city($result['name'], $np);
            print_r($cities);
            foreach ($cities['data'][0] as $city)
            {
                $create_city = ORM::factory('NpCities');
                $create_city
                    ->set('area_id', $result['id'])
                    ->set('name', $city['DescriptionRu'])
                    ->set('ref', $city['Ref'])
                    ->save();
            }
        }
    }

    public function get_best_match_item_tehnomir($items, $delivery_days, $amount){

        $temp = array();
        foreach ($items AS $key => $item)
        {
            if (($item->delivery > $delivery_days)||($item->amount < $amount)) { unset($items[$key]);} //
            else{ if( $item->amount == 0) $item->amount = 20; }


        }
//        echo $delivery_days;
//        echo "<br><br><br><br><br>";
//        foreach ($items AS $key => $item){
//            echo $item->delivery;
//            echo "<br>";
//            echo $item->amount;
//            echo "<br>";
//            echo $item->get_price_for_client();
//            echo "<br>";echo "||||||";
//        }
//        exit();

        foreach ($items AS $item){
            $temp[$item->part_id][] = array(
                'id' => $item->id,
                'price' => $item->get_price_for_client(),
                'delivery' => $item->delivery,
                'amount' => $item->amount,
                //'good_delivery' => $delivery_days,
            );
        }
        $result = array();
        foreach ($temp AS $part_id => $array){
            $result = $this->smart_sort_tehnomir($array);
        }

        foreach ($items AS $key => $item){
            if (!in_array($item->id, $result)) unset($items[$key]);
        }
//        var_dump($item);
//        exit();
        return $items;
    }
    public function smart_sort_tehnomir($array){

        usort($array, 'sort_by_price_tehnomir');
        $price = $array[0]['id'];

        $export = array(
            $price
        );

        return $export;
    }

    protected function get_city($name, $np)
    {
        $cities = $np->getCity(0, $name);
        if(empty($cities['data'][0]))
        {
            $cities = $this->get_city($name, $np);
        }
        return $cities;
    }

}
function sort_by_price_tehnomir($a, $b)
{
    if ($a['price'] == $b['price'] AND $a['delivery'] == $b['delivery']) return 0;
    elseif ($a['price'] == $b['price'])  return ($a['delivery'] > $b['delivery']) ? 1:-1;
    return ($a['price'] > $b['price']) ? 1:-1;
}




