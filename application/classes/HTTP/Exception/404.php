<?php defined('SYSPATH') OR die('No direct script access.');


class HTTP_Exception_404 extends Kohana_HTTP_Exception_404
{

    public function get_response()
    {

        $route = Route::get('error')->uri(array('controller'=>'error','action' => '404', 'message' => 'Error 404: Not found'));

        $request = Request::factory($route)
            ->execute()
            ->send_headers()
            ->body();

        return Response::factory()
            ->status(404)
            ->body($request);

    }

}