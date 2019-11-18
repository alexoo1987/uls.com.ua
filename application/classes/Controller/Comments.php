<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Comments extends Controller_Application {
    //public $template = 'template_blank';

    public function action_index()
    {
        $this->template->content = View::factory('comments/form')
            ->bind('errors', $errors)
            ->bind('message', $message)
            ->bind('comments', $comments)
            ->bind('pagination', $pagination)
            ->bind('data', $data)
            ->bind('header', $header);

        $this->template->title = 'Все отзывы - интернет магазин автозапчастей Eparts';
        $this->template->description = '';
        $this->template->keywords = '';
        $this->template->author = '';

        if (HTTP_Request::POST == $this->request->method())
        {
            try {
                $comment = ORM::factory('Comment')->values($this->request->post(), array(
                    'name',
                    'number_order',
                    'order_position',
                    'rating',
                    'manager_rating',
                    'like',
                    'dis_like',
                    'suggestions',
                ));

                if(!empty($comment->like) && isset($comment->like))
                    $comment->like = join($comment->like, ', ');
                $comment->active = 0;

                $comment->save();

                // Reset values so form is not sticky
                $_POST = array();

                $message = "Ваш комментарий принят к рассмотрению и будет опубликован после проверки модератором.";

            } catch (ORM_Validation_Exception $e) {
                $data = $_POST;
                // Set failure message
                $message = 'Исправьте ошибки!';

                // Set errors using custom messages
                $errors = $e->errors('models');
            }
        }


        $header = ORM::factory('Page')->where('id', '=', 14)->find();
        $comments = ORM::factory('Comment')->where('active', '=', 1);
        $comments->reset(FALSE);
        $count = $comments->count_all();

        $pagination = Pagination::factory(array(
            'current_page' => array('source' => 'query_string', 'key' => 'page'),
            'total_items' => $count,
            'items_per_page' => 10,
        ))->route_params(array(
            'controller' =>  'comments',
            'action' =>  'index'
        ));
        $comments = $comments->limit($pagination->items_per_page)
            ->offset($pagination->offset)
            ->order_by('date_time', "desc")
            ->find_all()
            ->as_array();

        //$this->template->scripts[] = 'common/comments_form';

        $this->template->scripts[] = 'common/order_form_step3';
    }
}
