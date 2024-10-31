<?php

namespace YaroRaci;

use YaroRaci\Constant;

class I18n
{
    /**
     * Load the text domain for translation.
     *
     * @since    1.0.0
     */
    public function loadThemeTextDomain()
    {
        load_plugin_textdomain(Constant::PLUG_SLUG, false, Constant::PLUG_DIR . '/languages/');
    }
}
