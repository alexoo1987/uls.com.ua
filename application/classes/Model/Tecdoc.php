<?php defined('SYSPATH') or die('No direct script access.');
 
Class Model_Tecdoc extends Model
{
	public function get_crosses($article, $brand)
	{
		// $brand = strtolower($brand);
		$article = strtolower($article);
		$article_upper = strtoupper($article);
		// $query = DB::select()->from('tof_articles_lookup');
		// $query = $query->join('tof_suppliers', 'LEFT')->on('tof_suppliers.id', '=', 'tof_articles_lookup.brand_id');
		// $query = $query->where('tof_articles_lookup.search', '=', $article);
		// $query = $query->and_where('tof_suppliers.brand_short', '=', $brand);

		// $result = $query->execute('tecdoc')->as_array();

		// $count = 0;
		// $query = DB::select()->from('tof_articles');
		// $query = $query->join('tof_suppliers', 'LEFT')->on('tof_suppliers.id', '=', 'tof_articles.supplier_id');
		// foreach($result as $article) {
			// if($count == 0) $query = $query->where('tof_articles.id', '=', $article['article_id']);
			// else $query = $query->or_where('tof_articles.id', '=', $article['article_id']);
			// $count++;
		// }
		$brand_query = "";
		$exclude_brand_query = "";
		$exclude_sup_query = "";
		if(!empty($brand) && !is_array($brand)) {
			$brand = array($brand);
		}
		if(!empty($brand) && count($brand) > 0) {
			$brand = array_map('strtolower', $brand);
			$brands_str = "('".implode("','",$brand)."')";
			$brand_query = " AND (tof_articles_lookup.article_type IN (3, 4) AND tof_brands.brand_short IN ".$brands_str." OR tof_suppliers.brand_short IN ".$brands_str.")";

			$exclude_brand_query = "IF(tof_articles_lookup2.article_type IN (2, 3),
										IF(tof_articles_lookup2.search !='".$article_upper."' OR 
											IF(tof_articles_lookup2.article_type = 3,
												IF(tof_brands2.brand_short NOT IN ".$brands_str.", 1, 0),
												IF(tof_suppliers2.brand_short NOT IN ".$brands_str.", 1, 0)
											) = 1, 1, 0),
										IF(tof_articles2.art != '".$article."' OR tof_suppliers2.brand_short NOT IN ".$brands_str.", 1, 0)
									) = 1";
		} else {
			$exclude_brand_query = "IF(tof_articles_lookup2.article_type IN (2, 3),
										IF(tof_articles_lookup2.search !='".$article_upper."', 1, 0),
										IF(tof_articles2.art != '".$article."', 1, 0)
									) = 1";
		}

		$query = "
			SELECT DISTINCT
			IF (tof_articles_lookup2.article_type = 3, tof_brands2.brand_short, tof_suppliers2.brand_short) AS brand_short,
			IF (tof_articles_lookup2.article_type IN (2, 3), tof_articles_lookup2.search, tof_articles2.art) AS art,
			tof_articles_lookup2.article_type
			FROM tof_articles_lookup
			LEFT JOIN tof_brands ON tof_brands.id = tof_articles_lookup.brand_id
			INNER JOIN tof_articles ON tof_articles.id = tof_articles_lookup.article_id
			INNER JOIN tof_suppliers ON tof_suppliers.id = tof_articles.supplier_id
			INNER JOIN tof_articles_lookup AS tof_articles_lookup2 ON tof_articles_lookup2.article_id = tof_articles_lookup.article_id
			LEFT JOIN tof_brands AS tof_brands2 ON tof_brands2.id = tof_articles_lookup2.brand_id
			INNER JOIN tof_articles AS tof_articles2 ON tof_articles2.id = tof_articles_lookup2.article_id
			INNER JOIN tof_suppliers AS tof_suppliers2 ON tof_suppliers2.id = tof_articles2.supplier_id
			WHERE
			tof_articles_lookup.search = '".$article_upper."'".$brand_query." AND
			(tof_articles_lookup.article_type, tof_articles_lookup2.article_type) IN ((1, 1), (1, 2), (1, 3),(2, 1), (2, 2), (2, 3),(3, 1), (3, 2), (3, 3), (4, 1)) AND
			".$exclude_brand_query."
			ORDER BY brand_short, art;
		";


		$result = DB::query(Database::SELECT,$query)->execute('tecdoc')->as_array();
		return $result;
	}

	public function check_crosses($article, $brand, $article_to, $brand_to)
	{

		$result = $this->get_crosses($article, array($brand));

		foreach($result as $cross) {
			if($brand_to == $cross['brand_short'] and $article_to == $cross['art']) return true;
		}

		return false;
	}

	public function get_brand($brand)
	{
		$brand = strtolower($brand);
		$query = DB::select()->from('tof_suppliers')->where('brand_short', '=', $brand);
		$result = $query->execute('tecdoc')->as_array();

		if(count($result) > 0) {
			return $result[0];
		} else {
			return false;
		}
	}

	public function get_articles($article, $brand = false) {
		$brand = strtolower($brand);
		$article = strtolower($article);
		$query = DB::select('tof_suppliers.*')->select('tof_articles.*')->from('tof_articles');
		$query = $query->join('tof_suppliers', 'LEFT')->on('tof_suppliers.id', '=', 'tof_articles.supplier_id');
		$query = $query->where('tof_articles.art', '=', $article);

		if($brand) {
			$query = $query->and_where('tof_suppliers.brand_short', '=', $brand);
		}

		$result = $query->execute('tecdoc')->as_array();
		if(count($result) > 0) {
			return $result;
		} else {
			return false;
		}
	}

	public function get_manufacturers($slug = false, $id = false, $q = false, $active = NULL) {
		$query = DB::select()->from('tof_manufacturers');
		$query = $query->where('passenger_car', '=', '1');

		if($slug) {
			$query = $query->and_where('slug', '=', $slug);
		}

		if($id) {
			$query = $query->and_where('id', '=', $id);
		}

		if($q) {
			$query = $query->and_where('brand', 'LIKE', $q."%");
		}

		if($active !== NULL) {
			$query = $query->and_where('active', '=', ($active ? 1 : 0));
		}

		$query = $query->order_by('brand');

		$result = $query->execute('tecdoc')->as_array();
		if(count($result) > 0) {
			return $result;
		} else {
			return false;
		}
	}

	public function update_manufacturers($data, $id) {
		$query = DB::update('tof_manufacturers');
		$query = $query->set($data);
		$query = $query->and_where('id', '=', $id);
		$query->execute('tecdoc');
	}

	public function get_cars($slug=false, $manufacturer_id = false, $q = false, $year = false, $modified = NULL) {
		$query = DB::select('tof_models.*')->distinct('tof_models.id')->from('tof_models');
		$query = $query->where('passenger_car', '=', '1');

		if($slug) {
			$query = $query->and_where('slug', '=', $slug);
		}

		if($manufacturer_id) {
			$query = $query->and_where('manufacturer_id', '=', $manufacturer_id);
		}

		if($q) {
			$query = $query->and_where('short_description', 'LIKE', $q."%");
		}

		if($year) {
			$query = $query->join('tof_types', 'LEFT')->on('tof_models.id', '=', 'tof_types.model_id');
			$query = $query->and_where('tof_types.start_date', '<=', $year."12");
			$query = $query->and_where_open()
						   ->where('tof_types.end_date', '>=', $year."01")
						   ->or_where('tof_types.end_date', 'IS', NULL)
						   ->and_where_close();
		}

		if($modified !== NULL) {
			$query = $query->and_where('modified', '=', ($modified ? 1 : 0));
		}

		$query = $query->order_by('short_description');

		$result = $query->execute('tecdoc')->as_array();
		if(count($result) > 0) {
			return $result;
		} else {
			return false;
		}
	}

	public function get_body_types($model_id=false, $year = false) {
		$query = DB::select('tof_types.body_type')->distinct('tof_types.body_type')->from('tof_types');

		if($model_id) {
			$query = $query->where('model_id', '=', $model_id);
		}

		if($year) {
			$query = $query->and_where('start_date', '<=', $year."12");
			$query = $query->and_where_open()
						   ->where('end_date', '>=', $year."01")
						   ->or_where('end_date', 'IS', NULL)
						   ->and_where_close();
		}

		$result = $query->execute('tecdoc')->as_array();
		if(count($result) > 0) {
			return $result;
		} else {
			return false;
		}
	}

	public function get_liters_fuel($model_id=false, $year = false, $body_type = false) {
		$query = DB::select('tof_types.engine_type', 'tof_types.capacity')
			->distinct('tof_types.engine_type', 'tof_types.capacity')
			->from('tof_types');

		if($model_id) {
			$query = $query->and_where('model_id', '=', $model_id);
		}

		if($body_type) {
			$query = $query->and_where('body_type', '=', $body_type);
		}

		if($year) {
			$query = $query->and_where('start_date', '<=', $year."12");
			$query = $query->and_where_open()
						   ->where('end_date', '>=', $year."01")
						   ->or_where('end_date', 'IS', NULL)
						   ->and_where_close();
		}

		$result = $query->execute('tecdoc')->as_array();
		if(count($result) > 0) {
			return $result;
		} else {
			return false;
		}
	}

	public function get_types($slug=false, $model_id=false, $q = false, $year = false, $body_type = false, $engine_type = false, $capacity = false) {
		$query = DB::select()->from('tof_types');

		if($slug) {
			$query = $query->and_where('slug', '=', $slug);
		}

		if($model_id) {
			$query = $query->and_where('model_id', '=', $model_id);
		}

		if($body_type) {
			$query = $query->and_where('body_type', '=', $body_type);
		}

		if($engine_type) {
			$query = $query->and_where('engine_type', '=', $engine_type);
		}

		if($capacity) {
			$query = $query->and_where('capacity', 'LIKE', $capacity.'%');
		}

		if($q) {
			$query = $query->and_where('description', 'LIKE', $q."%");
		}

		if($year) {
			$query = $query->and_where('start_date', '<=', $year."12");
			$query = $query->and_where_open()
						   ->where('end_date', '>=', $year."01")
						   ->or_where('end_date', 'IS', NULL)
						   ->and_where_close();
		}

		$query = $query->order_by('description');

		$result = $query->execute('tecdoc')->as_array();
		if(count($result) > 0) {
			return $result;
		} else {
			return false;
		}
	}

	public function update_car($data, $id) {
		$query = DB::update('tof_models');
		$query = $query->set($data);
		$query = $query->and_where('id', '=', $id);
		$query->execute('tecdoc');
	}

	public function update_types($data, $model_ids) {
		$query = DB::update('tof_types');
		$query = $query->set($data);
		$query = $query->and_where('model_id', 'IN', $model_ids);
		$query->execute('tecdoc');
	}

	public function delete_cars_where($ids) {
		$query = DB::delete('tof_models');
		$query = $query->where('id', 'IN', $ids);
		$query->execute('tecdoc');
	}

	public function get_duplicated_cars() {
		$query = "SELECT manufacturer_id, short_description, COUNT(id) FROM `tof_models` WHERE `modified` = 0 GROUP BY short_description HAVING COUNT(id) > 1";

		$result = DB::query(Database::SELECT,$query)->execute('tecdoc')->as_array();
		return $result;
	}

	public function get_type($type_id) {
		$query = DB::select(array('tof_manufacturers.brand', 'brand'), array('tof_models.description', 'model'),
						    array('tof_manufacturers.slug', 'manuf_slug'), array('tof_models.slug', 'model_slug'), 'tof_types.*')->from('tof_types');
		$query = $query->join('tof_models', 'LEFT')->on('tof_models.id', '=', 'tof_types.model_id');
		$query = $query->join('tof_manufacturers', 'LEFT')->on('tof_manufacturers.id', '=', 'tof_models.manufacturer_id');
		$query = $query->where('tof_types.id', '=', $type_id);

		$result = $query->execute('tecdoc')->as_array();
		if(count($result) > 0) {
			$type = $result[0];
			$type['full_slug'] = $type['manuf_slug'].'/'.$type['model_slug'].'/'.$type['slug'];
			return $type;
		} else {
			return false;
		}
	}

	public function get_tree($typ_id) {
		$query = DB::select('tof_search_tree.*')->from('tof_link_type_article');

		$query = $query->join('tof_link_generic_article_search_tree')
			->on('tof_link_generic_article_search_tree.generic_article_id', '=', 'tof_link_type_article.generic_article_id');

		$query = $query->join('tof_search_tree')
			->on('tof_search_tree.id', '=', 'tof_link_generic_article_search_tree.search_tree_id');

		$query = $query->where('tof_link_type_article.type_id', '=', $typ_id);
		$query = $query->and_where('tof_search_tree.type', '=', '1');

		$query = $query->order_by('tof_search_tree.sort');

		$query = $query->group_by('tof_search_tree.id');

		$result = $query->execute('tecdoc')->as_array();
		if(count($result) > 0) {
			return $result;
		} else {
			return false;
		}
	}

	public function get_tree_by_name($name) {
		$query = DB::select('id')->from('tof_search_tree');

		$query = $query->where('tof_search_tree.text', 'LIKE', $name);

		$query = $query->order_by('tof_search_tree.text');

		$result = $query->execute('tecdoc')->as_array();
		if(count($result) > 0) {
			return $result;
		} else {
			return false;
		}
	}

	public function get_tree_by_name_limited($name, $ids=array()) {
		$query = DB::select()->from('tof_search_tree');

		if($name)
			$query = $query->where('tof_search_tree.text', 'LIKE', "%".$name."%");
		if($ids)
			$query = $query->where('tof_search_tree.id', 'IN', $ids);

		$query = $query->order_by('tof_search_tree.text');
		// $query = $query->limit(30);

		$result = $query->execute('tecdoc')->as_array();
		if(count($result) > 0) {
			return $result;
		} else {
			return false;
		}
	}

	public function get_generic_articles($name = false) {
		$query = DB::select()->from('tof_generic_articles');
		if($name)
			$query = $query->where('tof_generic_articles.name', 'LIKE', $name."%");

		$query = $query->order_by('tof_generic_articles.name');

		$result = $query->execute('tecdoc')->as_array();
		if(count($result) > 0) {
			return $result;
		} else {
			return false;
		}
	}

	public function get_ga_model_or_type($typ_ids=false, $model_id=false) {

		$query = "
			SELECT tof_link_type_article.generic_article_id
			FROM tof_link_type_article
		";

		if($model_id) {
			$query .= "
				INNER JOIN tof_types ON tof_types.id = tof_link_type_article.type_id AND tof_types.model_id = ".$model_id;
		}
		if(!$model_id && $typ_ids) {
			$query .= "
				WHERE
			";
			if(!is_array($typ_ids)) {
				$typ_ids = array($typ_ids);
			}
			$typ_ids_str = implode(', ', $typ_ids);

			$query .= "
				tof_link_type_article.type_id IN (" . $typ_ids_str . ")
			";
		}

		$query .= "
			GROUP BY tof_link_type_article.generic_article_id
		";

		if(!empty($_GET['show_query'])) echo $query;
		// return false;
		$result = DB::query(Database::SELECT,$query)->execute(/*'tecdoc'*/)->as_array();

		$ids = array();
		if($result) {
			foreach ($result as $row) {
				$ids[] = $row['generic_article_id'];
			}
		}
		return $ids;
	}



	public function get_parts_ids($typ_ids, $model_id=false, $tree_ids, $limit = NULL, $offset = NULL, $only_in_stock = NULL, $filter = NULL) {
		$query = "
			SELECT tof_articles.id as article_id
			FROM tof_articles
			INNER JOIN tof_link_article ON tof_link_article.article_id = tof_articles.id
			LEFT JOIN parts_old ON tof_articles.id = parts_old.tecdoc_id
			LEFT JOIN priceitems_old ON parts_old.id = priceitems_old.part_id
			LEFT JOIN suppliers ON suppliers.id = priceitems_old.supplier_id
		";

		if($model_id || $typ_ids) {
			$query .= "
				INNER JOIN tof_link_type_article ON tof_link_article.id = tof_link_type_article.link_article_id";
		}
		if($model_id) {
			$query .= "
				INNER JOIN tof_types ON tof_types.id = tof_link_type_article.type_id AND tof_types.model_id = ".$model_id;
		}

		if(!empty($filter['location'])) {
			$query .= "
			LEFT JOIN tof_article_criteria ON tof_articles.id = tof_article_criteria.article_id
			";
		}

		if(!$model_id && $typ_ids || $tree_ids)
			$query .= "
				WHERE
			";

		if(!$model_id && $typ_ids) {
			if(!is_array($typ_ids)) {
				$typ_ids = array($typ_ids);
			}
			$typ_ids_str = implode(', ', $typ_ids);

			$query .= "
				tof_link_type_article.type_id IN (" . $typ_ids_str . ")
			";
		}

		if(!$model_id && $typ_ids && $tree_ids)
			$query .= "
				AND
			";
		if($tree_ids) {
			if(!is_array($tree_ids)) {
				$tree_ids = array($tree_ids);
			}
			$tree_ids_str = implode(', ', $tree_ids);

			$query .= "
				tof_link_article.generic_article_id IN (" . $tree_ids_str . ")
			";
		}

		$query .= " AND suppliers.dont_show = 0";
		if ($only_in_stock) $query .= " AND priceitems_old.amount > 0";

		if (!empty($filter['brand'])){
			$brands = '\'' . implode( '\', \'', $filter['brand'] ) . '\'';
			$query .= " AND parts_old.brand_long IN (". $brands .")";
		}

		if(!empty($filter['location'])) {
			$values = '\'' . implode( '\', \'', $filter['location'] ) . '\'';
			$query .= "
			 AND tof_article_criteria.criteria_id = 100
			AND tof_article_criteria.`value` IN (". $values .")";
		}

		$query .= "
			GROUP BY tof_articles.id
		";

		$query .= "
			ORDER BY priceitems_old.id is null,
					 suppliers.id is null
		";

		if($limit) {
			$offset = !empty($offset) ? $offset : 0;
			$query .= "
				LIMIT " . $offset . ", " . $limit . "
			";
		}
		if(!empty($_GET['show_query'])) echo $query;

		$result = DB::query(Database::SELECT,$query)->execute(/*'tecdoc'*/)->as_array();
		return $result;
	}

	public function get_parts($article_ids) {
		$part_ids = array();
		foreach ($article_ids as $row) {
			$part_ids[] = $row['article_id'];
		}

		$query = "
			SELECT DISTINCT tof_suppliers.*, tof_articles.*, parts_old.id as part_id, parts_old.images as part_img
			FROM tof_articles
			INNER JOIN tof_suppliers ON tof_suppliers.id = tof_articles.supplier_id
			LEFT JOIN parts_old ON tof_articles.id = parts_old.tecdoc_id
			LEFT JOIN priceitems_old ON parts_old.id = priceitems_old.part_id
			LEFT JOIN suppliers ON suppliers.id = priceitems_old.supplier_id AND suppliers.dont_show = 0
		";
		if($part_ids) {
			if(!is_array($part_ids)) {
				$part_ids = array($part_ids);
			}
			$part_ids_str = implode(', ', $part_ids);

			$query .= "
				WHERE tof_articles.id IN (" . $part_ids_str . ")
			";
		}

//		$query .= "
//			ORDER BY priceitems.id is null,
//					 suppliers.id is null
//		";

		if(!empty($_GET['show_query'])) echo $query;

		$result = DB::query(Database::SELECT,$query)->execute(/*'tecdoc'*/)->as_array();

		if(count($result) > 0) {
			return $result;
		} else {
			return false;
		}
	}

	public function get_part($part_id) {
		$query = DB::select('tof_suppliers.*')->select('tof_articles.*')->from('tof_articles');

		$query = $query->join('tof_suppliers')
			->on('tof_suppliers.id', '=', 'tof_articles.supplier_id');

		$query = $query->where('tof_articles.id', '=', $part_id);


		$result = $query->execute('tecdoc')->as_array();
		if(count($result) > 0) {
			return $result[0];
		} else {
			return false;
		}
	}

	public function get_types_by_art_id($article_id) {
		$query = DB::select(array('tof_manufacturers.brand', 'brand'), array('tof_models.short_description', 'model'),
						    array('tof_manufacturers.slug', 'manuf_slug'), array('tof_models.slug', 'model_slug'), 'tof_types.*')->from('tof_link_article');

		$query = $query->join('tof_link_type_article')
			->on('tof_link_article.id', '=', 'tof_link_type_article.link_article_id');

		$query = $query->join('tof_types')
			->on('tof_types.id', '=', 'tof_link_type_article.type_id');
		$query = $query->join('tof_models', 'INNER')->on('tof_models.id', '=', 'tof_types.model_id');
		$query = $query->join('tof_manufacturers', 'INNER')->on('tof_manufacturers.id', '=', 'tof_models.manufacturer_id');

		$query = $query->where('tof_link_article.article_id', '=', $article_id);

		$query = $query->order_by('tof_types.description');

		$query = $query->group_by('tof_types.id');

		$result = $query->execute('tecdoc')->as_array();
		if(count($result) > 0) {
			return $result;
		} else {
			return false;
		}
	}

	public function get_criterias($article_id) {
		$query = DB::select()->from('tof_article_criteria');

		$query = $query->join('tof_criteria', 'LEFT')
			->on('tof_criteria.id', '=', 'tof_article_criteria.criteria_id');


		$query = $query->where('tof_article_criteria.article_id', '=', $article_id);


		$result = $query->execute('tecdoc')->as_array();
		if(count($result) > 0) {
			return $result;
		} else {
			return false;
		}
	}

	public function get_graphics($article_id) {
		$query = DB::select()->from('tof_graphics');

		$query = $query->where('article_id', '=', $article_id);


		$result = $query->execute('tecdoc')->as_array();
		if(count($result) > 0) {
			return $result;
		} else {
			return false;
		}
	}

	public function get_images($article_ids) {
		$query = DB::select()->from('tof_graphics');

		$query = $query->where('article_id', 'IN', $article_ids);

		$result = $query->execute('tecdoc')->as_array();
		if(count($result) > 0) {
			return $result;
		} else {
			return array();
		}
	}

	public function update_models_names() {

		$selectquery = DB::select()->from('tof_models');
		$selectquery = $selectquery->order_by('description');
		$result = $selectquery->execute('tecdoc')->as_array();

		foreach ($result as $row) {
			$short_description = trim(preg_replace('/\(.+\)/', '', $row['description']));

			$query = DB::update('tof_models');
			$query = $query->set(array('short_description' => $short_description));
			$query = $query->and_where('id', '=', $row['id']);
			$query->execute('tecdoc');
		}
	}

	/**
	 * Get all brands by articles
	 * @param $article_ids array articles
	 * @return bool
	 */
	public function get_brands($article_ids)
	{
		if (empty($article_ids)) return false;

		$articles_string = array();
		foreach ($article_ids AS $key => $article_id){
			$articles_string[] = $article_id['article_id'];
		}
		$articles_string = implode(',', $articles_string);

		$query = "SELECT brand_long AS `name` FROM `parts_old` WHERE tecdoc_id IN (". $articles_string .") GROUP BY brand_long ORDER BY brand_long";

		$result = DB::query(Database::SELECT,$query)->execute(/*'tecdoc'*/)->as_array();

		$array = array();
		foreach ($result AS $key => $brand){
			$array[] = $brand['name'];
		}

		return $array;

	}

	/**
	 * Get distinct criteria values by articles and criteria_id
	 * @param $article_ids array - articles
	 * @param $criteria_id
	 * @return bool
	 */
	public function get_filters($article_ids, $criteria_id)
	{
		if (empty($article_ids) OR !$criteria_id) return false;

		$articles_string = array();
		foreach ($article_ids AS $key => $article_id){
			$articles_string[] = $article_id['article_id'];
		}
		$articles_string = implode(',', $articles_string);

		$query = "
		SELECT DISTINCT
			`value`
		FROM
			tof_article_criteria
		WHERE
			criteria_id = ". $criteria_id ."
		AND article_id IN (". $articles_string .")
		ORDER BY `value`
		";

		$result = DB::query(Database::SELECT,$query)->execute(/*'tecdoc'*/)->as_array();

		$array = array();
		foreach ($result AS $key => $location){
			$array[] = $location['value'];
		}
		return $array;

	}
}
