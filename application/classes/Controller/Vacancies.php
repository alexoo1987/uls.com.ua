<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Vacancies extends Controller_Application {

    public function action_index() {
        $this->template->content = View::factory('vacancies/form')
            ->bind('errors', $errors)
            ->bind('message', $message)
            ->bind('comments', $comments)
            ->bind('pagination', $pagination)
            ->bind('data', $data);

        $this->template->title = 'Вакансии - интернет магазин автозапчастей Eparts';
        $this->template->description = 'Вакансии компании Куряков Eparts';
        $this->template->keywords = '';
        $this->template->author = '';

        $comments = ORM::factory('Vacancies')->where('title', 'IS NOT', NULL);

        if(!empty($_GET['id'])) {
            $filters_id = $_GET['id'];
            $comments = $comments->and_where('id', '=', $filters_id);
        }

        $comments->reset(FALSE);
        $count = $comments->count_all();

        $pagination = Pagination::factory(array(
            'current_page' => array('source' => 'query_string', 'key' => 'page'),
            'total_items' => $count,
            'items_per_page' => 10,
        ))->route_params(array(
            'controller' =>  'vacancies',
            'action' =>  'index'
        ));
        $comments = $comments->limit($pagination->items_per_page)
            ->offset($pagination->offset)
            ->order_by('id', "desc")
            ->find_all()
            ->as_array();

        //$this->template->scripts[] = 'common/comments_form';
    }

} // End Admin_User



class Validation_Exception extends Exception {};
