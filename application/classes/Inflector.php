<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Inflector helper class.
 *
 * @package    Kohana
 * @category   Helpers
 */
class Inflector extends Kohana_Inflector {

    /**
     * Default map of accented and special characters to ASCII characters
     *
     * @var array
     */
    protected static $_transliteration = array(
        '/ä|æ|ǽ/' => 'ae',
        '/ö|œ/' => 'oe',
        '/ü/' => 'ue',
        '/Ä/' => 'Ae',
        '/Ü/' => 'Ue',
        '/Ö/' => 'Oe',
        '/À|Á|Â|Ã|Ä|Å|Ǻ|Ā|Ă|Ą|Ǎ|А/' => 'A',
        '/à|á|â|ã|å|ǻ|ā|ă|ą|ǎ|ª|а/' => 'a',
        '/Б/' => 'B',
        '/б/' => 'b',
        '/Ç|Ć|Ĉ|Ċ|Č/' => 'C',
        '/ç|ć|ĉ|ċ|č/' => 'c',
        '/Ð|Ď|Đ|Д/' => 'D',
        '/ð|ď|đ|д/' => 'd',
        '/È|É|Ê|Ë|Ē|Ĕ|Ė|Ę|Ě|Е|Э|Є/' => 'E',
        '/è|é|ê|ë|ē|ĕ|ė|ę|ě|е|э|є/' => 'e',
        '/Ф/' => 'F',
        '/ƒ|ф/' => 'f',
        '/Ĝ|Ğ|Ġ|Ģ|Г|Ґ/' => 'G',
        '/ĝ|ğ|ġ|ģ|г|ґ/' => 'g',
        '/Ĥ|Ħ/' => 'H',
        '/ĥ|ħ/' => 'h',
        '/Ì|Í|Î|Ï|Ĩ|Ī|Ĭ|Ǐ|Į|İ|І|И/' => 'I',
        '/ì|í|î|ï|ĩ|ī|ĭ|ǐ|į|ı|і|и/' => 'i',
        '/Ĵ|Й/' => 'J',
        '/ĵ|й/' => 'j',
        '/Ķ|К/' => 'K',
        '/ķ|к/' => 'k',
        '/Ĺ|Ļ|Ľ|Ŀ|Ł|Л/' => 'L',
        '/ĺ|ļ|ľ|ŀ|ł|л/' => 'l',
        '/М/' => 'M',
        '/м/' => 'm',
        '/Ñ|Ń|Ņ|Ň|Н/' => 'N',
        '/ñ|ń|ņ|ň|ŉ|н/' => 'n',
        '/Ò|Ó|Ô|Õ|Ō|Ŏ|Ǒ|Ő|Ơ|Ø|Ǿ|О/' => 'O',
        '/ò|ó|ô|õ|ō|ŏ|ǒ|ő|ơ|ø|ǿ|º|о/' => 'o',
        '/П/' => 'P',
        '/п/' => 'p',
        '/Ŕ|Ŗ|Ř|Р/' => 'R',
        '/ŕ|ŗ|ř|р/' => 'r',
        '/Ś|Ŝ|Ş|Š|С/' => 'S',
        '/ś|ŝ|ş|š|ſ|с/' => 's',
        '/Ţ|Ť|Ŧ|Т/' => 'T',
        '/ţ|ť|ŧ|т/' => 't',
        '/Ù|Ú|Û|Ũ|Ū|Ŭ|Ů|Ű|Ų|Ư|Ǔ|Ǖ|Ǘ|Ǚ|Ǜ|У/' => 'U',
        '/ù|ú|û|ũ|ū|ŭ|ů|ű|ų|ư|ǔ|ǖ|ǘ|ǚ|ǜ|у/' => 'u',
        '/В/' => 'V',
        '/в/' => 'v',
        '/Ý|Ÿ|Ŷ|Ы/' => 'Y',
        '/ý|ÿ|ŷ|ы/' => 'y',
        '/Ŵ/' => 'W',
        '/ŵ/' => 'w',
        '/Ź|Ż|Ž|З/' => 'Z',
        '/ź|ż|ž|з/' => 'z',
        '/Æ|Ǽ/' => 'AE',
        '/ß/' => 'ss',
        '/Ĳ/' => 'IJ',
        '/ĳ/' => 'ij',
        '/Œ/' => 'OE',
        // Составные (русский, украинский)
        '/ё/' => 'yo', '/Ё/' => 'Yo',
        '/ж/' => 'zh', '/Ж/' => 'Zh',
        '/х/' => 'kh', '/Х/' => 'Kh',
        '/ц/' => 'ts', '/Ц/' => 'Ts',
        '/ч/' => 'ch', '/Ч/' => 'Ch',
        '/ш/' => 'sh', '/Ш/' => 'Sh',
        '/щ/' => 'shch', '/Щ/' => 'Shch',
        '/ю/' => 'yu', '/Ю/' => 'Yu',
        '/я/' => 'ya', '/Я/' => 'Ya',
        '/ї/' => 'ji', '/Ї/' => 'Ji',
        // Спецсимволы
        '/ъ|Ъ|ь|Ь/' => '', // Твердые и мягкие знаки
        '/&/' => ' and ', // Амперсанд
        '/\'/' => '', // Апостроф
    );

    /**
     * Returns a string with all spaces converted to underscores (by default), accented
     * characters converted to non-accented characters, and non word characters removed.
     *
     * @static
     * @param $string $string the string you want to slug
     * @param string  $replacement will replace keys in map
     * @param bool    $tolower все в нижний регистр
     * @return mixed
     * @link http://book.cakephp.org/2.0/en/core-utility-libraries/inflector.html#Inflector::slug
     */
    public static function slug($string, $replacement = '-', $tolower = TRUE) {
        $string = ($tolower) ? UTF8::strtolower($string) : $string;

        $quoted_replacement = preg_quote($replacement, '/');

        $merge = array(
            '/[^\s\p{Ll}\p{Lm}\p{Lo}\p{Lt}\p{Lu}\p{Nd}]/mu' => ' ',
            '/\\s+/' => $replacement,
            sprintf('/^[%s]+|[%s]+$/', $quoted_replacement, $quoted_replacement) => '',
        );

        $map = self::$_transliteration + $merge;

        return preg_replace(array_keys($map), array_values($map), $string);
    }

    /**
     * @return  string
     * Example for $expressions: array("ответ", "ответа", "ответов")
     */
    public static function plural_ru($int = NULL, $expressions=  array()) {
        IF (COUNT($expressions) < 3) {
            $expressions[2] = $expressions[1];
        };

        SETTYPE($int, 'integer');

        $result = '';

        $count = $int % 100;

        IF ($count >= 5 && $count <= 20) {
            $result .= $expressions[2];
        }
        ELSE {
            $count = $count % 10;

            IF ($count == 1) {
                $result .= $expressions[0];
            }
            ELSEIF ($count >= 2 && $count <= 4) {
                $result .= $expressions[1];
            }
            ELSE {
                $result .= $expressions[2];
            };
        };
        RETURN $result;
    }

}
