<?php

class TecdocUpdate
{
    private $_dsn;
    private $_user;
    private $_password;
    private $_opt = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'',
    ];
    private $pdo;

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

    public function UrlForOwnModels()
    {
        $time_start = microtime(true);

//        $table = "own_models";
//        try {
//            $sql ="CREATE table $table(
//             tecdoc_id INT( 11 ) AUTO_INCREMENT PRIMARY KEY,
//             tecdoc_manufacture_id INT( 11 ) NOT NULL,
//             short_name VARCHAR( 60 ) NOT NULL,
//             url VARCHAR( 60 ) NOT NULL,
//             active TINYINT( 1 ) NOT NULL);" ;
//            $this->pdo->exec($sql);
//            print("Created $table Table.\n");
//
//        } catch(PDOException $e) {
//            echo $e->getMessage();
//        }

        $stmt = $this->pdo->query('SELECT MOD_ID,	MOD_MFA_ID,	TEX_TEXT AS MOD_CDS_TEXT, MODELS.MOD_PCON_END as END_DATA
            FROM MODELS
            INNER JOIN COUNTRY_DESIGNATIONS ON CDS_ID = MOD_CDS_ID
            INNER JOIN DES_TEXTS ON TEX_ID = CDS_TEX_ID
            WHERE	MOD_MFA_ID IN (SELECT tecdoc_id FROM own_manufactures WHERE active = 1) AND CDS_LNG_ID = 16
            ORDER BY	MOD_CDS_TEXT');

        $sql = 'INSERT INTO own_models (tecdoc_id, tecdoc_manufacture_id, short_name, url, active) VALUES ';
        $insertQuery = array();
        $insertData = array();

        while ($row = $stmt->fetch())
        {
            $name = trim($row['MOD_CDS_TEXT']);
            $name = preg_replace('/(\sc\s{1})|([а-яА-Я\/]*)|\(.*\)/u', '',$name);
            $name = trim($name);
            $short_name = $this->get_short_article($name);
            $short_name = trim($short_name);
            $url = $this->get_short_url($name);
            $url = trim($url);

            if($row['END_DATA'] < 198000 AND $row['END_DATA'] != NULL)
            {
                continue;
            }
            else
            {
                $insertQuery[] = '(?, ?, ?, ?, ?)';
                $insertData[] = $row['MOD_ID'];
                $insertData[] = $row['MOD_MFA_ID'];
                $insertData[] = $url;
                $insertData[] = $short_name;
                $insertData[] = 1;
            }
        }

        if (!empty($insertQuery)) {
            $sql .= implode(', ', $insertQuery);
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($insertData);
        }

        $time_end = microtime(true);
        $time = $time_end - $time_start;
        echo "\n Время: $time секунд \n";
    }

    public function Urlforotypes()
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
            $url = $this->get_short_url($url);
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

    public function TypeCategoryGroup()
    {
        $time_start = microtime(true);
        $stmt = $this->pdo->query('SELECT tecdoc_id as type_id FROM own_types');

        foreach ($stmt as $one)
        {
            $stmt2 = $this->pdo->query('SELECT category_id, GROUP_CONCAT(ga_tecdoc_id) as tecdoc_ids FROM category_to_tecdoc GROUP BY category_id');
            $insertQuery = array();
            $insertData = array();
            $insert = 'INSERT IGNORE INTO type_category_group (category_id, type_id, tecdoc_categories_ids) VALUES';

            foreach ($stmt2 as $one2)
            {
                if($one['type_id'] AND $one2['category_id'] AND $one2['tecdoc_ids'])
                {
                    $insertQuery[] = '(?, ?, ?)';
                    $insertData[] = $one2['category_id'];
                    $insertData[] = $one['type_id'];
                    $insertData[] = $one2['tecdoc_ids'];
                }
            }

            if (!empty($insertQuery)) {
                $insert .= implode(', ', $insertQuery);
                $stmt = $this->pdo->prepare($insert);
                $stmt->execute($insertData);
            }
        }

        $time_end = microtime(true);
        $time = $time_end - $time_start;
        echo "\n Время: $time секунд \n";
    }

    public function TypeCategoryGroupActive(){
        echo "\nstart set active type_category_group\n";
        $set_active_null = "UPDATE type_category_group SET type_category_group.active = 0";
        $set_active_null = $this->pdo->prepare($set_active_null);
        $set_active_null->execute();


        $set_active = "UPDATE type_category_group
        SET type_category_group.active = 1
        WHERE
            EXISTS (
                SELECT
                    1
                FROM
                    group_parts
                INNER JOIN priceitems ON group_parts.part_id = priceitems.part_id
                INNER JOIN suppliers ON supplier_id = suppliers.id
                WHERE
                    suppliers.dont_show = 0
        AND
        group_parts.group_id = type_category_group.id
         LIMIT 1
        )";
        $set_active = $this->pdo->prepare($set_active);
        $set_active->execute();
    }

    public function UpdatePartsBrand() //замена брендов в parts
    {
        $time_start = microtime(true);

        $hyundaikia_query = "UPDATE parts SET brand = 'hyundaikia', brand_long = 'hyundai/kia' WHERE brand = 'kia' OR brand = 'hyundai' ";
        $hyundaikia_query = $this->pdo->prepare($hyundaikia_query);
        $hyundaikia_query->execute();

        $citroenpeugeot_query = "UPDATE parts SET brand = 'citroenpeugeot', brand_long = 'citroen/peugeot' WHERE brand = 'citroen' OR brand = 'peugeot' ";
        $citroenpeugeot_query = $this->pdo->prepare($citroenpeugeot_query);
        $citroenpeugeot_query->execute();

        $hondaacura_query = "UPDATE parts SET brand = 'hondaacura', brand_long = 'honda/acura' WHERE brand = 'honda' OR brand = 'acura' ";
        $hondaacura_query = $this->pdo->prepare($hondaacura_query);
        $hondaacura_query->execute();

        $nissaninfiniti_query = "UPDATE parts SET brand = 'nissaninfiniti', brand_long = 'nissan/infiniti' WHERE brand = 'nissan' OR brand = 'infiniti' ";
        $nissaninfiniti_query = $this->pdo->prepare($nissaninfiniti_query);
        $nissaninfiniti_query->execute();

        $toyotalexus_query = "UPDATE parts SET brand = 'toyotalexus', brand_long = 'toyota/lexus' WHERE brand = 'toyota' OR brand = 'lexus' ";
        $toyotalexus_query = $this->pdo->prepare($toyotalexus_query);
        $toyotalexus_query->execute();

        $vag_query = "UPDATE parts SET brand = 'vag', brand_long = 'vag' WHERE brand = 'audi' OR brand = 'vw' OR brand = 'seat' OR brand = 'skoda' ";
        $vag_query = $this->pdo->prepare($vag_query);
        $vag_query->execute();

        $time_end = microtime(true);
        $time = $time_end - $time_start;
        echo "\n Время: $time секунд \n";
    }

    public function UniqueParts() //удаление дублей из parts
    {
        $time_start = microtime(true);

        echo "\nstart delete copies\n";

        $all_reapets = $this->pdo->query('select * FROM parts as p1 where exists (select 1 from parts as p2 where p1.article=p2.article AND p1.brand = p2.brand and p1.id<>p2.id)');
        $unique_parts = [];
        echo "\nstart delete copies\n";
        foreach ($all_reapets as $reapet)
        {
            if(in_array($this->get_hash($reapet['article'],$reapet['brand']),$unique_parts))
            {
                $id = $reapet['id'];
                $new_id = array_search($this->get_hash($reapet['article'],$reapet['brand']),$unique_parts);
                $finish_query = "UPDATE priceitems SET part_id =".$new_id." WHERE part_id = ".$id." ";
                $finish_query = $this->pdo->prepare($finish_query);
                $finish_query->execute();
                $delrows= $this->pdo->query('DELETE FROM parts WHERE id = '.$id.'');
            }
            else
            {
                $id = $reapet['id'];
                $unique_parts[$id] = $this->get_hash($reapet['article'],$reapet['brand']);
            }
        }

        $time_end = microtime(true);
        $time = $time_end - $time_start;
        echo "\n Время: $time секунд \n";
    }

    public function UniqueGroupParts() //удаление дублей из parts
    {
        $time_start = microtime(true);

        echo "\nstart delete copies in Group Parts\n";
        $query = "insert ignore into group_parts select * from group_parts_old;";
        $this->pdo->exec($query);
        $time_end = microtime(true);
        $time = $time_end - $time_start;
        echo "\n Время: $time секунд \n";
    }

    public function UniqueOurCrosses() //удаление дублей из parts
    {
        $time_start = microtime(true);

        echo "\nstart delete copies in Crosses\n";
        $query = "insert ignore into crosses select * from crosses_pre_old;";
        $this->pdo->exec($query);
        $time_end = microtime(true);
        $time = $time_end - $time_start;
        echo "\n Время: $time секунд \n";
    }

    public function UpdateOurCrosses() //удаление дублей из parts
    {
        $time_start = microtime(true);

        echo "\nstart update Crosses\n";
        $query = "UPDATE crosses_pre_old SET from_id = (SELECT id FROM parts WHERE
                  parts.brand =
                  LOWER(
                    regex_replace (
                        '[^0-9a-zA-Z]',
                        '',
                        crosses_pre_old.from_brand
                    ))
                 AND parts.article =
                 LOWER(
                    regex_replace (
                        '[^0-9a-zA-Z]',
                        '',
                        crosses_pre_old.from_art
                    )
                    ));";
        $this->pdo->exec($query);

        $query = "UPDATE crosses_pre_old SET from_id = (SELECT id FROM parts WHERE parts.brand = crosses_pre_old.from_brand
         AND parts.article = crosses_pre_old.from_art);";
        $this->pdo->exec($query);
        $time_end = microtime(true);
        $time = $time_end - $time_start;
        echo "\n Время: $time секунд \n";
    }

    public function UpdateImage() //удаление дублей из parts
    {
        $time_start = microtime(true);

        echo "\nstart update Image\n";
//        $query = "UPDATE parts SET images = NULL where parts.tecdoc_id IS NOT NULL;";
//        $query = $this->pdo->prepare($query);
//        $query->execute();

        $query = "UPDATE parts SET images = (SELECT	CONCAT(	'/',	GRA_TAB_NR, '/',	GRA_GRD_ID, '.',	IF(LOWER(DOC_EXTENSION)='jp2', 'jpg', LOWER(DOC_EXTENSION))	) AS PATH
FROM	LINK_GRA_ART
INNER JOIN GRAPHICS ON GRA_ID = LGA_GRA_ID
INNER JOIN DOC_TYPES ON DOC_TYPE = GRA_DOC_TYPE
WHERE	LGA_ART_ID = parts.tecdoc_id AND	(GRA_LNG_ID = 16 OR GRA_LNG_ID = 255) AND	GRA_DOC_TYPE <> 2
ORDER BY GRA_GRD_ID LIMIT 1) where parts.tecdoc_id IS NOT NULL AND parts.images IS NULL ;";
        $query = $this->pdo->prepare($query);
        $query->execute();
        $time_end = microtime(true);
        $time = $time_end - $time_start;
        echo "\n Время: $time секунд \n";
    }

    public function InsertOurCrossesToParts() //удаление дублей из parts
    {
        $time_start = microtime(true);

        echo "\nstart insert our crosses to parts \n";
        $query = " INSERT IGNORE INTO parts (article_long, brand_long, article, brand)
            SELECT
                from_art AS article_long,
                from_brand AS brand_long,
                from_art AS article,
                from_brand AS brand
                
            FROM
                crosses_pre_old WHERE to_id IS NULL;";
//        LOWER(
//            regex_replace (
//                '[^0-9a-zA-Z]',
//                '',
//                from_art
//            )
//        ) AS article,
//                LOWER(
//                    regex_replace (
//                        '[^0-9a-zA-Z]',
//                        '',
//                        from_brand
//                    )
//                ) AS brand
        $this->pdo->exec($query);
        $time_end = microtime(true);
        $time = $time_end - $time_start;
        echo "\n Время: $time секунд \n";
    }

    public function AddBrandsFromParts()
    {
        $time_start = microtime(true);
        echo "\nstart add brands from Parts\n";
        $query = "insert ignore into brands (brand, brand_long) select brand, brand_long from parts where brand IS NOT NULL;";
        $this->pdo->exec($query);
        $time_end = microtime(true);
        $time = $time_end - $time_start;
        echo "\n Время: $time секунд \n";
    }
    public function ChangeBrandId()
    {
        $time_start = microtime(true);
        echo "\nstart change brand_id Parts\n";
        $query = "UPDATE parts SET parts.brand_id = (SELECT id FROM brands WHERE brands.brand = parts.brand)";
        $this->pdo->exec($query);
        $time_end = microtime(true);
        $time = $time_end - $time_start;
        echo "\n Время: $time секунд \n";
    }

    public function createserialize()
    {
        $tecdoc_articles = $this->pdo->query('
           SELECT ART_ID, ART_ARTICLE_NR, SUP_BRAND
           FROM ARTICLES
           LEFT JOIN SUPPLIERS ON ART_SUP_ID =  SUP_ID');

        $tecdoc_hashes = [];

        foreach ($tecdoc_articles as $row) {
            $tecdoc_hashes[$this->get_hash($row['ART_ARTICLE_NR'], $row['SUP_BRAND'])] = $row['ART_ID'];
        }

        $file_pointer = "/var/tmp/serialize.txt";
        if (!$file_handle = fopen($file_pointer, 'wb')) exit;
        flock($file_handle, LOCK_EX);
        if (fwrite($file_handle, serialize($tecdoc_hashes)) === false) exit;
        flock($file_handle, LOCK_UN);
        fclose($file_handle);
    }

    public function Changepartstid() //изменение parts.tecdoc_id
    {
        $time_start = microtime(true);

        $file_pointer = "/var/tmp/serialize.txt";
        if ( !$file_handle = fopen($file_pointer, 'rb') ) exit;
        $tecdoc_hashes = unserialize( fread($file_handle, filesize($file_pointer)) );
        fclose($file_handle);

        $final_pairs = [];

        $sql = "SELECT count(*) FROM parts";
        $result = $this->pdo->prepare($sql);
        $result->execute();
        $parts_count = $result->fetchColumn();

        $step = 200000;
        for ($i = 0; $i <= $parts_count;  $i += $step)
        {
            $parts_articles = $this->pdo->query('SELECT id, article, brand FROM parts LIMIT '.$i.', 200000');

            foreach ($parts_articles as $row) {
                $hash = $this->get_hash($row['article'], $row['brand']);
                if (isset($tecdoc_hashes[$hash])) {
                    $final_pairs[$tecdoc_hashes[$hash]] = $row['id'];
                }
            }

            if (empty($final_pairs))
            {
                continue;
            }
            else
            {
                $finish_query = "UPDATE parts SET tecdoc_id = CASE id ";
                foreach ($final_pairs as $tecdoc_id => $id) {
                    $finish_query .= " WHEN $id THEN $tecdoc_id";
                }
                $finish_query .= " END WHERE id IN (" . implode(',', array_values($final_pairs)) . ")";
                $finish_query = $this->pdo->prepare($finish_query);
                $finish_query->execute();
//                print_r($finish_query);
                $count = count($final_pairs);
                $final_pairs = [];
            }
            echo "$i-ый шаг, записано $count tecdoc_id \n";
        }
    }

    public function AddParts() //добавление в партс с ARTICLES
    {
        $time_start = microtime(true);

        echo "start insert";

        $sql = "SELECT count(*) FROM ARTICLES";
        $result = $this->pdo->prepare($sql);
        $result->execute();
        $count = $result->fetchColumn();

        for($i = 0; $i<=$count; $i+=100000)
        {
            $insert = $this->pdo->prepare("INSERT IGNORE INTO parts (tecdoc_id, article_long, brand_long, article, brand, name)
            SELECT
                ART_ID AS tecdoc_id,
                ART_ARTICLE_NR AS article_long,
                SUP_BRAND AS brand_long,
                LOWER(
                    regex_replace (
                        '[^0-9a-zA-Z]',
                        '',
                        ART_ARTICLE_NR
                    )
                ) AS article,
                LOWER(
                    regex_replace (
                        '[^0-9a-zA-Z]',
                        '',
                        SUP_BRAND
                    )
                ) AS brand,
            TEX_TEXT AS name
            FROM
                ARTICLES
            LEFT JOIN SUPPLIERS ON ART_SUP_ID = SUP_ID
            LEFT JOIN DESIGNATIONS ON ART_DES_ID = DES_ID
            LEFT JOIN DES_TEXTS ON DES_TEX_ID = TEX_ID
            LIMIT ".$i.",100000 "); // ::TODO  WHERE ART_ID NOT IN (SELECT tecdoc_id FROM parts WHERE tecdoc_id IS NOT NULL) LIMIT 200000

            $insert->execute();
        }


        $time_end = microtime(true);
        $time = $time_end - $time_start;
        echo "\n Время: $time секунд \n";
    }

    public function ApdateCrosses() //обновление таблицы нашей таблицы кроссов (part_id)
    {
        echo "\n Обновление кроссов \n";
        $Crosses_query = "UPDATE crosses LEFT JOIN parts ON to_art = article AND to_brand = brand SET to_id = parts.id";
        $Crosses_query = $this->pdo->prepare($Crosses_query);
        $Crosses_query->execute();
    }

    public function GroupParts() //создание group_parts
    {
        $time_start = microtime(true);

        $sql = "SELECT count(*) FROM type_category_group";
        $result = $this->pdo->prepare($sql);
        $result->execute();
        $count_real = $result->fetchColumn();

        echo "\nSTART GROUP-PARTS \n";

        for($i = 0; $i<=$count_real; $i=$i+10000)
        {
            $stmt3 = $this->pdo->query('SELECT id, type_id, tecdoc_categories_ids as tecdoc_ids from type_category_group LIMIT '.$i.',10000');
            foreach ($stmt3 as $one)
            {

                $select_groups = $this->pdo->query('SELECT parts.id as part_id 
                  FROM GENERIC_ARTICLES 
                  LEFT JOIN LINK_LA_TYP ON LAT_GA_ID = GA_ID 
                  LEFT JOIN LINK_ART ON LA_ID = LAT_LA_ID 
                  LEFT JOIN parts ON LA_ART_ID = parts.tecdoc_id
                  WHERE GA_ID IN ('.$one['tecdoc_ids'].') AND LAT_TYP_ID = '.$one['type_id'].' ;');


                $sql = 'INSERT IGNORE INTO group_parts (group_id, part_id) VALUES ';
                $insertQuery = array();
                $insertData = array();

                foreach ($select_groups as $group)
                {
                    if($one['id'] AND $group['part_id'])
                    {
                        $insertQuery[] = '(?, ?)';
                        $insertData[] = $one['id'];
                        $insertData[] = $group['part_id'];
                    }
                }

                if (!empty($insertQuery)) {
                    $sql .= implode(', ', $insertQuery);
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->execute($insertData);
                }
            }

        }


        $time_end = microtime(true);
        $time = $time_end - $time_start;
        echo "\n Время: $time секунд \n";
    }

    public function AddLookupParts() //запись в parts c LOOKUP
    {
        $time_start = microtime(true);
        $step = 200000;
        echo "start insert lookup";

        $sql = "SELECT count(*) FROM ART_LOOKUP";
        $result = $this->pdo->prepare($sql);
        $result->execute();
        $parts_count = $result->fetchColumn();

        for ($i = 0; $i <= $parts_count;  $i += $step) {

            echo "крок $i \n";

            $insert = $this->pdo->prepare("INSERT IGNORE INTO parts (article_long, brand_long, article, brand)
            SELECT 
            AL.ARL_SEARCH_NUMBER as article_long,
            IF (AL.ARL_KIND IN (3,4), B.BRA_BRAND, S.SUP_BRAND) AS brand_long,
            LOWER(
                                regex_replace (
                                    '[^0-9a-zA-Z]',
                                    '',
                                    AL.ARL_SEARCH_NUMBER 
                                )
                            ) AS article,
            
            IF (AL.ARL_KIND IN (3,4), LOWER(regex_replace ('[^0-9a-zA-Z]','',B.BRA_BRAND) ),LOWER(regex_replace ('[^0-9a-zA-Z]','',S.SUP_BRAND) ) ) AS brand
            FROM ART_LOOKUP as AL
            LEFT JOIN SUPPLIERS as S ON S.SUP_ID = AL.ARL_BRA_ID
            LEFT JOIN BRANDS as B ON B.BRA_ID = AL.ARL_BRA_ID LIMIT ".$i.", 200000 ");

            $insert->execute();
        }


        $time_end = microtime(true);
        $time = $time_end - $time_start;
        echo "\n Время: $time секунд \n";
    }

    public function AddCrossesFromLookup() //запись в crosses c LOOKUP
    {
        $time_start = microtime(true);

        echo "start insert lookup`s crosses";

        $sql = "SELECT count(*) FROM ART_LOOKUP";
        $result = $this->pdo->prepare($sql);
        $result->execute();
        $parts_count = $result->fetchColumn();

        $step = 1000000;

        for ($i = 0; $i <= $parts_count;  $i += $step) {

            echo "step $i \n";

            $insert = $this->pdo->prepare("INSERT INTO crosses_td_mod (from_id, to_id)
                            SELECT
                              p1.id as from_id,
                              p2.id as to_id
                            FROM ART_LOOKUP AS ALO
                              INNER JOIN parts AS p1 ON p1.tecdoc_id = ALO.ARL_ART_ID
                              LEFT JOIN BRANDS ON BRANDS.BRA_ID = ALO.ARL_BRA_ID
                              LEFT JOIN SUPPLIERS ON SUPPLIERS.SUP_ID = ALO.ARL_BRA_ID
                              INNER JOIN parts AS p2 ON
                           p2.article = LOWER(
                               regex_replace(
                                   '[^0-9a-zA-Z]',
                                   '',
                                   ALO.ARL_SEARCH_NUMBER
                               )
                           )
                           AND p2.brand = LOWER(
                               regex_replace(
                                   '[^0-9a-zA-Z]',
                                   '',
                                   IF(ALO.ARL_KIND IN (3, 4), BRANDS.BRA_BRAND, SUPPLIERS.SUP_BRAND)
                               )
                           ) LIMIT ".$i.", 1000000 ");

            $insert->execute();
        }



        $time_end = microtime(true);
        $time = $time_end - $time_start;
        echo "\n Время: $time секунд \n";
    }

    public function AddCrossesToGroupParts() //запись кроссов в group_parts
    {
        $time_start = microtime(true);

        echo "start insert lookup`s crosses to froup parts \n";

        $step = 100000;
        $crossesCount = 33786330;/*количество в crosses_td_mod*/
        for ($i = 1248000; $i<=$crossesCount; $i+=$step) {
            $crosses = $this->pdo->query("SELECT * FROM crosses_td_mod LIMIT {$step} OFFSET {$i}");
            $query = '';
            foreach ($crosses as $one) {
                $query .= " INSERT INTO group_parts (group_id, part_id) 
                            SELECT 
                            group_id,
                            {$one['from_id']} AS part_id 
                            FROM group_parts 
                            WHERE part_id = {$one['to_id']}; ";
                $query .= "INSERT IGNORE INTO group_parts (group_id, part_id) 
                            SELECT 
                            group_id,
                            {$one['to_id']} AS part_id 
                            FROM group_parts 
                            WHERE part_id = {$one['from_id']}; ";

            }
            $this->pdo->exec($query);
            echo "{$i} crosses proceed. ". 100 * $i/$crossesCount . "% \n";
        }

        $time_end = microtime(true);
        $time = $time_end - $time_start;
        echo "\n Время: $time секунд \n";
    }


    public function get_short_url($article) {
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

    public function get_short_article($article) {
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

    public function get_hash($article = '', $brand = '')
    {
        $article = trim(mb_strtolower(preg_replace('/[^0-9a-zA-Z]/u', '', $article)));
        $brand = trim(mb_strtolower(preg_replace('/[^0-9a-zA-Z]/u', '', $brand)));
        echo "$article $brand \n";
        return md5($article.$brand);
    }
}



$dsn = 'mysql:dbname=eparts_tecdoc;host=localhost';
$user = 'root';
$password = 'eesiThae9ees';


$my = new TecdocUpdate($dsn, $user, $password);
$my->GroupParts();
