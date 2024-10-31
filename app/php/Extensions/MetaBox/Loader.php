<?php
namespace YaroRaci\Extensions\MetaBox;

use YaroRaci\Extensions\MetaBox\Core;

class Loader extends \RWMB_Loader
{
    protected function constants()
    {
    }
    /**
     * Bootstrap the plugin.
     */
    public function init()
    {
        $this->constants();
        // Register autoload for classes.
        require_once RWMB_INC_DIR . 'autoloader.php';
        $autoloader = new \RWMB_Autoloader();
        $autoloader->add(RWMB_INC_DIR, 'RW_');
        $autoloader->add(RWMB_INC_DIR, 'RWMB_');
        $autoloader->add(RWMB_INC_DIR . 'about', 'RWMB_');
        $autoloader->add(RWMB_INC_DIR . 'fields', 'RWMB_', '_Field');
        $autoloader->add(RWMB_INC_DIR . 'walkers', 'RWMB_Walker_');
        $autoloader->add(RWMB_INC_DIR . 'interfaces', 'RWMB_', '_Interface');
        $autoloader->add(RWMB_INC_DIR . 'storages', 'RWMB_', '_Storage');
        $autoloader->add(RWMB_INC_DIR . 'helpers', 'RWMB_Helpers_');
        $autoloader->register();

        // Plugin core.
        $core = new Core();
        $core->init();

        if (is_admin()) {
            $about = new \RWMB_About();
            $about->init();
        }

        // Validation module.
        new \RWMB_Validation();

        $sanitize = new \RWMB_Sanitizer();
        $sanitize->init();

        $media_modal = new \RWMB_Media_Modal();
        $media_modal->init();

        // WPML Compatibility.
        $wpml = new \RWMB_WPML();
        $wpml->init();

        // Public functions.
        require_once RWMB_INC_DIR . 'functions.php';
    }
}
