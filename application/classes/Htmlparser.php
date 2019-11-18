<?php defined('SYSPATH') or die('No direct script access.');

class Htmlparser {
	public static function http($url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; ru; rv:1.9.2.13) Gecko/20101203 Firefox/3.6.13');
		$contents = curl_exec ($ch);
		if ($contents) {
			$status = "success";
		} else {
			$status = "failed:".curl_error($ch);
		}
		curl_close ($ch);
		return $contents;
	}
	
	public static function httpFetchFile($file, $url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		$fp = fopen($file, 'w');
		curl_setopt($ch, CURLOPT_FILE, $fp);
		$ret = curl_exec ($ch);
		if ($ret) {
			$status = "success";
		} else {
			$status = "failed:".curl_error($ch);
		}
		curl_close ($ch);
		return $ret;
	}
	
	public static function htmlToXml($html) {
		$utf = $html;//iconv("cp1251", "utf-8", $html);
		$utfhtml = $utf;//mb_convert_encoding($utf, 'HTML-ENTITIES', "UTF-8"); 
		$doc = new DOMDocument('1.0', 'UTF-8');
		//disable warning
		$level = error_reporting(E_ERROR);
		libxml_use_internal_errors(true);
		$doc->loadHTML($utfhtml);
		libxml_clear_errors();
		//restore error level
		$level = error_reporting($level);
		try {
			$simplexml = @simplexml_import_dom($doc);
			return $simplexml;
		}
		catch (Exception $e) {
			return false;
		}
	}
	

	public static function urlParts($url) {
		$parts = array('path' => $url);
		if (preg_match('@(^[^:]*://[^/]*/?)(.*$)@', $url, $match)) {
			$parts['base'] = $match[1];
			$parts['path'] = $match[2];
		}
		return $parts;
	}

	public static function fullUrl($base, $url) {
		$fullurl = $url;
		$base_parts = self::urlParts($base);
		$url_parts = self::urlParts($url);
		$base = $base_parts['base'];
		$path = $url_parts['path'];
		if ($base[strlen($base)-1] != '/') {
			$base .= '/';
		}
		
		return $base . ltrim($path, "/");
	}


	private static function normalize_filename($filename) {

		$chars = array('\\','/','=','.','+','*','?','[','^',']','(','$',']','&','<','>');
   		$filename = str_replace($chars, "_", $filename);

   		return $filename;
	}
	
	public static function transliterate($string) {
		$converter = array(
			'а' => 'a',   'б' => 'b',   'в' => 'v',
			'г' => 'g',   'д' => 'd',   'е' => 'e',
			'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
			'и' => 'i',   'й' => 'y',   'к' => 'k',
			'л' => 'l',   'м' => 'm',   'н' => 'n',
			'о' => 'o',   'п' => 'p',   'р' => 'r',
			'с' => 's',   'т' => 't',   'у' => 'u',
			'ф' => 'f',   'х' => 'h',   'ц' => 'c',
			'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
			'ь' => '',  'ы' => 'y',   'ъ' => '',
			'э' => 'e',   'ю' => 'yu',  'я' => 'ya',
			'і' => 'i',   'ї' => 'i',  'є' => 'yе',

			'А' => 'A',   'Б' => 'B',   'В' => 'V',
			'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
			'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
			'И' => 'I',   'Й' => 'Y',   'К' => 'K',
			'Л' => 'L',   'М' => 'M',   'Н' => 'N',
			'О' => 'O',   'П' => 'P',   'Р' => 'R',
			'С' => 'S',   'Т' => 'T',   'У' => 'U',
			'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
			'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
			'Ь' => '',    'Ы' => 'Y',   'Ъ' => '',
			'І' => 'i',   'Ї' => 'i',  'Є' => 'Ye',
		);

		$str = strtr($string, $converter);

		// в нижний регистр
		$str = strtolower($str);

		// заменям все ненужное нам на "-"
		$str = preg_replace('~[^-a-z0-9_]+~', '-', $str);
		// удаляем начальные и конечные '-'
		$str = trim($str, "-");
		return $str;
	}
	
	private function is_url($path) {
    	return preg_match('/^(http|https|ftp):\/\//isS', $path);
	}
}