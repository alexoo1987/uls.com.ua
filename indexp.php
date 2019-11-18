<?php

//    class Connect
//    {
//        public $_dsn;
//        public $_user;
//        public $_password;
//        public $_opt = [
//            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
//            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
//            PDO::ATTR_EMULATE_PREPARES   => false,
//        ];
//        public $pdo;
//
//        public function __construct($dsn, $user, $password)
//        {
//            try {
//                $this->_dsn = $dsn;
//                $this->_user = $user;
//                $this->_password = $password;
//                $this->pdo = new PDO($dsn, $user, $password, $this->_opt);
//            }
//            catch (PDOException $e) {
//                die("Хьюстон, у нас проблемы  \n" . $e->getMessage()."\n \n");
//            }
//        }
//    }

class Urlforownmodels
{
    public $_dsn;
    public $_user;
    public $_password;
    public $_opt = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'',
    ];
    public $pdo;

    public function __construct($dsn, $user, $password)
    {
        try {
            $this->_dsn = $dsn;
            $this->_user = $user;
            $this->_password = $password;
            $this->pdo = new PDO($dsn, $user, $password, $this->_opt);
        }
        catch (PDOException $e) {
            die("Хьюстон, у нас проблемы  \n" . $e->getMessage()."\n \n");
        }
    }

    public function select_insert()
    {
        $time_start = microtime(true);

        $insert = $this->pdo->prepare("INSERT INTO own_models (tecdoc_id, tecdoc_manufacture_id, short_name, url, active) VALUES (:tecdoc_id, :tecdoc_manufacture_id, :short_name, :url, :active)");
        $insert->bindParam(':tecdoc_id', $tecdoc_id_insert);
        $insert->bindParam(':tecdoc_manufacture_id', $tecdoc_manufacture_id_insert);
        $insert->bindParam(':url', $url_insert);
        $insert->bindParam(':short_name', $short_name_insert);
        $insert->bindParam(':active', $active_insert);

        $stmt = $this->pdo->query('SELECT MOD_ID,	MOD_MFA_ID,	TEX_TEXT AS MOD_CDS_TEXT, MODELS.MOD_PCON_END as END_DATA
            FROM MODELS
            INNER JOIN COUNTRY_DESIGNATIONS ON CDS_ID = MOD_CDS_ID
            INNER JOIN DES_TEXTS ON TEX_ID = CDS_TEX_ID
            WHERE	MOD_MFA_ID IN (SELECT tecdoc_id FROM own_manufactures WHERE active = 1) AND CDS_LNG_ID = 16
            ORDER BY	MOD_CDS_TEXT');

        while ($row = $stmt->fetch())
        {
            $short_name = trim($row['MOD_CDS_TEXT']);
            $short_name = preg_replace('/(\sc\s{1})|([а-яА-Я\/]*)|\(.*\)/u', '',$short_name);
            $short_name1 = trim($short_name);
            $short_name = $this->get_short_article($short_name1);
            $short_name = trim($short_name);
            $url = $this->get_short_url($short_name1);
            $url = trim($url);

            if($row['END_DATA'] < 198000 AND $row['END_DATA'] != NULL)
            {
                continue;
            }
            else
            {
                $tecdoc_id_insert = $row['MOD_ID'];
                $tecdoc_manufacture_id_insert = $row['MOD_MFA_ID'];
                $url_insert = $url;
                $short_name_insert = $short_name;
                $active_insert = 1;
                $insert->execute();
            }

        }

        $time_end = microtime(true);
        $time = $time_end - $time_start;
        echo "\n Время: $time секунд \n";

    }

    static function get_short_url($article) {
        $article = strtolower($article);
        $article = str_replace("  ", "-", $article);
        $article = str_replace("  ", "-", $article);
        $article = str_replace(" ", "-", $article);
        $article = str_replace("-", "-", $article);
        $article = str_replace("/", "-", $article);
        $article = str_replace("_", "-", $article);
        $article = str_replace(".", "-", $article);
        $article = str_replace("=", "-", $article);
        $article = str_replace("'", "-", $article);
        $article = str_replace("\"", "-", $article);
        $article = str_replace(",", "-", $article);
        $article = str_replace("?", "", $article);
        $article = str_replace("\\", "-", $article);
        $article = str_replace("*", "-", $article);
        $article = str_replace("#", "-", $article);
        $article = str_replace("(", "-", $article);
        $article = str_replace(")", "-", $article);
        $article = str_replace("--", "-", $article);
        $article = str_replace("---", "-", $article);
        return $article;
    }

    static function get_short_article($article) {
        $article = str_replace(" ", " ", $article);
        $article = str_replace("-", "-", $article);
        $article = str_replace("/", "-", $article);
        $article = str_replace("_", "-", $article);
        $article = str_replace(".", "-", $article);
        $article = str_replace("=", "-", $article);
        $article = str_replace("'", "-", $article);
        $article = str_replace("\"", "-", $article);
        $article = str_replace(",", "-", $article);
        $article = str_replace("?", "", $article);
        $article = str_replace("\\", "-", $article);
        $article = str_replace("*", "-", $article);
        $article = str_replace("#", "-", $article);
        $article = str_replace("(", "-", $article);
        $article = str_replace(")", "-", $article);
        return $article;
    }

}

class Urlforotypes
{
    public $_dsn;
    public $_user;
    public $_password;
    public $_opt = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'',
    ];
    public $pdo;

    public function __construct($dsn, $user, $password)
    {
        try {
            $this->_dsn = $dsn;
            $this->_user = $user;
            $this->_password = $password;
            $this->pdo = new PDO($dsn, $user, $password, $this->_opt);
        }
        catch (PDOException $e) {
            die("Хьюстон, у нас проблемы  \n" . $e->getMessage()."\n \n");
        }
    }

    public function insert()
    {
        $time_start = microtime(true);

        $insert = $this->pdo->prepare("INSERT INTO own_types (tecdoc_id, tecdoc_models_id, url, short_name) VALUES (:tecdoc_id, :tecdoc_manufacture_id, :url, :short_name)");
        $insert->bindParam(':tecdoc_id', $tecdoc_id_insert);
        $insert->bindParam(':tecdoc_manufacture_id', $tecdoc_manufacture_id_insert);
        $insert->bindParam(':short_name', $short_name_insert);
        $insert->bindParam(':url', $url_insert);

        $stmt = $this->pdo->query('SELECT TYP_ID AS id, TEX_TEXT AS name, TYP_MOD_ID AS model_id
            FROM TYPES
            LEFT JOIN COUNTRY_DESIGNATIONS ON TYP_CDS_ID = CDS_ID
            LEFT JOIN DES_TEXTS ON CDS_TEX_ID = TEX_ID
            WHERE CDS_LNG_ID = 16 AND TYP_MOD_ID IN (SELECT tecdoc_id from own_models)');

        while ($row = $stmt->fetch())
        {

            $short_name = trim($row['name']);
            $short_name1 = preg_replace('/(\sc\s{1})|([а-яА-Я\/]*)|\(.*\)/u', '',$short_name);
            $url = trim($short_name1);

            $url = $url."-".$row['id'];
            $url = Urlforownmodels::get_short_url($url);
            $url = trim($url);

            $tecdoc_id_insert = $row['id'];
            $tecdoc_manufacture_id_insert = $row['model_id'];
            $url_insert = $url;
            $short_name_insert = $short_name;
            $insert->execute();

        }

        $time_end = microtime(true);
        $time = $time_end - $time_start;
        echo "\n Время: $time секунд \n";

    }
}

class TypeCategoryGroup
{
    public $_dsn;
    public $_user;
    public $_password;
    public $_opt = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'',
    ];
    public $pdo;

    public function __construct($dsn, $user, $password)
    {
        try {
            $this->_dsn = $dsn;
            $this->_user = $user;
            $this->_password = $password;
            $this->pdo = new PDO($dsn, $user, $password, $this->_opt);
        }
        catch (PDOException $e) {
            die("Хьюстон, у нас проблемы  \n" . $e->getMessage()."\n \n");
        }
    }

    public function insert()
    {
        $time_start = microtime(true);

        $insert = $this->pdo->prepare("INSERT INTO type_category_group (category_id, type_id, tecdoc_categories_ids) VALUES (:category_id, :type_id, :tecdoc_categories_ids)");
        $insert->bindParam(':category_id', $category_id);
        $insert->bindParam(':type_id', $type_id);
        $insert->bindParam(':tecdoc_categories_ids', $tecdoc_categories_ids);

        $stmt = $this->pdo->query('SELECT tecdoc_id as type_id FROM own_types');

        foreach ($stmt as $one)
        {
            echo $one['type_id']."\n";

            $stmt2 = $this->pdo->query('SELECT category_id, GROUP_CONCAT(ga_tecdoc_id) as tecdoc_ids FROM category_to_tecdoc GROUP BY category_id');

            foreach ($stmt2 as $one2)
            {
                echo $one2['category_id']."\n";
                echo $one2['tecdoc_ids']."\n\n";

                $stmt3 = $this->pdo->query('SELECT LA_ART_ID FROM GENERIC_ARTICLES LEFT JOIN LINK_LA_TYP ON LAT_GA_ID = GA_ID LEFT JOIN LINK_ART ON LA_ID = LAT_LA_ID WHERE GA_ID IN ('.$one2['tecdoc_ids'].') AND LAT_TYP_ID = '.$one['type_id'].' LIMIT 1;');

                if(!empty($stmt3))
                {
                    $category_id = $one2['category_id'];
                    $type_id = $one['type_id'];
                    $tecdoc_categories_ids = $one2['tecdoc_ids'];
                    $insert->execute();
                }
            }
        }

        $time_end = microtime(true);
        $time = $time_end - $time_start;
        echo "\n Время: $time секунд \n";

    }
}

class GroupParts
{
    public $_dsn;
    public $_user;
    public $_password;
    public $_opt = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'',
    ];
    public $pdo;

    public function __construct($dsn, $user, $password)
    {
        try {
            $this->_dsn = $dsn;
            $this->_user = $user;
            $this->_password = $password;
            $this->pdo = new PDO($dsn, $user, $password, $this->_opt);
        }
        catch (PDOException $e) {
            die("Хьюстон, у нас проблемы  \n" . $e->getMessage()."\n \n");
        }
    }

    public function insert()
    {
        $time_start = microtime(true);

        $insert = $this->pdo->prepare("INSERT INTO group_parts (group_id, part_id) VALUES (:group_id, :part_id)");
        $insert->bindParam(':group_id', $group_id);
        $insert->bindParam(':part_id', $part_id);

        $count_all = 10313660;

        $count = 12000000;

        for($i = 0; $i<$count; $i=$i+100000)
        {
            $stmt3 = $this->pdo->query(' SELECT id, type_id, tecdoc_categories_ids as tecdoc_ids from type_category_group LIMIT '.$i.',100000');
            foreach ($stmt3 as $one)
            {

                $select_groups = $this->pdo->query('SELECT parts.id as part_id 
                  FROM GENERIC_ARTICLES 
                  LEFT JOIN LINK_LA_TYP ON LAT_GA_ID = GA_ID 
                  LEFT JOIN LINK_ART ON LA_ID = LAT_LA_ID 
                  LEFT JOIN parts ON LA_ART_ID = parts.tecdoc_id
                  WHERE GA_ID IN ('.$one['tecdoc_ids'].') AND LAT_TYP_ID = '.$one['type_id'].' ;');
                foreach ($select_groups as $group)
                {
                    echo $one['tecdoc_ids']."\n".$one['type_id']."\n".$group['part_id']."\n\n\n";
                    $group_id = $one['id'];
                    $part_id = $group['part_id'];
                    $insert->execute();
                }
            }

        }


        $time_end = microtime(true);
        $time = $time_end - $time_start;
        echo "\n Время: $time секунд \n";

    }
}

$dsn = 'mysql:dbname=dev_eparts;host=46.101.240.34';
$user = 'eparts';
$password = 'eparts';

$url_models_insert = new GroupParts($dsn, $user, $password);
$url_models_insert->insert();



