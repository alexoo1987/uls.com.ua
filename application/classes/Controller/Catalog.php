<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Catalog extends Controller_Application
{

    public function action_index()
    {
        $this->template->content = View::factory('catalog/manufacturers')
            ->bind('content_catalog',$content_catalog);

        $seo_identifier = $this->request->uri();
        $content_catalog = ORM::factory('Seodata')->where('seo_identifier', '=', $seo_identifier)->find();
//        1
        $this->template->section_titles = $content_catalog->section_titles ? $content_catalog->section_titles :'🔧Онлайн каталог автозапчастей ULC';
        $this->template->title = $content_catalog->title ? $content_catalog->title : '🔧 Каталог запчастей для автомобиля в Украине';
        $content_catalog->h1=$this->template->h1 = $content_catalog->h1?$content_catalog->h1:'Каталог автозапчастей в Украине';
        $this->template->description =$content_catalog->description ? $content_catalog->description : 'Каталог запасных частей для Вашей машины 🚘. Каталог автозапчастей по выгодным ценам 💲 с быстрой доставкой по всей Украине 🚚🇺🇦. Просмотрите наш каталог запчастей на авто и Вы обязательно найдете нужную!';
//        $this->template->keywords = '';
        $this->template->author = '';




    }
//	new actions

    //сброс выбора авто
    public function action_select_another()
	{
		$this->auto_render = FALSE;
		$this->is_ajax = TRUE;
		$this->response->headers('Content-Type', 'application/json');

		Cookie::delete('car_modification');
		$data = View::factory('common/car_select_form')->render();
		$status = "success";

		echo json_encode(array('data' => $data, 'status' => $status));
	}

    //страница моделей по категории и производителю
    public function action_cat_mod()
    {
        $this->template->content = View::factory('catalog/cat_mod')
            ->bind('slug', $slug)
            ->bind('manufacture', $manufacture)
            ->bind('category_name', $category_name)
            ->bind('category', $category)
            ->bind('seo_data', $seo_data)
            ->bind('models', $models);


        $seo_identifier = $this->request->uri();/*.URL::query()*/
        $manuf = $this->request->param('manufacturer');
        $slug = $this->request->param('model');

        $seo_data = ORM::factory('Seodata')->where('seo_identifier', '=', $seo_identifier)->find();
        $category_id = explode('-', $slug);
        $category_id = (integer)end($category_id);

        $category = ORM::factory('Category')->where('slug', '=', $slug)->and_where('id', '=', $category_id)->find();

		if(!count($category) || !count($seo_data))
			throw HTTP_Exception::factory(404, 'File not found!');
        $category_name = $category->name;
		
        if ($category->level == 2) {
            $models = $this->tecdoc->models_by_category_id($category_id, $manuf);
            $manufacture = $this->tecdoc->get_manuf_info_by_url($manuf);
			
			if(!count($manufacture))
				throw HTTP_Exception::factory(404, 'File not found!');

            $this->template->title = "2 Купить ".$category_name." на ".$manufacture['short_name']." в Киеве и Украине - цены на Eparts";
            $this->template->h1 = $category_name." на ".$manufacture['short_name'];
            $this->template->description = $category_name." на ".$manufacture['short_name']." в интернет-магазине Eparts ➤ ➤ ➤ Огромный выбор оригинальных запчастей для автомобилей разных брендов и моделей! ✈ Доставка по Киеву и по всех городах Украины! ✔ Лучшие цены ✔ ☎ Заказать: 044-361-96-64 ✓ 067-291-18-25 ✓";

            $this->template->keywords = '';
            $this->template->author = '';

        } 
        else
            $this->action_category($slug);
        

    }

    // Начальная страница категории, где выводятся модели, на главной
    public function action_cat()
    {
        if (ORM::factory('Client')->logged_in()) {
            $guest = false;
        } else {
            $guest = true;
        }

        $this->template->content = View::factory('catalog/cat')
            ->bind('slug', $slug)
            ->bind('category_name', $category->name)
            ->bind('category', $category)
            ->bind('h1', $h1)
            ->bind('topParts', $topParts)
            ->bind('seo_data', $seo_data)
            ->bind('guest', $guest)
            ->bind('content_catalog',$content_catalog)//seo
            ->bind('readMoreButton', $readMoreButton)
            ->bind('buyTextButton', $buyTextButton)
            ->bind('manufacturers', $manufacturers);

        $this->template->title = '3 Каталог (производители)';
        $this->template->h1 = 'Каталог (производители)';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $seo_identifier = $this->request->uri();/*.URL::query()*/
        $seo_data = ORM::factory('Seodata')->where('seo_identifier', '=', $seo_identifier)->find();

        $slug = $this->request->param('manufacturer');

        $content_catalog = ORM::factory('Seodata')->where('seo_identifier', '=', $seo_identifier)->find();//seo

        $category_id = explode('/', $slug);
        $category_id = (string)end($category_id);
        $category_id=ORM::factory('Category')->where('slug', '=', $slug)->find();
        $category_id=$category_id->id;
        $category = ORM::factory('Category')->where('slug', '=', $slug)->and_where('id', '=', $category_id)->find();

		if(!count($category))
			throw HTTP_Exception::factory(404, 'File not found!');


        if ($category->level == 2)
        {
            $topParts = $this->tecdoc->get_all_top_article_for_cat($category_id);

            $readMoreButton = [];
            $readMoreButton['enable'] = $settingReadMore = ORM::factory('Setting')->where('code_name', '=', 'read_more_show')->find();
            $readMoreButton['text'] = $settingReadMore = ORM::factory('Setting')->where('code_name', '=', 'read_more')->find();

            $buyTextButton =  $settingReadMore = ORM::factory('Setting')->where('code_name', '=', 'button_buy_text')->find();
            switch ($category_id) {
                case 597:
                    $category_id=670;
                    break;
                case 598:
                    $category_id=669;
                    break;
                case 599:
                    $category_id=671;
                    break;
                case 600:
                    $category_id=782;
                    break;
                case 601:
                    $category_id=837;
                    break;
                case 602:
                    $category_id=613;
                    break;
                case 603:
                    $category_id=614;
                    break;
                case 604:
                    $category_id=838;
                    break;
                case 605:
                    $category_id=879;
                    break;
                case 606:
                    $category_id=886;
                    break;
                case 607:
                    $category_id=888;
                    break;
                case 608:
                    $category_id=651;
                    break;
                case 609:
                    $category_id=650;
                    break;
                case 610:
                    $category_id=654;
                    break;





            }
            $manufacturers = $this->tecdoc->manufacture_by_category_id($category_id);

//            $h1 = $category->name;
//            $this->template->title = "4 Купить ".$h1." на авто в Киеве и Украине - цены на Eparts";
//            $this->template->keywords = "";
//            $this->template->author = '';
//            $this->template->description = $h1." для автомобиля в интернет-магазине Eparts ➤ ➤ ➤ Огромный выбор оригинальных запчастей для автомобилей разных брендов и моделей! ✈ Доставка по Киеву и по всех городах Украины! ✔ Лучшие цены ✔ ☎ Заказать: 044-361-96-64 ✓ 067-291-18-25 ✓";
//
//            $content_text = "";

//           "🔧4 '
            $this->template->section_titles = $content_catalog->section_titles ;

            $this->template->title = $content_catalog->title ? $content_catalog->title : $category->name.' купить запасную часть на ваш автомобиль - ULC';

            $content_catalog->h1=$this->template->h1 = $content_catalog->h1 ? $content_catalog->h1 :( $h1 =$category->name. ' купить в Киеве и по всей Украине для вашей машины');

            $this->template->description =$content_catalog->description ? $content_catalog->description : ''.$category->name.' на автомобиль любой марки 🏎️🚘. '.$category->name.' - доступная цена 💲за отменное качество. Быстрая доставка автозапчастей на территории всей Украины 🚚🇺🇦. Большой выбор! '.$category->name.' специально для вашей машины!';
//        $this->template->keywords = '';
            $this->template->author = '';

        } else {
            $this->action_category($slug);
        }
    }

    public function action_parts()
    {
        if (ORM::factory('Client')->logged_in()) {
            $guest = false;
        } else {
            $guest = true;
        }
        //TOP products
        $top_items = ORM::factory('TopOrderitem')
            ->find_all()
            ->as_array();

        $top_orderitems = array();

        foreach ($top_items as $top_item => $key) {

            $top_orderitems[] = Article::get_short_article($key->article);
        }

        $this->template->content = View::factory('catalog/parts')
            ->bind('guest', $guest)
            ->bind('top_orderitems', $top_orderitems)
            ->bind('h1', $h1)
            ->bind('priceitems', $priceitems)
            ->bind('parts', $all_positions)
            ->bind('category', $category)
            ->bind('manufacturer', $manufacturer)
            ->bind('car', $car)
            ->bind('content_catalog',$content_catalog)//seo
            ->bind('info', $info)
            ->bind('brand_ids', $brand_active)
            ->bind('pagination', $pagination)
            ->bind('url_link', $url_link)
            ->bind('active_filter', $active_filter)
            ->bind('unother_models', $unother_models)
            ->bind('readMoreButton', $readMoreButton)
            ->bind('buyTextButton', $buyTextButton)
            ->bind('content_text', $content_text);

        $manufacturer = $this->url_array['manuf'];
        $info = [];
        $brand_active = "";
        $unother_models = [];

        $readMoreButton = [];
        $readMoreButton['enable'] = $settingReadMore = ORM::factory('Setting')->where('code_name', '=', 'read_more_show')->find();
        $readMoreButton['text'] = $settingReadMore = ORM::factory('Setting')->where('code_name', '=', 'read_more')->find();

        $buyTextButton =  $settingReadMore = ORM::factory('Setting')->where('code_name', '=', 'button_buy_text')->find();

        $filter_current['brand'] = $this->request->query('brand') ? $this->request->query('brand') : array();
        $filter_current['location'] = $this->request->query('location') ? $this->request->query('location') : array();

        $active_filter = $this->url_array['filter'];

        $category_id = explode('/', $this->url_array['category']);

        $category_id = (string)end($category_id);
        $category_id=ORM::factory('Category')->where('slug', '=', $category_id)->find();

        $seo_identifier = $this->request->uri();
        $content_catalog = ORM::factory('Seodata')->where('seo_identifier', '=', $seo_identifier)->find();

        $category_id=$category_id->id;
        $category = ORM::factory('Category')->where('id', '=', $category_id)->find();

        switch ($category_id) {
            case 597:
                $category_id=670;
                break;
            case 598:
                $category_id=669;
                break;
            case 599:
                $category_id=671;
                break;
            case 600:
                $category_id=782;
                break;
            case 601:
                $category_id=837;
                break;
            case 602:
                $category_id=613;
                break;
            case 603:
                $category_id=614;
                break;
            case 604:
                $category_id=838;
                break;
            case 605:
                $category_id=879;
                break;
            case 606:
                $category_id=886;
                break;
            case 607:
                $category_id=888;
                break;
            case 608:
                $category_id=651;
                break;
            case 609:
                $category_id=650;
                break;
            case 610:
                $category_id=654;
                break;

        }

		if(!count($category))
			throw HTTP_Exception::factory(404, 'File not found!');

        $is_root = ORM::factory('Category')->where('level', '=', 0)->and_where('slug', '=', $this->url_array['category'])->find_all()->count();

        if ($is_root)
            return $this->action_category($this->url_array['category']);

        $title = "5 Купить ";
        $h1 = $category->name;
        $description = "";

        if(empty($this->url_array['type'])){
            $url_link = URL::base()."katalog/".$this->url_array['manuf']."/".$this->url_array['model']."/".$this->url_array['category'];
        }
        else{
            $url_link = URL::base()."katalog/".$this->url_array['manuf']."/".$this->url_array['model']."/".$this->url_array['type']."/".$this->url_array['category'];
        }

//        new
        if (!empty($this->url_array['model']) and empty($this->url_array['type'])) {
            $car = $this->tecdoc->get_car_info_by_model_url($this->url_array['model'], $this->url_array['manuf']);

			if(!count($car))
				throw HTTP_Exception::factory(404, 'File not found!');

            $h1 .= " на ".$car['manuf_name']." ".$car['model_name'];
            $unother_models = $this->tecdoc->models_by_category_id($category_id, $this->url_array['manuf']);

            if(!empty($this->url_array['page']) AND empty($this->url_array['filter']) ){

                $number_page = explode('-', $this->url_array['page']);
                $number_page = (integer)end($number_page);
                $offset = ($number_page-1) * 30;
                $count = $this->tecdoc->get_count_articul_by_cat_model($this->url_array['model'], $this->url_array['manuf'], $category_id);
                $max_page = ceil($count/30);

                if($number_page == 1){
                    HTTP::redirect($url_link, 301);
                }

                if($max_page < $number_page){
                    HTTP::redirect($url_link."/page-".$max_page, 301);
                }
                elseif ( $number_page < 1)
                {
                    HTTP::redirect($url_link."", 301);
                }
                $pagination = [
                    'current' => $number_page,
                    'total' => $max_page
                ];
                $priceitems = $this->tecdoc->get_all_articul_by_cat_model($this->url_array['model'], $this->url_array['manuf'], $category_id, $offset);

                if(!empty($priceitems))
                {
                    if ($number_page == 1 AND $number_page!=$max_page){
                        $this->template->next = $url_link."/page-2";
                    }
                    elseif($number_page != 1 AND $number_page == $max_page){
                        if($max_page == 2){
                            $this->template->prev = $url_link;
                        }
                        else{
                            $this->template->prev = $url_link."/page-".($max_page-1);
                        }
                    }
                    elseif ($number_page != 1 AND $number_page != $max_page){
                        $this->template->next = $url_link."/page-".($number_page + 1);
                        if($number_page == 2){
                            $this->template->prev = $url_link;
                        }
                        else{
                            $this->template->prev = $url_link."/page-".($number_page - 1);
                        }
                    }
                }
            }
            elseif (empty($this->url_array['page']) AND !empty($this->url_array['filter']))
            {
                $number_page = 1;
                $offset = 0;
//                $count = $this->tecdoc->get_count_articul_by_cat_model($this->url_array['model'], $this->url_array['manuf'], $category_id);
//                $max_page = ceil($count/30);
                $max_page = 1;

                $brand_ids = explode("-", $this->url_array['filter']);
                unset($brand_ids[0]);
                $brand_active = $brand_ids;
                for($i = 1; $i <= count($brand_ids); $i++)
                {
                    $brand_ids[$i] = "'".$brand_ids[$i]."'";
                }

                if(count($brand_ids) > 1){
                    $this->template->noindex = true;
                }
                else{
                    $h1 .= " - производитель: ".$brand_active[1];
                }

                $brand_active = implode(",", $brand_active);
                $brand_ids = implode(",", $brand_ids);

                $pagination = [
                    'current' => $number_page,
                    'total' => $max_page
                ];

                $priceitems = $this->tecdoc->get_all_articul_by_cat_model_filtes($this->url_array['model'], $this->url_array['manuf'], $category_id, $brand_ids);
            }
            elseif (!empty($this->url_array['page']) AND !empty($this->url_array['filter']))
            {

                $number_page = 1;
                $offset = 0;
//                $count = $this->tecdoc->get_count_articul_by_cat_model($this->url_array['model'], $this->url_array['manuf'], $category_id);
//                $max_page = ceil($count/30);
                $max_page = 1;

                $brand_ids = explode("-", $this->url_array['filter']);
                unset($brand_ids[0]);
                $brand_active = $brand_ids;
                for($i = 1; $i <= count($brand_ids); $i++)
                {
                    $brand_ids[$i] = "'".$brand_ids[$i]."'";
                }

                $brand_active = implode(",", $brand_active);
                $brand_ids = implode(",", $brand_ids);

                $pagination = [
                    'current' => $number_page,
                    'total' => $max_page
                ];

                $priceitems = $this->tecdoc->get_all_articul_by_cat_model_filtes($this->url_array['model'], $this->url_array['manuf'], $category_id, $brand_ids);
            }
            else{

                $number_page = 1;
                $count = $this->tecdoc->get_count_articul_by_cat_model($this->url_array['model'], $this->url_array['manuf'], $category_id);
                $max_page = ceil($count/30);
                $pagination = [
                    'current' => $number_page,
                    'total' => $max_page
                ];
                $priceitems = $this->tecdoc->get_all_articul_by_cat_model($this->url_array['model'], $this->url_array['manuf'], $category_id, 0);
                if(!empty($priceitems)) {
                    if ($number_page == 1 AND $number_page != $max_page) {
                        $this->template->next = $url_link . "/page-2";
                    }
                }
            }

            if(!empty($this->url_array['page'] AND $number_page > 1)){
                $title .= "6".$h1." в Киеве и Украине - цены на Eparts - страница ".$number_page;
                $description .= $h1." в интернет-магазине Eparts ➤ ➤ ➤ Огромный выбор оригинальных запчастей для автомобилей разных брендов и моделей! ✈ Доставка по Киеву и по всех городах Украины! ✔ Лучшие цены ✔ ☎ Заказать: 044-361-96-64 ✓ 067-291-18-25 ✓ - страница ".$number_page;
                $h1 .= " - страница ".$number_page;
            }
            else{
                $title .="7".$h1." в Киеве и Украине - цены на Eparts";
                $description .= $h1." в интернет-магазине Eparts ➤ ➤ ➤ Огромный выбор оригинальных запчастей для автомобилей разных брендов и моделей! ✈ Доставка по Киеву и по всех городах Украины! ✔ Лучшие цены ✔ ☎ Заказать: 044-361-96-64 ✓ 067-291-18-25 ✓";
            }



        } else {

            $modification_id = explode('-', $this->url_array['type']);
            $modification_id = (integer)end($modification_id);
            $car = $this->tecdoc->get_car_info_by_type_url($modification_id);

            if(!count($car))
				throw HTTP_Exception::factory(404, 'File not found!');

            $h1 .= " на ".$car['manuf_name']." ".$car['model_name']." ".$car['type_name'];

            if(!empty($this->url_array['page']) AND empty($this->url_array['filter']) ) {

                $number_page = explode('-', $this->url_array['page']);
                $number_page = (integer)end($number_page);
                $offset = ($number_page - 1) * 30;

                $count = $this->tecdoc->get_count_articul_by_cat_type($modification_id, $category_id);
                $max_page = ceil($count/30);

                if($number_page == 1){
                    HTTP::redirect($url_link, 301);
                }
                if($max_page < $number_page){
                    HTTP::redirect($url_link."/page-".$max_page, 301);
                }
                elseif ($number_page < 1)
                {
                    HTTP::redirect($url_link."", 301);
                }
                $pagination = [
                    'current' => $number_page,
                    'total' => $max_page
                ];

                $priceitems = $this->tecdoc->get_all_articul_by_cat_type($modification_id, $category_id, $offset);

                if(!empty($priceitems)) {
                    if ($number_page == 1 AND $number_page != $max_page) {
                        $this->template->next = $url_link . "/page-2";
                    } elseif ($number_page != 1 AND $number_page == $max_page) {
                        if ($max_page == 2) {
                            $this->template->prev = $url_link;
                        } else {
                            $this->template->prev = $url_link . "/page-" . ($max_page - 1);
                        }
                    } elseif ($number_page != 1 AND $number_page != $max_page) {
                        $this->template->next = $url_link . "/page-" . ($number_page + 1);
                        if ($max_page == 2) {
                            $this->template->prev = $url_link;
                        } else {
                            $this->template->prev = $url_link . "/page-" . ($max_page - 1);
                        }
                    }
                }
            }
            elseif (empty($this->url_array['page']) AND !empty($this->url_array['filter']))
            {
                $number_page = 1;
                $offset = 0;
                $max_page = 1;

                $brand_ids = explode("-", $this->url_array['filter']);
                unset($brand_ids[0]);
                $brand_active = $brand_ids;
                for($i = 1; $i <= count($brand_ids); $i++)
                {
                    $brand_ids[$i] = "'".$brand_ids[$i]."'";
                }

                $brand_ids = implode(",", $brand_ids);
                $brand_active = implode(",", $brand_active);

                $pagination = [
                    'current' => $number_page,
                    'total' => $max_page
                ];

                $priceitems = $this->tecdoc->get_all_articul_by_type_filtes($modification_id, $category_id, $brand_ids);
            }
            elseif (!empty($this->url_array['page']) AND !empty($this->url_array['filter']))
            {
                $number_page = 1;
                $offset = 0;
                $max_page = 1;

                $brand_ids = explode("-", $this->url_array['filter']);
                unset($brand_ids[0]);
                $brand_active = $brand_ids;
                for($i = 1; $i <= count($brand_ids); $i++)
                {
                    $brand_ids[$i] = "'".$brand_ids[$i]."'";
                }

                $brand_ids = implode(",", $brand_ids);
                $brand_active = implode(",", $brand_active);

                $pagination = [
                    'current' => $number_page,
                    'total' => $max_page
                ];

                $priceitems = $this->tecdoc->get_all_articul_by_type_filtes($modification_id, $category_id, $brand_ids);

            }
            else{
                $number_page = 1;
                $count = $this->tecdoc->get_count_articul_by_cat_type($modification_id, $category_id);

                $max_page = ceil($count/30);
                $pagination = [
                    'current' => $number_page,
                    'total' => $max_page
                ];
                $priceitems = $this->tecdoc->get_all_articul_by_cat_type($modification_id, $category_id, 0);

                if(!empty($priceitems)) {
                    if ($number_page == 1 AND $number_page != $max_page) {
                        $this->template->next = $url_link . "/page-2";
                    }
                }
            }

            if(!empty($this->url_array['page'] AND $number_page > 1)){
                $title .="8".$h1." в Киеве и Украине - цены на Eparts - страница ".$number_page;
                $description .= $h1." в интернет-магазине Eparts ➤ ➤ ➤ Огромный выбор оригинальных запчастей для автомобилей разных брендов и моделей! ✈ Доставка по Киеву и по всех городах Украины! ✔ Лучшие цены ✔ ☎ Заказать: 044-361-96-64 ✓ 067-291-18-25 ✓ - страница ".$number_page;
                $h1 .= " - страница ".$number_page;
            }
            else{
                $title .="9".$h1." в Киеве и Украине - цены на Eparts";
                $description .= $h1." в интернет-магазине Eparts ➤ ➤ ➤ Огромный выбор оригинальных запчастей для автомобилей разных брендов и моделей! ✈ Доставка по Киеву и по всех городах Украины! ✔ Лучшие цены ✔ ☎ Заказать: 044-361-96-64 ✓ 067-291-18-25 ✓";
            }

        }
		
		if($category->slug!=$this->url_array['category'])
			throw HTTP_Exception::factory(404, 'File not found!');

        $seo_identifier = $this->request->uri();/*.URL::query()*/
        $seo_data = ORM::factory('Seodata')->where('seo_identifier', '=', $seo_identifier)->find();
		
        $info = ['category_id' => $category_id, 'manufacturer_slug' => $this->url_array['manuf'],  'model_slug' => $this->url_array['model'], 'modification_slug' => !empty($modification_id)?$modification_id:""];
//        $this->template->title = "10".$title;
//        $this->template->description = $description;
////        $this->template->keywords = "";
//        $this->template->author = '';
//        $h1 = $h1;
//        $content_text = $seo_data->content;

        $this->template->section_titles = $content_catalog->section_titles ;
        if (isset($car["type_name"])) {
            $car["type_name"] = $car["type_name"];
        }else{
            $car["type_name"] = '';
        }
//        "10🔧 '.
        $this->template->title = $content_catalog->title ? $content_catalog->title : $category->name.' на '.$car["manuf_name"].' '.$car["model_name"].' '.$car["type_name"].' купить в Украине - ULC';

        $content_catalog->h1=$this->template->h1 = $content_catalog->h1 ? $content_catalog->h1 :( $h1 =$category->name.' на '.$car["manuf_name"].' '.$car["model_name"].' '.$car["type_name"].' купить в Киеве и по всей Украине');

        $this->template->description =$content_catalog->description ? $content_catalog->description :$category->name.' на '.$car["manuf_name"].' '.$car["model_name"].' '.$car["type_name"].' для вашего автомобиля 🚘. '.$category->name.' на '.$car["manuf_name"].' '.$car["model_name"].' '.$car["type_name"].'  купить по выгодной цене 💲. Быстрая доставка автозапчастей на территории всей Украины 🚚🇺🇦. '.$category->name.' на '.$car["manuf_name"].' '.$car["model_name"].' '.$car["type_name"].' - только оригинальный товар!';
//        $this->template->keywords = '';
        $this->template->author = '';

        $this->template->scripts[] = 'dist/filters_nv';

        if(empty($priceitems))
            $this->template->noindex = true;

	}


    //car select block
    public function action_car_choose() //changed
    {
        $this->auto_render = FALSE;
        $this->is_ajax = TRUE;

        $year = !empty($_POST['year']) ? $_POST['year'] : false;
        $manuf = !empty($_POST['manuf']) ? $_POST['manuf'] : false;
        $model = !empty($_POST['model']) ? $_POST['model'] : false;
        $model_slug = !empty($_POST['model_slug']) ? $_POST['model_slug'] : false;
        $body_type = !empty($_POST['body_type']) ? $_POST['body_type'] : false;
        $liters_fuel = !empty($_POST['liters_fuel']) ? $_POST['liters_fuel'] : false;
        $car_mod = !empty($_POST['car_mod']) ? $_POST['car_mod'] : false;
        $capasity = !empty($_POST['capasity']) ? $_POST['capasity'] : false;

        if ($year) {
            if (!$manuf) {
                echo View::factory('car_select_form/manuf')->set('items',  $this->tecdoc->get_all_manufacture())->render();
            } elseif (!$model AND !$model_slug) {
                echo View::factory('car_select_form/model')->set('items',  $this->tecdoc->car_choose_model($manuf, $year))->render();
            } elseif (!$body_type) {
                if($model_slug)
                {
                    $model_id = $this->tecdoc->car_choose_model_by_modurl($manuf, $year, $model_slug);
                    echo View::factory('car_select_form/body_type')->set('items',  $this->tecdoc->get_body_types($model_id))->render();
                }
                else
                {
                    echo View::factory('car_select_form/body_type')->set('items',  $this->tecdoc->get_body_types($model))->render();
                }
            } elseif (!$liters_fuel) {
                if($model_slug)
                {
                    $model_id = $this->tecdoc->car_choose_model_by_modurl($manuf, $year, $model_slug);
                    echo View::factory('car_select_form/liters_fuel')->set('items',  $this->tecdoc->car_choose_liters_fuel($model_id, $body_type))->render();
                }
                else
                {
                    echo View::factory('car_select_form/liters_fuel')->set('items',  $this->tecdoc->car_choose_liters_fuel($model, $body_type))->render();
                }

            } elseif (!$car_mod) {
                if($model_slug)
                {
                    $model_id = $this->tecdoc->car_choose_model_by_modurl($manuf, $year, $model_slug);
                    echo View::factory('car_select_form/types')->set('items', $this->tecdoc->car_choose_types($model_id, $body_type, $liters_fuel, $capasity))->render();
                }
                else {
                    echo View::factory('car_select_form/types')->set('items', $this->tecdoc->car_choose_types($model, $body_type, $liters_fuel, $capasity))->render();
                }
            }
        }
    }

    //Запись какой автомобиль выбрали в куки
    public function action_set_car_mod()
    {
        $this->auto_render = FALSE;
        $this->is_ajax = TRUE;
        $this->response->headers('Content-Type', 'application/json');

//		$tecdoc = Model::factory('Tecdoc');

        $data = "";
        $status = "fail";
		
        $car_modification = !empty($_POST['car_modification']) ? $_POST['car_modification'] : false;
        $category_slug = !empty($_POST['category_slug']) ? $_POST['category_slug'] : false;
//        exit();
        if (!empty($car_modification)) {
            Cookie::set('car_modification', $car_modification);

            $view = View::factory('common/car_selected')
                ->bind('car_mod', $car_mod);
            $car_mod = $car_modification;
            $data = $view->render();
            $status = "success";

            if ($category_slug) {
                $modification = $this->tecdoc->get_url_by_type($car_modification);
                $redirect = URL::site('katalog/' . $modification['manuf_url'] . '/'. $modification['model_url'] . '/'. $modification['type_url'] . '/' . $category_slug);
            } else {
                $modification = $this->tecdoc->get_url_by_type($car_modification);
                $redirect = false;
            }
        }

        $json = array('data' => $data, 'status' => $status);
        if ($redirect) {
            $json['redirect'] = $redirect;
        }

        echo json_encode($json);
    }

// end of new actions

    // страница моделей по конкретному бренду
    public function action_car() //changed
    {
        $this->template->content = View::factory('catalog/cars')
            ->bind('manufacturer', $manufacturer)
            ->bind('h1', $h1)
            ->bind('content_catalog',$content_catalog)//seo
            ->bind('seo_data', $seo_data)
            ->bind('content_text', $content_text);

        $slug = $this->request->param('manufacturer');
        $manufacturer = $this->tecdoc->get_all_models_for_manufactures_url($slug);

        $seo_identifier = $this->request->uri();/*.URL::query()*/
        $seo_data = ORM::factory('Seodata')->where('seo_identifier', '=', $seo_identifier)->find();
        $content_catalog = ORM::factory('Seodata')->where('seo_identifier', '=', $seo_identifier)->find();//seo
		if(!count($manufacturer))
			throw HTTP_Exception::factory(404, 'File not found!');
		
//        $this->template->title = "11 Купить запчасти на ". $manufacturer[0]['brand'] ." в Eparts";
//        $this->template->description = "Запчасти на ". $manufacturer[0]['brand'] ." в интернет-магазине Eparts ➤ ➤ ➤ Огромный выбор оригинальных запчастей для автомобилей разных брендов и моделей! ✈ Доставка по Киеву и по всех городах Украины! ✔ Лучшие цены ✔ ☎ Заказать: 044-361-96-64 ✓ 067-291-18-25 ✓";
//        $this->template->keywords = "";
//        $this->template->author = '';
//        $h1 = $manufacturer[0]['brand'];
//        $content_text = "";
//        "11

        $this->template->section_titles = $content_catalog->section_titles ;

        $this->template->title = $content_catalog->title ? $content_catalog->title : '🔧 🔧 Купить запчасти для '. $manufacturer[0]["brand"] .' любой модели в Украине - ULC';

        $content_catalog->h1=$this->template->h1 = $content_catalog->h1 ? $content_catalog->h1 :( $h1 = 'Купить автомобильные запчасти на '. $manufacturer[0]["brand"] .' в Киеве и по всей Украине');

        $this->template->description =$content_catalog->description ? $content_catalog->description : 'Купить автозапчасти на '. $manufacturer[0]["brand"] .' для любых моделей автомобиля 🚘🏎️. Запчасти на '. $manufacturer[0]["brand"] .' с доставкой по всей Украине 🚚🇺🇦 по хорошим ценам💲. Большой выбор запасных частей на '. $manufacturer[0]["brand"] .' для всех моделей!';
//        $this->template->keywords = '';
        $this->template->author = '';
    }


    // страница, когда выбрали модель TODO
    public function action_model()
    {

        $this->template->content = View::factory('catalog/tree')
            ->bind('tree_list', $tree_list)
            ->bind('h1', $h1)
            ->bind('content_catalog',$content_catalog)//seo
            ->bind('car', $car)
            ->bind('seo_data', $seo_data)
            ->bind('content_text', $content_text);

        $seo_identifier = $this->request->uri();/*.URL::query()*/
        $seo_data = ORM::factory('Seodata')->where('seo_identifier', '=', $seo_identifier)->find();
        $content_catalog = ORM::factory('Seodata')->where('seo_identifier', '=', $seo_identifier)->find();//seo
        $car = $this->tecdoc->get_car_info_by_model_url($this->url_array['model'], $this->url_array['manuf']);

        $tree_list = ORM::factory('Category')->where('level', '=', 0)->order_by('id')->find_all()->as_array();
		
		if(!count($car) || !count($tree_list))
			throw HTTP_Exception::factory(404, 'File not found!');

//        $this->template->title = "12 Запчасти на ". $car['manuf_name'] . " " . $car['model_name'] ." - купить оригинальные запчасти в Киеве и Украине";
//        $this->template->description = "Запчасти на ". $car['manuf_name'] . " " . $car['model_name'] ." в интернет-магазине Eparts ➤ ➤ ➤ Огромный выбор оригинальных запчастей для автомобилей разных брендов и моделей! ✈ Доставка по Киеву и по всех городах Украины! ✔ Лучшие цены ✔ ☎ Заказать: 044-361-96-64 ✓ 067-291-18-25 ✓";
//        $this->template->keywords = "";
//        $this->template->author = '';
//        $h1 = "Автозапчасти на " . $car['manuf_name'] . " " . $car['model_name'];
//        $content_text = "";
//        "12

        $this->template->section_titles = $content_catalog->section_titles ;

        $this->template->title = $content_catalog->title ? $content_catalog->title : '12 🔧 Купить запчасти для '. $car["manuf_name"] . " " . $car["model_name"] .' в Украине - ULC';

        $content_catalog->h1=$this->template->h1 = $content_catalog->h1 ? $content_catalog->h1 :( $h1 = 'Купить запчасти на '. $car["manuf_name"] . " " . $car["model_name"] .' в Киеве и по всей Украине');

        $this->template->description =$content_catalog->description ? $content_catalog->description : 'Купить автозапчасти на '. $car["manuf_name"] . " " . $car["model_name"] .'  🚘. Оригинальные запчасти на '. $car["manuf_name"] . " " . $car["model_name"] .' с доставкой по всей Украине 🚚🇺🇦 по хорошим ценам💲. Большой выбор запасных частей на '. $car["manuf_name"] . " " . $car["model_name"] .'!';
//        $this->template->keywords = '';
        $this->template->author = '';

        $this->template->styles[] = 'dist/select_left';
    }

    //	если мы сделали подбор авто, первая страница со списком категорий и инфой об авто
    public function action_types()
    {
        //temp redirect from old pages
        $manufacturer = $this->request->param('manufacturer');

        if (in_array($manufacturer, array('types', 'model', 'parts'))) HTTP::redirect(URL::base(), 301);

        $this->template->content = View::factory('catalog/tree')
            ->bind('tree_list', $tree_list)
            ->bind('h1', $h1)
            ->bind('content_catalog',$content_catalog)//seo

            ->bind('car', $car)
            ->bind('seo_data', $seo_data)
            ->bind('content_text', $content_text);

        $seo_identifier = $this->request->uri();/*.URL::query()*/
        $seo_data = ORM::factory('Seodata')->where('seo_identifier', '=', $seo_identifier)->find();

        $type_id = explode('-', $this->url_array['type']);
        $type_id = (integer)end($type_id);

        $car = $this->tecdoc->get_car_info_by_type_url($type_id);

		if(!count($car) || !$type_id)
			throw HTTP_Exception::factory(404, 'File not found!');

        $tree_list = ORM::factory('Category')->where('level', '=', 0)->order_by('id')->find_all()->as_array();
		
		if(!count($tree_list))
			throw HTTP_Exception::factory(404, 'File not found!');
        $seo_identifier = $this->request->uri();
        $content_catalog = ORM::factory('Seodata')->where('seo_identifier', '=', $seo_identifier)->find();

//        $this->template->title = "13 Запчасти на " . $car['manuf_name'] . " " . $car['model_name']. " " . $car['type_name'] . " - купить оригинальные запчасти в Киеве и Украине";
//        $this->template->description = "Запчасти на ". $car['manuf_name'] . " " . $car['model_name']. " " . $car['type_name'] ." в интернет-магазине Eparts ➤ ➤ ➤ Огромный выбор оригинальных запчастей для автомобилей разных брендов и моделей! ✈ Доставка по Киеву и по всех городах Украины! ✔ Лучшие цены ✔ ☎ Заказать: 044-361-96-64 ✓ 067-291-18-25 ✓";
//        $this->template->keywords = "";
//        $this->template->author = '';
//        $h1 = "Автозапчасти на ". $car['manuf_name'] . " " . $car['model_name']. " " . $car['type_name'];
//        $content_text = "";
//            "13
        $this->template->section_titles = $content_catalog->section_titles ;

        $this->template->title = $content_catalog->title ? $content_catalog->title : ' 🔧 Купить запчасти для '. $car["manuf_name"] . " " . $car["model_name"] ." " . $car["type_name"] .' в Украине - ULC';

        $content_catalog->h1=$this->template->h1 = $content_catalog->h1 ? $content_catalog->h1 :( $h1 = 'Купить запчасти на '. $car["manuf_name"] . " " . $car["model_name"] ." " . $car["type_name"] .' в Киеве и по всей Украине');

        $this->template->description =$content_catalog->description ? $content_catalog->description : 'Купить автозапчасти на '. $car["manuf_name"] . " " . $car["model_name"] ." " . $car["type_name"] .'  🚘. Оригинальные запчасти на '. $car["manuf_name"] . " " . $car["model_name"] ." " . $car["type_name"] .' с доставкой по всей Украине 🚚🇺🇦 по хорошим ценам💲. Большой выбор запасных частей на '. $car["manuf_name"] . " " . $car["model_name"] ." " . $car["type_name"] .'!';
//        $this->template->keywords = '';
        $this->template->author = '';
    }

	public function action_product() //TODO::category and breadcums
    {

        $this->template->content = View::factory('catalog/article_new')
            ->bind('criterias', $criterias)
            ->bind('applied_to', $applied_to)
            ->bind('part', $part)
            ->bind('content_catalog',$content_catalog)
            ->bind('crosses', $crosses)
            ->bind('best_parts', $best_parts)
            ->bind('category', $category)
            ->bind('guest', $this->guest)
            ->bind('breadcumbs', $breadcumbs)
            ->bind('buyTextButton', $buyTextButton)
            ->bind('top_orderitems', $top_orderitems);


        if (!ORM::factory('Catalogfromip')->check_ip()) {
            throw new HTTP_Exception_503('Превышено число запросов');
            return false;
        }

        $buyTextButton =  $settingReadMore = ORM::factory('Setting')->where('code_name', '=', 'button_buy_text')->find();

//        $setting = ORM::factory('Setting')->where('code_name', '=', 'tekhnomir_active_site')->find();

        //TOP products
        $top_items = ORM::factory('TopOrderitem')
            ->find_all()
            ->as_array();
        $top_orderitems = array();
        foreach ($top_items as $top_item=>$key)
        {
            $top_orderitems[] = Article::get_short_article($key->article);
        }

        $crosses = [];
        $breadcumbs = [];

        $slug = $this->request->param('id');

        if (!is_object(json_decode(base64_decode(str_replace('_', '=', $slug))))) {
            $slug = explode('-', $slug);
            $id = end($slug);


            $tecdoc_id = NULL;
            $criterias = [];
            $applied_to = [];
            $price_tm = [];

            $query = "SELECT parts.*, brands.country  FROM parts LEFT JOIN brands ON brands.id = parts.brand_id WHERE parts.id = " . $id . " ";
            $part = DB::query(Database::SELECT, $query)->execute('tecdoc_new')->current();
            if(!$part['id'])
                throw HTTP_Exception::factory(404, 'File not found!');

            $partUrl = Helper_Url::getPartUrl($part, [], true);
            if ($partUrl !== Helper_Url::currentUrl()) {
                HTTP::redirect($partUrl, 301);
            }


            $urlReal = Htmlparser::transliterate($part['brand'] . "-" . $part['article'] . "-" . substr($part['name'], 0, 50)) . "-" . $part['id'];
//
//            if($urlReal != $this->request->param('id'))
//                HTTP::redirect(URL::base() . 'catalog/product/' . $urlReal, 301);
            
            
            // самая дешовая
            $cheapest_part = "SELECT
              priceitems.id, priceitems.amount, priceitems.delivery, priceitems.price*currencies.ratio as price_start,
              priceitems.price * currencies.ratio * (SELECT discount_limits.percentage / 100 + 1
                 FROM discount_limits
                   LEFT JOIN discounts ON discount_limits.discount_id = discounts.id
                 WHERE discounts.standart = 1
                       AND priceitems.price * currencies.ratio > discount_limits.from
                       AND (priceitems.price * currencies.ratio <= discount_limits.to OR
                            discount_limits.to = 0)
                 LIMIT 1) AS price,
                  priceitems.delivery
                FROM priceitems
                  LEFT JOIN currencies ON currencies.id = priceitems.currency_id
                  LEFT JOIN suppliers ON suppliers.id = priceitems.supplier_id
                WHERE priceitems.part_id = " . $part['id'] . "
                      AND suppliers.dont_show = 0
                ORDER BY price, delivery
                LIMIT 1";
            $cheapest_part = DB::query(Database::SELECT, $cheapest_part)->execute('tecdoc_new')->current();

            $quickly = "SELECT
              priceitems.id, priceitems.amount, priceitems.delivery, priceitems.price*currencies.ratio as price_start,
              priceitems.price * currencies.ratio * (SELECT discount_limits.percentage / 100 + 1
                 FROM discount_limits
                   LEFT JOIN discounts ON discount_limits.discount_id = discounts.id
                 WHERE discounts.standart = 1
                       AND priceitems.price * currencies.ratio > discount_limits.from
                       AND (priceitems.price * currencies.ratio <= discount_limits.to OR
                            discount_limits.to = 0)
                 LIMIT 1) AS price,
                  priceitems.delivery
                FROM priceitems
                  LEFT JOIN currencies ON currencies.id = priceitems.currency_id
                  LEFT JOIN suppliers ON suppliers.id = priceitems.supplier_id
                WHERE priceitems.part_id = " . $part['id'] . "
                      AND suppliers.dont_show = 0
                ORDER BY delivery, price
                LIMIT 1";
            $quickly = DB::query(Database::SELECT, $quickly)->execute('tecdoc_new')->current();

            if (!empty($part['tecdoc_id'])) {
                $criterias = $this->tecdoc->get_criterias_by_art_id($part['tecdoc_id']);
                $applied_to = $this->tecdoc->get_cars_by_art_id($part['tecdoc_id']);
                $breadcumbs = $this->tecdoc->category_by_part($part['id']);
            }

            if (!empty($part['id'])) {

                $tm_items = [];
                $setting = ORM::factory('Setting')->where('code_name', '=', 'tekhnomir_active_site')->find();
                if (ORM::factory('Findfromip')->check_ip() AND $setting)
                {
                    //            TEHNOMIR
                    $change_brand = ORM::factory('ChangeTmBrand')->where('replace_to_short', '=', Article::get_short_article($part['brand']))->find_all()->as_array();
                    $INFO = Tminfo::instance();
                    $INFO->SetLogin('Mir@eparts.kiev.ua');
                    $INFO->SetPasswd('9506678d');
                    if(!empty($change_brand) and !empty($change_brand[0]->replace_to ))
                    {
                        $setting = ORM::factory('Setting')->where('code_name', '=', 'tekhnomir_active_site')->find();
                        if (ORM::factory('Findfromip')->check_ip() AND $setting) {
                            $tm_items = [];
                            foreach ($change_brand as $brand_replace_one) {
                                $tm_items_time = $setting->value != 0 ? $INFO->GetPrice($part['article'], $brand_replace_one->replace_from_short, 1) : [];
                                $tm_items = array_merge($tm_items, $tm_items_time);
                            }
                        }
                    }
                    else
                    {
                        $tm_items = $setting->value != 0 ? $INFO->GetPrice($part['article'], $part['brand'], 1) : [];
                    }
                }

                if ($tm_items) {

                    $price_tm = $this->tehnomir->process_tm_in_product_page($tm_items, $part);
                }

                if(!empty($quickly) AND !empty($cheapest_part))
                {
                    if($quickly['id'] != $cheapest_part['id'])
                    {
                        $price_tm[] = $quickly;
                        $price_tm[] = $cheapest_part;
                    }
                    else
                    {
                        $price_tm[] = $quickly;
                    }
                }

                $crosses = "SELECT *
                    FROM (
                           SELECT
                             priceitems.id,
                             priceitems.part_id,
                             priceitems.amount,
                             priceitems.price * currencies.ratio AS price_start,
                             parts.article_long,
                             parts.article,
                             parts.brand,
                             brands.country,
                             brands.original,
                             parts.brand_long,
                             parts.images,
                             parts.`name`,
                             priceitems.price * currencies.ratio * (SELECT discount_limits.percentage / 100 + 1
                                                                    FROM discount_limits
                                                                      LEFT JOIN discounts ON discount_limits.discount_id = discounts.id
                                                                    WHERE discounts.standart = 1
                                                                          AND priceitems.price * currencies.ratio > discount_limits.from
                                                                          AND (priceitems.price * currencies.ratio <= discount_limits.to OR
                                                                               discount_limits.to = 0)
                                                                    LIMIT 1) AS price_final,
                             priceitems.delivery
                           FROM priceitems
                             INNER JOIN currencies ON currencies.id = priceitems.currency_id
                             INNER JOIN suppliers ON priceitems.supplier_id = suppliers.id
                             INNER JOIN (SELECT DISTINCT
                                           parts.*
                                         FROM (
                                                SELECT crosses_td_mod.to_id as id
                                                FROM parts
                                                  INNER JOIN crosses_td_mod ON parts.id = crosses_td_mod.from_id
                                                WHERE parts.id = ".$part['id']."
                                                
                                                UNION ALL
                                                SELECT crosses_td_mod.from_id
                                                FROM parts
                                                  INNER JOIN crosses_td_mod ON parts.id = crosses_td_mod.to_id
                                                WHERE parts.id = ".$part['id']."
                                                
                                                UNION ALL
                                                SELECT crosses.to_id
                                                FROM parts
                                                  INNER JOIN crosses ON parts.id = crosses.from_id
                                                WHERE parts.id = ".$part['id']."
                                                
                                                UNION ALL
                                                SELECT crosses.from_id
                                                FROM parts
                                                  INNER JOIN crosses ON parts.id = crosses.to_id
                                                WHERE parts.id = ".$part['id']."
                                                
                                                UNION ALL
                                                SELECT crosses2.to_id
                                                FROM parts
                                                  INNER JOIN crosses AS crosses1 ON parts.id = crosses1.from_id
                                                  INNER JOIN crosses AS crosses2 ON crosses1.to_id = crosses2.from_id
                                                WHERE parts.id = ".$part['id']."
                                        
                                                UNION ALL
                                                SELECT crosses2.from_id
                                                FROM parts
                                                  INNER JOIN crosses AS crosses1 ON parts.id = crosses1.to_id
                                                  INNER JOIN crosses AS crosses2 ON crosses1.from_id = crosses2.to_id
                                                WHERE parts.id = ".$part['id']."
                                                
                                                UNION ALL
                                                SELECT crosses2.from_id
                                                FROM parts
                                                  INNER JOIN crosses AS crosses1 ON parts.id = crosses1.from_id
                                                  INNER JOIN crosses AS crosses2 ON crosses1.to_id = crosses2.to_id
                                                WHERE parts.id = ".$part['id']."
                                                
                                                UNION ALL
                                                SELECT crosses2.to_id
                                                FROM parts
                                                  INNER JOIN crosses AS crosses1 ON parts.id = crosses1.to_id
                                                  INNER JOIN crosses AS crosses2 ON crosses1.from_id = crosses2.from_id
                                                WHERE parts.id = ".$part['id']."
                                                
                                              ) AS crosses_all
                                           INNER JOIN parts ON crosses_all.id = parts.id) AS parts ON priceitems.part_id = parts.id
                                           INNER JOIN brands ON brands.id = parts.brand_id
                                           WHERE suppliers.dont_show = 0
                           ORDER BY price_final
                         ) AS temp
                    GROUP BY part_id";

                $crosses = DB::query(Database::SELECT,$crosses)->execute('tecdoc_new')->as_array();

                $best_parts = $this->get_best_match_new($price_tm, $part['id']);
            }
            else
            {
                $best_parts = [];
            }
            $category = '';

//            $this->template->title = '14 Купить '.(substr($part['name'], 0, 50) . (strlen($part['name']) > 50 ? "..." : "")) . ' ' . $part['brand_long'] . ' ' . str_replace('="', '', str_replace('"', '', $part['article_long'])) . 'купить в Украине - ULC';
//            $this->template->description = str_replace('"', '', (substr($part['name'], 0, 50)) . (strlen($part['name']) > 50 ? "..." : "")) . ' ' . $part['brand_long'] . ' ' . str_replace('="', '', str_replace('"', '', $part['article_long'])) . ' в интернет-магазине Eparts ➤ ➤ ➤ Огромный выбор оригинальных запчастей для автомобилей разных брендов и моделей! ✈ Доставка по Киеву и по всех городах Украины! ✔ Лучшие цены ✔ ☎ Заказать: 044-361-96-64 ✓ 067-291-18-25 ✓';
//            $this->template->keywords = '';
//            $this->template->author = '';
            $seo_identifier = $this->request->uri();
            $content_catalog = ORM::factory('Seodata')->where('seo_identifier', '=', $seo_identifier)->find();
//            "14
            $this->template->section_titles = $content_catalog->section_titles ? $content_catalog->section_titles :'🔧Онлайн каталог автозапчастей ULC';
            $this->template->title = $content_catalog->title ? $content_catalog->title : ' Купить '.(substr($part['name'], 0, 50) . (strlen($part['name']) > 50 ? "..." : "")) . ' ' . $part['brand_long'] . ' ' . str_replace('="', '', str_replace('"', '', $part['article_long'])) . 'купить в Украине - ULC';
            $content_catalog->h1=$this->template->h1 = $content_catalog->h1?$content_catalog->h1:(substr($part['name'], 0, 50) . (strlen($part['name']) > 50 ? "..." : "")) . ' ' . $part['brand_long'] . ' ' . str_replace('="', '', str_replace('"', '', $part['article_long'])) ;
            $this->template->description =$content_catalog->description ? $content_catalog->description : (substr($part['name'], 0, 50) . (strlen($part['name']) > 50 ? "..." : "")) . ' ' . $part['brand_long'] . ' ' . str_replace('="', '', str_replace('"', '', $part['article_long'])) . ' для вашего автомобиля 🚘. '.(substr($part['name'], 0, 50) . (strlen($part['name']) > 50 ? "..." : "")) . ' ' . $part['brand_long'] . ' ' . str_replace('="', '', str_replace('"', '', $part['article_long'])) . ' купить по выгодной цене 💲. Быстрая доставка на территории всей Украины 🚚🇺🇦. '.(substr($part['name'], 0, 50) . (strlen($part['name']) > 50 ? "..." : "")) . ' ' . $part['brand_long'] . ' ' . str_replace('="', '', str_replace('"', '', $part['article_long'])) . ' - только оригинальный товар!';
//        $this->template->keywords = '';
            $this->template->author = '';

        }

        else
        {
            $json_price = (json_decode(base64_decode(str_replace('_','=',$slug))));
            $part_obj = $json_price;
            $tm_items = [];
            $setting = ORM::factory('Setting')->where('code_name', '=', 'tekhnomir_active_site')->find();
            if (ORM::factory('Findfromip')->check_ip() AND $setting) {
                //            TEHNOMIR
                $change_brand = ORM::factory('ChangeTmBrand')->where('replace_to_short', '=', Article::get_short_article($part_obj->brand))->find_all()->as_array();
                $INFO = Tminfo::instance();
                $INFO->SetLogin('Mir@eparts.kiev.ua');
                $INFO->SetPasswd('9506678d');
                if(!empty($change_brand) and !empty($change_brand[0]->replace_to ))
                {
                    $INFO = Tminfo::instance();
                    $$INFO->SetLogin('Mir@eparts.kiev.ua');
                    $INFO->SetPasswd('9506678d');
                    $setting = ORM::factory('Setting')->where('code_name', '=', 'tekhnomir_active_site')->find();
                    if (ORM::factory('Findfromip')->check_ip() AND $setting) {
                        $tm_items = [];
                        foreach ($change_brand as $brand_replace_one) {
                            $tm_items_time = $INFO->GetPrice($part_obj->article, $brand_replace_one->replace_from_short, 1);
                            $tm_items = array_merge($tm_items, $tm_items_time);
                        }
                    }
                }
                else
                {
                    $tm_items = $INFO->GetPrice($part_obj->article, $part_obj->brand, 1);
                }
            }

            $items = [];
            if ($tm_items) {

                $part_object = ['id'=>0, 'article_long'=>$json_price->article, 'brand_long' => $json_price->brand, 'name' => $json_price->name];
                $price_tm = $this->tehnomir->process_tm_in_product_page($tm_items, $part_object);

            }

            $best_parts = $this->get_best_match_new($price_tm, 0);

            $this->template->title = '15 Купить '. $json_price->brand . ' ' . str_replace('="', '', $json_price->article) . '  - цены в Eparts';
            $this->template->description = $json_price->brand . ' ' . str_replace('="', '', $json_price->article) . ' в интернет-магазине Eparts ➤ ➤ ➤ Огромный выбор оригинальных запчастей для автомобилей разных брендов и моделей! ✈ Доставка по Киеву и по всех городах Украины! ✔ Лучшие цены ✔ ☎ Заказать: 044-361-96-64 ✓ 067-291-18-25 ✓';
            $this->template->keywords = '';
            $this->template->author = '';
        }

	}

    public function action_article()
    {
        $id = $this->request->param('id');


        if (!empty($id)) {
            if(!is_object(json_decode(base64_decode(str_replace('_','=',$id)))))
            {
                $part = ORM::factory('Part')->where('id', '=', $id)->find()->as_array();
                if (empty($part['id'])) {
                    HTTP::redirect(URL::base(), 301);
                }
                else {
                    $url = Htmlparser::transliterate($part['brand'] . "-" . $part['article_long'] . "-" . substr($part['name'], 0, 50)) . "-" . $part['id'];
                }
                HTTP::redirect(URL::base() . 'katalog/produkt/' . $url, 301);
            }
            else
            {

                HTTP::redirect(URL::base() . 'katalog/produkt/' . $id, 301);
            }

        } else {
            HTTP::redirect(URL::base(), 301);
        }
    }


//
	public function action_category($slug)
	{
		$category = ORM::factory('Category')->where('level', '=', 0)->and_where('slug', '=', $slug)->find();
		
		if(!count($category))
			throw HTTP_Exception::factory(404, 'File not found!');

        $this->template->keywords = '';
        $this->template->author = ''; 

		$this->template->content = View::factory('catalog/category')
            ->bind('category', $category)
            ->bind('h1', $h1)
            ->bind('seo_data', $seo_data)
            ->bind('models', $models)
            ->bind('manufacture', $manufacture)
            ->bind('content_catalog',$content_catalog)//seo
		    ->bind('car', $car);

        $seo_identifier = $this->request->uri();/*.URL::query()*/
        $seo_data = ORM::factory('Seodata')->where('seo_identifier', '=', $seo_identifier)->find();

        $content_catalog = ORM::factory('Seodata')->where('seo_identifier', '=', $seo_identifier)->find();//seo



			
        if($this->url_array['manuf'] AND $this->url_array['model'])
        {
            if($this->url_array['type'])
            {
                $type_id = explode('-', $this->url_array['type']);
                $type_id = (integer)end($type_id);
				$manufacture = $this->tecdoc->get_manuf_info_by_url($this->url_array['manuf']);
                $car = $this->tecdoc->get_car_info_by_type_url($type_id);
				if(!count($car) || !count($manufacture))
					throw HTTP_Exception::factory(404, 'File not found!');



//				$h1 = $category->name." на ".$car['manuf_name']." ".$car['model_name']." ".$car['type_name'];
//                $this->template->title = "16 Купить ". $h1." в Киеве и Украине - цены на Eparts";
//                $this->template->description = $h1." в интернет-магазине Eparts ➤ ➤ ➤ Огромный выбор оригинальных запчастей для автомобилей разных брендов и моделей! ✈ Доставка по Киеву и по всех городах Украины! ✔ Лучшие цены ✔ ☎ Заказать: 044-361-96-64 ✓ 067-291-18-25 ✓";
//                "16🔧
                $this->template->section_titles = $content_catalog->section_titles ;

                $this->template->title = $content_catalog->title ? $content_catalog->title : 'Купить '.$category->name.' на '.mb_strtolower($car["manuf_name"]).' '.mb_strtolower($car["model_name"]).' '.mb_strtolower($car["type_name"]).' в Украине - ULC';

                $content_catalog->h1=$this->template->h1 = $content_catalog->h1 ? $content_catalog->h1 :( $h1 ='Купить '.$category->name.' на '.mb_strtolower($car["manuf_name"]).' '.mb_strtolower($car["model_name"]).' '.mb_strtolower($car["type_name"]).' в Киеве и по всей Украине');
                $this->template->description =$content_catalog->description ? $content_catalog->description :'Купить '.$category->name.' на '.mb_strtolower($car["manuf_name"]).' '.mb_strtolower($car["model_name"]).' '.mb_strtolower($car["type_name"]).' 🚘. Оригинальные '.$category->name.' на '.mb_strtolower($car["manuf_name"]).' '.mb_strtolower($car["model_name"]).' '.mb_strtolower($car["type_name"]).' купить по выгодной цене 💲. Быстрая доставка автозапчастей на территории всей Украины 🚚🇺🇦.  '.$category->name.' на '.mb_strtolower($car["manuf_name"]).' '.mb_strtolower($car["model_name"]).' '.mb_strtolower($car["type_name"]).' - только качественные запчасти!';
//        $this->template->keywords = '';
                $this->template->author = '';
            }
            else{
				$manufacture = $this->tecdoc->get_manuf_info_by_url($this->url_array['manuf']);
                $car = $this->tecdoc->get_car_info_by_model_url($this->url_array['model'], $this->url_array['manuf']);
				if(!count($car) || !count($manufacture))
					throw HTTP_Exception::factory(404, 'File not found!');
//                $h1 = $category->name." на ".$car['manuf_name']." ".$car['model_name'];
//                $this->template->title = "17 Купить ". $h1." в Киеве и Украине - цены на Eparts";
//                $this->template->description = $h1." в интернет-магазине Eparts ➤ ➤ ➤ Огромный выбор оригинальных запчастей для автомобилей разных брендов и моделей! ✈ Доставка по Киеву и по всех городах Украины! ✔ Лучшие цены ✔ ☎ Заказать: 044-361-96-64 ✓ 067-291-18-25 ✓";
//                "17🔧 '.

                $this->template->section_titles = $content_catalog->section_titles ;

                $this->template->title = $content_catalog->title ? $content_catalog->title : $category->name.' на '.$car["manuf_name"].' '.$car["model_name"].' купить в Украине - ULC';

                $content_catalog->h1=$this->template->h1 = $content_catalog->h1 ? $content_catalog->h1 :( $h1 =$category->name.' на '.$car["manuf_name"].' '.$car["model_name"].' купить в Киеве и по всей Украине');

                $this->template->description =$content_catalog->description ? $content_catalog->description :$category->name.' на '.$car["manuf_name"].' '.$car["model_name"].' для вашего автомобиля 🚘. '.$category->name.' на '.$car["manuf_name"].' '.$car["model_name"].' купить по выгодной цене 💲. Быстрая доставка автозапчастей на территории всей Украины 🚚🇺🇦. '.$category->name.' на '.$car["manuf_name"].' '.$car["model_name"].' - только оригинальный товар!';
//        $this->template->keywords = '';
                $this->template->author = '';
            }
        }

        else{
			if($this->url_array['manuf']) {
				$manufacture = $this->tecdoc->get_manuf_info_by_url($this->url_array['manuf']);
				if(!count($manufacture))
					throw HTTP_Exception::factory(404, 'File not found!');

                $models = $this->tecdoc->models_by_parent_category_id($category->id, $this->url_array['manuf']);

//                print_r($manufacture); exit();

                $h1 = $category->name.' на '.$manufacture['short_name'];
                $this->template->title = "18".$category->name.' на '.$manufacture['short_name'].' - цены в Eparts';
                $this->template->description = $category->name.' на '.$manufacture['short_name'].' в интернет-магазине Eparts ➤ ➤ ➤ Огромный выбор оригинальных запчастей для автомобилей разных брендов и моделей! ✈ Доставка по Киеву и по всех городах Украины! ✔ Лучшие цены ✔ ☎ Заказать: 044-361-96-64 ✓ 067-291-18-25 ✓';
			}
            else
            {
//                $h1 = $category->name;
//                "19

                $this->template->section_titles = $content_catalog->section_titles ;

                $this->template->title = $content_catalog->title ? $content_catalog->title : ' 🔧Купить ' .$category->name.' в Киеве и по всей Украине от компании ULC';

                $content_catalog->h1=$this->template->h1 = $content_catalog->h1 ? $content_catalog->h1 :( $h1 = 'Купить ' .$category->name. ' в Украине');

                $this->template->description =$content_catalog->description ? $content_catalog->description : 'Купить ' .$category->name. ' для любых автомобилей 🚘. Автомобильные ' .$category->name. ' по выгодным ценам 💲 с быстрой доставкой по всей Украине 🚚🇺🇦. Подбирайте ' .$category->name. ' специально на вашу машину!';
//        $this->template->keywords = '';
                $this->template->author = '';
            }

        }
    }

    public function get_best_match_new($items, $part_id){

        $temp = array();

        foreach ($items AS $item){
            if ($item['delivery'] == 0) $item['delivery'] = 1;
            if(empty($item['amount']))
                $item['amount'] = 10;
            if ($item['amount'] < 0 ){
                unset($item);
                continue;
            };
            $temp[$part_id][] = array(
                'id' => $item['id'],
                'price' => $item['price'],
                'delivery' => $item['delivery'],
                'amount' => $item['amount'],
            );
        }

        $result = array();
        foreach ($temp AS $part_id => $array){
            $result = $this->smart_sort($array);
        }

        foreach ($items AS $key => $item){
            if (!in_array($item['id'], $result)) unset($items[$key]);
        }
        return $items;
    }
//
//	/**
//	 * Ищет позицию с минимальным сроком доставки и минимальной стоимостью
//	 * @param $array
//	 * @return mixed
//	 */
	public function smart_sort($array){

        usort($array, 'sort_by_delivery');
        $delivery = $array[0]['id'];

        usort($array, 'sort_by_price');
        $price = $array[0]['id'];

        $export = array(
            $delivery,
            $price
        );

		return $export;
	}

}
//
//} // End Admin_Pages
//

function sort_by_price($a, $b)
{
    $a['delivery'] = $a['delivery']==0 ? 1 : $a['delivery'];
    $b['delivery'] = $b['delivery']==0 ? 1 : $b['delivery'];
    if ($a['price'] == $b['price'] AND $a['delivery'] == $b['delivery']) return 0;
    elseif ($a['price'] == $b['price'])  return ($a['delivery'] > $b['delivery']) ? 1:-1;
    return ($a['price'] > $b['price']) ? 1:-1;

}

function sort_by_delivery($a, $b)
{
    $a['delivery'] = $a['delivery']==0 ? 1 : $a['delivery'];
    $b['delivery'] = $b['delivery']==0 ? 1 : $b['delivery'];
    if ($a['delivery'] == $b['delivery'] AND $a['price'] == $b['price']) return 0;
    elseif ($a['delivery'] == $b['delivery'])  return ($a['price'] > $b['price']) ? 1:-1;
    return ($a['delivery'] > $b['delivery']) ? 1:-1;

}
