<?php defined('SYSPATH') or die('No direct script access.');

class Task_Sitemap extends Minion_Task
{
    private $namespace = 'http://www.sitemaps.org/schemas/sitemap/0.9';

    private $directory = 'sitemaps/';

    private $tecdoc = null;

    private $manufacturers = [];

    private $redirectUrls  = [];

    protected function _execute(array $params)
    {
        echo date('Y-m-d H:i:s') . "_____BEGIN Synchronization\n";

        $this->redirectUrls = Kohana::$config->load('params')->redirectUrls;
        $this->tecdoc = Model::factory('NewTecdoc');

        $sitemapsUrls = $this->createSitemaps();

        $document = new SimpleXMLElement('<sitemapindex/>');
        $document->addAttribute('xmlns', $this->namespace);
        foreach ($sitemapsUrls as $sitemapUrl) {
            $sitemap = $document->addChild('sitemap');
            $sitemap->addChild('loc', $sitemapUrl);
            $sitemap->addChild('lastmod', date("Y-m-d"));
        }
        $document->saveXml('sitemap.xml');

        echo date('Y-m-d H:i:s') . "_____END\n";
    }

    private function createSitemaps()
    {
        $directory = __DIR__ . '/' . $this->directory;
        if (!file_exists($this->directory)) {
            mkdir($this->directory);
        }

        return array_merge(
            $this->updateSitemapCatalog(),
            $this->updateSitemapManufacturers(),
            $this->updateSitemapModels(),
            $this->updateSitemapPages(),
            $this->updateSitemapParts()
        );
    }

    private function updateSitemapCatalog()
    {
        $categories = Model::factory('Category')->find_all()->as_array(NULL, 'slug');
        $fileName = 'sitemap-catalog';
        $preCallback = function (&$uri, $category, &$break, &$tagsParams) {
            $uri = 'katalog/' . urlencode($category);
            $tagsParams = [
                'priority'   => 0.9,
                'changefreq' => 'daily',
            ];
            $break = isset($this->redirectUrls[$uri]);
        };

        return $this->updateSitemap($categories, $fileName, $preCallback);
    }

    private function updateSitemapManufacturers()
    {
        $this->manufacturers = $this->tecdoc->get_all_manufacture();
        $fileName = 'sitemap-auto-brands';
        $preCallback = function (&$uri, $manufacturer, &$break, &$tagsParams) {
            $uri = 'katalog/' . urlencode($manufacturer['url']);
            $tagsParams = [
                'priority'   => 0.9,
                'changefreq' => 'daily',
            ];
            $break = isset($this->redirectUrls[$uri]);
        };

        return $this->updateSitemap($this->manufacturers, $fileName, $preCallback);
    }

    private function updateSitemapModels()
    {
        $models = [];
        foreach ($this->manufacturers as $manufacturer) {
            $manufacturerId = $manufacturer['id'];
            $manufactureModels = $this->tecdoc->get_all_for_id_manufactures($manufacturerId);

            foreach ($manufactureModels as $index => $model) {
                $models[] = 'katalog/' . urlencode($manufacturer['url']) . "/" .
                    urlencode($model['url_model']);
            }
        }
        $models = array_unique($models);
        $fileName = 'sitemap-auto-models';
        $preCallback = function (&$uri, $record, &$break, &$tagsParams) {
            $uri = $record;
            $tagsParams = [
                'priority'   => 0.9,
                'changefreq' => 'daily',
            ];
            $break = isset($this->redirectUrls[$uri]);
        };

        return $this->updateSitemap($models, $fileName, $preCallback);
    }

    private function updateSitemapPages()
    {
        $pages = Model::factory('Page')
            ->where('active', '=', 1)
            ->find_all()
            ->as_array(NULL, 'syn');

        $fileName = 'sitemap_info';
        $preCallback = function (&$uri, $page, &$break, &$tagsParams) {
            $uri = $page;
            $isHomePage = (trim($uri, '/') == 'home');
            $tagsParams = [
                'priority'   => ($isHomePage) ? 1 : 0.5,
                'changefreq' => ($isHomePage) ? 'daily' : 'monthly',
            ];
            $break = !$isHomePage && isset($this->redirectUrls[$uri]);
        };

        return $this->updateSitemap($pages, $fileName, $preCallback);
    }

    private function updateSitemapParts()
    {
        $fileName = 'sitemap-produkt';

        // Remove old files
        array_map('unlink', glob($this->directory . $fileName . '*.xml'));

        $query = "SELECT MAX(id) as max, MIN(id) as min FROM parts";
        $limits = DB::query(Database::SELECT,$query)->execute('tecdoc_new')->as_array()[0];

        $minId = (int) $limits['min'];
        $maxId = (int) $limits['max'];

        $filesUrls = [];

        $partInd = 0;
        $partSize = 49998;
        while ($minId < $maxId) {
            $offset = $partInd * $partSize;
            $query = "SELECT id, brand, article, name FROM parts " .
                    "WHERE id >= $minId LIMIT $partSize OFFSET $offset";

            $records = DB::query(Database::SELECT, $query)
                    ->execute('tecdoc_new')
                    ->as_array();

            if (empty($records)) break;

            $document = new SimpleXMLElement('<urlset/>');
            $document->addAttribute('xmlns', $this->namespace);

            foreach ($records AS $record) {
                $url = $document->addChild('url');
                $url->addChild('loc', Helper_Url::getPartUrl($record));
                $url->addChild('changefreq', 'weekly');
                $url->addChild('priority', 0.7);
            }

            $tmpFileName = $this->directory . $fileName;
            $partInd && $tmpFileName .= '-' . $partInd;
            $document->saveXml($tmpFileName . '.xml');
            $filesUrls[] = Helper_Url::createUrl($tmpFileName . '.xml');

            $partInd++;
            $lastElement = end($records);
            $minId = (int) $lastElement['id'] + 1;
        }

        return $filesUrls;
    }

    private function updateSitemap($records, $fileName, $preCallback = false)
    {
        $filesUrls = [];
        $recordsParts = array_chunk($records, 50000);

        foreach ($recordsParts as $partInd => $records) {
            $document = new SimpleXMLElement('<urlset/>');
            $document->addAttribute('xmlns', $this->namespace);

            foreach ($records AS $record) {
                $uri = null;
                $break = false;
                $tagsParams = [
                    'priority'   => 0.5,
                    'changefreq' => 'monthly',
                ];
                if (is_callable($preCallback)) {
                    $preCallback($uri, $record, $break, $tagsParams);
                }
                if ($break) continue;

                $url = $document->addChild('url');
                $url->addChild('loc', Helper_Url::createUrl($uri));
                $url->addChild('changefreq', $tagsParams['changefreq']);
                $url->addChild('priority', $tagsParams['priority']);
            }

            $tmpFileName = $this->directory . $fileName;
            $partInd && $tmpFileName . '-' . $partInd;
            $document->saveXml($tmpFileName . '.xml');
            $filesUrls[] = Helper_Url::createUrl($tmpFileName . '.xml');
        }
        return $filesUrls;
    }
}
