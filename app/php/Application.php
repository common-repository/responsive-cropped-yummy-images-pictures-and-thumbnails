<?php
namespace YaroRaci;

use YaroRaci\Loader;
use YaroRaci\Scripts;
use YaroRaci\Styles;
use YaroRaci\Settings;
use YaroRaci\I18n;
use YaroRaci\MetaBoxes;
use YaroRaci\Media;
use YaroRaci\Helper\Api;
use YaroRaci\Extensions\MetaBox\Loader as MBLoader;

class Application
{
    /**
     * Add all hook and filter
     * 
     * @return void
     */
    public function run()
    {
        // $rwmb_loader = new MBLoader();
        // $rwmb_loader->init();
        $loader = new Loader;
        $scripts = new Scripts;
        $loader->addAction('admin_enqueue_scripts', $scripts, 'enqueueAdminScripts');
        $styles = new Styles;
        $loader->addAction('admin_enqueue_scripts', $styles, 'enqueueAdminStyles');
        $i18n = new I18n;
        $loader->addAction('plugins_loaded', $i18n, 'loadThemeTextDomain');
        $settings = new Settings;
        $loader->addAction('admin_menu', $settings, 'addMenu');
        $api = new Api;
        $loader->addAction('wp_enqueue_scripts', $api, 'initAjaxUrl');

        $loader->run();
        // new MetaBoxes;

        $media = new Media;
        $media->initModal();
    }
}