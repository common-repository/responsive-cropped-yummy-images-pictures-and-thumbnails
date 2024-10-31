<?php
namespace YaroRaci;

use YaroRaci\Constant;

class Styles
{
    /**
     * Add this function to WP action `admin_enqueue_scripts`
     * 
     * @return void
     */
    public function enqueueAdminStyles()
    {
        wp_enqueue_style('wp-jquery-ui-dialog');
        $url = YARORACI_PLUG_DIR . 'assets/css/bundle.min.css';
        $version = hash_file('adler32', $url);
        wp_enqueue_style(
            Constant::PREFIX . 'styles',
            $url . '?v=' . $version,
            [],
            $version
        );
    }
}
