<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: artem
 * Date: 12.12.16
 * Time: 23:26
 */
class Task_Mongotecdoc extends Minion_Task
{

    public $step = 1000;

    public function _execute(array $params)
    {

        $mongo = new MongoClient(); // соединение

        $mongo->selectDB('eparts')->selectCollection('main')->remove();//чистим коллекцию

        $collection = $mongo->selectDB('eparts')->selectCollection('main');

//        //cоздаем индексы
//        $collection->createIndex(['type_id' => 1]);
        $collection->createIndex(['model_id' => 1]);
        $collection->createIndex(['category_id' => 1]);
//        $collection->createIndex(['part_id' => 1]);


        $query_all_category = "SELECT id, slug FROM categories WHERE categories.`level` = 2";
        $all_categories = DB::query(Database::SELECT, $query_all_category)->execute('tecdoc')->as_array(); // вытягиваем все категории

        foreach ($all_categories as $category) {

            $query_category = "SELECT
               TYP_MOD_ID AS model_id,
               TYP_ID AS type_id,
               parts.id AS part_id
           FROM
               TYPES
           LEFT JOIN LINK_LA_TYP ON TYP_ID = LAT_TYP_ID
           LEFT JOIN LINK_ART ON LA_ID = LAT_LA_ID
           LEFT JOIN category_to_tecdoc ON LA_GA_ID = ga_tecdoc_id
           AND LAT_GA_ID = ga_tecdoc_id
           INNER JOIN parts ON tecdoc_id = LA_ART_ID
           WHERE
               category_id = " . $category['id'] . "
           AND EXISTS (
               SELECT
                   1
               FROM
                   priceitems
               WHERE
                   part_id = parts.id
               LIMIT 1
)

ORDER BY model_id, type_id";
            $result = DB::query(Database::SELECT, $query_category)->execute('tecdoc')->as_array();


            $data = [];


            foreach ($result as $key => $row) {

                if (!isset($data[(int)$row['model_id']])) {
                    $data[(int)$row['model_id']] = [
                        'category_id' => (int)$category['id'],
                        'model_id' => (int)$row['model_id'],
                        'types' => []
                    ];
                }


                if (!isset($data[(int)$row['model_id']]['types'][(int)$row['type_id']])) {
                    $data[(int)$row['model_id']]['types'][(int)$row['type_id']] = [
                        'type_id' => (int)$row['type_id'],
                        'parts' => []
                    ];
                }

                $data[(int)$row['model_id']]['types'][(int)$row['type_id']]['parts'][] = [
                    'part_id' => $row['part_id']
                ];

            }

            foreach ($data as $key => $row) {
                $data[$key]['types'] = array_values($data[$key]['types']);
            }


            $count = count($data);

            if ($count > $this->step) {
                $chunks = (array_chunk($data, $this->step));
                foreach ($chunks as $chunk) {
                    $collection->batchInsert($chunk);
                    $this->log("Записали " . count($chunk) . " строк в категории " . $category['slug']);
                }
            } elseif ($count > 0) {
                $collection->batchInsert($data);
                $this->log("Записали $count строк в категории " . $category['slug']);
            } else {
                $this->log("Категория " . $category['slug'] . " пустая!");
            }

            //реконнект
            $mongo->close();
            $mongo = new MongoClient();
            $collection = $mongo->selectDB('eparts')->selectCollection('main');
        }

        //количество
        $count = $collection->count();
        $this->log($count . " записей хранит монго");

        $mongo->close();
    }


    public function log($message)
    {
        fwrite(STDOUT, date('Y-m-d H:i:s') . "___________" . $message . "\n");
    }
}
