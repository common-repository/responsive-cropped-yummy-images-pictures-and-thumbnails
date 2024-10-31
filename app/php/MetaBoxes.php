<?php
namespace YaroRaci;

use YaroRaci\Loader;
use YaroRaci\Config;

class Metaboxes
{

    public function __construct()
    {
        $loader = new Loader();
        $loader->addFilter('rwmb_custom_meta_boxes', $this, 'registerChatOpenerPage');
        $loader->addFilter('rwmb_meta_box_class_name', $this, 'getMetaboxClass', 10, 2);
        $loader->addFilter('rwmb_show', $this, 'isShow', 20, 2);
        $loader->run();
    }

    public function isShow($show, $meta_box)
    {
        if (isset($meta_box['isSettingsPage']) && $meta_box['isSettingsPage']) {
            return true;
        }
        if (isset($meta_box['pageTemplate'])) {
            if (strpos(filter_input(INPUT_SERVER, 'REQUEST_URI'), 'post-new.php')) {
                return false;
            }
            $post_id = (filter_input(INPUT_GET, 'post'))
                ? filter_input(INPUT_GET, 'post', FILTER_VALIDATE_INT)
                : ((filter_input(INPUT_GET, 'post_ID')) ? filter_input(INPUT_GET, 'post_ID', FILTER_VALIDATE_INT) : false);
            if ($post_id) {
                $template = get_post_meta($post_id, '_wp_page_template', true);
                return $meta_box['pageTemplate'] == $template;
            }
        }
        return $show;
    }
    public function getMetaboxClass($class_name, $meta_box)
    {
        if (isset($meta_box['isSettingsPage']) && $meta_box['isSettingsPage']) {
            return '\YaroRaci\Extensions\MetaBox\MetaBox';
        }
        return $class_name;
    }
    public function registerChatOpenerPage($meta_boxes)
    {
        $config = Config::get('pageboxes', 'php', true);
        return array_merge($meta_boxes, $config);
    }
}
