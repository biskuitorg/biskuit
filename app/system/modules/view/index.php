<?php

use Biskuit\Util\ArrObject;
use Biskuit\View\Event\ResponseListener;

return [

    'name' => 'system/view',

    'main' => function ($app) {

        $app->extend('twig', function ($twig) use ($app) {

            $twig->addFilter(new Twig_SimpleFilter('trans', '__'));
            $twig->addFilter(new Twig_SimpleFilter('transChoice', '_c'));

            return $twig;

        });

        $app->extend('assets', function ($assets) use ($app) {

            $assets->register('file', 'Biskuit\View\Asset\FileLocatorAsset');

            return $assets;
        });

    },

    'autoload' => [

        'Biskuit\\View\\' => 'src'

    ],

    'events' => [

        'boot' => function ($event, $app) {
            $app->subscribe(new ResponseListener());
        },

        'site' => function ($event, $app) {
            $app->on('view.meta', function ($event, $meta) use ($app) {

                if ((isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) || (isset($_SERVER['HTTPS']) && $_SERVER['SERVER_PORT'] == 443)) {
                    $_SERVER['HTTPS'] = true;
                } elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
                    $_SERVER['HTTPS'] = true;
                } else {
                    $_SERVER['HTTPS'] = false;
                }

                $url = $app['url']->get($app['request']->attributes->get('_route'), $app['request']->attributes->get('_route_params', []), 0);
                if (substr($url, 0, 5) === "http:" && $_SERVER['HTTPS'] === true) {
                    $url = str_replace(substr($url, 0, 4), "https", $url);
                }
                $meta->add('canonical', $url);
            }, 60);
        },

        'view.init' => [function ($event, $view) {
            $view->defer('head');
            $view->meta(['generator' => 'Biskuit']);
            $view->addGlobal('params', new ArrObject());
        }, 20],

        'view.data' => function ($event, $data) use ($app) {
            $data->add('$biskuit', [
                'url' => $app['router']->getContext()->getBaseUrl(),
                'csrf' => $app['csrf']->generate()
            ]);
        },

        'view.styles' => function ($event, $styles) {
            $styles->register('codemirror-hint', 'app/assets/codemirror/hint.css');
            $styles->register('codemirror', 'app/assets/codemirror/codemirror.css', ['codemirror-hint']);
        },

        'view.scripts' => function ($event, $scripts) use ($app) {
            $scripts->register('codemirror', 'app/assets/codemirror/codemirror.js');
            $scripts->register('jquery', 'app/assets/jquery/jquery.min.js');
            $scripts->register('lodash', 'app/assets/lodash/lodash.min.js');
            $scripts->register('marked', 'app/assets/marked/marked.min.js');
            $scripts->register('uikit', 'app/assets/uikit/js/uikit.min.js', 'jquery');
            $scripts->register('uikit-accordion', 'app/assets/uikit/js/components/accordion.min.js', 'uikit');
            $scripts->register('uikit-autocomplete', 'app/assets/uikit/js/components/autocomplete.min.js', 'uikit');
            $scripts->register('uikit-datepicker', 'app/assets/uikit/js/components/datepicker.min.js', 'uikit');
            $scripts->register('uikit-form-password', 'app/assets/uikit/js/components/form-password.min.js', 'uikit');
            $scripts->register('uikit-form-select', 'app/assets/uikit/js/components/form-select.min.js', 'uikit');
            $scripts->register('uikit-grid', 'app/assets/uikit/js/components/grid.min.js', 'uikit');
            $scripts->register('uikit-htmleditor', 'app/assets/uikit/js/components/htmleditor.min.js', ['uikit', 'marked', 'codemirror']);
            $scripts->register('uikit-nestable', 'app/assets/uikit/js/components/nestable.min.js', 'uikit');
            $scripts->register('uikit-notify', 'app/assets/uikit/js/components/notify.min.js', 'uikit');
            $scripts->register('uikit-tooltip', 'app/assets/uikit/js/components/tooltip.min.js', 'uikit');
            $scripts->register('uikit-pagination', 'app/assets/uikit/js/components/pagination.min.js', 'uikit');
            $scripts->register('uikit-slider', 'app/assets/uikit/js/components/slider.min.js', 'uikit');
            $scripts->register('uikit-slideshow', 'app/assets/uikit/js/components/slideshow.min.js', 'uikit');
            $scripts->register('uikit-sortable', 'app/assets/uikit/js/components/sortable.min.js', 'uikit');
            $scripts->register('uikit-sticky', 'app/assets/uikit/js/components/sticky.min.js', 'uikit');
            $scripts->register('uikit-upload', 'app/assets/uikit/js/components/upload.min.js', 'uikit');
            $scripts->register('uikit-lightbox', 'app/assets/uikit/js/components/lightbox.min.js', 'uikit');
            $scripts->register('uikit-parallax', 'app/assets/uikit/js/components/parallax.min.js', 'uikit');
            $scripts->register('uikit-timepicker', 'app/assets/uikit/js/components/timepicker.js', 'uikit-autocomplete');
            $scripts->register('vue', 'app/system/app/bundle/vue.js', ['vue-dist', 'jquery', 'lodash', 'locale']);
            $scripts->register('vue-dist', 'app/assets/vue/' . ($app->debug() ? 'vue.js' : 'vue.min.js'));
            $scripts->register('locale', $app->url('@system/intl', ['locale' => $app->module('system/intl')->getLocale(), 'v' => $scripts->getFactory()->getVersion()]), [], ['type' => 'url']);
        }

    ]

];
