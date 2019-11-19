<?php defined('SYSPATH') or die('No direct script access.');
ini_set('memory_limit', '2G');
class Controller_Admin_Images extends Controller_Admin_Application {
    public function action_update() {
        if(!ORM::factory('Permission')->checkPermission('crosses')) Controller::redirect('admin');

        $this->template->title = 'Обновление изображений :: Шаг 1';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $this->template->content = View::factory('admin/images/update')
            ->bind('data', $data);

        $this->template->scripts[] = 'bootstrap.validate';
        $this->template->scripts[] = 'bootstrap.validate.ru';
        $this->template->scripts[] = 'common/images_update';
    }

    public function action_update_step2() {
        if(!ORM::factory('Permission')->checkPermission('crosses')) Controller::redirect('admin');

        $this->template->content = View::factory('admin/images/update_step2')
            ->bind('permissions', $permissions)
            ->bind('filepath', $filepath)
            ->bind('date_time', $date_time)
            ->bind('columns', $columns)
            ->bind('data', $data);

        $this->template->title = 'Обновление изображений :: Шаг 2';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';


        $this->template->scripts[] = 'bootstrap.validate';
        $this->template->scripts[] = 'bootstrap.validate.ru';
        $this->template->scripts[] = 'common/images_update_step2';

        if (HTTP_Request::POST == $this->request->method())
        {
            $date_time = date("YmdHis");
            $filepath = Upload::save($_FILES['filename'], "images_article_".$date_time.".csv", "uploads");
            $f = fopen('php://memory', 'w+');
            fwrite($f, iconv('CP1251', 'UTF-8', file_get_contents($filepath)));
            rewind($f);
            $columns = fgetcsv($f, 0, ';', '"');
            fclose($f);
            $columns = array('' => '---') + $columns;

//            $image_path = '/var/tecdoc_png_new/custom/'.$date_time;
//            mkdir($image_path, 0777);
//            $filepath_zip = Upload::save($_FILES['photos'], "images_article_".$date_time.".zip", "/var/tecdoc_png_new/custom/".$date_time);
//            $zip = new ZipArchive;
//            $zip->open($image_path.'/images_article_'.$date_time.'.zip');
//            $zip->extractTo($image_path);
//            $zip->close();
//            unlink($image_path.'/images_article_'.$date_time.'.zip');
        }
        else Controller::redirect('admin/images/update');
    }

    public function action_update_step3() {
        if(!ORM::factory('Permission')->checkPermission('crosses')) Controller::redirect('admin');

        $this->template->content = View::factory('admin/images/update_step3')
            ->bind('permissions', $permissions)
            ->bind('lines_count', $lines_count)
            ->bind('date_time', $date_time)
            ->bind('data', $data);

        $this->template->title = 'Обновление изображений :: Шаг 3';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        $this->template->scripts[] = 'common/images_update_step3';

        if (HTTP_Request::POST == $this->request->method())
        {
            $sess_data = array();
            $sess_data['filepath'] = $this->request->post('filepath');
            $sess_data['date_time'] = $this->request->post('date_time');
            $date_time = $this->request->post('date_time');
            $sess_data['article'] = $this->request->post('article');
            $sess_data['brand'] = $this->request->post('brand');
            $sess_data['brand_text'] = $this->request->post('brand_text');
            $sess_data['image_path'] = $this->request->post('image_path');
            $sess_data['lines_processed'] = 1;

            $lines_count = 0;

            $f = fopen('php://memory', 'w+');
            fwrite($f, iconv('CP1251', 'UTF-8', file_get_contents($sess_data['filepath'])));
            rewind($f);

            while (fgetcsv($f, 0, ';', '"') !== false) $lines_count++;

            fclose($f);

            $sess_data['lines_count'] = $lines_count;

            Session::instance()->set("images_parser", $sess_data);

        } else Controller::redirect('admin/images/update');
    }

    public function action_proccess() {
        $this->auto_render = false;
        $json = array();

        $sess_data = Session::instance()->get('images_parser');
        $counter = 0;
        $lines_count = $sess_data['lines_count'];

        $f = fopen('php://memory', 'w+');
        fwrite($f, iconv('CP1251', 'UTF-8', file_get_contents($sess_data['filepath'])));
        rewind($f);

        $trim_charset = " \t\n\r\0.'\"(),";

        for($i = 0; $data = fgetcsv($f, 0, ';', '"'); $i++)
        {
            if($i < $sess_data['lines_processed']) continue;
            if($counter >= 1000) break;
//            print_r($data); exit();

            if(empty($sess_data['brand']) && $sess_data['brand'] !== '0')
                $brand = Article::get_short_article(trim($sess_data['brand_text'], $trim_charset));
            else
                $brand = Article::get_short_article(trim($data[$sess_data['brand']], $trim_charset));

            $brand_instance = ORM::factory('Brand')->get_brand($brand, false, 0);
            $article_long = trim($data[$sess_data['article']], $trim_charset);
            $article_long = $brand_instance->apply_rules($article_long);
            $article = Article::get_short_article($article_long);

            $query = "UPDATE parts SET images = '/custom/".$sess_data['date_time']."/".$data[$sess_data['image_path']]."' WHERE article = '".$article."' AND brand ='".$brand."' ";
            DB::query(Database::UPDATE,$query)->execute('tecdoc');

            $sess_data['lines_processed']++;
            $counter++;
        }

        Session::instance()->set("images_parser", $sess_data);

        $json['current'] = $sess_data['lines_processed'];
        $json['status'] = ($sess_data['lines_processed'] >= $lines_count) ? "complete" : "continue";
        fclose($f);
        $this->response->body(json_encode($json));
    }

} // End Admin_User



class Validation_Exception extends Exception {};
