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
        $query = Asset::find()->filename($path)->one();
        if (!$query)
            throw new \Exception('Asset not found.');
            
        $settings = Plugin::getInstance()->getSettings();
        if ($settings->signed) {
            $parts = parse_url($query->volume->url);
            SignatureFactory::create($settings->key)->validateRequest($parts['path'].$path, $params);
        }
        
        // Load Glide
        $server = ServerFactory::create([
            'source' => $query->volume->path,
            'cache' => '../storage/glide',
            'driver' => $settings->driver
        ]);
        
        // Render Image
        $server->outputImage($path, $_GET);
        
        die();
    }
}