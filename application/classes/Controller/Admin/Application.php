<?php defined('SYSPATH') or die('No direct script access.');

abstract class Controller_Admin_Application extends Controller_Template {

    public $template = 'admin_template';

    public $tecdoc;

    public $activeStates = [2,3,5,6,8,16,17,19,31,32,33,34,36,37,38];

    public $disactiveStates = [1,4,7,13,14,15,18,35,39,41];

//    public function __construct(Request $request, Response $response)
//    {
//        $this->tecdoc = Model::factory('NewTecdoc');
//        parent::__construct( $request, $response);
//    }

    /**
     * The before() method is called before your controller action.
     * In our template controller we override this method so that we can
     * set up default values. These variables are then available to our
     * controllers if they need to be modified.
     */
    public function before()
    {
        parent::before();

        error_reporting( E_ERROR );

        if ($this->auto_render)
        {
            // keep the last url if it's not home/language
            /*if(Request::current()->action() != 'language')
            {
                Session::instance()->set('controller', Request::current()->uri());
            }

            if (Auth::instance()->logged_in('participant'))
            {
                $this->template->loged = TRUE;
            }

                        if (Auth::instance()->logged_in('admin'))
            {
                $this->template->loged = TRUE;
            }*/

            View::set_global('site_name', __('ulc.com.ua'));

            $this->template->content = '';
            $this->template->description = '';
            $this->template->kewords = '';
            $this->template->title = '';
            $this->template->author = '';

            $this->template->styles = array(
                'bootstrap.min',
//                            'dist/bootstrap',
                'bootstrap-responsive.min',
                'style_admin',
                'south-street/jquery-ui-1.10.4.custom.min',
            );

            $this->template->scripts = array(
                'jquery.min',
                'jquery-ui-1.10.4.custom.min',
                'bootstrap.min',
                'ui.datepicker-ru',
                'common/admin_td',
                'notify-combined.min',
                'common/jquery.mask.min'
            );
        }

        if(ORM::factory('Permission')->checkRole('Владелец') OR ORM::factory('Permission')->checkRole('Програмист') OR ORM::factory('Permission')->checkRole('Закупка') OR ORM::factory('Permission')->checkRole('Руководитель склада'))
        {
            $suppliers = ORM::factory('Supplier')->where('order_to', '<=', date('G:i:s', strtotime("+30 minutes")))->and_where('dont_show', '=', 0)->and_where('order_to', '>', date('G:i:s'))->and_where('order_to', 'IS NOT', NULL)->find_all()->as_array();
            $suppliers_ids = [];
            if(!empty($suppliers))
            {
                foreach ($suppliers as $supplier)
                {
                    $suppliers_ids[] = $supplier->id;
                }

                $order_items_warning = ORM::factory('Orderitem')->where('supplier_id', 'IN', $suppliers_ids)->and_where('state_id', '=', 16)->find_all()->as_array();

                if(!empty($order_items_warning))
                {
                    View::set_global('order_items_warning', $order_items_warning);
                }
            }

        }

        if (ORM::factory('Permission')->checkPermission('manage_orders')) {
            $newPaidOrders = ORM::factory('ClientPayment')
                ->where('user_id', '=', Auth::instance()->get_user()->id)
                ->and_where('manager_got_acquainted', '=', 0)
                ->group_by('order_id')
                ->find_all()
                ->as_array();
            View::set_global('newPaidOrders', $newPaidOrders);
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
        if ($this->auto_render)
        {
            //$this->template->styles = array_merge( $this->template->styles, $styles );
            //$this->template->scripts = array_merge( $this->template->scripts, $scripts );
        }
        parent::after();
    }

}