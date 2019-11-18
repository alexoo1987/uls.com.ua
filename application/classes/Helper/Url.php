<?php defined('SYSPATH') or die('No direct script access.');

class Helper_Url
{
    public static function createUrl($uri = null, $addParams = [], $useGet = false)
    {
        is_null($uri) && $uri = Request::detect_uri();
        $uri = trim($uri, '/');
        $uri === '' && $uri = '/';

        if ($redirectUrl = Helper_Seo::getRedirectUrl($uri)) {
            $uri = $redirectUrl;
        }

        $request = Request::factory($uri);
        $query = URL::query($addParams, $useGet);
        $url = strtolower(rtrim($request->url(), '/'));

        return $url . $query;
    }

    public static function currentUrl()
    {
        $domain = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
        return $_SERVER['REQUEST_SCHEME'] . '://' . $domain . $_SERVER['REQUEST_URI'];
    }

    public static function getPartUrl($part, $addParams = [], $useGet = false)
    {
        $partId = isset($part['part_id']) ? $part['part_id'] : $part['id'];
        $uri = Htmlparser::transliterate($part['brand'] . '-' . $part['article'] .
                '-' . substr($part['name'], 0, 50)) . '-' . $partId;
        return self::createUrl('katalog/produkt/' . $uri, $addParams, $useGet);
    }

    public static function getCanonicalUrl()
    {
        $getParams = $_GET;
        $allowedParams = [];
        $whiteListParams = Kohana::$config->load('params')->whiteListGetParams;
        foreach ($getParams as $key => $value) {
            if (!in_array($key, $whiteListParams)) continue;
            $allowedParams[$key] = $value;
        }
        return self::createUrl(null, $allowedParams);
    }

    /**
     * @param $url
     * @param $items
     * @return string
     */
    public static function replaceUriParts($url, $items)
    {
        $uriParts = explode('/', $url);
        foreach ($uriParts as $key => $val) {
            if (array_key_exists($val, $items)) {
                $uriParts[$key] = $items[$val];
            }
        }

        return $url = implode('/', $uriParts);
    }


}