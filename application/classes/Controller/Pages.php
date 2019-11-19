<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Pages extends Controller_Application {
	
	public function action_index()
	{

        $session=Session::instance();
	    //comment from user
        $post=$this->request->post();
	    if($post)
        {
            try {
                $this->send_email($post);
                $msg = "Ваш комментарий принят к рассмотрению и будет опубликован после проверки модератором.";
            }catch (Exception $exc){
                $msg = "Ваш комментарий не был отправлен.";
            }

            $session->set('message', $msg);
            return Controller::redirect($_SERVER['HTTP_REFERER']);
        }

	    if($session->get('message')){
	        $message=$session->get('message');
	        $session->delete('message');
        }

		$this->template->content = View::factory('pages/view')
//			->bind('manufacturers', $manufacturers)
			->bind('tree_list', $tree_list)
			->bind('page', $page)
			->bind('h1', $h1)
            ->bind('message', $message)
			->bind('content', $content);
		
		$url = $this->request->param('page');
        $content = ORM::factory('Page')->where('syn', '=', $url)->find()->content;
		// SEO. Redirect from duplicated page
		if (!is_null($url) && $url == "home") HTTP::redirect(Helper_Url::createUrl('/'), 301);

		if(empty($url)) $url = "home";
		$page = ORM::factory('Page')->where('syn', '=', $url)->find();


		if(!$page->loaded()){
		   throw new HTTP_Exception_404('page not found');
		};

		$seo_identifier = $this->request->uri()/*.URL::query()*/;
		$seo_data = ORM::factory('Seodata')->where('seo_identifier', '=', $seo_identifier)->find();
		if(!empty($seo_data->id)) {
	        $this->template->title = $seo_data->title;
	        $this->template->description = $seo_data->description;
	        $this->template->keywords = $seo_data->keywords;
	        $this->template->author = '';
	        $h1 = $seo_data->h1;
	        $content_text = $seo_data->content;
		} else {
	        $this->template->title = $page->title;
	        $this->template->description = $page->meta_description;
	        $this->template->keywords = $page->meta_keywords;
	        $this->template->author = '';
	        $h1 = $page->h1_title;
	        $content_text = "";
		}
	}

	private function send_email($post){
        Email::factory('СНГ', '')
            ->to('dima@eparts.kiev.ua') //
            ->from('no-reply@eparts.kiev.ua')
            ->message('Имя: '.$post['name'].' Телефон: '.$post['phone'].' Сообщение: '.$post['comment'], 'text/html')
            ->send();
    }

}
