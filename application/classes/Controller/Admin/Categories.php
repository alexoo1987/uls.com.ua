<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Categories extends Controller_Admin_Application {

//new action edit
    public function action_newedit()
    {
        $id = $this->request->param('id');

        if(empty($id)) Controller::redirect('categories');

        $this->template->content = View::factory('admin/categories/form')
            ->bind('data', $data)
            ->bind('array_td_id', $all_id);

        $data = $this->tecdoc->get_categories_tree();

        $query = "
			SELECT ga_tecdoc_id 
            FROM category_to_tecdoc
            WHERE category_id = ".$id."
		";

        $array_td_id = DB::query(Database::SELECT,$query)->execute('tecdoc')->as_array();
//        var_dump($array_td_id);
//        exit();
        $all_id = array_column($array_td_id,'ga_tecdoc_id');

        if (HTTP_Request::POST == $this->request->method())
        {
            try {
                $query = "
                    DELETE  
                    FROM category_to_tecdoc
                    WHERE category_id = ".$id."
                ";
                DB::query(Database::DELETE,$query)->execute('tecdoc');
                foreach (array_unique($_POST['category_tecdoc_id']) as $post=>$key)
                {

                    DB::insert('category_to_tecdoc', array('category_id', 'ga_tecdoc_id',))
                        ->values(array($id,$key))->execute();
                }
                Controller::redirect('admin/categories/');
            } catch (ORM_Validation_Exception $e) {

                $data = $_POST;

                // Set failure message
                $message = 'Исправьте ошибки!';

                // Set errors using custom messages
                $errors = $e->errors('models');

            }

        }

        $this->template->title = 'Редактировать';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
    }
// end of new actions
	
	public function action_index() {
		if(!ORM::factory('Permission')->checkPermission('tecdoc')) Controller::redirect('admin');
		
		$this->template->content = View::factory('admin/categories/list')
			->bind('filters', $filters)
			->bind('managers', $managers)
			->bind('pagination', $pagination)
			->bind('tree_list', $tree_list)
			->bind('categories', $categories);
			
		$this->template->title = 'Категории';
		$this->template->description = '';
		$this->template->keywords = '';
		$this->template->author = '';
		
		$categories_orm = ORM::factory('Category');
		$categories_orm->reset(FALSE);

		$categories = $categories_orm->where('level', '=', 0)->order_by('id')->find_all()->as_array();

		$this->template->scripts[] = 'jquery-ui-1.10.4.custom.min';

		$tecdoc = Model::factory('Tecdoc');
		$tree_list_tmp = $tecdoc->get_generic_articles();
		$tree_list = array();
		if($tree_list_tmp) {
			foreach ($tree_list_tmp as $row) {
				$tree_list[$row['id']] = $row['name']." (".$row['assembly'].")";
			}
		}
	}

	public function action_edit() {
		$id = $this->request->param('id');
		if(empty($id)) Controller::redirect('categories');
		
		$this->template->content = View::factory('admin/categories/form')
			->bind('errors', $errors)
			->bind('message', $message)
			->bind('tree_list', $tree_list)
			->bind('data', $data);
			
        $this->template->title = 'Редактировать';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		$category = ORM::factory('Category')->where('id', '=', $id)->find();
		$data = array();
		$data = $category->as_array();
		$data['tecdoc_ids'] = explode(',', $data['tecdoc_ids']);
        var_dump($data);
        echo "<br>";

		$tecdoc = Model::factory('Tecdoc');
		$tree_list_tmp = $tecdoc->get_generic_articles();
        var_dump($tree_list_tmp);
        exit();
		$tree_list = array();
		if($tree_list_tmp) {
			foreach ($tree_list_tmp as $row) {
				$tree_list[$row['id']] = $row['name']." (".$row['assembly'].")";
			}
		}
		
		
		if (HTTP_Request::POST == $this->request->method()) 
		{			
			try {
				if(!empty($_POST['tecdoc_ids']) && is_array($_POST['tecdoc_ids'])) {
					$category->tecdoc_ids = implode(',', $_POST['tecdoc_ids']);
				}
				else {
					$category->tecdoc_ids = "";
				}
				
				$category->save();
				
				// Reset values so form is not sticky
				$_POST = array();
				
				Controller::redirect('admin/categories/index/');
			} catch (ORM_Validation_Exception $e) {
				$data = $_POST;
				// Set failure message
				$message = 'Исправьте ошибки!';
				
				// Set errors using custom messages
				$errors = $e->errors('models');
			}
		}
		
		$this->template->styles[] = 'chosen/chosen.min';
		$this->template->scripts[] = 'chosen/chosen.jquery.min';//chosen
		// $this->template->scripts[] = 'cacomplete';
		$this->template->scripts[] = 'common/categories_form';
	}
	
	public function action_set_categories() {
		if(!ORM::factory('Permission')->checkPermission('tecdoc')) Controller::redirect('admin');
		$this->auto_render = FALSE;
		$tecdoc = Model::factory('Tecdoc');

		$categories_orm = ORM::factory('Category');
		$categories = $categories_orm->where('level', '=', 2)->order_by('id')->find_all()->as_array();

		foreach ($categories as $category) {
			$category->tecdoc_ids = "";

			$result = $tecdoc->get_generic_articles($category->name);
			if($result) {
				$tecdoc_arr = array();
				foreach($result as $k=>$v) {
				    $tecdoc_arr[] = $v['id'];
				}
				$tecdoc_ids = "";
				$tecdoc_ids = implode(',', $tecdoc_arr);

				$category->tecdoc_ids = $tecdoc_ids;
			}
			$category->save();

			echo $category->name.': '.$category->tecdoc_ids.'<br>';
		}
	}
	
	public function action_get() {
		if(!ORM::factory('Permission')->checkPermission('tecdoc')) Controller::redirect('admin');
		DB::delete('categories')->execute();
		$tecdoc = Model::factory('Tecdoc');

		$this->auto_render = FALSE;
		
		$page = Htmlparser::htmlToXml(Htmlparser::http("http://dok.dbroker.com.ua/"));

		$cats = $page->xpath("//ul[contains(@class, 'main_ul')]/li");
		foreach($cats as $cat) {
			if((string)$cat->a['href'] == '/') continue;
			echo (string)$cat->a[0].'<br>';
			$category = ORM::factory('Category');
			$category->name = $cat->a[0];
			$category->parent_id = NULL;
			$category->level = 0;

			// $result = $tecdoc->get_tree_by_name($cat->a[0]);
			// if($result) {
			// 	$tecdoc_ids = "";
			// 	$tecdoc_ids = join(',', array_values($result));

			// 	$category->tecdoc_ids = $tecdoc_ids;
			// }
			$category->save();

			foreach($cat->xpath("./ul/li") as $cat2) {
				foreach($cat2->xpath("./ul/li") as $cat3) {
					if($cat3['class'] == 'podrazdel') {
						$category2 = ORM::factory('Category');
						$category2->name = $cat3->a[0];
						$category2->parent_id = $category->id;
						$category2->level = 1;

						// $result = $tecdoc->get_tree_by_name($cat3->a[0]);
						// if($result) {
						// 	$tecdoc_ids = "";
						// 	$tecdoc_ids = join(',', array_values($result));

						// 	$category2->tecdoc_ids = $tecdoc_ids;
						// }
						$category2->save();
						echo '|___'.(string)$cat3->a[0].'<br>';
					} else {
						$category3 = ORM::factory('Category');
						$category3->name = $cat3->a[0];
						$category3->parent_id = $category2->id;
						$category3->level = 2;

						$result = $tecdoc->get_tree_by_name($cat3->a[0]);
						if($result) {
							$tecdoc_arr = array();
							foreach($result as $k=>$v) {
							    $tecdoc_arr[$k] = $v['id'];
							}
							$tecdoc_ids = "";
							$tecdoc_ids = join(',', $tecdoc_arr);

							$category3->tecdoc_ids = $tecdoc_ids;
						}
						$category3->save();
						echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|___'.(string)$cat3->a[0].'<br>';
					}
				}
			}
		}

		//Controller::redirect('admin/categories');
	}
}



class Validation_Exception extends Exception {};