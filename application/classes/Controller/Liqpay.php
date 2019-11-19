<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Liqpay extends Controller_Application
{
    public function action_checkout()
    {
        $amount = (int) $this->request->post('amount');

        if (HTTP_Request::POST !== $this->request->method() || empty($amount)) {
            return Controller::redirect('liqpay/prepare');
        }

        $this->template->content = View::factory('liqpay/checkout')
            ->bind('signature', $signature)
            ->bind('data', $data);

        $this->template->title = 'Пополнить кошелёк';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $params = [
            'action'      => 'pay',
            'currency'    => 'UAH',
            'description' => 'Пополнение личного кошелька',
            'order_id'    => 'order_id_1',
            'version'     => '3',
            'amount'      => $amount,
        ];

        $liqpay = new Libs_Liqpay();
        $liqData = $liqpay->getData($params);
        $data = $liqData['data'];
        $signature = $liqData['signature'];
    }

    public function action_prepare()
    {
        $this->template->content = View::factory('liqpay/prepare');

        $this->template->title = 'Пополнить кошелёк';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
    }

    public function action_order_pay()
    {
        $this->template->content = View::factory('liqpay/checkout')
            ->bind('signature', $signature)
            ->bind('data', $data)
            ->bind('partialPayment', $partialPayment)
            ->bind('orderId', $orderId);

        $this->template->title = 'Пополнить кошелёк';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $orderId = $this->request->param('orderId');
        if (empty($orderId)) {
            return Controller::redirect('orders/add');
        }

        $order = ORM::factory('Order', $orderId);
        $partialPayment = (!empty($order->partial_payment) && ($this->request->query('full') != 1)) ? $order->partial_payment : null;

        if (!empty($partialPayment)) {
            $resultCost = $partialPayment;
        } else {
            $cost = 0;
            foreach ($order->orderitems->find_all()->as_array() as $item) {
                $cost += $item->sale_per_unit * $item->amount;
            }

            $clientPayments = ORM::factory('ClientPayment')
                ->where('order_id', '=', $order->id)
                ->find_all()
                ->as_array();

            foreach ($clientPayments as $clientPayment) {
                $cost -= $clientPayment->value;
            }

            $orderitems = ORM::factory('Orderitem')
                ->with('order')
                ->where('order.client_id', '=', $order->client_id)
                ->and_where('state_id', 'NOT IN', [15, 39])
                ->find_all()
                ->as_array();

            $totalCost = 0;
            foreach ($orderitems as $orderitem) {
                $totalCost += $orderitem->sale_per_unit * $orderitem->amount;
            }

            foreach ($clientPayments as $clientPayment) {
                $totalCost -= $clientPayment->value;
            }

            if (round($cost) > round($totalCost)) {
                $resultCost = $totalCost;
            } elseif (round($cost) > 0) {
                $resultCost = $cost;
            } elseif (round($totalCost) > 0) {
                $resultCost = $totalCost;
            }
        }

        if (isset($resultCost)) {
            // Комиссия банка 3,25%
            $resultCost += round($resultCost / 100 * 3.25, 2);
            $params = [
                'action'      => 'pay',
                'currency'    => 'UAH',
                'description' => 'Оплата заказа #' . $orderId,
                'order_id'    => 'order_id_' . time() . '_' . $orderId,
                'version'     => '3',
                'amount'      => $resultCost,
                'server_url'  => Helper_Url::createUrl('liqpay/response_by_order'),
            ];

            $liqpay = new Libs_Liqpay();
            $liqData = $liqpay->getData($params);
            $data = $liqData['data'];
            $signature = $liqData['signature'];
        } else {
            $data = null;
            $signature = null;
        }
    }

    public function action_response_by_order()
    {
        if (HTTP_Request::POST !== $this->request->method()) exit();

        $responses = json_decode(base64_decode($this->request->post('data')));
        $file = '/tmp/liqpay_response.txt';
        $current = file_get_contents($file);
        $current .= date('d.m.Y H:i:s') . "\n" . json_encode($responses) . "\n\n";
        file_put_contents($file, $current);

        $liqpay = new Libs_Liqpay();
        $response = $liqpay->getPaymentData($this->request->post('data'), $this->request->post('signature'));
        if (in_array($response->status, ['success', 'wait_accept'])) {
            $paid = ORM::factory('ClientPayment')
                ->where('liqpay_order_id', '=', $response->order_id)
                ->find_all()
                ->as_array();
            if (!empty($paid)) exit();

            preg_match('/(?<orderId>\d+)$/', $response->order_id, $respData);
            $order = ORM::factory('Order', $respData['orderId']);

            $clientPayment = ORM::factory('ClientPayment');

            $amount = $response->amount;
            $amount -= $amount / 100 * 3.25;
            $amount = round(ceil($amount * 100) / 100,2);

            // Если поступивший платеж равен сумме частичной оплате по заказу, скидываем частиную оплату
            if (round($amount) == round($order->partial_payment)) {
                $order->partial_payment = null;
                $order->save();
            }

            $clientPayment->values([
                'order_id'        => $order->id,
                'user_id'         => Libs_Liqpay::SYSTEM_USER_ID,
                'client_id'       => $order->client_id,
                'value'           => $amount,
                'date_time'       => date('Y-m-d H:i:s'),
                'liqpay_order_id' => $response->order_id,
                'manager_got_acquainted' => 0,
            ]);

            $clientPayment->save();
        }
        exit();
    }
}
