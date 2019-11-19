<?php defined('SYSPATH') or die('No direct script access.');
class Task_Genereteseomodels extends Minion_Task {
    protected function _execute(array $params)
    {
		$tecdoc = Model::factory('Tecdoc');
        $manufacturers = $tecdoc->get_manufacturers(false, false, false, true);

        foreach ($manufacturers as $manufacturer) {
            $manufacturer_str = ucfirst(strtolower($manufacturer['brand']));
            Minion_CLI::write($manufacturer_str);

            $models = $tecdoc->get_cars(false, $manufacturer['id']);
            foreach ($models as $model) {
                $modifications = $tecdoc->get_types(false, $model['id']);
                $years = array();
                $engines = array();
                foreach ($modifications as $modification) {
                    $start_year = substr($modification['start_date'], 0, 4);
                    if(!empty($modification['end_date'])) {
                        $end_year = substr($modification['end_date'], 0, 4);
                    } else {
                        $end_year = date('Y');
                    }

                    $years = array_merge($years, range($start_year, $end_year));

                    $engines[] = $modification['capacity'];
                }
                $years = array_unique($years);
                $engines = array_unique($engines);
                sort($years);
                sort($engines);

                $years_str = implode(", ", $years);
                $engines_str = implode(", ", $engines);
                $model_str = $model['short_description'];

                $slug = 'katalog/'.$manufacturer['slug'].'/'.$model['slug'].'/';

                foreach (ORM::factory('Category')->where('level', '=', 2)->order_by('id')->find_all()->as_array() as $category) {
                    $seo_data = ORM::factory('Seodata');
                    $seo_data->seo_identifier = $slug.$category->slug;
                    $seo_data->title = "Купить ".$category->name." на ".$manufacturer_str." ".$model_str." - ".$engines_str." в магазине eparts  ".$years_str.", в Киеве, Харькове, Одесса, Днепропетровск";
                    $seo_data->h1 = $category->name." на ".$manufacturer_str." ".$model_str;
                    $seo_data->description = $category->name." на ".$manufacturer_str." модели ".$model_str." года выпуска ".$years_str." с обьемом ".$engines_str.". Киев, Харьков, Одесса, Днепровск!";
                    $seo_data->keywords = $category->name."  ".$manufacturer_str." ".$model_str." ".$years_str.",  ".
                                          $manufacturer_str." ".$model_str." ".$category->name." ".$years_str.", ".
                                          $model_str." ".$years_str." ".$manufacturer_str." ".$category->name.", ".
                                          $years_str." ".$model_str." ".$category->name." ".$manufacturer_str.", ".
                                          $category->name." ".$years_str." ".$model_str." ".$manufacturer_str.", ".
                                          $manufacturer_str." ".$years_str." ".$category->name." ".$model_str;
                    $seo_data->content = "
В нашем интернет магазине Eparts вы можете купить ".$category->name." на ".$manufacturer_str." ".$model_str." с объемом двигателя ".$engines_str.". Так же вы можете получить полную консультацию по выбранной вами детали, узнать о новых акциях и скидках и уточнить сроки поставки в ваш город.<br>
Для более точного подбора детали воспользуйтесь нашей интерактивной формой. И после выбора интересующего вас товара позвоните по одному из указанных выше номеров.<br>
Так же в нашем каталоге вы можете выбрать интересующего вас производителя на автомобиля ".$manufacturer_str." ".$model_str." в категории ".$category->name.".<br>
В нашем каталоге представлены актуальные фото товара, а также подробное описание с характеристиками к ним. Для более комфортного и точного подбора детали на ".$manufacturer_str." ".$model_str.". В случае возникновения вопросов или по консультации обратитесь к нам. Номера телефонов, вы можете видеть в верху страницы. Наши специалисты не только помогут подобрать деталь, а также сделают все возможное чтобы выбранная вами деталь, попала к вам в руки как можно быстрее.";
                    $seo_data->save();
                }
            }
        }

  //       ob_start();
		// var_dump($result);
		// $output = ob_get_clean();

		// Minion_CLI::write($output);
    }
}

function sortfunc($a,$b){
    return strlen($b)-strlen($a);
}

?>
