<?php
namespace YaroRaci\Extensions\MetaBox;

class Core extends \RWMB_Core
{
    /**
     * Register meta boxes.
     * Advantages:
     * - prevents incorrect hook.
     * - no need to check for class existences.
     */
    public function register_meta_boxes()
    {
        $configs    = apply_filters('rwmb_custom_meta_boxes', []);
        $meta_boxes = rwmb_get_registry('meta_box');
        foreach ($configs as $config) {
            $meta_box = rwmb_get_meta_box($config);
            $meta_boxes->add($meta_box);
            $meta_box->register_fields();
        }
    }
}
