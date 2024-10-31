<?php
namespace YaroRaci;

use YaroRaci\Loader;
use YaroRaci\Constant;
use YaroRaci\Helper\Api;

class Media
{
    public function __construct()
    {

    }

    public function initModal()
    {
        $loader = new Loader;
        $loader->addAction('print_media_templates', $this, 'addButton');
        $loader->addAction('print_media_templates', $this, 'printModalTemplate');
        $loader->addAjax($this, 'getModalContent');
        $loader->addAjax($this, 'saveCropedImage');
        $loader->run();
    }

    public function addButton()
    {
        echo '<script>
            let scr = $("#tmpl-attachment-details-two-column");
            let str = "<# } else if ( \'pdf\' === data.subtype && data.sizes ) { #>"
            let html = $(scr).html().replace(str, "<button type=\'button\' class=\'button button-primary yraci_crop-image\'>'.__('Crop image', Constant::PLUG_SLUG).'</button>\n" + str);
            $(scr).html(html);

            scr = $("#tmpl-attachment-details");
            str = "<# if ( data.fileLength && data.fileLengthHumanReadable ) { #>"
            html = $(scr).html().replace(str, "<button type=\'button\' class=\'button-link yraci_crop-image\'>'.__('Crop image', Constant::PLUG_SLUG).'</button></br>\n" + str);
            $(scr).html(html);
        </script>';
    }

    public function getModalContent()
    {
        $result = (object) [];
        $id = Api::input('id', 'integer', 'REQUIRED');
        $thumb_url = wp_get_attachment_image_src($id, 'full', true);
        $result->image  = $thumb_url[0];
        $result->width  = $thumb_url[1];
        $result->height = $thumb_url[2];
        $result->type = image_type_to_mime_type(exif_imagetype($thumb_url[0]));
        $result->id = $id;
        $sizes = static::getImageSizes($id);
        $result->sizes = $sizes;
        wp_send_json($result);
    }

    public function printModalTemplate()
    {
        echo '<div id="yraci_crop-dialog" class="">
            <div id="yraci_crop-content" vue-app="vue-app">
                <modalform :data="data">
                </modalform>
            </div>
        </div>';
    }

    public function saveCropedImage()
    {
        $id = Api::input('id', 'integer', 'REQUIRED');
        $content = Api::input('base64String', 'base64', 'REQUIRED');
        $currentSize = Api::input('size', 'string', 'REQUIRED');
        $f = finfo_open();
        $uploadType = finfo_buffer($f, $content, FILEINFO_MIME_TYPE);
        finfo_close($f);

        $thumbUrl = wp_get_attachment_image_src($id, $currentSize, true);
        $fullThumb = wp_get_attachment_image_src($id, 'full', true);
        if ($thumbUrl[0] == $fullThumb[0]) {
            wp_send_json(['error' => true, 'message' => __('You need to generate thumb first', Constant::PLUG_SLUG)]);
        }
        $imageType = image_type_to_mime_type(exif_imagetype($thumbUrl[0]));

        if ($uploadType == $imageType) {
            $validation = [
                'x'      => 'float',
                'y'      => 'float',
                'width'  => 'float',
                'height' => 'float',
            ];
            $cropInfo = Api::input('cropInfo', $validation, 'REQUIRED');
            $cropsInfo = get_post_meta($id, 'cropsInfo', true);
            if (!is_array($cropsInfo)) {
                $cropsInfo = [];
            }
            $cropsInfo[$currentSize] = $cropInfo;
            update_post_meta($id, 'cropsInfo', $cropsInfo);

            $dirs = wp_upload_dir();
            $path = $dirs['basedir'];
            $url  = $dirs['baseurl'];
            $thumb_path = str_replace($url, $path, $thumbUrl[0]);
            file_put_contents($thumb_path, $content);
            wp_send_json(
                [
                    'error' => false,
                    'url' => $thumbUrl[0],
                    'cropsInfo' => $cropsInfo
                ]
            );
        } else {
            wp_send_json(['error' => true, 'message' => __('Wrong image type', Constant::PLUG_SLUG)]);
        }
    }

    public static function getImageSizes($id = null)
    {
        $wais = & $GLOBALS['_wp_additional_image_sizes'];
        $sizes = [];
        foreach (get_intermediate_image_sizes() as $_size) {
            if (in_array($_size, array('thumbnail', 'medium', 'medium_large', 'large'))) {
                if ($crop = (bool) get_option("{$_size}_crop")) {
                    $width = (int) get_option("{$_size}_size_w");
                    $height = (int) get_option("{$_size}_size_h");
                    if ($width > 0 && $height) {
                        $arr = [
                            'name'   => $_size,
                            'width'  => $width,
                            'height' => $height,
                            'ratio'  => $width/$height,
                        ];
                        if ($id) {
                            $thumb_url  = wp_get_attachment_image_src($id, $_size, true);
                            $arr['url'] = $thumb_url[0];
                        }
                        $sizes[] = $arr;
                    }
                }
            } elseif (isset($wais[$_size])) {
                if ($crop = (bool) $wais[$_size]['crop']) {
                    if ($wais[$_size]['width'] > 0 && $wais[$_size]['height'] > 0) {
                        $arr = [
                            'name'   => $_size,
                            'width'  => $wais[$_size]['width'],
                            'height' => $wais[$_size]['height'],
                            'ratio'  => $wais[$_size]['width']/$wais[$_size]['height']
                        ];
                        if ($id) {
                            $thumb_url  = wp_get_attachment_image_src($id, $_size, true);
                            $arr['url'] = $thumb_url[0];
                        }
                        $sizes[] = $arr;
                    }
                }
            }
        }
    
        return $sizes;
    }

}