<?php defined('SYSPATH') or die('No direct script access.');

Class Model_Mongo extends Model
{
    CONST DB = 'eparts';
    CONST HOST = '91.218.212.196';
    CONST COLLECTION = 'main';

    /**
     * @var MongoCollection - коллекция
     */
    public $collection;

    public function __construct()
    {
        $this->collection = $this->getCollection();
    }

    public function getCollection()
    {
        return $this->collection ? : (new MongoClient(self::HOST))->selectDB(self::DB)->selectCollection(self::COLLECTION);
    }


    /**
     * Ищет связи и возвращает обьектом либо массивом
     * @param array $query - вид ['field_name' => 'field_value']
     * @param array $fields - необходимые поля. Вид ['field_1' => 1, 'field_n' => 1]
     * @param int $limit - лимит
     * @param int $offset - пропустить
     * @param bool $as_array - вернуть как массив
     * @return array|MongoCursor
     */
    public function data($query = [], $fields = [], $limit = 100, $offset = 0, $as_array = true)
    {
        $data = $this->collection
            ->find($query, $fields)
            ->skip($offset);
            //->limit($limit);

        if ($as_array) {
            $data = iterator_to_array($data);
        }

        if (count($fields) == 1) {
            $data = array_column($data, key($fields));
        }

        return $data;
    }

    /**
     * Количество элеметов по $query
     * @param array $query -  - вид ['field_name' => 'field_value']
     * @return int - количество
     */
    public function count(array $query)
    {
        return $this->collection->count($query) ?: 0;
    }
}
