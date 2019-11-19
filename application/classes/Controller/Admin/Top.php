<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Top extends Controller_Admin_Application{
    public function action_index()
    {
        $this->template->title = 'Топ продаж';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';
        $this->template->content = View::factory('admin/top/index')
            ->bind('filters', $filters);
        $filters = "Hello";
        $sql = "TRUNCATE TABLE top_orderitems";
        DB::query(NULL, 'TRUNCATE `' . ORM::factory('TopOrderitem')->table_name() . '`')->execute();
        $sql = "SELECT *, COUNT(id) as countid from orderitems GROUP BY article ORDER BY countid DESC LIMIT 1700";
        $all_orders = DB::query(Database::SELECT,$sql )->execute()->as_array();
        foreach ($all_orders as $order)
        {
            $log = ORM::factory('TopOrderitem');
            $log
                ->set('article', $order['article'])
                ->save();
        }
    }
//    public function action_img()
//    {
//        $url = "KOLBENSCHMIDT 40830610";
//        if(empty($_REQUEST['raw'])){
//            $raw = false;
//        }
//        else{
//            $raw = true;
//        }
//        echo $this->fetch_google($url, $raw);
//
//        exit();
//    }
//    public function fetch_google($u, $raw, $terms="sample search",$numpages=1,$user_agent='Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0')
//    {
//        $ch = curl_init();
//        $url = 'http://www.google.com/imghp?hl=en&tab=wi';
//        curl_setopt ($ch, CURLOPT_URL, $url);
//        curl_setopt ($ch, CURLOPT_USERAGENT, $user_agent);
//        curl_setopt ($ch, CURLOPT_HEADER, 0);
//        curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
//        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
//        curl_setopt ($ch, CURLOPT_REFERER, 'http://www.google.com/');
//        curl_setopt ($ch,CURLOPT_CONNECTTIMEOUT,120);
//        curl_setopt ($ch,CURLOPT_TIMEOUT,120);
//        curl_setopt ($ch,CURLOPT_MAXREDIRS,10);
//        curl_setopt ($ch,CURLOPT_COOKIEFILE,"cookie.txt");
//        curl_setopt ($ch,CURLOPT_COOKIEJAR,"cookie.txt");
//        curl_exec($ch);
//        $searched="";
//        for($i=0;$i<=$numpages;$i++)
//        {
//            $ch = curl_init();
//            $url="http://www.google.com/searchbyimage?hl=en&image_url=".urlencode($u);
//            curl_setopt ($ch, CURLOPT_URL, $url);
//            curl_setopt ($ch, CURLOPT_USERAGENT, $user_agent);
//            curl_setopt ($ch, CURLOPT_HEADER, 0);
//            curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
//            curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
//            curl_setopt ($ch, CURLOPT_REFERER, 'http://www.google.com/imghp?hl=en&tab=wi');
//            curl_setopt ($ch,CURLOPT_CONNECTTIMEOUT,120);
//            curl_setopt ($ch,CURLOPT_TIMEOUT,120);
//            curl_setopt ($ch,CURLOPT_MAXREDIRS,10);
//            curl_setopt ($ch,CURLOPT_COOKIEFILE,"cookie.txt");
//            curl_setopt ($ch,CURLOPT_COOKIEJAR,"cookie.txt");
//            $searched=$searched.curl_exec ($ch);
//            curl_close ($ch);
//        }
//        if($raw){
//            return $searched;
//        }
//        else{
//            $matches = array();
//            preg_match('/Best guess for this image:[^<]+<a[^>]+>([^<]+)/', $searched, $matches);
//            return (count($matches) > 1 ? $matches[1] : false);
//        }
//    }
}