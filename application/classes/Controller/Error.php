<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Error extends Controller_Application {

    public function before()
    {
        $this->template = View::factory($this->template);

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

    }

    public function action_404() {


        $this->template->content = View::factory('error/404');
        $this->template->title = 'Ошибка 404: Страница не найдена.';
        $this->template->h1 = 'Ошибка 404: Страница не найдена.';
        $delivery_methods = ORM::factory('DeliveryMethod')->find_all()->as_array();

        //facebook credential
        View::set_global('site_name', __('ulc.com.ua'));


        $this->template->description = '';
        $this->template->robots = '';
        $this->template->author = '';

        //$this->template->current_url = Helper_Url::createUrl(null,[],true);
        $this->template->uri = $this->request->uri();
        $this->template->currentPage = isset($_GET['page']) ? $_GET['page'] : false;
        $this->template->canonical = '';

        $this->template->delivery_methods = array_merge(
            [0 => '---'],
            array_combine(
                array_keys($delivery_methods, 'id'),
                array_keys($delivery_methods, 'name')
            )
        );

    }
}