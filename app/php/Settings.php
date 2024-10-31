<?php
namespace YaroRaci;

use YaroRaci\Config;
use YaroRaci\PugWp;
use YaroRaci\Constant;

class Settings
{
    public function __construct()
    {
    }

    public function addMenu()
    {
        add_options_page(
            __('Chat opener', Constant::PLUG_SLUG),
            __('Chat opener', Constant::PLUG_SLUG),
            'manage_options',
            Constant::PREFIX.'options',
            [$this, 'settingsPageTemplate']
        );
    }

    public function settingsPageTemplate()
    {

        if (!empty($_POST['submit'])) {
            $this->saveOptions();
        }
        $columns = 2;
        $template = new PugWp();
        $template->setTemplate('metabox-page');
        $template->set('header', __('Chat opener', Constant::PLUG_SLUG));
        $template->set('columns', $columns);
        $template->set(
            'wp_nonce_field_1',
            $template->outputBufferContents('wp_nonce_field', ['closedpostboxes', 'closedpostboxesnonce', false])
        );
        $template->set(
            'wp_nonce_field_2',
            $template->outputBufferContents('wp_nonce_field', ['meta-box-order', 'meta-box-order-nonce', false])
        );
        if ($columns > 1) {
            $template->set(
                'side_boxes',
                $template->outputBufferContents('do_meta_boxes', [Constant::PREFIX.'options', 'side', null])
            );
        }
        $template->set(
            'normal_boxes',
            $template->outputBufferContents('do_meta_boxes', [Constant::PREFIX.'options', 'normal', null])
        );
        $template->set(
            'advanced_boxes',
            $template->outputBufferContents('do_meta_boxes', [Constant::PREFIX.'options', 'advanced', null])
        );
        $template->set(
            'submit_button',
            $template->outputBufferContents(
                'submit_button',
                [__('Save Changes', Constant::PLUG_SLUG), 'primary', 'submit', false]
            )
        );
        echo $template->renderBlock();
    }
    public function saveOptions()
    {
        $config = Config::get('pageboxes', 'php', true);
        foreach ($config as $metabox) {
            $nonce = filter_input(INPUT_POST, "nonce_{$metabox['id']}", FILTER_SANITIZE_STRING);
            if (!wp_verify_nonce($nonce, "rwmb-save-{$metabox['id']}")) {
                continue;
            }
            foreach ($metabox['fields'] as $option) {
                if (isset($option['id']) && isset($_POST[$option['id']])) {
                    update_option($option['id'], sanitize_text_field($_POST[$option['id']]));
                }
            }
        }
    }
}
