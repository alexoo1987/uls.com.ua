<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 08.01.19
 * Time: 20:37
 */

class Controller_Migrations extends Controller_Application
{

    public function action_migrate(){

        $categories=ORM::factory('Category')->find_all();



        foreach ($categories as $category){

            $category->slug=Inflector::slug($category->name);
//            switch ($category->id){
//                case 786:
//                    $category->name='запчасти для электрики';
//                    break;
//                case 848:
//                    $category->name='запчасти для кузовов и составляющих';
//                    break;
//                case 595:
//                    $category->name='запчасти для то';
//                    break;
//                case 611:
//                    $category->name='запчасти для тормозной системы';
//                    break;
//                case 884:
//                    $category->name='смазки и жидкости';
//                    break;
//                case 629:
//                    $category->name='запчасти для двигателя';
//                    break;
//                case 889:
//                    $category->name='запчасти для системы выхлопа';
//                    break;
//                case 890:
//                    $category->name='запчасти для рулевого управления';
//                    break;
//                case 891:
//                    $category->name='запчасти для освещения';
//                    break;
//                case 687:
//                    $category->name='запчасти для подвески';
//                    break;
//                case 727:
//                    $category->name='запчасти для коробки передач';
//                    break;
//                case 753:
//                    $category->name='запчасти для охлаждения и отопления';
//                    break;
//
//            }


            $category->save();


        }

    }


}