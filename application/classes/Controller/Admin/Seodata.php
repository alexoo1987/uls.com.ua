<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Seodata extends Controller_Admin_Application {
	
	public function action_index()
	{
		if(!ORM::factory('Permission')->checkPermission('manage_seodata')) Controller::redirect('admin');
		
		$this->template->content = View::factory('admin/seodata/list')
			->bind('filters', $filters)
			->bind('pagination', $pagination)
			->bind('seodata', $seodata);
		
		$this->template->title = 'SEO данные';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		$seodata_orm = ORM::factory('Seodata');
		$seodata_orm->reset(FALSE);
		
		if(!empty($_GET['seo_identifier'])) {
			$filters['seo_identifier'] = $_GET['seo_identifier'];
			$seodata_orm = $seodata_orm->and_where('seo_identifier', 'LIKE', $filters['seo_identifier'].'%');
		}
		
		$count = $seodata_orm->count_all();
		
		$pagination = Pagination::factory(array('total_items' => $count))->route_params(array(
		  'controller' =>  'seodata',
		  'action' =>  'index'
		));
		$seodata = $seodata_orm->limit($pagination->items_per_page)->offset($pagination->offset)
			->find_all()->as_array();
		
		$this->template->scripts[] = "common/seodata_list";
	}
	
	public function action_add() {
		if(!ORM::factory('Permission')->checkPermission('manage_seodata')) Controller::redirect('admin');
		
		$this->template->content = View::factory('admin/seodata/form')
			->bind('errors', $errors)
			->bind('message', $message)
			->bind('data', $data);
			
		$this->template->title = 'SEO данные';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
			
		if (HTTP_Request::POST == $this->request->method()) 
		{
            $post = $this->request->post();
            $post['noindex'] = Arr::get($post, 'noindex', 0);
			try {
				$page = ORM::factory('Seodata');
				$page->values($post, array(
					'seo_identifier',
					'title',
//					'keywords',
					'description',
					'h1',
					'content',
					'noindex',
                    'canonical_address',
                    'section_titles',
				));
				$page->save();
				
				// Reset values so form is not sticky
				$_POST = array();
				
				Controller::redirect('admin/seodata');
			} catch (ORM_Validation_Exception $e) {
				$data = $_POST;
				// Set failure message
				$message = 'Fix errors!!';
				
				// Set errors using custom messages
				$errors = $e->errors('models');
			}
		}
		
		$this->template->scripts[] = 'bootstrap.validate';
		$this->template->scripts[] = 'bootstrap.validate.ru';
		$this->template->scripts[] = 'ckeditor/ckeditor';
		$this->template->scripts[] = 'Djenx.Explorer/djenx-explorer';
		$this->template->scripts[] = 'common/seodata_form';
	}
	
	public function action_edit() {
		if(!ORM::factory('Permission')->checkPermission('manage_seodata')) Controller::redirect('admin');


		$id = $this->request->param('id');
		if(empty($id)) Controller::redirect('admin/seodata');
		
		$this->template->content = View::factory('admin/seodata/form')
			->bind('errors', $errors)
			->bind('message', $message)
			->bind('data', $data);
			
        $this->template->title = 'SEO данные';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		$page = ORM::factory('Seodata')->where('id', '=', $id)->find();
		$data = $page->as_array();
		
		if (HTTP_Request::POST == $this->request->method()) 
		{
            $post = $this->request->post();
            $post['noindex'] = Arr::get($post, 'noindex', 0);
			try {
				$page->values($post, array(
					'seo_identifier',
					'title',
					'keywords',
					'description',
					'h1',
					'content',
					'noindex',
                    'canonical_address',
                    'section_titles',

				));
				$page->save();
				
				// Reset values so form is not sticky
				$_POST = array();
				
				Controller::redirect('admin/seodata');
			} catch (ORM_Validation_Exception $e) {
				$data = $_POST;
				// Set failure message
				$message = 'Fix errors!!';
				
				// Set errors using custom messages
				$errors = $e->errors('models');
			}
		}
		
		$this->template->scripts[] = 'bootstrap.validate';
		$this->template->scripts[] = 'bootstrap.validate.ru';
		$this->template->scripts[] = 'ckeditor/ckeditor';
		$this->template->scripts[] = 'Djenx.Explorer/djenx-explorer';
		$this->template->scripts[] = 'common/seodata_form';
	}
	
	public function action_delete() {
		if(!ORM::factory('Permission')->checkPermission('manage_seodata')) Controller::redirect('admin');
		
		$this->template->title = 'Administrator';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		$id = $this->request->param('id');
		if(!empty($id)) {
			$page = ORM::factory('Seodata')->where('id', '=', $id)->find();
			
			$page->delete();
		}
		
		Controller::redirect('admin/seodata');
	}
	
	public function action_csv()
	{
		$path = 'uploads/eparts_categories_new.csv';
		$f = fopen($path, 'w');
		
		$parentCatQuery = "SELECT c_p.name, c_p.slug, c_p.id
			FROM categories as c_p
			WHERE parent_id IS NULL";
		$parentCats = DB::query(Database::SELECT, $parentCatQuery)->execute()->as_array();

		foreach ($parentCats as $parentCat)
		{
			$childsCatQuery = "SELECT 
				c_h_2.name, c_h_2.slug 
				FROM categories as c_p
				INNER JOIN categories as c_h ON c_h.parent_id = c_p.id
				INNER JOIN categories as c_h_2 ON c_h_2.parent_id = c_h.id
				WHERE c_h.parent_id = ".$parentCat['id']."";
			$childsCats = DB::query(Database::SELECT, $childsCatQuery)->execute()->as_array();

			$fields = [
				$parentCat['name'],
				'https://ulc.com.ua/katalog/'.$parentCat['slug'],
			];

			array_walk($fields, 'encodeCSV');
			fputcsv($f, $fields, ';');

			foreach ($childsCats as $childCat)
			{
				$fields = [
					$parentCat['name'],
					'https://ulc.com.ua/katalog/'.$parentCat['slug'],
					$childCat['name'],
					'https://ulc.com.ua/katalog/'.$childCat['slug']
				];

				array_walk($fields, 'encodeCSV');
				fputcsv($f, $fields, ';');
			}
		}

		fclose($f);
	}

	public function action_csv_model()
	{
		$tecdoc = Model::factory('NewTecdoc');
		$manufactures = $tecdoc->get_all_manufacture();

		$zip = new ZipArchive();
		$filename = "uploads/eparts_models.zip";
		if(file_exists($filename)) @unlink($filename);

		if ($zip->open($filename, ZIPARCHIVE::CREATE)!==TRUE) {
			exit("Невозможно открыть <$filename>\n");
		}

		foreach ($manufactures as $manufacture)
		{
			$path = 'uploads/eparts_models-'.$manufacture['url'].'.csv';
			$f = fopen($path, 'w');

			$fields = [
				$manufacture['name'],
				'https://ulc.com.ua/katalog/'.$manufacture['url'],
			];
			array_walk($fields, 'encodeCSV');
			fputcsv($f, $fields, ';');

			$models = $tecdoc->get_all_models_for_manufactures_url($manufacture['url']);

			foreach ($models as $model)
			{
				$fields = [
					$manufacture['name'],
					'https://ulc.com.ua/katalog/'.$manufacture['url'],
					$model['model'],
					'https://ulc.com.ua/katalog/'.$manufacture['url'].'/'.$model['url_model'],
				];
				array_walk($fields, 'encodeCSV');
				fputcsv($f, $fields, ';');
				
				$types = $tecdoc->get_all_types_info_by_urls($model['url_model'], $manufacture['url']);
				
				foreach ($types as $type)
				{
					$fields = [
						$manufacture['name'],
						'https://ulc.com.ua/katalog/'.$manufacture['url'],
						$model['model'],
						'https://ulc.com.ua/katalog/'.$manufacture['url'].'/'.$model['url_model'],
						$type['name'],
						'https://ulc.com.ua/katalog/'.$manufacture['url'].'/'.$model['url_model'].'/'.$type['url'],
					];
					array_walk($fields, 'encodeCSV');
					fputcsv($f, $fields, ';');
				}
			}

			fclose($f);
			$zip->addFile('uploads/eparts_models-'.$manufacture['url'].'.csv');
		}

		$zip->close();
	}
	
} // End Admin_Pages
