<?php
namespace YaroRaci\Helper;

use YaroRaci\Constant;

class API
{
    /**
     * Get and sanitize post parametr
     *
     * @param mixed  $name     The field name
     * @param string $sanitize The name of sanitize function in this class
     * @param mixed  $default  The default value. If REQUIRED - send error validate message
     *
     * @return mixed Post value
     */
    public static function input($name, $sanitize = null, $default = null, $fields = null)
    {
        if ($fields === null) {
            $fields = $_POST;
        }
        if (!isset($fields[$name])) {
            if ($default === 'REQUIRED') {
                wp_send_json(
                    [
                        'error' => true,
                        'message' => sprintf(__('Field %s is required', Constant::PLUG_SLUG), $name)
                    ]
                );
            }
            return $default;
        }
        if (is_array($sanitize)) {
            $arr = [];
            foreach ($sanitize as $key => $value) {
                //@todo Now not use $default for array values.
                $arr[$key] = self::input($key, $value, $default, $fields[$name]);
            }
            return $arr;
        }
        if ($sanitize && method_exists(__CLASS__, $sanitize)) {
            $res = call_user_func([__CLASS__, $sanitize], $fields[$name]);
            if ($res === null) {
                if ($default==='REQUIRED') {
                    wp_send_json($result);
                } else {
                    return $default;
                }
            }
            return $res;
        } else {
            return filter_var($fields[$name], FILTER_SANITIZE_STRING);
        }
    }

    public static function integer($value)
    {
        $res = filter_var($value, FILTER_VALIDATE_INT);
        if (false === $res) {
            return null;
        } else {
            return $res;
        }
    }

    public static function float($value)
    {
        $res = filter_var($value, FILTER_VALIDATE_FLOAT);
        if (false === $res) {
            return null;
        } else {
            return $res;
        }
    }

    public static function base64($base64_string)
    {
        if (preg_match('/^data:image\/(\w+);base64,/', $base64_string, $type)) {
            $data = substr($base64_string, strpos($base64_string, ',') + 1);
            $type = strtolower($type[1]);
            if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png' ])) {
                wp_send_json(
                    [
                        'error' => true,
                        'message' => __('Invalid image type', Constant::PLUG_SLUG)
                    ]
                );
            }
            $data = base64_decode($data);
        
            if ($data === false) {
                wp_send_json(
                    [
                        'error' => true,
                        'message' => __('Base64_decode failed', Constant::PLUG_SLUG)
                    ]
                );
            }
        } else {
            wp_send_json(
                [
                    'error' => true,
                    'message' => __('Did not match data URI with image data', Constant::PLUG_SLUG)
                ]
            );
        }        
        return $data;
    }

    /**
     * Get timestamp from string
     *
     * @param string $value The date in format !d-m-Y
     *
     * @return date
     */
    public static function strDate($value)
    {
        $date = \DateTime::createFromFormat('!d-m-Y', $value);
        if ($date) {
            return $date->getTimestamp();
        } else {
            return null;
        }
    }

    public function initAjaxUrl()
    {
        wp_localize_script(
            'yaro-scripts',
            'myajax',
            ['url' => admin_url('admin-ajax.php')]
        );
    }

}