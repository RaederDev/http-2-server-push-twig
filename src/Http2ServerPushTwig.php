<?php

namespace Raeder\Technology\ServerPush;

use Craft;
use craft\base\Plugin;
use craft\web\View;
use Raeder\Technology\ServerPush\Twig\Http2ServerPushTwigExtension;
use yii\base\Event;


class Http2ServerPushTwig extends Plugin
{

    public static $plugin;

    public string $schemaVersion = '2.0.0';

    public function init()
    {
        parent::init();
        self::$plugin = $this;

        if (!Craft::$app->getRequest()->getIsSiteRequest()) {
            return;
        }

        // Add in our Twig extensions
        Craft::$app->view->registerTwigExtension(new Http2ServerPushTwigExtension());

        Event::on(View::class, View::EVENT_AFTER_RENDER_PAGE_TEMPLATE, function () {
            $assets = [];
            $modules = [];
            $hostUrl = preg_replace("/\/?$/", '', Craft::$app->request->getAbsoluteUrl());

            //iterate over assets to build Link substrings
            foreach (Http2ServerPushTwigExtension::$assetsToPush as $asset => $config) {
                $asset = str_replace($hostUrl, '', $asset);

                //modules
                if ($config['module']) {
                    $modules[] = $asset;
                    continue;
                }

                //normal assets
                $type = $config['type'];
                if ($config['crossorigin']) {
                    $assets[] = "<$asset>; rel=preload; as=$type; crossorigin";
                } else {
                    $assets[] = "<$asset>; rel=preload; as=$type";
                }
            }

            //now we add our headers
            $headers = Craft::$app->getResponse()->getHeaders();
            if (count($assets) > 0) {
                $headers->add('Link', implode(',', $assets));
            }

            foreach ($modules as $module) {
                $headers->add('Link', "<$module>; rel=modulepreload");
            }
        });
    }

}
