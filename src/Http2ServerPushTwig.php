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

    public $schemaVersion = '1.0.1';

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
            $hostUrl = preg_replace("/\/?$/", '', Craft::$app->request->getAbsoluteUrl());

            //iterate over assets to build Link substrings
            foreach (Http2ServerPushTwigExtension::$assetsToPush as $asset => $config) {
                $type = $config['type'];
                $asset = str_replace($hostUrl, '', $asset);
                if ($config['crossorigin']) {
                    $assets[] = "<$asset>; rel=preload; as=$type; crossorigin";
                } else {
                    $assets[] = "<$asset>; rel=preload; as=$type";
                }
            }

            if (count($assets) < 1) {
                return;
            }

            //now we add our header
            Craft::$app
                ->getResponse()
                ->getHeaders()
                ->add('Link', implode(',', $assets));
        });
    }

}
