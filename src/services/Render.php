<?php
namespace blundell\glide\services;

use yii\base\Component;
use blundell\glide\Plugin;
use craft\elements\Asset;
use League\Glide\Urls\UrlBuilderFactory;

class Render extends Component
{
    public function url($path, $params)
    {
        $settings = Plugin::getInstance()->getSettings();

        // Find asset by filename
        $query = Asset::find()->filename(basename($path))->all();

        // Filter out assets not in this folder
        $query = array_filter($query, function ($asset) use ($path) {
          /**
           * @var Asset $asset
           */
          return (strpos($path, $asset->folderPath . $asset->filename) !== false);
        });

        if (empty($query))
        {
           throw new \Exception("No assets found.");
        }

        $firstItemKey = array_key_first($query);
        $query        = $query[$firstItemKey];

        // Create an instance of the URL builder
        $urlBuilder = UrlBuilderFactory::create($query->volume->url, $settings->key);

        // Generate a URL
        return $urlBuilder->getUrl($path, $params);
    }
}
