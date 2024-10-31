<?php
/*
Plugin Name:     Responsive cropped Yummy images, pictures and thumbnails
Description:     Manual cropping of images for different sizes
Author:          Konstantin Melnikov <cartman.zp@gmail.com>
Author URI:      http://yaro.info
Text Domain:     yaro-raci
Domain Path:     /languages
Version:         1.0.0
*/
namespace YaroRaci;

use YaroRaci\Application;

define('YARORACI_PLUG_DIR', plugin_dir_url(__FILE__));

require_once __DIR__ .'/vendor/autoload.php';

$app = new Application();
$app->run();