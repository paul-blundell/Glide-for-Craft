<?php 
namespace blundell\glide\controllers;

use Craft;
use craft\web\Controller;
use craft\elements\Asset;
use blundell\glide\Plugin;

use League\Glide\ServerFactory;
use League\Glide\Signatures\SignatureFactory;

class ParseController extends Controller
{
    protected $allowAnonymous = true;
    
    public function actionIndex($path)
    {
        $params = $_GET;
        unset($params['p']);
        
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
          throw new \Exception("No assets found.");

        $firstItemKey = array_key_first($query);
        $query        = $query[$firstItemKey];
            
        $settings = Plugin::getInstance()->getSettings();
        if ($settings->signed) {
            $parts = parse_url($query->volume->url);
            SignatureFactory::create($settings->key)->validateRequest($parts['path'].$path, $params);
        }
        
        // Load Glide
        $server = ServerFactory::create([
            'source' => Craft::parseEnv($query->volume->path),
            'cache' => '../storage/glide',
            'driver' => $settings->driver
        ]);
        
        // Render Image
        $server->outputImage($path, $_GET);
        
        die();
    }
}
