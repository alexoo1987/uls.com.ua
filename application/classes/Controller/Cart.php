<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Cart extends Controller_Application {

	public function action_add()
	{
        if(ORM::factory('Client')->logged_in()) {
            //return Controller::redirect('authorization/login?order_add=true');
            $guest = false;
        }else{
            $guest = true;
        }
		$this->auto_render = FALSE;
		$content = View::factory('cart/add')
			->bind('errors', $errors)
			->bind('current_url', $current_url)
			->bind('message', $message)
			->bind('priceitem', $priceitem)
            ->bind('number', $number)
            ->bind('guest', $guest)
			->bind('data', $data);
		
		if(!empty($_GET['price_id'])) $price_id = $_GET['price_id'];
		else {
			if(!empty($_SERVER['HTTP_REFERER'])) {
				return Controller::redirect($_SERVER['HTTP_REFERER']);
			}
			return Controller::redirect('/');
		}
		
		$data['priceitem_id'] = $price_id;
		
		if(is_numeric($price_id)) {
			$priceitem = ORM::factory('Priceitem')->where('id', '=', $price_id)->find();
		} else {
			$json_array = json_decode(base64_decode(str_replace('_','=',$price_id)), true);
			$priceitem = ORM::factory('Priceitem')->get_from_arr($json_array);
		}
		
		if (HTTP_Request::POST == $this->request->method()) 
		{
			$priceitem_id = $this->request->post('priceitem_id');
			$qty = $this->request->post('qty');
            $number = $this->request->post('number');
			
			Cart::instance()->add($priceitem_id, $qty, $number);

			$cart_result = Cart::instance()->get_count();
			echo json_encode(array('status' => 'success', 'cart_count' => $cart_result['qty'], 'cart_price' => $cart_result['price']));
			return;
		}

		$data['redirect_to'] = "cart/show"."?cart_add=true";
		$current_url = URL::base().$this->request->uri().URL::query();

		$this->response->body($content);
	}
	
	public function action_show() {
        if(ORM::factory('Client')->logged_in()) {
            //return Controller::redirect('authorization/login?order_add=true');
            $guest = false;
        }else{
            $guest = true;
        }
		$this->template->content = View::factory('cart/show')
			->bind('errors', $errors)
			->bind('message', $message)
			->bind('items', $items)
            ->bind('guest', $guest)
			->bind('data', $data);

		$this->template->title = 'Корзина покупок';
		$this->template->description = '';
		$this->template->keywords = '';
		$this->template->author = '';
		
		$items = array();

		foreach(Cart::instance()->content as $item) {

			if(is_numeric($item['id'])) {
				$priceitem = ORM::factory('Priceitem', $item['id']);
			} else {
				$json_array = json_decode(base64_decode(str_replace('_','=',$item['id'])), true);
				$priceitem = ORM::factory('Priceitem')->get_from_arr($json_array);
			}
			$items[] = array(
				'id' => $item['id'],
				'priceitem' => $priceitem,
				'qty' => $item['qty'],
                'number' => $item['number']
			);
		}
	}
	
	public function action_remove() {
		$this->auto_render = FALSE;
        $this->template->title = 'Корзина';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		if(!empty($_GET['cart_id'])) {
			$cart_id = $_GET['cart_id'];
			Cart::instance()->delete($cart_id);
		}
		
		$cart_result = Cart::instance()->get_count();
		echo json_encode(array('status' => 'success', 'cart_count' => $cart_result['qty'], 'cart_price' => $cart_result['price']));
		return;
	}
}
