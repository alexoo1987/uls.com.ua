<?php defined('SYSPATH') or die('No direct script access.');

class Libs_Phonet {

    private static $apiDomain = 'eparts-kiev.phonet.com.ua';
    private static $apiKey    = 'FwJPwChMyvaucGrZkSMl2Cc0PGbRbNrH';

    // private $sessionId        = null;
    private $sessionId        = '501A49C7919E0D49D9009FC8960DFCED';
    private static $apiUrl    = 'https://eparts-kiev.phonet.com.ua/';
    private static $authPath  = 'rest/security/authorize/';

    private static $makeCall         = 'rest/user/makeCall';
    private static $usersPath        = 'rest/users/';
    private static $companyCallsPath = 'rest/calls/company.api/';
    private static $usersCallsPath   = 'rest/calls/users.api/';
    private static $missedCallsPath  = 'rest/calls/missed.api/';
    private static $activeCallsPath  = 'rest/calls/active/v3';
    private static $_instance = null;

    private $startTime = null;
    private $endTime   = null;
    private $page      = null;
    private $rawsLimit = null;

    private static $defaultRawsLimit = 30;
    public static  $missedCallsDirections = [
        1  => 'внутренний звонок',
        2  => 'исходящий звонок',
        4  => 'входящий звонок',
        32 => 'установка на паузу (нет на месте)',
        64 => 'снятие с паузы (есть на месте)',
    ];

    /**
     * Make the constructor private.
     *
     * @return void
     */
    private function __construct() {}

    /**
     * Make the constructor private.
     *
     * @return void
     */
    private function __clone() {}

    /**
     * Get the instance of Libs_Phonet (Singleton)
     *
     * @return Libs_Phonet
     */
    public static function getInstance($startTime = null, $endTime = null, $page = null, $rawsLimit = null)
    {
        if (is_null(self::$_instance)) {
            $_instance = new self();
            if (!isset($_instance->sessionId))
                $_instance->sessionId = $_instance->getSessionId();

            is_null($startTime)
                ? $_instance->setStartTime()
                : $_instance->startTime = $startTime;

            is_null($endTime)
                ? $_instance->setEndTime()
                : $_instance->endTime = $endTime;

            is_null($page)
                ? $_instance->setPage()
                : $_instance->page = $page;

            $_instance->rawsLimit = is_null($rawsLimit)
                ? self::$defaultRawsLimit
                : $rawsLimit;

            self::$_instance = $_instance;
        }
        return self::$_instance;
    }

    public function setPage($page = null)
    {
        if (!is_null($page)) {
            $this->page = $page;
        } else {
            $this->page = !is_null(Request::current()->query('p'))
                ? (int) Request::current()->query('p') : 1;
        }
    }

    public function setStartTime($startTime = null)
    {
        if (!is_null($startTime)) {
            $this->startTime = $startTime;
        } else {
            $this->startTime = (is_null($dateFrom = Request::current()->query('date_from')))
                ? (time() - (2 * 24 * 3600)) * 1000
                : strtotime($dateFrom . ' 00:00:00') * 1000;
        }
    }

    public function setEndTime($endTime = null)
    {
        if (!is_null($endTime)) {
            $this->endTime = $endTime;
        } else {
            $this->endTime = (is_null($dateTo = Request::current()->query('date_to')))
                ? time() * 1000
                : strtotime($dateTo . ' 23:59:59') * 1000;
        }
    }

    public function getDateFrom()
    {
        return date("d.m.Y", $this->startTime / 1000);
    }

    public function getDateTo()
    {
        return date("d.m.Y", $this->endTime / 1000);
    }

    public function getPage()
    {
        return $this->page > 0 ? $this->page : null;
    }

    public function getNextPageUrl($countCalls)
    {
        if ($pageUrl = ($countCalls === $this->rawsLimit)) {
            $pageUrl = Helper_Url::createUrl(null, ['p' => $this->getPage() + 1], true);
        }
        return $pageUrl;
    }

    public function getPrevPageUrl($countCalls)
    {
        if (($prevPage = ($this->getPage() - 1)) === 1) {
            $prevPage = null;
        }
        $pageUrl = false;
        if ($this->page > 1) {
            $pageUrl = Helper_Url::createUrl(null, ['p' => $prevPage], true);
        }
        return $pageUrl;
    }

    /**
     * Get sessionId. Required for requests
     *
     * @return string
     */
    private function getSessionId()
    {
        $request = Request::factory(self::$apiUrl . self::$authPath)
            ->method(Request::POST)
            ->headers('content-type', 'application/json')
            ->body(json_encode([
                'domain' => self::$apiDomain,
                'apiKey' => self::$apiKey,
            ]))
            ->execute();
        $setCookie = $request->headers('set-cookie');
        preg_match('/JSESSIONID=(?<id>\w+);/', $setCookie, $jSessionId);

        return $jSessionId['id'];
    }

    private function getRequest($url)
    {
        return Request::factory(self::$apiUrl . $url)
            ->cookie('JSESSIONID', $this->sessionId)
            ->headers('content-type', 'application/json');
    }

    public function getCallsCompany()
    {
        $rawsLimit = $this->rawsLimit;
        $request = $this->getRequest(self::$companyCallsPath)
            ->method(Request::GET)
            ->query([
                'timeFrom' => $this->startTime,
                'timeTo'   => $this->endTime,
                'limit'    => $rawsLimit,
                'offset'   => ($this->page * $rawsLimit) - $rawsLimit,
            ])
            ->execute();
        return json_decode($request->body());
    }

    public function getCallsUsers($from = null, $to = null, $limit = null)
    {
        $rawsLimit = $this->rawsLimit;
        $request = $this->getRequest(self::$usersCallsPath)
            ->method(Request::GET)
            ->query([
                'timeFrom' => $this->startTime,
                'timeTo'   => $this->endTime,
                'limit'    => $rawsLimit,
                'offset'   => ($this->page * $rawsLimit) - $rawsLimit,
            ])
            ->execute();
        return json_decode($request->body());
    }

    public function getMissedCalls($from = null, $to = null, $limit = null)
    {
        $rawsLimit = $this->rawsLimit;
        $request = $this->getRequest(self::$missedCallsPath)
            ->method(Request::GET)
            ->query([
                'timeFrom' => $this->startTime,
                'timeTo'   => $this->endTime,
                'limit'    => $rawsLimit,
                'offset'   => ($this->page * $rawsLimit) - $rawsLimit,
            ])
            ->execute();
        return json_decode($request->body());
    }

    public function getActiveCalls()
    {
        $request = $this->getRequest(self::$activeCallsPath)
            ->method(Request::GET)
            ->execute();
        return json_decode($request->body());
    }

    public function getUsers()
    {
        $request = $this->getRequest(self::$usersPath)
            ->method(Request::GET)
            ->execute();
        return json_decode($request->body());
    }

    public function doCall($from, $to)
    {
        $request = $this->getRequest(self::$makeCall)
            ->method(Request::POST)
            ->body(json_encode([
                'legExt'      => $from,
                'otherLegNum' => $to
            ]))
            ->execute();
        return json_decode($request->body());
    }

    public function getStartDateFromRequest($time)
    {
        return ($time === false) ? $this->getDefaultStartTime() : $time;
    }

    public function getEndDateFromRequest($time)
    {
        return ($time === false) ? time() : $time;
    }
}
