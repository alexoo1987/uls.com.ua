<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Admin_Pages extends Controller_Admin_Application
{

    public function action_index()
    {

    }

    public function action_edit_content()
    {
        if (!ORM::factory('Permission')->checkPermission('manage_pages')) Controller::redirect('admin');


        $this->template->content = View::factory('admin/pages/edit')
            ->bind('page', $page);

        $id = $this->request->param('id');
        $page = ORM::factory('Page')->where('page_id', '=', $id)->find();


        $this->template->title = 'Administrator::Page editor';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $this->template->styles[] = "south-street/jquery-ui-1.10.4.custom.min";
        $this->template->scripts[] = "jquery-ui-1.10.4.custom.min";

        $this->template->scripts[] = 'context';
        $this->template->scripts[] = 'jquery.htmlClean';
        $this->template->scripts[] = 'ckeditor/ckeditor';
        $this->template->scripts[] = 'common/edit_page';
    }

    public function action_excel()
    {
        $this->template->title = 'SEO Excel generation';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $location = 'uploads/seo/';
        $ext = '.csv';
        $host = URL::base();
        $begin = array("article;","url;","brand;\r\n");
        $end = array("article;","url;","brand;\r\n");
        $file = 'seo_table';
        $articul = ' ';
        $name_article = array();
        $strings = 0;
        $current = 1;
        $my_test = 0;

        ///////////////////// ARTICLES ///////////////////////

        //file_put_contents($location.$file.$current.$ext, $begin);
        # /catalog/article/


        $articles_count = Model::factory('Part')->count_all();
        for ($i = 0; $i < $articles_count; $i += 5000) {
            $articles = Model::factory('Part')->limit(5000)->offset($i)->find_all()->as_array();
	    	foreach ($articles AS $value) {
                try{

                    if ($value->tecdoc_id){
                        $tecdoc = ORM::factory('Tecdoc');
                        $part_tecdoc = $tecdoc->get_part($value->tecdoc_id);
                        if(!empty($part_tecdoc['article_nr']))
                        {
                            $url = Htmlparser::transliterate($part_tecdoc['brand'] . "-" . $part_tecdoc['article_nr'] . "-" . substr($part_tecdoc['description'], 0, 50)) . "-" .$value->id;
                        }
                        else
                        {
                            $url = Htmlparser::transliterate($part_tecdoc['brand'] . "-" . substr($part_tecdoc['description'], 0, 50)) . "-" .$value->id;
                        }

                    }
                    else{
                        if((!empty($value->article_long))&&(!empty($value->brand))&&(!empty($value->name)))
                        {
                            $url = Htmlparser::transliterate($value->brand . "-" . $value->article_long . "-" . substr($value->name, 0, 50)) . "-" . $value->id;
                        }
                        else
                        {
                            if(empty($value->article_long))
                            {
                                if(empty($value->brand))
                                {
                                    if(empty($value->name))
                                    {
                                        $url = $value->id;
                                    }
                                    else
                                    {
                                        $url = Htmlparser::transliterate(substr($value->name, 0, 50)) . "-" . $value->id;
                                    }
                                }
                                else
                                {
                                    if(empty($value->name))
                                    {
                                        $url = Htmlparser::transliterate($value->brand) . "-" . $value->id;
                                    }
                                    else
                                    {
                                        $url = Htmlparser::transliterate($value->brand . "-". substr($value->name, 0, 50)) . "-" . $value->id;
                                    }

                                }
                            }
                            else
                            {
                                if(empty($value->name))
                                {
                                    if(empty($value->brand))
                                    {
                                        $url = Htmlparser::transliterate($value->article_long) . "-" . $value->id;
                                    }
                                    else
                                    {
                                        $url = Htmlparser::transliterate($value->brand . "-" .$value->article_long) . "-" . $value->id;
                                    }
                                }
                                else
                                {
                                    if(empty($value->brand))
                                    {
                                        $url = Htmlparser::transliterate($value->article_long. "-" . substr($value->name, 0, 50)) . "-" . $value->id;
                                    }
                                    else
                                    {
                                        $url = Htmlparser::transliterate($value->brand . "-" . $value->article_long . "-" . substr($value->name, 0, 50)) . "-" . $value->id;
                                    }
                                }
                            }
                        }

                    }
//                    echo $value->id ."      ";
//                    echo $my_test."<br>";
//                    $my_test ++;
                    if(!empty($value->article_long))
                    {
                        $articul = $value->article_long;
                    }
                    else
                    {
                        $articul = '';
                    }
                    if (!empty($value->id)){

                        $syr = $name_article = $value->get_brand()." ".$value->article_long." ".Article::shorten_string($value->name, 3)."";
                        $name_article = str_replace(";","",$syr);
                    }


                    $xml = $name_article.";".$url.";".$articul.";\r\n";

                    file_put_contents($location . $file . $current . $ext, $xml, FILE_APPEND);
                    $strings++;
                    //if 49999 string in file, create new
                    if ($strings == 49999) {
                        //write end
                        file_put_contents($location . $file . $current . $ext, $end, FILE_APPEND);
                        //get next file
                        $current++;
                        //write begin
                        file_put_contents($location . $file . $current . $ext, $begin, FILE_APPEND);
                        //empty string
                        $strings = 0;
                    }
                }
                catch (Exception $e)
                {
                    continue;
                }
            }

        }
        echo $articles_count;

        //Controller::redirect('admin');

    }
	
	public function action_list() { 
		if(!ORM::factory('Permission')->checkPermission('manage_pages')) Controller::redirect('admin');
		
		$this->template->content = View::factory('admin/pages/list')
			->bind('pages', $pages);
		
		$this->template->title = 'Страницы';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		$pages = ORM::factory('Page')->find_all()->as_array();
		
		$this->template->scripts[] = "common/pages_list";
	}
	
	public function action_add() {
		if(!ORM::factory('Permission')->checkPermission('manage_pages')) Controller::redirect('admin');
		
		$this->template->content = View::factory('admin/pages/form')
			->bind('errors', $errors)
			->bind('message', $message)
			->bind('types', $types)
			->bind('data', $data);
			
		$this->template->title = 'Administrator';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
			
		if (HTTP_Request::POST == $this->request->method()) 
		{			
			try {
				$page = ORM::factory('Page');
				$page->values($this->request->post(), array(
					'title',
					'meta_keywords',
					'meta_description',
					'h1_title',
					'active',
					'content',
				));
				$_POST['syn'] = empty($_POST['syn']) ? $_POST['title'] : $_POST['syn'];
				$page->set('syn', URL::title(UTF8::transliterate_to_ascii(Arr::get($_POST, 'syn', NULL))));
				$page->save();
				
				// Reset values so form is not sticky
				$_POST = array();
				
				Controller::redirect('admin/pages/list');
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
		$this->template->scripts[] = 'common/pages_form';
	}
	public function action_change_day(){
        $this->template->title = 'Administrator';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
        $articles_count = Model::factory('Priceitem')->where('supplier_id', '=', 156)->count_all();
        for ($i = 0; $i < $articles_count; $i += 5000) {
            $articles = Model::factory('Priceitem')->where('supplier_id', '=', 156)->limit(5000)->offset($i)->find_all()->as_array();
            //var_dump($articles);
            foreach ($articles as $user_penalty=> $row_penalty)
            {
                $row_penalty->delivery = 8;
                //echo "<br>";
                $row_penalty->save();
//                echo $row_penalty->salary;
            }
        }
//        $articles_count = Model::factory('Priceitem')->where('supplier_id', '=', 165)->count_all();
//        for ($i = 0; $i < $articles_count; $i += 5000) {
//            $articles = Model::factory('Priceitem')->where('supplier_id', '=', 165)->limit(5000)->offset($i)->find_all()->as_array();
//            //var_dump($articles);
//            foreach ($articles as $user_penalty=> $row_penalty)
//            {
//                $row_penalty->delivery = 8;
//                //echo "<br>";
//                $row_penalty->save();
////                echo $row_penalty->salary;
//            }
//        }
//        $articles_count = Model::factory('Priceitem')->where('supplier_id', '=', 166)->count_all();
//        for ($i = 0; $i < $articles_count; $i += 5000) {
//            $articles = Model::factory('Priceitem')->where('supplier_id', '=', 166)->limit(5000)->offset($i)->find_all()->as_array();
//            //var_dump($articles);
//            foreach ($articles as $user_penalty=> $row_penalty)
//            {
//                $row_penalty->delivery = 8;
//                //echo "<br>";
//                $row_penalty->save();
////                echo $row_penalty->salary;
//            }
//        }
//        $articles_count = Model::factory('Priceitem')->where('supplier_id', '=', 167)->count_all();
//        for ($i = 0; $i < $articles_count; $i += 5000) {
//            $articles = Model::factory('Priceitem')->where('supplier_id', '=', 167)->limit(5000)->offset($i)->find_all()->as_array();
//            //var_dump($articles);
//            foreach ($articles as $user_penalty=> $row_penalty)
//            {
//                $row_penalty->delivery = 8;
//                //echo "<br>";
//                $row_penalty->save();
////                echo $row_penalty->salary;
//            }
//        }
//        $articles_count = Model::factory('Priceitem')->where('supplier_id', '=', 168)->count_all();
//        for ($i = 0; $i < $articles_count; $i += 5000) {
//            $articles = Model::factory('Priceitem')->where('supplier_id', '=', 168)->limit(5000)->offset($i)->find_all()->as_array();
//            //var_dump($articles);
//            foreach ($articles as $user_penalty=> $row_penalty)
//            {
//                $row_penalty->delivery = 8;
//                //echo "<br>";
//                $row_penalty->save();
////                echo $row_penalty->salary;
//            }
//        }
//        $articles_count = Model::factory('Priceitem')->where('supplier_id', '=', 169)->count_all();
//        for ($i = 0; $i < $articles_count; $i += 5000) {
//            $articles = Model::factory('Priceitem')->where('supplier_id', '=', 169)->limit(5000)->offset($i)->find_all()->as_array();
//            //var_dump($articles);
//            foreach ($articles as $user_penalty=> $row_penalty)
//            {
//                $row_penalty->delivery = 8;
//                //echo "<br>";
//                $row_penalty->save();
////                echo $row_penalty->salary;
//            }
//        }


    }
	
	public function action_edit() {
		if(!ORM::factory('Permission')->checkPermission('manage_pages')) Controller::redirect('admin');
		
		$id = $this->request->param('id');
		if(empty($id)) Controller::redirect('admin/pages/list');
		
		$this->template->content = View::factory('admin/pages/form')
			->bind('errors', $errors)
			->bind('message', $message)
			->bind('types', $types)
			->bind('data', $data);
			
        $this->template->title = 'Administrator';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		$page = ORM::factory('Page')->where('id', '=', $id)->find();
		$data = array();
		$data['title'] = $page->title;
		$data['syn'] = $page->syn;
		$data['meta_keywords'] = $page->meta_keywords;
		$data['meta_description'] = $page->meta_description;
		$data['h1_title'] = $page->h1_title;
		$data['active'] = $page->active;
		$data['content'] = $page->content;
		
		if (HTTP_Request::POST == $this->request->method()) 
		{			
			try {
				$page->values($this->request->post(), array(
					'title',
					'meta_keywords',
					'meta_description',
					'type_id',
					'h1_title',
					'active',
					'content',
				));
				$_POST['syn'] = empty($_POST['syn']) ? $_POST['title'] : $_POST['syn'];
				$page->set('syn', URL::title(UTF8::transliterate_to_ascii(Arr::get($_POST, 'syn', NULL))));
				$page->save();
				
				// Reset values so form is not sticky
				$_POST = array();
				
				Controller::redirect('admin/pages/list');
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
		$this->template->scripts[] = 'common/pages_form';
	}
	
	public function action_delete() {
		if(!ORM::factory('Permission')->checkPermission('manage_pages')) Controller::redirect('admin');
		
		$this->template->title = 'Administrator';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
		
		$id = $this->request->param('id');
		if(!empty($id)) {
			$page = ORM::factory('Page')->where('id', '=', $id)->find();
			
			$page->delete();
		}
		
		Controller::redirect('admin/pages/list');
	}


	/**
	 * Generate file sitemap.xml in site root
	 */
	public function action_generate_sitemap()
	{
		if (!Auth::instance()->logged_in()) exit();

		$this->auto_render = FALSE;

		$main_file = 'sitemap.xml';
		$location = 'sitemaps/';
		$current = 1;
		$ext = '.xml';
		$host = URL::base();
		$sitemap_index = array();

		//empty sitemap dir
		array_map('unlink', glob($location."*"));

		$begin = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';
		$end = "\n</urlset>";


		//////////////////// CATEGORIES ///////////////////////
		# /catalog/category
		$file = 'sitemap-catalog';
		$strings = 0;
		$categories = Model::factory('Category')->find_all()->as_array(NULL, 'slug');

		file_put_contents($location.$file.$current.$ext, $begin);
		$sitemap_index [] = $location . $file . $current . $ext;
		foreach ($categories AS $one => $category) {
			$xml = "\n<url><loc>" . $host .  "katalog/" . $category . "</loc></url>";
			file_put_contents($location . $file . $current . $ext, $xml, FILE_APPEND);
			$strings++;
			//if 49999 string in file, create new
			if ($strings == 49999) {
				//write end
				file_put_contents($location . $file . $current . $ext, $end, FILE_APPEND);
				//write to sitemap index
				$sitemap_index [] = $location . $file . $current . $ext;
				//get next file
				$current++;
				//write begin
				file_put_contents($location . $file . $current . $ext, $begin, FILE_APPEND);
				//empty string
				$strings = 0;
			}
		}

		//write end
		if ($strings != 0) file_put_contents($location . $file . $current . $ext, $end, FILE_APPEND);

		//////////////////// MANUFACTURERS ///////////////////////
		# /catalog/manufacturer
		$file = 'sitemap-auto-brands';
		$strings = 0;
		$current = 1;
		$tecdoc = Model::factory('Tecdoc');
		$manufacturers = $tecdoc->get_manufacturers(false, false, false, true);

		file_put_contents($location.$file.$current.$ext, $begin);
		$sitemap_index [] = $location . $file . $current . $ext;
		foreach ($manufacturers AS $one => $manufacturer) {
			$xml = "\n<url><loc>" . $host .  "katalog/" . $manufacturer['slug'] . "</loc></url>";
			file_put_contents($location . $file . $current . $ext, $xml, FILE_APPEND);
			$strings++;
			//if 49999 string in file, create new
			if ($strings == 49999) {
				//write end
				file_put_contents($location . $file . $current . $ext, $end, FILE_APPEND);
				//write to sitemap index
				$sitemap_index [] = $location . $file . $current . $ext;
				//get next file
				$current++;
				//write begin
				file_put_contents($location . $file . $current . $ext, $begin, FILE_APPEND);
				//empty string
				$strings = 0;
			}
		}

		//write end
		if ($strings != 0) file_put_contents($location . $file . $current . $ext, $end, FILE_APPEND);

		//////////////////// MODELS ///////////////////////
		# //catalog/manufacturer/model
		$file = 'sitemap-auto-models';
		$strings = 0;
		$current = 1;

		file_put_contents($location.$file.$current.$ext, $begin);
		$sitemap_index [] = $location . $file . $current . $ext;
		foreach ($manufacturers AS $key => $manufacturer) {
			$models = $tecdoc->get_cars(false, $manufacturer['id']);
			foreach ($models AS $k => $model) {
				$xml = "\n<url><loc>" . $host . 'katalog/' . $manufacturer['slug'] . "/" . $model['slug'] . "</loc></url>";
				file_put_contents($location.$file.$current.$ext, $xml, FILE_APPEND);
				$strings ++;
				//if 49999 string in file, create new
				if ($strings == 49999){
					//write end
					file_put_contents($location.$file.$current.$ext, $end, FILE_APPEND);
					//write to sitemap index
					$sitemap_index [] = $location . $file . $current . $ext;
					//get next file
					$current++;
					//write begin
					file_put_contents($location.$file.$current.$ext, $begin, FILE_APPEND);
					//empty string
					$strings = 0;
				}
			}
		}

		//write end
		if ($strings != 0) file_put_contents($location . $file . $current . $ext, $end, FILE_APPEND);

		///////////////////// ARTICLES ///////////////////////
		$file = 'sitemap-products';
		$strings = 0;
		$current = 1;

		# /catalog/article/
		file_put_contents($location.$file.$current.$ext, $begin);
		$sitemap_index [] = $location . $file . $current . $ext;
		$articles_count = Model::factory('Part')->count_all();
		for ($i = 0; $i < $articles_count; $i += 5000) {
			$articles = Model::factory('Part')->limit(5000)->offset($i)->find_all()->as_array();
	    	foreach ($articles AS $value) {
//            foreach ($articles AS $key => $article) {

                if ($value->tecdoc_id){
                    $tecdoc = ORM::factory('Tecdoc');
                    $part_tecdoc = $tecdoc->get_part($value->tecdoc_id);
                    $url = Htmlparser::transliterate($part_tecdoc['brand'] . "-" . $part_tecdoc['article_nr'] . "-" . substr($part_tecdoc['description'], 0, 50)) . "-" .$value->id;
                }
                else{
                    $url = Htmlparser::transliterate($value->brand . "-" . $value->article_long . "-" . substr($value->name, 0, 50)) . "-" . $value->id;
                }

                $xml = "\n<url><loc>" . $host . 'katalog/produkt/' . $url . "</loc></url>";
//                $xml = "\n<url><loc>" . $host . 'catalog/product/' . $article . "</loc></url>";

				file_put_contents($location . $file . $current . $ext, $xml, FILE_APPEND);
				$strings++;
				//if 49999 string in file, create new
				if ($strings == 49999) {
					//write end
					file_put_contents($location . $file . $current . $ext, $end, FILE_APPEND);
					//write to sitemap index
					$sitemap_index [] = $location . $file . $current . $ext;
					//get next file
					$current++;
					//write begin
					file_put_contents($location . $file . $current . $ext, $begin, FILE_APPEND);
					//empty string
					$strings = 0;
				}
			}
		}

		//write end
		if ($strings != 0) file_put_contents($location . $file . $current . $ext, $end, FILE_APPEND);


		///////////////////// PAGES ///////////////////////
		$file = 'sitemap_info';
		$strings = 0;
		$current = 1;

		# /catalog/article/
		$pages = Model::factory('Page')->where('active', '=', 1)->find_all()->as_array(NULL, 'syn');

		file_put_contents($location.$file.$current.$ext, $begin);
		$sitemap_index [] = $location . $file . $current . $ext;
		foreach ($pages AS $one => $page) {
			$xml = "\n<url><loc>" . $host .  "" . $page . "</loc></url>";
			file_put_contents($location . $file . $current . $ext, $xml, FILE_APPEND);
			$strings++;
			//if 49999 string in file, create new
			if ($strings == 49999) {
				//write end
				file_put_contents($location . $file . $current . $ext, $end, FILE_APPEND);
				//write to sitemap index
				$sitemap_index [] = $location . $file . $current . $ext;
				//get next file
				$current++;
				//write begin
				file_put_contents($location . $file . $current . $ext, $begin, FILE_APPEND);
				//empty string
				$strings = 0;
			}
		}

		//write end
		if ($strings != 0) file_put_contents($location . $file . $current . $ext, $end, FILE_APPEND);

		$xml = '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';
		file_put_contents($main_file, $xml);
		foreach ($sitemap_index AS $index) {
			$xml = "\n<sitemap><loc>" . $host . $index . "</loc></sitemap>";
			file_put_contents($main_file, $xml, FILE_APPEND);
		}
		$xml = "\n</sitemapindex>";
		file_put_contents($main_file, $xml, FILE_APPEND);
	}

} // End Admin_Pages
