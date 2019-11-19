<?php use Composer\Script\Event;

defined('SYSPATH') or die('No direct script access.');

abstract class Controller_Application extends Controller_Template {

    public $template = 'template';
    public $tecdoc;
    public $tehnomir;
    public $guest;
    public $url_array = [
        'manuf' => '',
        'model' => '',
        'type' => '',
        'category' => '',
        'filter' => '',
        'page' => '',
    ];

    public function __construct(Request $request, Response $response)
    {
        $this->tecdoc = Model::factory('NewTecdoc');
        $this->tehnomir = Model::factory('Tehnomir');
        if(ORM::factory('Client')->logged_in()) {
            $this->guest = false;
        }else{
            $this->guest = true;
        }
        parent::__construct( $request, $response);

    }

    private function getDeliveryMethods(){
        $delivery_methods = ORM::factory('DeliveryMethod')->find_all()->as_array();
        array_merge(
            [0 => '---'],
            array_combine(
                array_keys($delivery_methods, 'id'),
                array_keys($delivery_methods, 'name')
            )
        );
    }





    /**
     * The before() method is called before your controller action.
     * In our template controller we override this method so that we can
     * set up default values. These variables are then available to our
     * controllers if they need to be modified.
     */
    public function before()
    {
        parent::before();

        Helper_Seo::checkRedirectStatic();
        Helper_Seo::checkRedirectCategory();

        $SeoNoindexFlag = ORM::factory('SeoNoindex')->where('url', '=', Helper_Url::currentUrl())->find();
        if($SeoNoindexFlag->loaded()) {
            $this->template->noindex = true;
        }


        if ($this->auto_render)
        {
            //facebook credential
            View::set_global('site_name', __('ulc.com.ua'));
            $seo_identifier = $this->request->uri();
            $content_catalog = ORM::factory('Seodata')->where('seo_identifier', '=', $seo_identifier)->find();

            $this->template->content = '';
            $this->template->description = '';
            $this->template->robots = '';
//            $this->template->kewords = '';
            $this->template->title = '';
            $this->template->h1 = '';
            $this->template->author = '';
            $this->template->current_url = Helper_Url::createUrl(null,[],true);
            $this->template->uri = $this->request->uri();
            $this->template->currentPage = isset($_GET['page']) ? $_GET['page'] : false;
            $this->template->canonical =$content_catalog->canonical_address ? $content_catalog->canonical_address :  Helper_Url::getCanonicalUrl();
            $this->template->delivery_methods = $this->getDeliveryMethods();

            $this->template->styles = array(
                'chosen/chosen.min',
                'dist/bootstrap',
                'dist/font-awesome',
                'dist/styles_eparts4',
                'dist/animate',
                'dist/star-rating.min',
            );

            $this->template->scripts = array(
                'jquery.min',
                'bootstrap.min',
                'bootstrap.validate',
                'bootstrap.validate.ru',
                'login',
                'chosen/chosen.jquery.min',
                'jquery.form.min',
                'functions',
//                'dist/uncompressed/functions',
                'top_menu',
                'dist/icheck',
                'dist/moment-with-locales.min',
                'dist/bootstrap-datetimepicker.min',
                'dist/ionrangeslider',
                'dist/jqzoom',
                'dist/card-payment',
                'dist/owl-carousel',
                'dist/magnific',
                'dist/jquery.mask.min',
                'dist/bootstrap-notify.min',
                'dist/bootstrap-notify',
                'dist/custom_nd2',
                'dist/star-rating.min',
                'common/orders_add',
            );

            $manufacturer_slug = $this->request->param('manufacturer', false);
            $model_slug = $this->request->param('model', false);
            $modification_slug = $this->request->param('modification', false);
            $category_slug = $this->request->param('category', false);

            //////////////////////////
            //////// Get catalog_url and generic_article_ids

            //Clear car block if choose manufacturer
            $cookie = Cookie::get('car_modification', NULL);


            if ($manufacturer_slug AND $cookie) {
                $cookie_slug = $this->tecdoc->get_url_by_type($cookie)['manuf_url'];
                if ($cookie_slug != $manufacturer_slug){
                    Cookie::delete('car_modification');
                }
            }

            $modification_url = "";
            $modification_ids = false;
            $model_id = false;
            $active_categories = [];

            if($cookie)
            {
                $active_categories = $this->tecdoc->get_cat_id_by_type($cookie);
                $active_categories = array_column($active_categories, 'category_id');
                View::set_global('active_categories', $active_categories);
            }

            if(!$cookie)
            {
                $category = ORM::factory('Category')->where('slug', '=', $model_slug)->find();
                if(!empty($category->id))
                {
                    $category_slug = $model_slug;
                    $model_slug = "";
                } else if(!empty($category->id) && $model_slug != false)
                {
                    throw HTTP_Exception::factory(404, 'File not found!');
                }
                if($manufacturer_slug && $model_slug)
                {
                    $active_categories = $this->tecdoc->get_cat_id_by_model($model_slug, $manufacturer_slug);
                    $active_categories = array_column($active_categories, 'category_id');
                    View::set_global('active_categories', $active_categories);
                }
            }

            if($modification_slug) {
                $category = ORM::factory('Category')->where('slug', '=', $modification_slug)->find();
                if(!empty($category->id)) {
                    $modification_url = $manufacturer_slug.'/'.$model_slug.'/';
                } else {
                    $modification_ids_by_slug = explode('-', $modification_slug);
                    $modification_ids_by_slug = (integer)end($modification_ids_by_slug);

                    $modification_url = $manufacturer_slug.'/'.$model_slug.'/'.$modification_slug.'/';
                    $modification = $this->tecdoc->get_info_by_type_analog_old($modification_ids_by_slug);

                    if($manufacturer_slug != $modification['manuf_slug'] || $model_slug != $modification['model_slug'])
                    {
                        if(!empty($category_slug)){
                            $redirectUri = 'katalog/' . $manufacturer_slug . '/';
                            if (!empty($model_slug)) {
                                $redirectUri .= $model_slug . '/';
                            }
                            $redirectUri .= $category_slug;
                            HTTP::redirect(Helper_Url::createUrl($redirectUri), 301);
                        }
                        else{
                            HTTP::redirect(URL::base() . 'katalog/' .$manufacturer_slug.'/'.$model_slug, 301);
                        }
                    }

                    $modification['full_description'] = $modification['brand']." ".$modification['model']." ".$modification['description'];
                    $modification['full_slug'] = $modification['manuf_url']."/".$modification['mod_url']."/".$modification['type_url'];
                    $modification_ids[] = $modification_ids_by_slug;
                }
            } elseif($manufacturer_slug && $model_slug) {
                //!!!
                $modification_url = $manufacturer_slug.'/'.$model_slug.'/';
            }

            if(!$model_id && !$modification_ids) {
                $cookie_modification_id = Cookie::get('car_modification', NULL);
                if(!empty($cookie_modification_id)) {
                    $modification = $this->tecdoc->get_info_by_type_analog_old($cookie_modification_id);
                    $modification['full_description'] = $modification['brand']." ".$modification['model']." ".$modification['description'];
                    $modification['full_slug'] = $modification['manuf_url']."/".$modification['mod_url']."/".$modification['type_url'];
                    $modification_url = $modification['full_slug']."/";
                    $modification_ids = array($modification['id']);
                }
            }

            View::set_global('modification_url', $modification_url);

            $this->route($this->request);



            ///////// end //////////////////////////////
        }
    }

    /**
     * The after() method is called after your controller action.
     * In our template controller we override this method so that we can
     * make any last minute modifications to the template before anything
     * is rendered.
     */
    public function after()
    {
        parent::after();
    }

    private function route($request)
    {


        $manufacturer_slug = $this->request->param('manufacturer', false);
        $model_slug = $this->request->param('model', false);
        $modification_slug = $this->request->param('modification', false);
        $category_slug = $this->request->param('category', false);
        $page = $this->request->param('page', false);
        $filter = $this->request->param('filter', false);

        $this->url_array['manuf'] = $manufacturer_slug;
        $this->url_array['model'] = $model_slug;
        $this->url_array['type'] = $modification_slug;
        $this->url_array['category'] = $category_slug;
        $this->url_array['filter'] = $filter;
        $this->url_array['page'] = $page;

        if (!$this->url_array['page'] AND $this->url_array['filter'] AND $this->url_array['type'] AND $this->url_array['category'])
        {
            $pos = strripos($this->url_array['category'], 'filter');
            $pos2 = strripos($this->url_array['filter'], 'page');
            if($pos !== false AND $pos2 !== false)
            {
                $this->url_array['page'] = $this->url_array['filter'];
                $this->url_array['filter'] = $this->url_array['category'];
                $this->url_array['category'] = $this->url_array['type'];
                $this->url_array['type'] = "";
            }
            elseif ($pos2 !== false AND $pos === false)
            {
                $this->url_array['page'] = $this->url_array['filter'];
                $this->url_array['filter'] = "";
            }
        }
        elseif($this->url_array['type'] AND $this->url_array['category'] AND $this->url_array['filter'])
        {
            if($this->url_array['filter'] AND ! $this->url_array['page'])
            {
                $pos = strripos($this->url_array['filter'], 'page');
                if($pos !== false)
                {
                    $this->url_array['page'] = $this->url_array['filter'];
                    $this->url_array['filter'] = "";
                }
            }
        }

        elseif (!$this->url_array['filter'] AND $this->url_array['type'] AND $this->url_array['category'])
        {
            $pos = strripos($this->url_array['category'], 'page');
            if($pos !== false)
            {
                $this->url_array['page'] = $this->url_array['category'];
                $this->url_array['category'] = $this->url_array['type'];
                $this->url_array['type'] = "";
            }
            else
            {
                $pos = strripos($this->url_array['category'], 'filter');
                if($pos !== false)
                {
                    $this->url_array['filter'] = $this->url_array['category'];
                    $this->url_array['category'] = $this->url_array['type'];
                    $this->url_array['type'] = "";
                }
            }
        }

        if($manufacturer_slug AND !$category_slug){
            $category = ORM::factory('Category')->where('slug', '=', $manufacturer_slug)->find();
            if (!empty($category->id)) {
                $this->url_array['manuf'] = "";
                $this->url_array['category'] = $manufacturer_slug;
                $this->request->action('cat');
            }
        }

        if($model_slug AND !$category_slug){
            $category = ORM::factory('Category')->where('slug', '=', $model_slug)->find();
            if (!empty($category->id)) {
                $this->url_array['model'] = "";
                $this->url_array['category'] = $model_slug;
                $this->request->action('cat_mod');
            }
        }

        if($modification_slug AND !$category_slug){
            $category = ORM::factory('Category')->where('slug', '=', $modification_slug)->find();
            if (!empty($category->id)) {
                $this->url_array['type'] = "";
                $this->url_array['category'] = $modification_slug;
                $this->request->action('parts');
            }
        }


    }

}