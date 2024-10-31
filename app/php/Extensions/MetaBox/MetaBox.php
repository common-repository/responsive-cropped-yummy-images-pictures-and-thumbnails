<?php
namespace YaroRaci\Extensions\MetaBox;

class MetaBox extends \RW_Meta_Box
{
    /**
     * Check if we're on the right edit screen.
     *
     * @param WP_Screen $screen Screen object. Optional. Use current screen object by default.
     *
     * @return bool
     */
    public function is_edit_screen($screen = null)
    {
        return true;
    }


    /**
     * Specific hooks for meta box object. Default is 'post'.
     * This should be extended in sub-classes to support meta fields for terms, user, settings pages, etc.
     */
    protected function object_hooks()
    {
        // Add meta box.
        add_action('admin_menu', [$this, 'add_meta_boxes']);
        add_filter('rwmb_field_meta', [$this, 'fieldMeta'], 10, 2);
        // Hide meta box if it's set 'default_hidden'.
        add_filter('default_hidden_meta_boxes', [$this, 'hide'], 10, 2);
    }
    public function fieldMeta($meta, $field)
    {
        if (filter_input(INPUT_GET, 'page')) {
            $meta = get_option($field['id']);
        }
        return $meta;
    }

    /**
     * Add meta box for multiple post types
     */
    public function add_meta_boxes()
    {
        $screen = get_current_screen();
        if ($screen) {
            add_filter("postbox_classes_{$screen->id}_{$this->id}", array($this, 'postbox_classes'));
        }
        foreach ( $this->post_types as $post_type ) {
            add_meta_box(
                $this->id,
                $this->title,
                [$this, 'show'],
                $post_type,
                $this->context,
                $this->priority
            );
        }
    }
}