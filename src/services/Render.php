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
        $assets = Asset::find()->filename(basename($path))->all();

        // Filter out assets not in this folder
        $assets = array_filter($assets, function ($asset) use ($path) {
          /**
           * @var Asset $asset
           */
          return (strpos($path, $asset->folderPath . $asset->filename) !== false);
        });

        if (empty($assets))
        {
           throw new \Exception("No assets found.");
        }

        $firstItemKey = array_key_first($assets);
        $asset        = $assets[$firstItemKey];

        // Create an instance of the URL builder
        $urlBuilder = UrlBuilderFactory::create($asset->volume->url, $settings->key);

        // Generate a URL
        return $urlBuilder->getUrl($asset->folderPath . $asset->filename, $params);
    }
}
