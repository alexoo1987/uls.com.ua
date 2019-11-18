<?php
class Model_NewTecdoc extends Model {

//    -------------- CATEGORIES ---------------

    public function get_categories_tree()
    {
        $query = "
			SELECT SEARCH_TREE.STR_LEVEL,
            ELT(SEARCH_TREE.STR_LEVEL, DES_TEXTS.TEX_TEXT, DES_TEXTS2.TEX_TEXT, DES_TEXTS3.TEX_TEXT, DES_TEXTS4.TEX_TEXT, DES_TEXTS5.TEX_TEXT) AS STR_TEXT1,
            ELT(SEARCH_TREE.STR_LEVEL, SEARCH_TREE.STR_ID, SEARCH_TREE2.STR_ID, SEARCH_TREE3.STR_ID, SEARCH_TREE4.STR_ID, SEARCH_TREE5.STR_ID) AS STR_ID1,
            ELT(SEARCH_TREE.STR_LEVEL-1, DES_TEXTS.TEX_TEXT, DES_TEXTS2.TEX_TEXT, DES_TEXTS3.TEX_TEXT, DES_TEXTS4.TEX_TEXT, DES_TEXTS5.TEX_TEXT) AS STR_TEXT2,
            ELT(SEARCH_TREE.STR_LEVEL-1, SEARCH_TREE.STR_ID, SEARCH_TREE2.STR_ID, SEARCH_TREE3.STR_ID, SEARCH_TREE4.STR_ID, SEARCH_TREE5.STR_ID) AS STR_ID2,
            ELT(SEARCH_TREE.STR_LEVEL-2, DES_TEXTS.TEX_TEXT, DES_TEXTS2.TEX_TEXT, DES_TEXTS3.TEX_TEXT, DES_TEXTS4.TEX_TEXT, DES_TEXTS5.TEX_TEXT) AS STR_TEXT3,
            ELT(SEARCH_TREE.STR_LEVEL-2, SEARCH_TREE.STR_ID, SEARCH_TREE2.STR_ID, SEARCH_TREE3.STR_ID, SEARCH_TREE4.STR_ID, SEARCH_TREE5.STR_ID) AS STR_ID3,
            ELT(SEARCH_TREE.STR_LEVEL-3, DES_TEXTS.TEX_TEXT, DES_TEXTS2.TEX_TEXT, DES_TEXTS3.TEX_TEXT, DES_TEXTS4.TEX_TEXT, DES_TEXTS5.TEX_TEXT) AS STR_TEXT4,
            ELT(SEARCH_TREE.STR_LEVEL-3, SEARCH_TREE.STR_ID, SEARCH_TREE2.STR_ID, SEARCH_TREE3.STR_ID, SEARCH_TREE4.STR_ID, SEARCH_TREE5.STR_ID) AS STR_ID4,
            ELT(SEARCH_TREE.STR_LEVEL-4, DES_TEXTS.TEX_TEXT, DES_TEXTS2.TEX_TEXT, DES_TEXTS3.TEX_TEXT, DES_TEXTS4.TEX_TEXT, DES_TEXTS5.TEX_TEXT) AS STR_TEXT5,
            ELT(SEARCH_TREE.STR_LEVEL-4, SEARCH_TREE.STR_ID, SEARCH_TREE2.STR_ID, SEARCH_TREE3.STR_ID, SEARCH_TREE4.STR_ID, SEARCH_TREE5.STR_ID) AS STR_ID5
            FROM SEARCH_TREE
            LEFT JOIN DESIGNATIONS ON DESIGNATIONS.DES_ID = SEARCH_TREE.STR_DES_ID AND DESIGNATIONS.DES_LNG_ID = 16
            LEFT JOIN DES_TEXTS ON DES_TEXTS.TEX_ID = DESIGNATIONS.DES_TEX_ID
            LEFT JOIN SEARCH_TREE AS SEARCH_TREE2 ON SEARCH_TREE2.STR_ID = SEARCH_TREE.STR_ID_PARENT
            LEFT JOIN DESIGNATIONS AS DESIGNATIONS2 ON DESIGNATIONS2.DES_ID = SEARCH_TREE2.STR_DES_ID AND DESIGNATIONS2.DES_LNG_ID = 16
            LEFT JOIN DES_TEXTS AS DES_TEXTS2 ON DES_TEXTS2.TEX_ID = DESIGNATIONS2.DES_TEX_ID
            LEFT JOIN SEARCH_TREE AS SEARCH_TREE3 ON SEARCH_TREE3.STR_ID = SEARCH_TREE2.STR_ID_PARENT
            LEFT JOIN DESIGNATIONS AS DESIGNATIONS3 ON DESIGNATIONS3.DES_ID = SEARCH_TREE3.STR_DES_ID AND DESIGNATIONS3.DES_LNG_ID = 16
            LEFT JOIN DES_TEXTS AS DES_TEXTS3 ON DES_TEXTS3.TEX_ID = DESIGNATIONS3.DES_TEX_ID
            LEFT JOIN SEARCH_TREE AS SEARCH_TREE4 ON SEARCH_TREE4.STR_ID = SEARCH_TREE3.STR_ID_PARENT
            LEFT JOIN DESIGNATIONS AS DESIGNATIONS4 ON DESIGNATIONS4.DES_ID = SEARCH_TREE4.STR_DES_ID AND DESIGNATIONS4.DES_LNG_ID = 16
            LEFT JOIN DES_TEXTS AS DES_TEXTS4 ON DES_TEXTS4.TEX_ID = DESIGNATIONS4.DES_TEX_ID
            LEFT JOIN SEARCH_TREE AS SEARCH_TREE5 ON SEARCH_TREE5.STR_ID = SEARCH_TREE4.STR_ID_PARENT
            LEFT JOIN DESIGNATIONS AS DESIGNATIONS5 ON DESIGNATIONS5.DES_ID = SEARCH_TREE5.STR_DES_ID AND DESIGNATIONS5.DES_LNG_ID = 16
            LEFT JOIN DES_TEXTS AS DES_TEXTS5 ON DES_TEXTS5.TEX_ID = DESIGNATIONS5.DES_TEX_ID
            ORDER BY	STR_TEXT1,	STR_TEXT2,	STR_TEXT3,	STR_TEXT4,	STR_TEXT5	;
		";
        $result = DB::query(Database::SELECT,$query)->execute('tecdoc_new')->as_array();
        return $result;
    }

    public function category_by_part ($part_id)
    {
        $query ="
            SELECT categories.slug as cat_slug, categories.name as cat_name, cat3.slug as parent_slug, cat3.name as parent_name from categories
            INNER JOIN type_category_group ON categories.id = type_category_group.category_id
            INNER JOIN group_parts ON group_parts.group_id = type_category_group.id
            INNER JOIN categories as cat2 ON cat2.id = categories.parent_id
            INNER JOIN categories as cat3 ON cat3.id = cat2.parent_id
            WHERE group_parts.part_id = ".$part_id."
            LIMIT 1";

        $results = DB::query(Database::SELECT,$query)->execute('default')->current();
        return $results;
    }

    public function get_categories_group_by_cat ($cat_id) // получение групы товаров по категории, для админки
    {
        $query = "
			SELECT DISTINCT GA_ID AS group_id, TEX_TEXT AS name FROM
            GENERIC_ARTICLES
            LEFT JOIN DESIGNATIONS ON GA_DES_ID = DES_ID AND DES_LNG_ID = 16
            LEFT JOIN DES_TEXTS ON DES_TEX_ID = TEX_ID
            LEFT JOIN LINK_GA_STR ON LGS_GA_ID = GA_ID
            WHERE LGS_STR_ID = ".$cat_id."
            ORDER BY TEX_TEXT
		";
        $result = DB::query(Database::SELECT,$query)->execute('tecdoc_new')->as_array();
        return $result;
    }

    public function get_cat_id_by_model($model_url, $manuf_url) // получение id категорий по модели, на которые у нас есть запчасти
    {
        $query = "SELECT DISTINCT category_id
        FROM categories
          INNER JOIN type_category_group ON type_category_group.category_id = categories.id
          INNER JOIN own_types ON type_category_group.type_id = own_types.tecdoc_id
          INNER JOIN own_models ON own_models.tecdoc_id = own_types.tecdoc_models_id
          INNER JOIN own_manufactures ON own_models.tecdoc_manufacture_id = own_manufactures.tecdoc_id
        WHERE own_manufactures.url = \"".$manuf_url."\" AND own_models.url = \"".$model_url."\" AND EXISTS(SELECT 1
             FROM group_parts
               INNER JOIN priceitems
                 ON priceitems.part_id =
                    group_parts.part_id
             WHERE group_id = type_category_group.id
             LIMIT 1);";

        $results = DB::query(Database::SELECT,$query)->execute('default')->as_array();
        return $results;
    }

    public function get_cat_id_by_manufacturer($manufacturer_slug) // получение id категорий по производителю, на которые у нас есть запчасти
    {
        $query = "SELECT DISTINCT category_id
        FROM categories
          INNER JOIN type_category_group ON type_category_group.category_id = categories.id
          INNER JOIN own_types ON type_category_group.type_id = own_types.tecdoc_id
          INNER JOIN own_models ON own_models.tecdoc_id = own_types.tecdoc_models_id
          INNER JOIN own_manufactures ON own_models.tecdoc_manufacture_id = own_manufactures.tecdoc_id
        WHERE own_manufactures.url = \"".$manufacturer_slug."\" AND EXISTS(SELECT 1
             FROM group_parts
               INNER JOIN priceitems
                 ON priceitems.part_id =
                    group_parts.part_id
             WHERE group_id = type_category_group.id
             LIMIT 1);";

        $results = DB::query(Database::SELECT,$query)->execute('default')->as_array();
        return $results;
    }

    public function get_cat_id_by_type($type_id) // получение id категорий по модели, на которые у нас есть запчасти
    {
        $query = " SELECT DISTINCT category_id
        FROM categories
          INNER JOIN type_category_group ON type_category_group.category_id = categories.id
          INNER JOIN own_types ON type_category_group.type_id = own_types.tecdoc_id
        WHERE own_types.tecdoc_id = ".$type_id." AND EXISTS(SELECT 1
             FROM group_parts
             INNER JOIN priceitems ON priceitems.part_id = group_parts.part_id
			 INNER JOIN suppliers ON suppliers.id = priceitems.supplier_id
             WHERE group_id = type_category_group.id AND suppliers.dont_show = 0
             LIMIT 1)";

        $results = DB::query(Database::SELECT,$query)->execute('default')->as_array();
        return $results;
    }

    public function get_info_by_type($type_id) // получение инфы по типу ::TODO vados
    {
        $query = "SELECT	TYP_ID,	MFA_BRAND,	DES_TEXTS7.TEX_TEXT AS MOD_CDS_TEXT,	DES_TEXTS.TEX_TEXT AS TYP_CDS_TEXT,	TYP_PCON_START,	TYP_PCON_END, TYP_CCM,	TYP_KW_FROM,	TYP_KW_UPTO,	TYP_HP_FROM,	TYP_HP_UPTO,	TYP_CYLINDERS,	ENGINES.ENG_CODE,	DES_TEXTS2.TEX_TEXT AS TYP_ENGINE_DES_TEXT,	DES_TEXTS3.TEX_TEXT AS TYP_FUEL_DES_TEXT,	IFNULL(DES_TEXTS4.TEX_TEXT, DES_TEXTS5.TEX_TEXT) AS TYP_BODY_DES_TEXT, DES_TEXTS6.TEX_TEXT AS TYP_AXLE_DES_TEXT,	TYP_MAX_WEIGHT
            FROM	TYPES
            INNER JOIN MODELS ON MOD_ID = TYP_MOD_ID
            INNER JOIN MANUFACTURERS ON MFA_ID = MOD_MFA_ID
            INNER JOIN COUNTRY_DESIGNATIONS AS COUNTRY_DESIGNATIONS2 ON COUNTRY_DESIGNATIONS2.CDS_ID = MOD_CDS_ID AND COUNTRY_DESIGNATIONS2.CDS_LNG_ID = 16
            INNER JOIN DES_TEXTS AS DES_TEXTS7 ON DES_TEXTS7.TEX_ID = COUNTRY_DESIGNATIONS2.CDS_TEX_ID
            INNER JOIN COUNTRY_DESIGNATIONS ON COUNTRY_DESIGNATIONS.CDS_ID = TYP_CDS_ID AND COUNTRY_DESIGNATIONS.CDS_LNG_ID = 16
            INNER JOIN DES_TEXTS ON DES_TEXTS.TEX_ID = COUNTRY_DESIGNATIONS.CDS_TEX_ID
            LEFT JOIN DESIGNATIONS ON DESIGNATIONS.DES_ID = TYP_KV_ENGINE_DES_ID AND DESIGNATIONS.DES_LNG_ID = 16
            LEFT JOIN DES_TEXTS AS DES_TEXTS2 ON DES_TEXTS2.TEX_ID = DESIGNATIONS.DES_TEX_ID
            LEFT JOIN DESIGNATIONS AS DESIGNATIONS2 ON DESIGNATIONS2.DES_ID = TYP_KV_FUEL_DES_ID AND DESIGNATIONS2.DES_LNG_ID = 16
            LEFT JOIN DES_TEXTS AS DES_TEXTS3 ON DES_TEXTS3.TEX_ID = DESIGNATIONS2.DES_TEX_ID
            LEFT JOIN LINK_TYP_ENG ON LTE_TYP_ID = TYP_ID
            LEFT JOIN ENGINES ON ENG_ID = LTE_ENG_ID
            LEFT JOIN DESIGNATIONS AS DESIGNATIONS3 ON DESIGNATIONS3.DES_ID = TYP_KV_BODY_DES_ID AND DESIGNATIONS3.DES_LNG_ID = 16
            LEFT JOIN DES_TEXTS AS DES_TEXTS4 ON DES_TEXTS4.TEX_ID = DESIGNATIONS3.DES_TEX_ID
            LEFT JOIN DESIGNATIONS AS DESIGNATIONS4 ON DESIGNATIONS4.DES_ID = TYP_KV_MODEL_DES_ID AND DESIGNATIONS4.DES_LNG_ID = 16
            LEFT JOIN DES_TEXTS AS DES_TEXTS5 ON DES_TEXTS5.TEX_ID = DESIGNATIONS4.DES_TEX_ID
            LEFT JOIN DESIGNATIONS AS DESIGNATIONS5 ON DESIGNATIONS5.DES_ID = TYP_KV_AXLE_DES_ID AND DESIGNATIONS5.DES_LNG_ID = 16
            LEFT JOIN DES_TEXTS AS DES_TEXTS6 ON DES_TEXTS6.TEX_ID = DESIGNATIONS5.DES_TEX_ID
            WHERE	TYP_ID = ".$type_id."
            ";

        $results = DB::query(Database::SELECT,$query)->execute('tecdoc_new')->current();
        return $results;
    }

    public function get_info_by_type_analog_old($type_id) // получение инфы по типу ::TODO vados
    {
        $query = "SELECT	TYP_ID as id,	MFA_BRAND as brand,	
            own_manufactures.url as manuf_slug,
            own_models.url as model_slug,
            own_types.url as slug,
            own_types.short_name as description,
            own_manufactures.url as manuf_url,
            own_models.url as mod_url,
            own_types.url as type_url,
            DES_TEXTS7.TEX_TEXT AS model,	
            DES_TEXTS.TEX_TEXT AS capacity,	
            TYP_PCON_START as start_date,	
            TYP_PCON_END as end_date, 
            TYP_CCM,	
            TYP_KW_FROM as capacity_kw_from,	TYP_HP_FROM as capacity_hp_from,	
            DES_TEXTS2.TEX_TEXT AS engine_type,
            IFNULL(DES_TEXTS4.TEX_TEXT, DES_TEXTS5.TEX_TEXT) AS body_type
                        FROM	TYPES
                        INNER JOIN MODELS ON MOD_ID = TYP_MOD_ID
                        INNER JOIN MANUFACTURERS ON MFA_ID = MOD_MFA_ID
                        INNER JOIN COUNTRY_DESIGNATIONS AS COUNTRY_DESIGNATIONS2 ON COUNTRY_DESIGNATIONS2.CDS_ID = MOD_CDS_ID AND COUNTRY_DESIGNATIONS2.CDS_LNG_ID = 16
                        INNER JOIN DES_TEXTS AS DES_TEXTS7 ON DES_TEXTS7.TEX_ID = COUNTRY_DESIGNATIONS2.CDS_TEX_ID
                        INNER JOIN COUNTRY_DESIGNATIONS ON COUNTRY_DESIGNATIONS.CDS_ID = TYP_CDS_ID AND COUNTRY_DESIGNATIONS.CDS_LNG_ID = 16
                        INNER JOIN DES_TEXTS ON DES_TEXTS.TEX_ID = COUNTRY_DESIGNATIONS.CDS_TEX_ID
                        LEFT JOIN DESIGNATIONS ON DESIGNATIONS.DES_ID = TYP_KV_ENGINE_DES_ID AND DESIGNATIONS.DES_LNG_ID = 16
                        LEFT JOIN DES_TEXTS AS DES_TEXTS2 ON DES_TEXTS2.TEX_ID = DESIGNATIONS.DES_TEX_ID
                        LEFT JOIN DESIGNATIONS AS DESIGNATIONS2 ON DESIGNATIONS2.DES_ID = TYP_KV_FUEL_DES_ID AND DESIGNATIONS2.DES_LNG_ID = 16
                        LEFT JOIN DES_TEXTS AS DES_TEXTS3 ON DES_TEXTS3.TEX_ID = DESIGNATIONS2.DES_TEX_ID
                        LEFT JOIN LINK_TYP_ENG ON LTE_TYP_ID = TYP_ID
                        LEFT JOIN ENGINES ON ENG_ID = LTE_ENG_ID
                        LEFT JOIN DESIGNATIONS AS DESIGNATIONS3 ON DESIGNATIONS3.DES_ID = TYP_KV_BODY_DES_ID AND DESIGNATIONS3.DES_LNG_ID = 16
                        LEFT JOIN DES_TEXTS AS DES_TEXTS4 ON DES_TEXTS4.TEX_ID = DESIGNATIONS3.DES_TEX_ID
                        LEFT JOIN DESIGNATIONS AS DESIGNATIONS4 ON DESIGNATIONS4.DES_ID = TYP_KV_MODEL_DES_ID AND DESIGNATIONS4.DES_LNG_ID = 16
                        LEFT JOIN DES_TEXTS AS DES_TEXTS5 ON DES_TEXTS5.TEX_ID = DESIGNATIONS4.DES_TEX_ID
                        LEFT JOIN DESIGNATIONS AS DESIGNATIONS5 ON DESIGNATIONS5.DES_ID = TYP_KV_AXLE_DES_ID AND DESIGNATIONS5.DES_LNG_ID = 16
                        LEFT JOIN DES_TEXTS AS DES_TEXTS6 ON DES_TEXTS6.TEX_ID = DESIGNATIONS5.DES_TEX_ID
                                    INNER JOIN own_types ON TYP_ID = own_types.tecdoc_id
            INNER JOIN own_models ON own_types.tecdoc_models_id = own_models.tecdoc_id
            INNER JOIN own_manufactures ON own_manufactures.tecdoc_id = own_models.tecdoc_manufacture_id
            WHERE	TYP_ID = ".$type_id."
            ";

        $results = DB::query(Database::SELECT,$query)->execute('tecdoc_new')->current();
        return $results;
    }

    public function get_tecdoc_category_name_by_category($category_id) // получение названий техдоковской категории, которые связаны с нашей, для админки
    {
        $query = "
            SELECT
            DISTINCT TEX_TEXT AS name
            FROM
            category_to_tecdoc
            LEFT JOIN GENERIC_ARTICLES ON ga_tecdoc_id = GA_ID
            LEFT JOIN DESIGNATIONS ON GA_DES_ID = DES_ID
            LEFT JOIN DES_TEXTS ON DES_TEX_ID = TEX_ID
            WHERE
            DES_LNG_ID = 16
            AND category_id = ".$category_id."
		";
        $results = DB::query(Database::SELECT,$query)->execute('tecdoc_new')->as_array();
        return $results;
    }

    //    --------------------------------------------------
    //    -------------------- MANUFACTURE -----------------
    //    --------------------------------------------------
    public function get_all_manufacture() // вывод всех производителей
    {
        $query = "
			SELECT own_manufactures.url AS url, own_manufactures.short_name AS name, tecdoc_id as id
            FROM own_manufactures WHERE active = 1 ORDER BY name
		";
        $results = DB::query(Database::SELECT,$query)->execute('default')->as_array();
        return $results;
    }

    public function get_manuf_ids_by_url($model_url) // получаем id производителя по урлу производителя
    {
        $query = "
            SELECT DISTINCT
            tecdoc_id as id 
            from own_manufactures
            where url = '".$model_url."'
		";
        $results = DB::query(Database::SELECT,$query)->execute('default')->current();
        return $results;
    }

    public function get_manuf_info_by_url($manuf_url) // получаем все по производителю по урлу производителя
    {
        $query = "
            SELECT DISTINCT
            *
            from own_manufactures
            where url = \"".$manuf_url."\"
		";

        $results = DB::query(Database::SELECT,$query)->execute('default')->current();
        return $results;
    }


    //    --------------------------------------------------
    //    -------------------- MODELS -----------------
    //    --------------------------------------------------
    public function get_all_models_for_manufactures_url($manufacture_url) // получение всех моделей и производителей по ссылке производителя TODO::vados
    {
        $query = "
			SELECT DISTINCT
                omanuf.short_name AS brand,
                omod.short_name AS model,
                omod.url AS url_model,
                omanuf.url AS url_manufact
            FROM
                own_models AS omod
            INNER JOIN own_manufactures AS omanuf ON omanuf.tecdoc_id = omod.tecdoc_manufacture_id
            INNER JOIN own_types as otype ON otype.tecdoc_models_id = omod.tecdoc_id
            INNER JOIN type_category_group ON type_category_group.type_id = otype.tecdoc_id
            WHERE
                omanuf.active = 1
            AND omanuf.url = '".$manufacture_url."' AND type_category_group.active = 1
            ORDER BY model
		";
        $results = DB::query(Database::SELECT,$query)->execute('default')->as_array();
        return $results;
    }

    public function get_all_for_id_manufactures($manufacture_id) // вывод всех моделей по id производетеля
    {
        $query = "
			SELECT own_models.short_name AS model, own_models.url AS url_model
            FROM own_models
            WHERE	own_models.active=1 AND tecdoc_manufacture_id = ".$manufacture_id."
		";
        $results = DB::query(Database::SELECT,$query)->execute('default')->as_array();
        return $results;
    }

    public function get_one_info_for_url_model($model_url, $manufacturer_slug) // вывод информации 1 модели по url
    {
        $query = "
			SELECT own_models.*
            FROM own_models
            INNER JOIN own_manufactures ON own_manufactures.tecdoc_id = own_models.tecdoc_manufacture_id
            WHERE own_models.active=1 AND own_manufactures.active=1 AND own_models.url = '".$model_url."' AND own_manufactures.url = '".$manufacturer_slug."'
            LIMIT 1
		";
        $results = DB::query(Database::SELECT,$query)->execute('default')->current();
        return $results;
    }

    public function get_type_info_for_id_type($type_id) // вывод информации 1 типа по url
    {
        $query = "
			SELECT *
            FROM own_types
            WHERE own_types.tecdoc_id= ".$type_id."
            LIMIT 1
		";
        $results = DB::query(Database::SELECT,$query)->execute('default')->current();
        return $results;
    }

    public function manufacture_by_category_id ($category_id){
//        $query = "SELECT * FROM own_manufactures
//            WHERE active = 1 AND EXISTS(
//              SELECT 1 FROM  type_category_group
//                INNER JOIN own_types ON type_category_group.type_id =own_types.tecdoc_id
//                INNER JOIN own_models ON own_types.tecdoc_models_id = own_models.tecdoc_id
//                INNER JOIN group_parts ON type_category_group.id = group_parts.group_id
//                INNER JOIN priceitems ON group_parts.part_id = priceitems.part_id
//                INNER JOIN suppliers ON priceitems.supplier_id = suppliers.id
//              WHERE type_category_group.category_id = ".$category_id." AND own_models.tecdoc_manufacture_id = own_manufactures.tecdoc_id
//                AND suppliers.dont_show = 0
//              LIMIT 1
//            )";
//print_r($query);exit();
        $query = "SELECT DISTINCT own_manufactures.url as manuf_url, own_manufactures.short_name as manuf_name, ownmod.short_name as model_name, ownmod.url as model_url FROM own_manufactures
            INNER JOIN own_models as ownmod ON own_manufactures.tecdoc_id = ownmod.tecdoc_manufacture_id
            WHERE own_manufactures.active = 1 AND ownmod.active = 1 AND EXISTS(
              SELECT 1 FROM  type_category_group
                INNER JOIN own_types ON type_category_group.type_id =own_types.tecdoc_id
                INNER JOIN own_models ON own_types.tecdoc_models_id = own_models.tecdoc_id
                INNER JOIN group_parts ON type_category_group.id = group_parts.group_id
                INNER JOIN priceitems ON group_parts.part_id = priceitems.part_id
                INNER JOIN suppliers ON priceitems.supplier_id = suppliers.id
              WHERE type_category_group.category_id = ".$category_id." 
                AND own_models.tecdoc_id = ownmod.tecdoc_id
                AND suppliers.dont_show = 0
              LIMIT 1
            ) ORDER BY manuf_name, model_name";

        $results = DB::query(Database::SELECT,$query)->execute('default')->as_array();
        return $results;
    }

    public function models_by_category_id ($category_id, $manuf){
        $query = "
            SELECT DISTINCT own_models.url, own_models.short_name  FROM own_models
            LEFT JOIN own_types ON own_types.tecdoc_models_id = own_models.tecdoc_id
            LEFT JOIN own_manufactures ON own_manufactures.tecdoc_id = own_models.tecdoc_manufacture_id
            WHERE own_models.active = 1 AND own_manufactures.url = '".$manuf."'
            AND EXISTS(
              SELECT 1 FROM type_category_group WHERE category_id = ".$category_id." AND type_id = own_types.tecdoc_id AND type_category_group.active = 1 LIMIT 1
            )
        ";
        $results = DB::query(Database::SELECT,$query)->execute('default')->as_array();
        return $results;
    }

    public function models_by_parent_category_id ($category_id, $manuf){
        $query = "
            SELECT DISTINCT own_models.url, own_models.short_name  FROM own_models
            LEFT JOIN own_types ON own_types.tecdoc_models_id = own_models.tecdoc_id
            LEFT JOIN own_manufactures ON own_manufactures.tecdoc_id = own_models.tecdoc_manufacture_id
            WHERE own_models.active = 1 AND own_manufactures.url = '".$manuf."'
            AND EXISTS(
              SELECT 1 FROM type_category_group WHERE category_id IN (SELECT ch.id
                    FROM categories c
                    INNER JOIN categories ch ON c.id = ch.parent_id
                    WHERE c.parent_id = ".$category_id.") AND type_id = own_types.tecdoc_id AND type_category_group.active = 1 LIMIT 1
            )
        ";
        $results = DB::query(Database::SELECT,$query)->execute('default')->as_array();
        return $results;
    }

    public function get_models_ids_by_url_and_manuf_id($model_url, $manuf_id) // получаем id моделей по урлу и id производителя
    {

        $query = "
            SELECT DISTINCT
            GROUP_CONCAT(tecdoc_id SEPARATOR ', ') as parts_ids
            from own_models
            WHERE tecdoc_manufacture_id = ".$manuf_id."
                and url = '".$model_url."'
		";

        $results = DB::query(Database::SELECT,$query)->execute('default')->get('parts_ids',0);
        return $results;
    }

    //    --------------------------------------------------
    //    ------------ ARTICLES ----------------------------
    //    --------------------------------------------------

    public function get_all_top_article_for_cat($catID)
    {
        $query = "

            SELECT *
            FROM (
                   SELECT
                     priceitems.id,
                     priceitems.part_id,
                     priceitems.price * currencies.ratio             AS price_start,
                     parts.article_long,
                     parts.article,
                     parts.brand,
                     brands.country, 
                     parts.brand_long,
                     parts.images,
                     parts.`name`,
                     priceitems.amount,
                     priceitems.price * currencies.ratio * (SELECT discount_limits.percentage / 100 + 1
                                                            FROM discount_limits
                                                              LEFT JOIN discounts ON discount_limits.discount_id = discounts.id
                                                            WHERE discounts.standart = 1
                                                                  AND priceitems.price * currencies.ratio > discount_limits.from
                                                                  AND (priceitems.price * currencies.ratio <= discount_limits.to OR
                                                                       discount_limits.to = 0)
                                                            LIMIT 1) AS price_final,
                     priceitems.delivery
                   FROM priceitems
                     INNER JOIN currencies ON currencies.id = priceitems.currency_id
                     INNER JOIN suppliers ON priceitems.supplier_id = suppliers.id
                     

										 INNER JOIN (SELECT DISTINCT parts.*
                                 FROM parts
                                   INNER JOIN top_products_category ON top_products_category.part_id = parts.id
																	 WHERE EXISTS(SELECT 1
                                              FROM priceitems
																							INNER JOIN suppliers ON suppliers.id = priceitems.supplier_id
                                              WHERE suppliers.dont_show = 0 AND priceitems.part_id = top_products_category.part_id
                                              LIMIT 1) 

																	 AND top_products_category.category_id = ".$catID.") 
															AS parts ON priceitems.part_id = parts.id


                                 INNER JOIN brands ON brands.id = parts.brand_id
                                 WHERE suppliers.dont_show = 0
                   ORDER BY priceitems.part_id, IF(priceitems.delivery = 1, priceitems.delivery, price_final),
                     IF(priceitems.delivery = 1, price_final, priceitems.delivery)
                 ) AS temp
            GROUP BY part_id
            LIMIT 12
		";

        $results = DB::query(Database::SELECT,$query)->execute('default')->as_array();
        return $results;
    }

    public function get_all_articul_by_cat_model_manufacture($manufacture, $model, $cat_slug_id) // вывод запчастей для автомобиля (модели, например, AUDI 100) по категории
    {
        $query = "

            SELECT DISTINCT GROUP_CONCAT(group_parts.part_id SEPARATOR ', ' ) as parts_ids
            FROM type_category_group
              LEFT JOIN own_types ON type_category_group.type_id = own_types.tecdoc_id
                LEFT JOIN own_models ON own_models.tecdoc_id = own_types.tecdoc_models_id
            LEFT JOIN own_manufactures ON own_manufactures.tecdoc_id = own_models.tecdoc_manufacture_id
              LEFT JOIN group_parts ON type_category_group.id = group_parts.group_id
            WHERE
              type_category_group.category_id = ".$cat_slug_id."
              AND
              own_models.url = '".$model."'
            AND 
            own_manufactures.url = '".$manufacture."'
		";

        $results = DB::query(Database::SELECT,$query)->execute('default')->get('parts_ids',0);
        return $results;
    }

    public function get_all_articul_by_cat_model($model_url, $manuf_url, $cat_id, $offset) // вывод запчастей для типа автомобиля по категории с учетом самой дешовой
    {
        $selectPartsQuery = "SELECT DISTINCT GROUP_CONCAT(group_parts.part_id) as parts_id
                                 FROM group_parts
                                 INNER JOIN type_category_group ON group_parts.group_id = type_category_group.id
                                 INNER JOIN own_types ON type_category_group.type_id = own_types.tecdoc_id
                                 INNER JOIN own_models ON own_types.tecdoc_models_id = own_models.tecdoc_id
                                 INNER JOIN own_manufactures ON own_models.tecdoc_manufacture_id = own_manufactures.tecdoc_id
                                 WHERE EXISTS(SELECT 1
                                                        FROM priceitems
                                                        INNER JOIN suppliers ON suppliers.id = priceitems.supplier_id
                                                        WHERE suppliers.dont_show = 0 AND priceitems.part_id = group_parts.part_id
                                                        LIMIT 1)
                                AND type_category_group.category_id = ".$cat_id."
                                AND own_models.url = '".$model_url."'
                                AND own_manufactures.url = '".$manuf_url."' ";

        $selectPartsIds = DB::query(Database::SELECT,$selectPartsQuery)->execute('default')->get('parts_id',0);

        if(substr($selectPartsIds, -1) == ',')
            $selectPartsIds = mb_substr($selectPartsIds, 0, -1);


        $selectCrosses = "  SELECT DISTINCT parts.*
                            FROM (
                                SELECT crosses_td_mod.to_id  as id
                                FROM crosses_td_mod
                                WHERE crosses_td_mod.from_id IN (".$selectPartsIds.")
                                
                                
                                UNION ALL
                                SELECT crosses_td_mod.from_id  as id
                                FROM crosses_td_mod
                                WHERE crosses_td_mod.to_id IN (".$selectPartsIds.")
                                
                                
                                UNION ALL
                                SELECT crosses.to_id as id
                               FROM crosses
                                WHERE crosses.from_id IN (".$selectPartsIds.")
                                
                                
                                UNION ALL
                                SELECT crosses.from_id  as id
                                FROM crosses
                                WHERE crosses.to_id IN (".$selectPartsIds.")
                                
                                
                                UNION ALL
                                SELECT parts.id  as id
                                FROM parts
                                WHERE parts.id IN (".$selectPartsIds.")
        
                            ) AS crosses_all
                            INNER JOIN parts ON crosses_all.id = parts.id
                            WHERE EXISTS(SELECT 1
                                FROM priceitems
                                INNER JOIN suppliers ON suppliers.id = priceitems.supplier_id
                                WHERE suppliers.dont_show = 0 AND priceitems.part_id = parts.id
                            LIMIT 1)";


        $query = "
            SELECT *
            FROM (
                   SELECT
                     priceitems.id,
                     priceitems.part_id,
                     priceitems.price * currencies.ratio             AS price_start,
                     parts.article_long,
                     parts.article,
                     parts.brand,
                     brands.country, 
                     parts.brand_long,
                     parts.images,
                     parts.`name`,
                     priceitems.amount,
                     priceitems.price * currencies.ratio * (SELECT discount_limits.percentage / 100 + 1
                                                            FROM discount_limits
                                                              LEFT JOIN discounts ON discount_limits.discount_id = discounts.id
                                                            WHERE discounts.standart = 1
                                                                  AND priceitems.price * currencies.ratio > discount_limits.from
                                                                  AND (priceitems.price * currencies.ratio <= discount_limits.to OR
                                                                       discount_limits.to = 0)
                                                            LIMIT 1) AS price_final,
                     priceitems.delivery
                   FROM priceitems
                     INNER JOIN currencies ON currencies.id = priceitems.currency_id
                     INNER JOIN suppliers ON priceitems.supplier_id = suppliers.id
                     INNER JOIN (".$selectCrosses."
                                 ) AS parts ON priceitems.part_id = parts.id
                                 INNER JOIN brands ON brands.id = parts.brand_id
                                 WHERE suppliers.dont_show = 0
                   ORDER BY priceitems.part_id, IF(priceitems.delivery = 1, priceitems.delivery, price_final),
                     IF(priceitems.delivery = 1, price_final, priceitems.delivery)
                 ) AS temp
            GROUP BY part_id ORDER BY price_final
            LIMIT ".$offset.",30/*ОСЬ ДЕ ЦЕЙ ЛІМІТ*/
		";


//        $queryOld = "
//            SELECT *
//            FROM (
//                   SELECT
//                     priceitems.id,
//                     priceitems.part_id,
//                     priceitems.price * currencies.ratio             AS price_start,
//                     parts.article_long,
//                     parts.article,
//                     parts.brand,
//                     brands.country,
//                     parts.brand_long,
//                     parts.images,
//                     parts.`name`,
//                     priceitems.amount,
//                     priceitems.price * currencies.ratio * (SELECT discount_limits.percentage / 100 + 1
//                                                            FROM discount_limits
//                                                              LEFT JOIN discounts ON discount_limits.discount_id = discounts.id
//                                                            WHERE discounts.standart = 1
//                                                                  AND priceitems.price * currencies.ratio > discount_limits.from
//                                                                  AND (priceitems.price * currencies.ratio <= discount_limits.to OR
//                                                                       discount_limits.to = 0)
//                                                            LIMIT 1) AS price_final,
//                     priceitems.delivery
//                   FROM priceitems
//                     INNER JOIN currencies ON currencies.id = priceitems.currency_id
//                     INNER JOIN suppliers ON priceitems.supplier_id = suppliers.id
//                     INNER JOIN (SELECT DISTINCT parts.*
//                                 FROM parts
//                                   INNER JOIN group_parts ON group_parts.part_id = parts.id
//                                   INNER JOIN type_category_group ON group_parts.group_id = type_category_group.id
//                                   INNER JOIN own_types ON type_category_group.type_id = own_types.tecdoc_id
//                                   INNER JOIN own_models ON own_types.tecdoc_models_id = own_models.tecdoc_id
//                                   INNER JOIN own_manufactures
//                                     ON own_models.tecdoc_manufacture_id = own_manufactures.tecdoc_id
//                                 WHERE EXISTS(SELECT 1
//                                              FROM priceitems
//                                                INNER JOIN suppliers ON suppliers.id = priceitems.supplier_id
//                                              WHERE suppliers.dont_show = 0 AND priceitems.part_id = group_parts.part_id
//                                              LIMIT 1) AND type_category_group.category_id = ".$cat_id."
//                                       AND own_models.url = \"".$model_url."\"
//                                       AND own_manufactures.url = \"".$manuf_url."\"
//                                 ) AS parts ON priceitems.part_id = parts.id
//                                 INNER JOIN brands ON brands.id = parts.brand_id
//                                 WHERE suppliers.dont_show = 0
//                   ORDER BY priceitems.part_id, IF(priceitems.delivery = 1, priceitems.delivery, price_final),
//                     IF(priceitems.delivery = 1, price_final, priceitems.delivery)
//                 ) AS temp
//            GROUP BY part_id ORDER BY price_final
//LIMIT ".$offset.",30/*ОСЬ ДЕ ЦЕЙ ЛІМІТ*/
//		";

        $results = DB::query(Database::SELECT,$query)->execute('default')->as_array();
        return $results;
    }

    public function get_count_articul_by_cat_model($model_url, $manuf_url, $cat_id) // вывод количества запчастей для типа автомобиля по категории с учетом самой дешовой
    {

        $selectPartsQuery = "SELECT DISTINCT GROUP_CONCAT(group_parts.part_id) as parts_id
                                 FROM group_parts
                                 INNER JOIN type_category_group ON group_parts.group_id = type_category_group.id
                                 INNER JOIN own_types ON type_category_group.type_id = own_types.tecdoc_id
                                 INNER JOIN own_models ON own_types.tecdoc_models_id = own_models.tecdoc_id
                                 INNER JOIN own_manufactures ON own_models.tecdoc_manufacture_id = own_manufactures.tecdoc_id
                                 WHERE EXISTS(SELECT 1
                                                        FROM priceitems
                                                        INNER JOIN suppliers ON suppliers.id = priceitems.supplier_id
                                                        WHERE suppliers.dont_show = 0 AND priceitems.part_id = group_parts.part_id
                                                        LIMIT 1)
                                AND type_category_group.category_id = ".$cat_id."
                                AND own_models.url = '".$model_url."'
                                AND own_manufactures.url = '".$manuf_url."' ";

        $selectPartsIds = DB::query(Database::SELECT,$selectPartsQuery)->execute('default')->get('parts_id',0);

        if(substr($selectPartsIds, -1) == ',')
            $selectPartsIds = mb_substr($selectPartsIds, 0, -1);


//        print_r($selectPartsQuery); exit();


        $selectCrosses = "  SELECT DISTINCT parts.*
                            FROM (
                                SELECT crosses_td_mod.to_id  as id
                                FROM crosses_td_mod
                                WHERE crosses_td_mod.from_id IN (".$selectPartsIds.")
                                
                                
                                UNION ALL
                                SELECT crosses_td_mod.from_id  as id
                                FROM crosses_td_mod
                                WHERE crosses_td_mod.to_id IN (".$selectPartsIds.")
                                
                                
                                UNION ALL
                                SELECT crosses.to_id as id
                               FROM crosses
                                WHERE crosses.from_id IN (".$selectPartsIds.")
                                
                                
                                UNION ALL
                                SELECT crosses.from_id  as id
                                FROM crosses
                                WHERE crosses.to_id IN (".$selectPartsIds.")
                                
                                
                                UNION ALL
                                SELECT parts.id  as id
                                FROM parts
                                WHERE parts.id IN (".$selectPartsIds.")
        
                            ) AS crosses_all
                            INNER JOIN parts ON crosses_all.id = parts.id
                            WHERE EXISTS(SELECT 1
                                FROM priceitems
                                INNER JOIN suppliers ON suppliers.id = priceitems.supplier_id
                                WHERE suppliers.dont_show = 0 AND priceitems.part_id = parts.id
                            LIMIT 1)";


        $query = "
            SELECT COUNT(*) as count_article FROM (SELECT *
            FROM (
                   SELECT
                     priceitems.id,
                     priceitems.part_id,
                     priceitems.price * currencies.ratio             AS price_start,
                     parts.article_long,
                     parts.article,
                     parts.brand,
                     brands.country, 
                     parts.brand_long,
                     parts.images,
                     parts.`name`,
                     priceitems.amount,
                     priceitems.price * currencies.ratio * (SELECT discount_limits.percentage / 100 + 1
                                                            FROM discount_limits
                                                              LEFT JOIN discounts ON discount_limits.discount_id = discounts.id
                                                            WHERE discounts.standart = 1
                                                                  AND priceitems.price * currencies.ratio > discount_limits.from
                                                                  AND (priceitems.price * currencies.ratio <= discount_limits.to OR
                                                                       discount_limits.to = 0)
                                                            LIMIT 1) AS price_final,
                     priceitems.delivery
                   FROM priceitems
                     INNER JOIN currencies ON currencies.id = priceitems.currency_id
                     INNER JOIN suppliers ON priceitems.supplier_id = suppliers.id
                     INNER JOIN (".$selectCrosses.") AS parts ON priceitems.part_id = parts.id
                                 INNER JOIN brands ON brands.id = parts.brand_id
                                 WHERE suppliers.dont_show = 0
                   ORDER BY priceitems.part_id, IF(priceitems.delivery = 1, priceitems.delivery, price_final),
                     IF(priceitems.delivery = 1, price_final, priceitems.delivery)
                 ) AS temp
            GROUP BY part_id) AS T
		";
//print_r($query); exit();
//
//        $queryOld = "
//            SELECT COUNT(*) as count_article FROM (SELECT *
//            FROM (
//                   SELECT
//                     priceitems.id,
//                     priceitems.part_id,
//                     priceitems.price * currencies.ratio             AS price_start,
//                     parts.article_long,
//                     parts.article,
//                     parts.brand,
//                     brands.country,
//                     parts.brand_long,
//                     parts.images,
//                     parts.`name`,
//                     priceitems.amount,
//                     priceitems.price * currencies.ratio * (SELECT discount_limits.percentage / 100 + 1
//                                                            FROM discount_limits
//                                                              LEFT JOIN discounts ON discount_limits.discount_id = discounts.id
//                                                            WHERE discounts.standart = 1
//                                                                  AND priceitems.price * currencies.ratio > discount_limits.from
//                                                                  AND (priceitems.price * currencies.ratio <= discount_limits.to OR
//                                                                       discount_limits.to = 0)
//                                                            LIMIT 1) AS price_final,
//                     priceitems.delivery
//                   FROM priceitems
//                     INNER JOIN currencies ON currencies.id = priceitems.currency_id
//                     INNER JOIN suppliers ON priceitems.supplier_id = suppliers.id
//                     INNER JOIN (SELECT DISTINCT parts.*
//                                 FROM parts
//                                   INNER JOIN group_parts ON group_parts.part_id = parts.id
//                                   INNER JOIN type_category_group ON group_parts.group_id = type_category_group.id
//                                   INNER JOIN own_types ON type_category_group.type_id = own_types.tecdoc_id
//                                   INNER JOIN own_models ON own_types.tecdoc_models_id = own_models.tecdoc_id
//                                   INNER JOIN own_manufactures
//                                     ON own_models.tecdoc_manufacture_id = own_manufactures.tecdoc_id
//                                 WHERE EXISTS(SELECT 1
//                                              FROM priceitems
//                                                INNER JOIN suppliers ON suppliers.id = priceitems.supplier_id
//                                              WHERE suppliers.dont_show = 0 AND priceitems.part_id = group_parts.part_id
//                                              LIMIT 1) AND type_category_group.category_id = ".$cat_id."
//                                       AND own_models.url = '".$model_url."'
//                                       AND own_manufactures.url = '".$manuf_url."') AS parts ON priceitems.part_id = parts.id
//                                 INNER JOIN brands ON brands.id = parts.brand_id
//                                 WHERE suppliers.dont_show = 0
//                   ORDER BY priceitems.part_id, IF(priceitems.delivery = 1, priceitems.delivery, price_final),
//                     IF(priceitems.delivery = 1, price_final, priceitems.delivery)
//                 ) AS temp
//            GROUP BY part_id) AS T
//		";

        $results = DB::query(Database::SELECT,$query)->execute('default')->get('count_article',0);
        return $results;
    }

    public function get_years_model($manuf_url, $model_url)
    {
        $query = "SELECT DISTINCT	TYP_PCON_START as start_date,	TYP_PCON_END as end_date
            FROM	TYPES
            INNER JOIN MODELS ON MOD_ID = TYP_MOD_ID
            INNER JOIN MANUFACTURERS ON MFA_ID = MOD_MFA_ID
            INNER JOIN own_models ON own_models.tecdoc_id = MOD_ID
            INNER JOIN own_manufactures ON MFA_ID = own_manufactures.tecdoc_id
            WHERE	own_manufactures.url = \"".$manuf_url."\" AND own_models.url = \"".$model_url."\" AND TYP_PCON_START > 198000
            ORDER BY	TYP_PCON_START";
        $results = DB::query(Database::SELECT,$query)->execute('tecdoc_new')->as_array();
        return $results;
    }

    public function get_all_articul_by_cat_type($type_id, $cat_id, $offset) // вывод запчастей для типа автомобиля по категории с учетом самой дешовой
    {

        $selectPartsQuery = "SELECT DISTINCT GROUP_CONCAT(group_parts.part_id) as parts_id
                                 FROM group_parts
                                 INNER JOIN type_category_group ON group_parts.group_id = type_category_group.id
                                 INNER JOIN own_types ON type_category_group.type_id = own_types.tecdoc_id
                                 INNER JOIN own_models ON own_types.tecdoc_models_id = own_models.tecdoc_id
                                 INNER JOIN own_manufactures ON own_models.tecdoc_manufacture_id = own_manufactures.tecdoc_id
                                 WHERE EXISTS(SELECT 1
                                                        FROM priceitems
                                                        INNER JOIN suppliers ON suppliers.id = priceitems.supplier_id
                                                        WHERE suppliers.dont_show = 0 AND priceitems.part_id = group_parts.part_id
                                                        LIMIT 1)
                                AND type_category_group.category_id = ".$cat_id."
                                AND own_types.tecdoc_id = ".$type_id." ";

        $selectPartsIds = DB::query(Database::SELECT,$selectPartsQuery)->execute('default')->get('parts_id',0);

        if(substr($selectPartsIds, -1) == ',')
            $selectPartsIds = mb_substr($selectPartsIds, 0, -1);


        $selectCrosses = "  SELECT DISTINCT parts.*
                            FROM (
                                SELECT crosses_td_mod.to_id  as id
                                FROM crosses_td_mod
                                WHERE crosses_td_mod.from_id IN (".$selectPartsIds.")
                                
                                
                                UNION ALL
                                SELECT crosses_td_mod.from_id  as id
                                FROM crosses_td_mod
                                WHERE crosses_td_mod.to_id IN (".$selectPartsIds.")
                                
                                
                                UNION ALL
                                SELECT crosses.to_id as id
                               FROM crosses
                                WHERE crosses.from_id IN (".$selectPartsIds.")
                                
                                
                                UNION ALL
                                SELECT crosses.from_id  as id
                                FROM crosses
                                WHERE crosses.to_id IN (".$selectPartsIds.")
                                
                                
                                UNION ALL
                                SELECT parts.id  as id
                                FROM parts
                                WHERE parts.id IN (".$selectPartsIds.")
        
                            ) AS crosses_all
                            INNER JOIN parts ON crosses_all.id = parts.id
                            WHERE EXISTS(SELECT 1
                                FROM priceitems
                                INNER JOIN suppliers ON suppliers.id = priceitems.supplier_id
                                WHERE suppliers.dont_show = 0 AND priceitems.part_id = parts.id
                            LIMIT 1)";


        $query = "
            SELECT *
            FROM (
                   SELECT
                     priceitems.id,
                     priceitems.part_id,
                     priceitems.price * currencies.ratio             AS price_start,
                     parts.article_long,
                     parts.article,
                     parts.brand,
                     brands.country, 
                     parts.brand_long,
                     parts.images,
                     parts.`name`,
                     priceitems.amount,
                     priceitems.price * currencies.ratio * (SELECT discount_limits.percentage / 100 + 1
                                                            FROM discount_limits
                                                              LEFT JOIN discounts ON discount_limits.discount_id = discounts.id
                                                            WHERE discounts.standart = 1
                                                                  AND priceitems.price * currencies.ratio > discount_limits.from
                                                                  AND (priceitems.price * currencies.ratio <= discount_limits.to OR
                                                                       discount_limits.to = 0)
                                                            LIMIT 1) AS price_final,
                     priceitems.delivery
                   FROM priceitems
                     INNER JOIN currencies ON currencies.id = priceitems.currency_id
                     INNER JOIN suppliers ON priceitems.supplier_id = suppliers.id
                     INNER JOIN (".$selectCrosses."
                                 ) AS parts ON priceitems.part_id = parts.id
                                 INNER JOIN brands ON brands.id = parts.brand_id
                                 WHERE suppliers.dont_show = 0
                   ORDER BY priceitems.part_id, IF(priceitems.delivery = 1, priceitems.delivery, price_final),
                     IF(priceitems.delivery = 1, price_final, priceitems.delivery)
                 ) AS temp
            GROUP BY part_id ORDER BY price_final
            LIMIT ".$offset.",30/*ОСЬ ДЕ ЦЕЙ ЛІМІТ*/
		";




//        $query = "
//            SELECT *
//            FROM (
//                   SELECT
//                     priceitems.id,
//                     priceitems.part_id,
//                     priceitems.price * currencies.ratio             AS price_start,
//                     parts.article_long,
//                     parts.article,
//                     parts.brand,
//                     parts.brand_long,
//                     parts.images,
//                     brands.country,
//                     parts.`name`,
//                     priceitems.amount,
//                     priceitems.price * currencies.ratio * (SELECT discount_limits.percentage / 100 + 1
//                                                            FROM discount_limits
//                                                              LEFT JOIN discounts ON discount_limits.discount_id = discounts.id
//                                                            WHERE discounts.standart = 1
//                                                                  AND priceitems.price * currencies.ratio > discount_limits.from
//                                                                  AND (priceitems.price * currencies.ratio <= discount_limits.to OR
//                                                                       discount_limits.to = 0)
//                                                            LIMIT 1) AS price_final,
//                     priceitems.delivery
//                   FROM priceitems
//                     INNER JOIN currencies ON currencies.id = priceitems.currency_id
//                     INNER JOIN suppliers ON priceitems.supplier_id = suppliers.id
//                     INNER JOIN (SELECT DISTINCT parts.*
//                                 FROM parts
//                                   INNER JOIN group_parts ON group_parts.part_id = parts.id
//                                   INNER JOIN type_category_group ON group_parts.group_id = type_category_group.id
//                                   INNER JOIN own_types ON type_category_group.type_id = own_types.tecdoc_id
//                                 WHERE EXISTS(SELECT 1
//                                              FROM priceitems
//                                                INNER JOIN suppliers ON suppliers.id = priceitems.supplier_id
//                                              WHERE suppliers.dont_show = 0 AND priceitems.part_id = group_parts.part_id
//                                              LIMIT 1) AND type_category_group.category_id = ".$cat_id."
//                                       AND own_types.tecdoc_id = ".$type_id."
//                                 ) AS parts ON priceitems.part_id = parts.id
//                                 INNER JOIN brands ON brands.id = parts.brand_id
//                                 WHERE suppliers.dont_show = 0
//                   ORDER BY priceitems.part_id, IF(priceitems.delivery = 1, priceitems.delivery, price_final),
//                     IF(priceitems.delivery = 1, price_final, priceitems.delivery)
//                 ) AS temp
//            GROUP BY part_id ORDER BY price_final LIMIT ".$offset.",30/*ОСЬ ДЕ ЦЕЙ ЛІМІТ*/
//		";

        $results = DB::query(Database::SELECT,$query)->execute('default')->as_array();
        return $results;
    }

    public function get_count_articul_by_cat_type($type_id, $cat_id) // вывод запчастей для типа автомобиля по категории с учетом самой дешовой
    {

        $selectPartsQuery = "SELECT DISTINCT GROUP_CONCAT(group_parts.part_id) as parts_id
                                 FROM group_parts
                                 INNER JOIN type_category_group ON group_parts.group_id = type_category_group.id
                                 INNER JOIN own_types ON type_category_group.type_id = own_types.tecdoc_id
                                 INNER JOIN own_models ON own_types.tecdoc_models_id = own_models.tecdoc_id
                                 INNER JOIN own_manufactures ON own_models.tecdoc_manufacture_id = own_manufactures.tecdoc_id
                                 WHERE EXISTS(SELECT 1
                                                        FROM priceitems
                                                        INNER JOIN suppliers ON suppliers.id = priceitems.supplier_id
                                                        WHERE suppliers.dont_show = 0 AND priceitems.part_id = group_parts.part_id
                                                        LIMIT 1)
                                AND type_category_group.category_id = ".$cat_id."
                                AND own_types.tecdoc_id = ".$type_id." ";

        $selectPartsIds = DB::query(Database::SELECT,$selectPartsQuery)->execute('default')->get('parts_id',0);

        if(substr($selectPartsIds, -1) == ',')
            $selectPartsIds = mb_substr($selectPartsIds, 0, -1);


        $selectCrosses = "  SELECT DISTINCT parts.*
                            FROM (
                                SELECT crosses_td_mod.to_id  as id
                                FROM crosses_td_mod
                                WHERE crosses_td_mod.from_id IN (".$selectPartsIds.")
                                
                                
                                UNION ALL
                                SELECT crosses_td_mod.from_id  as id
                                FROM crosses_td_mod
                                WHERE crosses_td_mod.to_id IN (".$selectPartsIds.")
                                
                                
                                UNION ALL
                                SELECT crosses.to_id as id
                               FROM crosses
                                WHERE crosses.from_id IN (".$selectPartsIds.")
                                
                                
                                UNION ALL
                                SELECT crosses.from_id  as id
                                FROM crosses
                                WHERE crosses.to_id IN (".$selectPartsIds.")
                                
                                
                                UNION ALL
                                SELECT parts.id  as id
                                FROM parts
                                WHERE parts.id IN (".$selectPartsIds.")
        
                            ) AS crosses_all
                            INNER JOIN parts ON crosses_all.id = parts.id
                            WHERE EXISTS(SELECT 1
                                FROM priceitems
                                INNER JOIN suppliers ON suppliers.id = priceitems.supplier_id
                                WHERE suppliers.dont_show = 0 AND priceitems.part_id = parts.id
                            LIMIT 1)";


        $query = "
            SELECT COUNT(*) as count_article FROM (SELECT *
            FROM (
                   SELECT
                     priceitems.id,
                     priceitems.part_id,
                     priceitems.price * currencies.ratio             AS price_start,
                     parts.article_long,
                     parts.article,
                     parts.brand,
                     brands.country, 
                     parts.brand_long,
                     parts.images,
                     parts.`name`,
                     priceitems.amount,
                     priceitems.price * currencies.ratio * (SELECT discount_limits.percentage / 100 + 1
                                                            FROM discount_limits
                                                              LEFT JOIN discounts ON discount_limits.discount_id = discounts.id
                                                            WHERE discounts.standart = 1
                                                                  AND priceitems.price * currencies.ratio > discount_limits.from
                                                                  AND (priceitems.price * currencies.ratio <= discount_limits.to OR
                                                                       discount_limits.to = 0)
                                                            LIMIT 1) AS price_final,
                     priceitems.delivery
                   FROM priceitems
                     INNER JOIN currencies ON currencies.id = priceitems.currency_id
                     INNER JOIN suppliers ON priceitems.supplier_id = suppliers.id
                     INNER JOIN (".$selectCrosses.") AS parts ON priceitems.part_id = parts.id
                                 INNER JOIN brands ON brands.id = parts.brand_id
                                 WHERE suppliers.dont_show = 0
                   ORDER BY priceitems.part_id, IF(priceitems.delivery = 1, priceitems.delivery, price_final),
                     IF(priceitems.delivery = 1, price_final, priceitems.delivery)
                 ) AS temp
            GROUP BY part_id) AS T
		";



//        $query = "
//            SELECT COUNT(*) as count_article FROM (SELECT *
//            FROM (
//                   SELECT
//                     priceitems.id,
//                     priceitems.part_id,
//                     priceitems.price * currencies.ratio             AS price_start,
//                     parts.article_long,
//                     parts.article,
//                     parts.brand,
//                     parts.brand_long,
//                     parts.images,
//                     brands.country,
//                     parts.`name`,
//                     priceitems.amount,
//                     priceitems.price * currencies.ratio * (SELECT discount_limits.percentage / 100 + 1
//                                                            FROM discount_limits
//                                                              LEFT JOIN discounts ON discount_limits.discount_id = discounts.id
//                                                            WHERE discounts.standart = 1
//                                                                  AND priceitems.price * currencies.ratio > discount_limits.from
//                                                                  AND (priceitems.price * currencies.ratio <= discount_limits.to OR
//                                                                       discount_limits.to = 0)
//                                                            LIMIT 1) AS price_final,
//                     priceitems.delivery
//                   FROM priceitems
//                     INNER JOIN currencies ON currencies.id = priceitems.currency_id
//                     INNER JOIN suppliers ON priceitems.supplier_id = suppliers.id
//                     INNER JOIN (SELECT DISTINCT parts.*
//                                 FROM parts
//                                   INNER JOIN group_parts ON group_parts.part_id = parts.id
//                                   INNER JOIN type_category_group ON group_parts.group_id = type_category_group.id
//                                   INNER JOIN own_types ON type_category_group.type_id = own_types.tecdoc_id
//                                 WHERE EXISTS(SELECT 1
//                                              FROM priceitems
//                                                INNER JOIN suppliers ON suppliers.id = priceitems.supplier_id
//                                              WHERE suppliers.dont_show = 0 AND priceitems.part_id = group_parts.part_id
//                                              LIMIT 1) AND type_category_group.category_id = ".$cat_id."
//                                       AND own_types.tecdoc_id = ".$type_id.") AS parts ON priceitems.part_id = parts.id
//                                 INNER JOIN brands ON brands.id = parts.brand_id
//                                 WHERE suppliers.dont_show = 0
//                   ORDER BY priceitems.part_id, IF(priceitems.delivery = 1, priceitems.delivery, price_final),
//                     IF(priceitems.delivery = 1, price_final, priceitems.delivery)
//                 ) AS temp
//            GROUP BY part_id) AS T
//		";

        $results = DB::query(Database::SELECT,$query)->execute('default')->get('count_article',0);
        return $results;
    }

    public function get_all_articul_by_cat_model_filtes($model_url, $manuf_url, $cat_id, $brand_name) // вывод запчастей для типа автомобиля по категории с учетом самой дешовой после фильтра
    {
        $selectPartsQuery = "SELECT DISTINCT GROUP_CONCAT(group_parts.part_id) as parts_id
                                 FROM group_parts
                                 INNER JOIN type_category_group ON group_parts.group_id = type_category_group.id
                                 INNER JOIN own_types ON type_category_group.type_id = own_types.tecdoc_id
                                 INNER JOIN own_models ON own_types.tecdoc_models_id = own_models.tecdoc_id
                                 INNER JOIN own_manufactures ON own_models.tecdoc_manufacture_id = own_manufactures.tecdoc_id
                                 WHERE EXISTS(SELECT 1
                                                        FROM priceitems
                                                        INNER JOIN suppliers ON suppliers.id = priceitems.supplier_id
                                                        WHERE suppliers.dont_show = 0 AND priceitems.part_id = group_parts.part_id
                                                        LIMIT 1)
                                AND type_category_group.category_id = ".$cat_id."
                                AND own_models.url = '".$model_url."'
                                AND own_manufactures.url = '".$manuf_url."' ";

        $selectPartsIds = DB::query(Database::SELECT,$selectPartsQuery)->execute('default')->get('parts_id',0);

        if(substr($selectPartsIds, -1) == ',')
            $selectPartsIds = mb_substr($selectPartsIds, 0, -1);


        $selectCrosses = "  SELECT DISTINCT parts.*
                            FROM (
                                SELECT crosses_td_mod.to_id  as id
                                FROM crosses_td_mod
                                WHERE crosses_td_mod.from_id IN (".$selectPartsIds.")
                                
                                
                                UNION ALL
                                SELECT crosses_td_mod.from_id  as id
                                FROM crosses_td_mod
                                WHERE crosses_td_mod.to_id IN (".$selectPartsIds.")
                                
                                
                                UNION ALL
                                SELECT crosses.to_id as id
                               FROM crosses
                                WHERE crosses.from_id IN (".$selectPartsIds.")
                                
                                
                                UNION ALL
                                SELECT crosses.from_id  as id
                                FROM crosses
                                WHERE crosses.to_id IN (".$selectPartsIds.")
                                
                                
                                UNION ALL
                                SELECT parts.id  as id
                                FROM parts
                                WHERE parts.id IN (".$selectPartsIds.")
        
                            ) AS crosses_all
                            INNER JOIN parts ON crosses_all.id = parts.id
                            WHERE EXISTS(SELECT 1
                                FROM priceitems
                                INNER JOIN suppliers ON suppliers.id = priceitems.supplier_id
                                WHERE suppliers.dont_show = 0 AND priceitems.part_id = parts.id
                            LIMIT 1) AND parts.brand IN (".$brand_name.")";


        $query = "
            SELECT *
            FROM (
                   SELECT
                     priceitems.id,
                     priceitems.part_id,
                     priceitems.price * currencies.ratio             AS price_start,
                     parts.article_long,
                     parts.article,
                     parts.brand,
                     brands.country, 
                     parts.brand_long,
                     parts.images,
                     parts.`name`,
                     priceitems.amount,
                     priceitems.price * currencies.ratio * (SELECT discount_limits.percentage / 100 + 1
                                                            FROM discount_limits
                                                              LEFT JOIN discounts ON discount_limits.discount_id = discounts.id
                                                            WHERE discounts.standart = 1
                                                                  AND priceitems.price * currencies.ratio > discount_limits.from
                                                                  AND (priceitems.price * currencies.ratio <= discount_limits.to OR
                                                                       discount_limits.to = 0)
                                                            LIMIT 1) AS price_final,
                     priceitems.delivery
                   FROM priceitems
                     INNER JOIN currencies ON currencies.id = priceitems.currency_id
                     INNER JOIN suppliers ON priceitems.supplier_id = suppliers.id
                     INNER JOIN (".$selectCrosses."
                                 ) AS parts ON priceitems.part_id = parts.id
                                 INNER JOIN brands ON brands.id = parts.brand_id
                                 WHERE suppliers.dont_show = 0
                   ORDER BY priceitems.part_id, IF(priceitems.delivery = 1, priceitems.delivery, price_final),
                     IF(priceitems.delivery = 1, price_final, priceitems.delivery)
                 ) AS temp
            GROUP BY part_id ORDER BY price_final
		";


        $results = DB::query(Database::SELECT,$query)->execute('default')->as_array();
        return $results;
    }

    public function get_all_articul_by_type_filtes($type_id, $cat_id, $brand_name) // вывод запчастей для типа автомобиля по категории с учетом самой дешовой после фильтра
    {
        $selectPartsQuery = "SELECT DISTINCT GROUP_CONCAT(group_parts.part_id) as parts_id
                                 FROM group_parts
                                 INNER JOIN type_category_group ON group_parts.group_id = type_category_group.id
                                 INNER JOIN own_types ON type_category_group.type_id = own_types.tecdoc_id
                                 INNER JOIN own_models ON own_types.tecdoc_models_id = own_models.tecdoc_id
                                 INNER JOIN own_manufactures ON own_models.tecdoc_manufacture_id = own_manufactures.tecdoc_id
                                 WHERE EXISTS(SELECT 1
                                                        FROM priceitems
                                                        INNER JOIN suppliers ON suppliers.id = priceitems.supplier_id
                                                        WHERE suppliers.dont_show = 0 AND priceitems.part_id = group_parts.part_id
                                                        LIMIT 1)
                                AND type_category_group.category_id = ".$cat_id."
                                AND own_types.tecdoc_id = ".$type_id." ";

        $selectPartsIds = DB::query(Database::SELECT,$selectPartsQuery)->execute('default')->get('parts_id',0);

        if(substr($selectPartsIds, -1) == ',')
            $selectPartsIds = mb_substr($selectPartsIds, 0, -1);


        $selectCrosses = "  SELECT DISTINCT parts.*
                            FROM (
                                SELECT crosses_td_mod.to_id  as id
                                FROM crosses_td_mod
                                WHERE crosses_td_mod.from_id IN (".$selectPartsIds.")
                                
                                
                                UNION ALL
                                SELECT crosses_td_mod.from_id  as id
                                FROM crosses_td_mod
                                WHERE crosses_td_mod.to_id IN (".$selectPartsIds.")
                                
                                
                                UNION ALL
                                SELECT crosses.to_id as id
                               FROM crosses
                                WHERE crosses.from_id IN (".$selectPartsIds.")
                                
                                
                                UNION ALL
                                SELECT crosses.from_id  as id
                                FROM crosses
                                WHERE crosses.to_id IN (".$selectPartsIds.")
                                
                                
                                UNION ALL
                                SELECT parts.id  as id
                                FROM parts
                                WHERE parts.id IN (".$selectPartsIds.")
        
                            ) AS crosses_all
                            INNER JOIN parts ON crosses_all.id = parts.id
                            WHERE EXISTS(SELECT 1
                                FROM priceitems
                                INNER JOIN suppliers ON suppliers.id = priceitems.supplier_id
                                WHERE suppliers.dont_show = 0 AND priceitems.part_id = parts.id
                            LIMIT 1) AND parts.brand IN (".$brand_name.")";


        $query = "
            SELECT *
            FROM (
                   SELECT
                     priceitems.id,
                     priceitems.part_id,
                     priceitems.price * currencies.ratio             AS price_start,
                     parts.article_long,
                     parts.article,
                     parts.brand,
                     brands.country, 
                     parts.brand_long,
                     parts.images,
                     parts.`name`,
                     priceitems.amount,
                     priceitems.price * currencies.ratio * (SELECT discount_limits.percentage / 100 + 1
                                                            FROM discount_limits
                                                              LEFT JOIN discounts ON discount_limits.discount_id = discounts.id
                                                            WHERE discounts.standart = 1
                                                                  AND priceitems.price * currencies.ratio > discount_limits.from
                                                                  AND (priceitems.price * currencies.ratio <= discount_limits.to OR
                                                                       discount_limits.to = 0)
                                                            LIMIT 1) AS price_final,
                     priceitems.delivery
                   FROM priceitems
                     INNER JOIN currencies ON currencies.id = priceitems.currency_id
                     INNER JOIN suppliers ON priceitems.supplier_id = suppliers.id
                     INNER JOIN (".$selectCrosses."
                                 ) AS parts ON priceitems.part_id = parts.id
                                 INNER JOIN brands ON brands.id = parts.brand_id
                                 WHERE suppliers.dont_show = 0
                   ORDER BY priceitems.part_id, IF(priceitems.delivery = 1, priceitems.delivery, price_final),
                     IF(priceitems.delivery = 1, price_final, priceitems.delivery)
                 ) AS temp
            GROUP BY part_id ORDER BY price_final
		";
//
//        $query = "
//            SELECT *
//            FROM (
//                   SELECT
//                     priceitems.id,
//                     priceitems.part_id,
//                     priceitems.price * currencies.ratio             AS price_start,
//                     parts.article_long,
//                     parts.article,
//                     parts.brand,
//                     parts.brand_long,
//                     parts.images,
//                     parts.`name`,
//                     priceitems.price * currencies.ratio * (SELECT discount_limits.percentage / 100 + 1
//                                                            FROM discount_limits
//                                                              LEFT JOIN discounts ON discount_limits.discount_id = discounts.id
//                                                            WHERE discounts.standart = 1
//                                                                  AND priceitems.price * currencies.ratio > discount_limits.from
//                                                                  AND (priceitems.price * currencies.ratio <= discount_limits.to OR
//                                                                       discount_limits.to = 0)
//                                                            LIMIT 1) AS price_final,
//                     priceitems.delivery
//                   FROM priceitems
//                     INNER JOIN currencies ON currencies.id = priceitems.currency_id
//                     INNER JOIN suppliers ON priceitems.supplier_id = suppliers.id
//                     INNER JOIN (SELECT DISTINCT parts.*
//                                 FROM parts
//                                   INNER JOIN group_parts ON group_parts.part_id = parts.id
//                                   INNER JOIN type_category_group ON group_parts.group_id = type_category_group.id
//                                   INNER JOIN own_types ON type_category_group.type_id = own_types.tecdoc_id
//                                 WHERE EXISTS(SELECT 1
//                                              FROM priceitems
//                                                INNER JOIN suppliers ON suppliers.id = priceitems.supplier_id
//                                              WHERE suppliers.dont_show = 0 AND priceitems.part_id = group_parts.part_id
//                                              LIMIT 1) AND type_category_group.category_id = ".$cat_id."
//                                       AND own_types.tecdoc_id = ".$type_id." AND parts.brand IN (".$brand_name.")
//                                 ) AS parts ON priceitems.part_id = parts.id
//                                 WHERE suppliers.dont_show = 0
//                   ORDER BY priceitems.part_id, IF(priceitems.delivery = 1, priceitems.delivery, price_final),
//                     IF(priceitems.delivery = 1, price_final, priceitems.delivery)
//                 ) AS temp
//            GROUP BY part_id ORDER BY price_final
//		";

        $results = DB::query(Database::SELECT,$query)->execute('default')->as_array();
        return $results;
    }

    public function get_all_articul_by_part_id ($parts_ids) // для поиска
    {
        $query = "
			SELECT
                     priceitems.id,
                     priceitems.part_id,
                     priceitems.price*currencies.ratio as price_start,
                     parts.article_long, parts.article, parts.brand, parts.brand_long, parts.images, parts.`name`,
                     priceitems.price * currencies.ratio * (SELECT discount_limits.percentage / 100 + 1
                                                            FROM discount_limits
                                                              LEFT JOIN discounts ON discount_limits.discount_id = discounts.id
                                                            WHERE discounts.standart = 1
                                                                  AND priceitems.price * currencies.ratio > discount_limits.from
                                                                  AND (priceitems.price * currencies.ratio <= discount_limits.to OR
                                                                       discount_limits.to = 0)
                                                            LIMIT 1) AS price_final,
                     priceitems.delivery
                   FROM priceitems
                LEFT JOIN currencies ON currencies.id = priceitems.currency_id
                         LEFT JOIN suppliers ON suppliers.id = priceitems.supplier_id
                         LEFT JOIN parts ON parts.id = part_id
                WHERE part_id IN (".$parts_ids.")
                GROUP BY part_id
		";

        $results = DB::query(Database::SELECT,$query)->execute('default')->as_array();
        return $results;
    }

    public function get_all_types_ids_by_urls($model_url, $manuf_url) // вывод всех типов по урлу модели и производителя
    {
        $query = "
			SELECT
            DISTINCT GROUP_CONCAT(own_types.tecdoc_id SEPARATOR ', ' ) as types_ids 
            FROM own_manufactures
            INNER JOIN own_models ON own_models.tecdoc_manufacture_id = own_manufactures.tecdoc_id
            INNER JOIN own_types ON own_types.tecdoc_models_id = own_models.tecdoc_id
            WHERE own_models.active = 1 AND own_models.url = \"".$model_url."\" AND own_manufactures.url = \"".$manuf_url."\"
		";
        $results = DB::query(Database::SELECT,$query)->execute('default')->get('types_ids',0);
        return $results;
    }

    public function get_all_types_info_by_urls($model_url, $manuf_url) // вывод всех типов по урлу модели и производителя
    {
        $query = "
			SELECT
            own_types.url, own_types.short_name as name
            FROM own_manufactures
            INNER JOIN own_models ON own_models.tecdoc_manufacture_id = own_manufactures.tecdoc_id
            INNER JOIN own_types ON own_types.tecdoc_models_id = own_models.tecdoc_id
            WHERE own_models.active = 1 AND own_models.url = \"".$model_url."\" AND own_manufactures.url = \"".$manuf_url."\"
		";
        $results = DB::query(Database::SELECT,$query)->execute('default')->as_array();
        return $results;
    }

    //    --------------------------------------------------
    //    ---------------CAR CHOOSE BLOCK-------------------
    //    --------------------------------------------------
    public function car_choose_model($manuf, $year) //верхний блок, блок выбора модели
    {
        $query = "
			SELECT own_models.short_name AS name, GROUP_CONCAT(tecdoc_id) AS id
            FROM MODELS
            LEFT JOIN own_models ON MOD_ID = tecdoc_id
            WHERE tecdoc_manufacture_id = ".$manuf."
                AND ".$year.'12'." >= MOD_PCON_START 
                AND (".$year.'00'." <= MOD_PCON_END OR MOD_PCON_END IS NULL )
            GROUP BY own_models.short_name 
            ORDER BY own_models.short_name
		";
        $results = DB::query(Database::SELECT,$query)->execute('tecdoc_new')->as_array();
        return $results;
    }

    public function car_choose_model_by_modurl($manuf, $year, $model) //верхний блок, блок выбора модели
    {
        $query = "
			SELECT GROUP_CONCAT(tecdoc_id) AS id
            FROM MODELS
            LEFT JOIN own_models ON MOD_ID = tecdoc_id
            WHERE tecdoc_manufacture_id = ".$manuf."
                AND ".$year.'12'." >= MOD_PCON_START 
                AND (".$year.'00'." <= MOD_PCON_END OR MOD_PCON_END IS NULL ) AND own_models.url = '".$model."'
            GROUP BY own_models.short_name 
            ORDER BY own_models.short_name
		";

        $results = DB::query(Database::SELECT,$query)->execute('tecdoc_new')->get('id',0);
        return $results;
    }


    public function get_body_types($models_id) // верхний блок, выбор типа кузова
    {
        $query = "
			SELECT DISTINCT TYP_KV_BODY_DES_ID AS id, TEX_TEXT AS name
            FROM TYPES
            LEFT JOIN DESIGNATIONS ON TYP_KV_BODY_DES_ID = DES_ID
            LEFT JOIN DES_TEXTS ON DES_TEX_ID = TEX_ID
            WHERE DES_LNG_ID = 16
            AND TYP_MOD_ID IN (".$models_id.")
		";
        $results = DB::query(Database::SELECT,$query)->execute('tecdoc_new')->as_array();
        return $results;
    }

    public function car_choose_liters_fuel($models_id, $body_types_id) // верхний блок, выбор обьема
    {
//        $models_id = '5110,1891';
//        $body_types_id = 145947;
        $query = "
			SELECT DISTINCT TYP_KV_ENGINE_DES_ID AS fuel_id, TEX_TEXT AS engine_type, TYP_CCM AS capacity
            FROM TYPES
            LEFT JOIN DESIGNATIONS ON TYP_KV_ENGINE_DES_ID = DES_ID
            LEFT JOIN DES_TEXTS ON DES_TEX_ID = TEX_ID
            WHERE DES_LNG_ID = 16
            AND TYP_MOD_ID IN (".$models_id.")
            AND TYP_KV_BODY_DES_ID = ".$body_types_id."
            ORDER BY fuel_id
		";
        $results = DB::query(Database::SELECT,$query)->execute('tecdoc_new')->as_array();
        if($results) {
            foreach ($results as $row) {
                if(!isset($items[$row['engine_type']])) $items[$row['engine_type']] = array();
                $items[$row['engine_type']][] = array('id' => $row['fuel_id'], 'name' => $row['capacity']);
            }
        }
        return $items;
    }

    public function car_choose_types($models_id, $body_types_id, $fuel_id, $capasity) // верхний блок, выбор типа
    {
        $query = "
			SELECT TYP_ID AS id, TEX_TEXT AS name, TYP_HP_FROM AS capacity_hp_from
            FROM TYPES
            LEFT JOIN COUNTRY_DESIGNATIONS ON TYP_MMT_CDS_ID = CDS_ID
            LEFT JOIN DES_TEXTS ON CDS_TEX_ID = TEX_ID
            WHERE CDS_LNG_ID = 16
            AND TYP_MOD_ID IN (".$models_id.")
            AND TYP_KV_BODY_DES_ID = ".$body_types_id."
            AND TYP_KV_ENGINE_DES_ID = ".$fuel_id."
            AND TYP_CCM = ".$capasity."
		";
        $results = DB::query(Database::SELECT,$query)->execute('tecdoc_new')->as_array();
        return $results;
    }


    //    --------------------------------------------------
    //    --------------------------------------------------
    //    --------------------------------------------------

    public function get_best_priceitem_by_partid($parts_id)
    {
        $query = "SELECT priceitems.id as price_id, priceitems.amount, priceitems.delivery, priceitems.currency_id, parts.*, currencies.`name` as cur_name, currencies.ratio, currencies.id as cur_id,
            IF (delivery =1, (SELECT min(priceitems.price) FROM priceitems WHERE part_id = parts.id AND delivery = 1), (SELECT min(priceitems.price) FROM priceitems WHERE part_id = parts.id)) as price_best
            FROM parts
            INNER JOIN priceitems ON priceitems.part_id = parts.id
            INNER JOIN currencies ON priceitems.currency_id = currencies.id
            INNER JOIN suppliers ON suppliers.id = priceitems.supplier_id
            WHERE part_id IN (".$parts_id.") AND suppliers.dont_show = 0
            GROUP BY part_id
            
		";

        $results = DB::query(Database::SELECT,$query)->execute('default')->as_array();
        return $results;
    }

    public function get_url_by_type ($type_id) //ссылка для типа авто
    {
        $query = "
            SELECT own_types.url as type_url, own_models.url as model_url, own_manufactures.url as manuf_url FROM own_types
            INNER JOIN own_models ON own_models.tecdoc_id = own_types.tecdoc_models_id
            INNER JOIN own_manufactures ON own_models.tecdoc_manufacture_id = own_manufactures.tecdoc_id
            WHERE own_types.tecdoc_id = ".$type_id."
		";
        $results = DB::query(Database::SELECT,$query)->execute('default')->current();
        return $results;
    }

    public function get_car_info_by_type_url($type_id) //ссылка для типа авто
    {
        $query = "
            SELECT own_types.short_name as type_name, own_types.url as type_url, own_models.short_name as model_name, own_models.url as model_url, own_manufactures.short_name as manuf_name, own_manufactures.url as manuf_url FROM own_types
            INNER JOIN own_models ON own_models.tecdoc_id = own_types.tecdoc_models_id
            INNER JOIN own_manufactures ON own_models.tecdoc_manufacture_id = own_manufactures.tecdoc_id
            WHERE own_types.tecdoc_id = ".$type_id."
		";
        $results = DB::query(Database::SELECT,$query)->execute('default')->current();
        return $results;
    }

    public function get_car_info_by_model_url($mod_url, $manuf_url) //ссылка для типа авто
    {
        $query = "
            SELECT own_models.short_name as model_name, own_models.url as model_url, own_manufactures.short_name as manuf_name, own_manufactures.url as manuf_url FROM own_models
            INNER JOIN own_manufactures ON own_models.tecdoc_manufacture_id = own_manufactures.tecdoc_id
            WHERE own_models.url = \"".$mod_url."\" AND own_manufactures.url = \"".$manuf_url."\"
		";
        $results = DB::query(Database::SELECT,$query)->execute('default')->current();
        return $results;
    }

    //    --------------------------------------------------
    //    Характеристики
    //    --------------------------------------------------

    public function get_criterias_by_art_id ($art_id) //ссылка для типа авто
    {
        $query = "
            SELECT DES_TEXTS.TEX_TEXT AS CRITERIA_DES_TEXT, IFNULL(DES_TEXTS2.TEX_TEXT, ACR_VALUE) AS CRITERIA_VALUE_TEXT
            FROM ARTICLE_CRITERIA
            LEFT JOIN DESIGNATIONS AS DESIGNATIONS2 ON DESIGNATIONS2.DES_ID = ACR_KV_DES_ID
            LEFT JOIN DES_TEXTS AS DES_TEXTS2 ON DES_TEXTS2.TEX_ID = DESIGNATIONS2.DES_TEX_ID
            LEFT JOIN CRITERIA ON CRI_ID = ACR_CRI_ID 
            LEFT JOIN DESIGNATIONS ON DESIGNATIONS.DES_ID = CRI_DES_ID
            LEFT JOIN DES_TEXTS ON DES_TEXTS.TEX_ID = DESIGNATIONS.DES_TEX_ID
            WHERE	ACR_ART_ID = ".$art_id." AND (DESIGNATIONS.DES_LNG_ID IS NULL OR DESIGNATIONS.DES_LNG_ID = 16) AND (DESIGNATIONS2.DES_LNG_ID IS NULL OR DESIGNATIONS2.DES_LNG_ID = 16);
		";
        $results = DB::query(Database::SELECT,$query)->execute('tecdoc_new')->as_array();
        return $results;
    }

    //    --------------------------------------------------
    //    Применяемость
    //    --------------------------------------------------
    public function get_cars_by_art_id ($art_id) //ссылка для типа авто
    {
//        echo $art_id; exit();
        $query = "
            SELECT DISTINCT MFA_BRAND,	DES_TEXTS7.TEX_TEXT AS MOD_CDS_TEXT,	DES_TEXTS.TEX_TEXT AS TYP_CDS_TEXT, manuf.url as manuf_url, mods.url as mod_url, tps.url as type_url
            FROM LINK_ART
            INNER JOIN LINK_LA_TYP ON LAT_LA_ID = LA_ID
            INNER JOIN TYPES ON TYP_ID = LAT_TYP_ID
            INNER JOIN COUNTRY_DESIGNATIONS ON COUNTRY_DESIGNATIONS.CDS_ID = TYP_CDS_ID
            INNER JOIN DES_TEXTS ON DES_TEXTS.TEX_ID = COUNTRY_DESIGNATIONS.CDS_TEX_ID
            INNER JOIN MODELS ON MOD_ID = TYP_MOD_ID
            INNER JOIN MANUFACTURERS ON MFA_ID = MOD_MFA_ID
            INNER JOIN own_manufactures as manuf ON MFA_ID = manuf.tecdoc_id
            INNER JOIN own_models as mods ON MOD_ID = mods.tecdoc_id
            INNER JOIN own_types as tps ON TYP_ID = tps.tecdoc_id
            INNER JOIN COUNTRY_DESIGNATIONS AS COUNTRY_DESIGNATIONS2 ON COUNTRY_DESIGNATIONS2.CDS_ID = MOD_CDS_ID
            INNER JOIN DES_TEXTS AS DES_TEXTS7 ON DES_TEXTS7.TEX_ID = COUNTRY_DESIGNATIONS2.CDS_TEX_ID
            LEFT JOIN DESIGNATIONS ON DESIGNATIONS.DES_ID = TYP_KV_ENGINE_DES_ID
            LEFT JOIN DES_TEXTS AS DES_TEXTS2 ON DES_TEXTS2.TEX_ID = DESIGNATIONS.DES_TEX_ID
            LEFT JOIN DESIGNATIONS AS DESIGNATIONS2 ON DESIGNATIONS2.DES_ID = TYP_KV_FUEL_DES_ID
            LEFT JOIN DES_TEXTS AS DES_TEXTS3 ON DES_TEXTS3.TEX_ID = DESIGNATIONS2.DES_TEX_ID
            LEFT JOIN LINK_TYP_ENG ON LTE_TYP_ID = TYP_ID
            LEFT JOIN ENGINES ON ENG_ID = LTE_ENG_ID
            LEFT JOIN DESIGNATIONS AS DESIGNATIONS3 ON DESIGNATIONS3.DES_ID = TYP_KV_BODY_DES_ID
            LEFT JOIN DES_TEXTS AS DES_TEXTS4 ON DES_TEXTS4.TEX_ID = DESIGNATIONS3.DES_TEX_ID
            LEFT JOIN DESIGNATIONS AS DESIGNATIONS4 ON DESIGNATIONS4.DES_ID = TYP_KV_MODEL_DES_ID
            LEFT JOIN DES_TEXTS AS DES_TEXTS5 ON DES_TEXTS5.TEX_ID = DESIGNATIONS4.DES_TEX_ID
            LEFT JOIN DESIGNATIONS AS DESIGNATIONS5 ON DESIGNATIONS5.DES_ID = TYP_KV_AXLE_DES_ID
            LEFT JOIN DES_TEXTS AS DES_TEXTS6 ON DES_TEXTS6.TEX_ID = DESIGNATIONS5.DES_TEX_ID
            WHERE	LA_ART_ID = ".$art_id." AND	COUNTRY_DESIGNATIONS.CDS_LNG_ID = 16 AND	COUNTRY_DESIGNATIONS2.CDS_LNG_ID = 16 AND
            (DESIGNATIONS.DES_LNG_ID IS NULL OR DESIGNATIONS.DES_LNG_ID = 16) AND
            (DESIGNATIONS2.DES_LNG_ID IS NULL OR DESIGNATIONS2.DES_LNG_ID = 16) AND
            (DESIGNATIONS3.DES_LNG_ID IS NULL OR DESIGNATIONS3.DES_LNG_ID = 16) AND
            (DESIGNATIONS4.DES_LNG_ID IS NULL OR DESIGNATIONS4.DES_LNG_ID = 16) AND
            (DESIGNATIONS5.DES_LNG_ID IS NULL OR DESIGNATIONS5.DES_LNG_ID = 16)
            ORDER BY	MFA_BRAND,	MOD_CDS_TEXT,	TYP_CDS_TEXT,	TYP_PCON_START,	TYP_CCM
		";
        $results = DB::query(Database::SELECT,$query)->execute('tecdoc_new')->as_array();
        return $results;
    }
}
