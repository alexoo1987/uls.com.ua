<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Created by PhpStorm.
 * User: artem
 * Date: 12.12.16
 * Time: 23:26
 */

class Task_Sitemap extends Minion_Task
{
    protected function _execute(array $params)
    {
        echo date('Y-m-d H:i:s') . "_____BEGIN Synchronization\n";

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
            $xml = "\n<url><loc>" . $host .  "katalog/" . urlencode($category) . "</loc></url>";
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
        $tecdoc = Model::factory('NewTecdoc');
        $manufacturers = $tecdoc->get_all_manufacture();

        file_put_contents($location.$file.$current.$ext, $begin);
        $sitemap_index [] = $location . $file . $current . $ext;
        foreach ($manufacturers AS $manufacturer) {
            $xml = "\n<url><loc>" . $host .  "katalog/" . urlencode($manufacturer['url']) . "</loc></url>";
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
        foreach ($manufacturers AS $manufacturer) {
            $models = $tecdoc->get_all_for_id_manufactures($manufacturer['id']);
            foreach ($models AS $model) {
                $xml = "\n<url><loc>" . $host . 'katalog/' . urlencode($manufacturer['url']) . "/" . urlencode($model['url_model']) . "</loc></url>";
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
//        $file = 'sitemap-products';
//        $strings = 0;
//        $current = 1;
//
//        # /catalog/article/
//        file_put_contents($location.$file.$current.$ext, $begin);
//        $sitemap_index [] = $location . $file . $current . $ext;
//        $articles_count = Model::factory('Part')->count_all();
//        for ($i = 0; $i < $articles_count; $i += 5000) {
//            $articles = Model::factory('Part')->limit(5000)->offset($i)->find_all()->as_array();
//            foreach ($articles AS $value) {
////            foreach ($articles AS $key => $article) {
//
//                if ($value->tecdoc_id){
//                    $tecdoc = ORM::factory('Tecdoc');
//                    $part_tecdoc = $tecdoc->get_part($value->tecdoc_id);
//                    $url = Htmlparser::transliterate($part_tecdoc['brand'] . "-" . $part_tecdoc['article_nr'] . "-" . substr($part_tecdoc['description'], 0, 50)) . "-" .$value->id;
//                }
//                else{
//                    $url = Htmlparser::transliterate($value->brand . "-" . $value->article_long . "-" . substr($value->name, 0, 50)) . "-" . $value->id;
//                }
//
//                $xml = "\n<url><loc>" . $host . 'catalog/product/' . $url . "</loc></url>";
////                $xml = "\n<url><loc>" . $host . 'catalog/product/' . $article . "</loc></url>";
//
//                file_put_contents($location . $file . $current . $ext, $xml, FILE_APPEND);
//                $strings++;
//                //if 49999 string in file, create new
//                if ($strings == 49999) {
//                    //write end
//                    file_put_contents($location . $file . $current . $ext, $end, FILE_APPEND);
//                    //write to sitemap index
//                    $sitemap_index [] = $location . $file . $current . $ext;
//                    //get next file
//                    $current++;
//                    //write begin
//                    file_put_contents($location . $file . $current . $ext, $begin, FILE_APPEND);
//                    //empty string
//                    $strings = 0;
//                }
//            }
//        }
//
//        //write end
//        if ($strings != 0) file_put_contents($location . $file . $current . $ext, $end, FILE_APPEND);


        ///////////////////// PAGES ///////////////////////
        $file = 'sitemap_info';
        $strings = 0;
        $current = 1;

        # /catalog/article/
        $pages = Model::factory('Page')->where('active', '=', 1)->find_all()->as_array(NULL, 'syn');

        file_put_contents($location.$file.$current.$ext, $begin);
        $sitemap_index [] = $location . $file . $current . $ext;
        foreach ($pages AS $one => $page) {
			if($page != 'original')
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
        echo date('Y-m-d H:i:s') . "_____END\n";

    }
}