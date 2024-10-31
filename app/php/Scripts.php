<?php
namespace YaroRaci;

use YaroRaci\Constant;

class Scripts
{

    public function enqueueAdminScripts()
    {
        wp_enqueue_script('jquery-ui-dialog');
        wp_enqueue_script('jquery-ui-tabs');
        wp_enqueue_media();
        $url = YARORACI_PLUG_DIR . 'assets/js/bundle.js';
        $version = hash_file('adler32', $url);
        wp_enqueue_script(
            Constant::PREFIX . 'scripts',
            $url . '?v=' . $version,
            [],
            $version,
            true
        );
    }
}
