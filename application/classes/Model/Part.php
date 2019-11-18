<?php defined('SYSPATH') or die('No direct script access.');
 
class Model_Part extends ORM {
    protected $_table_name = 'parts';
    protected $_db_group = 'tecdoc_new';

	
	protected $_has_many = array(
		'priceitems'  => array(
			'model'       => 'Priceitem',
			'foreign_key' => 'part_id',
		),
    );
	public $brand_factory = null;
    private $tecdoc = NULL;

    /**
     * @param $article_long
     * @param $brand_long
     * @param string $name
     * @param bool $operation_id
     * @param bool $create_brand
     * @param bool $create_part
     * @return bool|ORM|string
     */
    public function get_article($article_long, $brand_long, $name = "", $operation_id = false, $create_brand = false, $create_part = false) {
		$trim_charset = " \t\n\r\0.'\"(),";

		if($this->brand_factory == null) $this->brand_factory = ORM::factory('Brand');

		$brand_long = trim($brand_long, $trim_charset);

		if(!empty($brand_long)) {
			$brand_long = trim($brand_long, $trim_charset);
			$brand_instance = $this->brand_factory->get_brand($brand_long, $operation_id, 0, $create_brand);


            if ($brand_instance == 'bad_brand') {
                return 'bad_brand';
            }

			if($brand_instance->dont_upload != 1) {
				$brand = $brand_instance->brand;
			} else {
				return false;
			}
		} else {
			return false;
		}

		$article_long = trim($article_long, $trim_charset);
		$article_long = $brand_instance->apply_rules($article_long);
		$article = Article::get_short_article($article_long);

		if(empty($article)) return false;

		$part = ORM::factory('Part')->where('article', '=', $article)->and_where('brand', '=', $brand)->find();
		if(empty($part->id)) {
            return 'bad_article';
		}

		return $part;
	}

	public function get_part($article_long, $brand_long, $name = "", $article = null, $brand = null) {
		$trim_charset = " \t\n\r\0.'\"(),";
        if($this->tecdoc == NULL) {
            $this->tecdoc = Model::factory('Tecdoc');
        }

		$brand_long = trim($brand_long, $trim_charset);
		$article_long = trim($article_long, $trim_charset);

		if(empty($brand)) $brand = Article::get_short_article($brand_long);
		if(empty($article)) $article = Article::get_short_article($article_long);

		$part = ORM::factory('Part')->where('article', '=', $article)->and_where('brand', '=', $brand)->find();
		if(empty($part->id)) {
			$part = ORM::factory('Part');

			$tecdoc_articles = $this->tecdoc->get_articles($article, $brand);
			if($tecdoc_articles) {
				$article_long = $tecdoc_articles[0]['article_nr'];
				$name = $tecdoc_articles[0]['description'];

				$part->set('tecdoc_id', $tecdoc_articles[0]['id']);
			}
			$part->set('article', $article);
			$part->set('brand', $brand);
			$part->set('article_long', $article_long);
			$part->set('brand_long', $brand_long);
			$part->set('name', $name);
		}

		return $part;
	}

	public function get_brand() {
		return ucfirst(strtolower(self::toASCII($this->brand_long)));
	}

	private static function ru2Lat($string)
	{
		$table = array(
					'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D',
					'Е' => 'E', 'Ё' => 'YO', 'Ж' => 'ZH', 'З' => 'Z', 'И' => 'I',
					'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N',
					'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T',
					'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C', 'Ч' => 'CH',
					'Ш' => 'SH', 'Щ' => 'SCH', 'Ь' => '', 'Ы' => 'Y', 'Ъ' => '',
					'Э' => 'E', 'Ю' => 'YU', 'Я' => 'YA',

					'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
					'е' => 'e', 'ё' => 'yo', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
					'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
					'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
					'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch',
					'ш' => 'sh', 'щ' => 'sch', 'ь' => '', 'ы' => 'y', 'ъ' => '',
					'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
		);

		$output = str_replace(
			array_keys($table),
			array_values($table),$string
		);

		return $output;
	}

	private static function toASCII( $str )
    {
		try {
			setlocale(LC_ALL, 'en_US.UTF8');
            $str = iconv('UTF-8', 'UTF-8//TRANSLIT', self::ru2Lat($str));
			$str = iconv("UTF-8", "ascii//IGNORE", $str);
			return iconv("ascii", "ascii//TRANSLIT", $str);
		} catch(Exception $e) {
			return $str;
		}
    }
}
