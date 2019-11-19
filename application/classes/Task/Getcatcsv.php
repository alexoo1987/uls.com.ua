<?php defined('SYSPATH') or die('No direct script access.');
class Task_Getcatcsv extends Minion_Task {
    protected function _execute(array $params)
    {
        $this->action_get_csv();
    }

    public function action_get_csv(){
        Minion_CLI::write('Start');
        $path = 'uploads/eparts_categories.csv';
        
        $f = fopen($path, 'w');
	
	$generic_articles_done = array();
        foreach (array(595,611,629,687,727,753,786,848,884) as $parent_id) {
            $category = ORM::factory('Category')->where('id', '=', $parent_id)->find();

            $categories_parent = ORM::factory('Category');
            $categories_parent = $categories_parent->where('parent_id', '=', $parent_id)->order_by('id')->find_all()->as_array();

            $parent_arr = array();
            foreach ($categories_parent as $cat) {
                $parent_arr[] = $cat->id;
            }

            $categories = ORM::factory('Category');
            $categories = $categories->where('parent_id', 'IN', $parent_arr)->order_by('id')->find_all()->as_array();

            foreach ($categories as $cat) {
                if(empty($cat->tecdoc_ids)) {
                    Minion_CLI::write($cat->name." | skip");
                    continue;
                }
                $tecdoc_ids = array();
        		foreach(explode(',', $cat->tecdoc_ids) as $tid) {
        			if(in_array($tid, $generic_articles_done)) {
        				continue;
        			}
        			$tecdoc_ids[] = $tid;
        			$generic_articles_done[] = $tid;
        		}
        		if(count($tecdoc_ids) == 0) {
        			Minion_CLI::write($cat->name." | skip");
        			continue;
        		}
                $tecdoc_ids_str = implode(', ', $tecdoc_ids);
                Minion_CLI::write($cat->name);
                $query = "
                    SELECT DISTINCT tof_link_article.article_id
                    FROM tof_link_article
                    WHERE tof_link_article.generic_article_id IN (" . $tecdoc_ids_str . ")
                ";

                $result = DB::query(Database::SELECT,$query)->execute('tecdoc')->as_array();

                $part_ids = array();
                foreach ($result as $row) {
                    $part_ids[] = $row['article_id'];
                }
                if(count($part_ids) == 0) continue;
                /*$query = DB::select('tof_suppliers.*')->select('tof_articles.*')->from('tof_articles');
                    
                $query = $query->join('tof_suppliers')
                    ->on('tof_suppliers.id', '=', 'tof_articles.supplier_id');
                
                $query = $query->where('tof_articles.id', 'IN', $part_ids);
                
                $result = $query->execute('tecdoc')->as_array();*/

                $parts = ORM::factory('Part')->where('tecdoc_id', 'IN', $part_ids)->find_all()->as_array();
                //foreach ($result as $row) {
        		$i = 0;
        		foreach($parts as $part) {
                    //$part = ORM::factory('Part')->where('tecdoc_id', '=', $row['id'])->find();
                    /*if(empty($part->id)) {
                        $link = URL::site('catalog/article/')."?id=".$row['id'];
                        $name = $row['brand']." ".$row['article_nr']." ".$row['description'];
                    } else {*/
                        $link = URL::site('katalog/article/'.$part->id);
                        $name = $part->get_brand()." ".$part->article_long." ".Article::shorten_string($part->name, 3);
                    //}

                    $fields = array($category->name, 
                                    $cat->name,
                                    $name,
                                    $link
                                    );
                                    
                    array_walk($fields, 'encodeCSV');
                    fputcsv($f, $fields, ';');
        		    $i++;
        		    if($i % 500 == 0) Minion_CLI::write($i);
                }
            }

        }

        fclose($f);

        Minion_CLI::write('<a href="'.$path.'">download</a>');
        Minion_CLI::write('End');
    }
}

function encodeCSV(&$value, $key){
    try {
        $value = iconv('UTF-8', 'Windows-1251', $value);
    } catch(Exception $e) {}
}

?>
