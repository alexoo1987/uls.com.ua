<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Sitemap extends Controller_Application {

    public function action_index()
    {
        $this->template->content = View::factory('pages/sitemap')
            ->bind('h1', $h1)
            ->bind('manufacture', $manufacture)
            ->bind('categories', $categories);

        $this->template->title = "Карта сайта - интернет магазин автозапчастей Eparts";
        $this->template->h1 = "";
        $this->template->description = "";


        $h1 = "Карта сайта";

        $manufacture = $this->tecdoc->get_all_manufacture();

        $categories = ORM::factory('Category')->where('level', '=', 2)->order_by('id')->find_all()->as_array();

    }
}
