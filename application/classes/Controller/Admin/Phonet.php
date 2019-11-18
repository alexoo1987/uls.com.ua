<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Phonet extends Controller_Admin_Application
{
    private $phonet;

    public function before()
    {
        parent::before();

        if (!ORM::factory('Permission')->checkPermission('cash_movement')) Controller::redirect('admin');
        $this->phonet = Libs_Phonet::getInstance();
    }

    public function action_index()
    {
        $users = $this->phonet->getUsers();

        $this->template->content = View::factory('admin/phonet/index')
            ->bind('customers', $users);
        $this->template->title = 'IP телефония Phonet';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
    }

    public function action_missed()
    {
        $calls = $this->phonet->getMissedCalls();
        $directions = Libs_Phonet::$missedCallsDirections;

        $dateFrom    = $this->phonet->getDateFrom();
        $dateTo      = $this->phonet->getDateTo();
        $nextPageUrl = $this->phonet->getNextPageUrl(count($calls));
        $prevPageUrl = $this->phonet->getPrevPageUrl(count($calls));

        $this->template->content = View::factory('admin/phonet/missed_calls')
            ->bind('calls', $calls)
            ->bind('directions', $directions)
            ->bind('nextPageUrl', $nextPageUrl)
            ->bind('prevPageUrl', $prevPageUrl)
            ->bind('dateTo', $dateTo)
            ->bind('dateFrom', $dateFrom);
        $this->template->title = 'IP телефония Phonet';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
    }

    public function action_companycalls()
    {
        $calls  = $this->phonet->getCallsCompany();
        $directions = Libs_Phonet::$missedCallsDirections;

        $dateFrom    = $this->phonet->getDateFrom();
        $dateTo      = $this->phonet->getDateTo();
        $nextPageUrl = $this->phonet->getNextPageUrl(count($calls));
        $prevPageUrl = $this->phonet->getPrevPageUrl(count($calls));

        $this->template->content = View::factory('admin/phonet/company_calls')
            ->bind('calls', $calls)
            ->bind('directions', $directions)
            ->bind('nextPageUrl', $nextPageUrl)
            ->bind('prevPageUrl', $prevPageUrl)
            ->bind('dateTo', $dateTo)
            ->bind('dateFrom', $dateFrom);
        $this->template->title = 'IP телефония Phonet';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
        $this->template->dateFrom = $dateFrom;
        $this->template->dateTo = $dateTo;

        $this->template->scripts[] = 'common/phonet';
        $this->template->scripts[] = 'jquery-ui-1.10.4.custom.min';
    }

    public function action_userscalls()
    {
        $calls = $this->phonet->getCallsUsers();

        $dateFrom    = $this->phonet->getDateFrom();
        $dateTo      = $this->phonet->getDateTo();
        $nextPageUrl = $this->phonet->getNextPageUrl(count($calls));
        $prevPageUrl = $this->phonet->getPrevPageUrl(count($calls));

        $this->template->content = View::factory('admin/phonet/users_calls')
            ->bind('calls', $calls)
            ->bind('directions', $directions);
        $this->template->title = 'IP телефония Phonet';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
        $this->template->dateFrom = $dateFrom;
        $this->template->dateTo = $dateTo;
    }

    public function action_call()
    {
        $response = $this->phonet->doCall('777','+380961604216');
        var_dump($response);
    }

    public function action_active_calls()
    {
        $calls = $this->phonet->getActiveCalls();
        $directions = Libs_Phonet::$missedCallsDirections;

        $this->template->content = View::factory('admin/phonet/active_calls')
            ->bind('calls', $calls)
            ->bind('directions', $directions);
        $this->template->title = 'IP телефония Phonet';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
    }
}