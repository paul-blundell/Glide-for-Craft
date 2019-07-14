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
        
        $query = Asset::find()->filename($path)->one();
        if (!$query)
            throw new \Exception('Asset not found.');
        
        // Create an instance of the URL builder
        $urlBuilder = UrlBuilderFactory::create($query->volume->url, $settings->key);
        
        // Generate a URL
        return $urlBuilder->getUrl($path, $params);
    }
}