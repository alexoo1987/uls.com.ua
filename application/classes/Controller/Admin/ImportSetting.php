<?php defined('SYSPATH') or die('No direct script access.');
ini_set('memory_limit', '512M');

class Controller_Admin_ImportSetting extends Controller_Admin_Application
{

	public function action_index()
	{
		if (!ORM::factory('Permission')->checkPermission('suppliers')) Controller::redirect('admin');

		$this->template->title = 'Автоматическая загрузка прайсов';
		$this->template->description = '';
		$this->template->keywords = '';
		$this->template->author = '';

		$this->template->content = View::factory('admin/import_setting/index')
			->bind('suppliers', $suppliers)
			->bind('data', $data);

		$suppliers = array('' => '---');

		$data = ORM::factory('ImportSetting')->order_by('last_date', 'desc')->find_all()->as_array();

		foreach (ORM::factory('Supplier')->find_all()->as_array() as $supplier) {
			$suppliers[$supplier->id] = $supplier->name;
		}
	}

	public function action_create()
	{
		if (!ORM::factory('Permission')->checkPermission('suppliers')) Controller::redirect('admin');

		$this->template->title = 'Автоматическая загрузка прайсов::Создать';
		$this->template->description = '';
		$this->template->keywords = '';
		$this->template->author = '';

		$this->template->content = View::factory('admin/import_setting/create')
			->bind('suppliers', $suppliers)
			->bind('currencies', $currencies);


		$suppliers = array('' => '---');

		foreach (ORM::factory('Supplier')->find_all()->as_array() as $supplier) {
			$suppliers[$supplier->id] = $supplier->name;
		}

		$currencies = array('' => '---');

		foreach (ORM::factory('Currency')->find_all()->as_array() as $currency) {
			$currencies[$currency->id] = $currency->name;
		}

		if (HTTP_Request::POST == $this->request->method()) {

			$data = array();
			$data['start']['dayOfWeek'] = $this->request->post('dayOfWeek');
			$data['start']['time'] = $this->request->post('time');
			$data['email']['from'] = $this->request->post('from');
			$data['email']['subject'] = $this->request->post('subject');
			$data['email']['ext'] = $this->request->post('ext');
			$data['columns']['article'] = $this->request->post('article');
			$data['columns']['brand'] = $this->request->post('brand');
			$data['columns']['price'] = $this->request->post('price');
			$data['columns']['name'] = $this->request->post('name');
			$data['currency_id'] = $this->request->post('currency_id');
			$data['firstLine'] = $this->request->post('firstLine');
			$data['encoding'] = $this->request->post('encoding');

			$counts = $this->request->post('count');
			$deliveries = $this->request->post('delivery');
			$deliveries_const = $this->request->post('delivery_const');

			foreach ($counts AS $key => $count) {
				$temp = array();
				if ($deliveries[$key]) $temp['delivery_column'] = $deliveries[$key];
				if ($deliveries_const[$key]) $temp['delivery_const'] = $deliveries_const[$key];
				$temp['count'] = $count;

				$data['columns']['variants'][] = $temp;
			}

			$data = json_encode($data, JSON_UNESCAPED_UNICODE);
			$setting = ORM::factory('ImportSetting');
			$setting
				->set('supplier_id', $this->request->post('supplier_id'))
				->set('setting', $data)
                ->set('comment', $this->request->post('comment'))
				->save();

			Controller::redirect('admin/importSetting/index');
		}
	}

	public function action_edit()
	{
		if (!ORM::factory('Permission')->checkPermission('suppliers')) Controller::redirect('admin');

		$this->template->title = 'Автоматическая загрузка прайсов::Редактировать';
		$this->template->description = '';
		$this->template->keywords = '';
		$this->template->author = '';

		$this->template->content = View::factory('admin/import_setting/edit')
			->bind('object', $object)
			->bind('suppliers', $suppliers)
			->bind('currencies', $currencies);


		$suppliers = array('' => '---');

		foreach (ORM::factory('Supplier')->find_all()->as_array() as $supplier) {
			$suppliers[$supplier->id] = $supplier->name;
		}

		$currencies = array('' => '---');

		foreach (ORM::factory('Currency')->find_all()->as_array() as $currency) {
			$currencies[$currency->id] = $currency->name;
		}

		$id = $this->request->param('id');

		$object = ORM::factory('ImportSetting')->where('id', '=', $id)->find();

		if (HTTP_Request::POST == $this->request->method()) {

			$data = array();
			$data['start']['dayOfWeek'] = $this->request->post('dayOfWeek');
			$data['start']['time'] = $this->request->post('time');
			$data['email']['from'] = $this->request->post('from');
			$data['email']['subject'] = $this->request->post('subject');
			$data['email']['ext'] = $this->request->post('ext');
			$data['columns']['article'] = $this->request->post('article');
			$data['columns']['brand'] = $this->request->post('brand');
			$data['columns']['price'] = $this->request->post('price');
			$data['columns']['name'] = $this->request->post('name');
			$data['currency_id'] = $this->request->post('currency_id');
			$data['firstLine'] = $this->request->post('firstLine');
			$data['encoding'] = $this->request->post('encoding');

			$counts = $this->request->post('count');
			$deliveries = $this->request->post('delivery');
			$deliveries_const = $this->request->post('delivery_const');


			foreach ($counts AS $key => $count) {
				$temp = array();
				if ($deliveries[$key]) $temp['delivery_column'] = $deliveries[$key];
				if ($deliveries_const[$key]) $temp['delivery_const'] = $deliveries_const[$key];
				$temp['count'] = $count;

				$data['columns']['variants'][] = $temp;
			}

			$data = json_encode($data, JSON_UNESCAPED_UNICODE);

			$object
				->set('supplier_id', $this->request->post('supplier_id'))
				->set('setting', $data)
				->set('comment', $this->request->post('comment'))
				->save();

			Controller::redirect('admin/importSetting/index');
		}

		$object->setting = json_decode($object->setting);

	}

	public function action_delete()
	{

		$id = $this->request->param('id');

		ORM::factory('ImportSetting')->where('id', '=', $id)->find()->delete();

		Controller::redirect('admin/importSetting/index');
	}


	public function action_run()
	{

		exec('sudo /var/cron/parser.sh >> /dev/null 2>/dev/null &');

		Controller::redirect('admin/importSetting/index?run=1');
	}


} // End Admin_User



class Validation_Exception extends Exception {};
