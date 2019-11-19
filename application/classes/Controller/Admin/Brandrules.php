<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Brandrules extends Controller_Admin_Application {
	private $types = array('delete_start' => "Убрать в начале", 'delete_end' => "Убрать в конце");
	
	public function action_list() {
		if(!ORM::factory('Permission')->checkPermission('manage_operations')) Controller::redirect('admin');
		
		$id = $this->request->param('id');
		$this->template->content = View::factory('admin/brandrules/list')
			->bind('brandrules', $brandrules)
			->bind('types', $this->types)
			->bind('brand_id', $brand_id);
		$this->template->title = 'Правила';
		$this->template->description = '';
		$this->template->keywords = '';
		$this->template->author = '';
		
		$brandrules = ORM::factory('Brandrule')->where('brand_id', '=', $id)->find_all()->as_array();
		$brand_id = $id;
		
		$this->template->scripts[] = "common/brandrules_list";
	}
	
	public function action_add() {
		if(!ORM::factory('Permission')->checkPermission('manage_operations')) Controller::redirect('admin');
		
		$brand_id = $this->request->param('id');
		if(empty($brand_id)) Controller::redirect('admin/brandrules/list');
		
		$this->template->content = View::factory('admin/brandrules/form')
			->bind('permissions', $permissions)
			->bind('errors', $errors)
			->bind('message', $message)
			->bind('types', $this->types)
			->bind('data', $data);
			
        $this->template->title = 'Добавить правило';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
			
		if (HTTP_Request::POST == $this->request->method()) 
		{			
			try {
				$brandrule = ORM::factory('Brandrule');
				$brandrule->values($this->request->post(), array(
					'type',
					'value',
				));
				$brandrule->brand_id = $brand_id;
				$brandrule->save();
				
				// Reset values so form is not sticky
				$_POST = array();
				
				Controller::redirect('admin/brandrules/list/'.$brand_id);
			} catch (ORM_Validation_Exception $e) {
				$data = $_POST;
				// Set failure message
				$message = 'Исправте ошибки!';
				
				// Set errors using custom messages
				$errors = $e->errors('models');
			}
		}
	}
	
	public function action_edit() {
		if(!ORM::factory('Permission')->checkPermission('manage_operations')) Controller::redirect('admin');
		
		$id = $this->request->param('id');
		if(empty($id)) Controller::redirect('admin/brandrules/list');
		
		$this->template->content = View::factory('admin/brandrules/form')
			->bind('permissions', $permissions)
			->bind('errors', $errors)
			->bind('message', $message)
			->bind('types', $this->types)
			->bind('data', $data);
			
        $this->template->title = 'Редактирование правила';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		$brandrule = ORM::factory('Brandrule')->where('id', '=', $id)->find();
		$data = array();
		$data['type'] = $brandrule->type;
		$data['value'] = $brandrule->value;
		
		if (HTTP_Request::POST == $this->request->method()) 
		{			
			try {
				$brandrule->values($this->request->post(), array(
					'type',
					'value',
				));
				$brandrule->save();
				
				// Reset values so form is not sticky
				$_POST = array();
				
				Controller::redirect('admin/brandrules/list/'.$brandrule->brand_id);
			} catch (ORM_Validation_Exception $e) {
				$data = $_POST;
				// Set failure message
				$message = 'Исправте ошибки!';
				
				// Set errors using custom messages
				$errors = $e->errors('models');
			}
		}
	}
	
	public function action_delete() {
		if(!ORM::factory('Permission')->checkPermission('manage_operations')) Controller::redirect('admin');
		
        $this->template->title = '';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		$id = $this->request->param('id');
		if(!empty($id)) {
			$brandrule = ORM::factory('Brandrule')->where('id', '=', $id)->find();
			$brand_id = $brandrule->brand_id;
			$brandrule->delete();
		}
		
		Controller::redirect('admin/brandrules/list/'.$brand_id);
	}
	
	public function action_apply() {
		if(!ORM::factory('Permission')->checkPermission('manage_operations')) Controller::redirect('admin');
		
		$this->auto_render = FALSE;
		$tecdoc = Model::factory('Tecdoc');
		
		$id = $this->request->param('id');
		if(!empty($id)) {
			$brand = ORM::factory('Brand')->where('id', '=', $id)->find();
			$parts = ORM::factory('Part')->where('brand', '=', $brand->brand)->find_all()->as_array();
			
			foreach($parts as $part) {
				$article_long = $brand->apply_rules($part->article_long);
				$article = Article::get_short_article($article_long);
				
				if($article == $part->article) continue;
				
				$part_in_db = ORM::factory('Part')->where('article', '=', $article)->and_where('brand', '=', $brand->brand)->find();
				if(!empty($part_in_db->id)) {
					DB::update('priceitems')->set(array('part_id'=>$part_in_db->id))->where('part_id','=',$part->id)->execute();
					DB::update('crosses')->set(array('from_id'=>$part_in_db->id))->where('from_id','=',$part->id)->execute();
					DB::update('crosses')->set(array('to_id'=>$part_in_db->id))->where('to_id','=',$part->id)->execute();
					$part->delete();
				} else {
					$part->article_long = $article_long;
					$part->article = $article;
					
					$tecdoc_articles = $tecdoc->get_articles($part->article, $part->brand);
					if($tecdoc_articles) {
						$part->set('article_long', $tecdoc_articles[0]['article_nr']);
						$part->set('name', $tecdoc_articles[0]['description']);
						
						$part->set('tecdoc_id', $tecdoc_articles[0]['id']);
					}
					$part->save();
				}
			}
		}
		
		Controller::redirect('admin/brandrules/list/'.$id);
	}


} // End Admin_User



class Validation_Exception extends Exception {};
