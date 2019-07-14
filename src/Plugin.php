<?php
namespace blundell\glide;

use Craft;
use yii\base\Event;
use craft\web\UrlManager;
use craft\events\RegisterUrlRulesEvent;
use craft\web\twig\variables\CraftVariable;
use craft\utilities\ClearCaches;
use craft\events\RegisterCacheOptionsEvent;
use craft\helpers\FileHelper;

class Plugin extends \craft\base\Plugin
{
    public function init()
    {
        parent::init();
        
        // Register our site routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['glide/<path:.*>'] = 'glide/parse';
                $event->rules['glide'] = 'glide/parse';
            }
        );
        
        // Custom cache option
        Event::on(
            ClearCaches::class,
            ClearCaches::EVENT_REGISTER_CACHE_OPTIONS,
            function (RegisterCacheOptionsEvent $event) {
                $event->options[] = [
                    'key' => 'glide-transforms',
                    'label' => Craft::t('app', 'Glide Transforms'),
                    'action' => function() {
                        FileHelper::clearDirectory('../storage/glide');
                    }
                ];
            }
        );
        
        // Services
        $this->setComponents([
            'render' => \blundell\glide\services\Render::class,
        ]);
        
        // Twig
        Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, function(Event $e) {
            $variable = $e->sender;
            $variable->set('glide', \blundell\glide\services\Render::class);
        });
    }
    
    protected function createSettingsModel()
    {
        return new \blundell\glide\models\Settings();
    }
}