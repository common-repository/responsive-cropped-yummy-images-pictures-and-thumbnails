<?php

namespace YaroRaci;

use YaroRaci\Constant;

class Config
{
    /**
     * Load config from JSON
     * 
     * @param string $name Config name 
     * 
     * @return object The config
     *
     */
    public static function loadJSON($name)
    {
        $config = Constant::CONFIG_DIR . "{$name}.json";
        if (file_exists($config)) {
            return json_decode(file_get_contents($config));
        }
        return false;
    }

    public static function set($name, $array, $type = 'php')
    {
        if ($type == 'php') {
            $content = "<?php\nreturn " . static::varExportShort($json, true) . ";\n";
        } else {
            $content = json_encode($array, JSON_PRETTY_PRINT);
        }
        file_put_contents(Constant::CONFIG_DIR . "/{$name}.{$type}", $content);
    }

    public static function get($name, $type = 'php', $isDir = false)
    {
        if ($isDir) {
            $path = Constant::CONFIG_DIR ."/{$name}";
            $allFiles = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
            $files = new \RegexIterator($allFiles, '/\.'.$type.'$/');
            $arr = [];
            foreach ($files as $file) {
                $arr[] = include $file;
            }
            return $arr;
        }
        $file = Constant::CONFIG_DIR ."/{$name}.{$type}";
        if (file_exists($file)) {
            return include $file;
        }
        return false;
    }

    public static function varExportShort($data, $return=true)
    {
        $dump = var_export($data, true);
        $dump = preg_replace('#(?:\A|\n)([ ]*)array \(#i', '[', $dump); // Starts
        $dump = preg_replace('#\n([ ]*)\),#', "\n$1],", $dump); // Ends
        $dump = preg_replace('#=> \[\n\s+\],\n#', "=> [],\n", $dump); // Empties
        if (gettype($data) == 'object') { // Deal with object states
            $dump = str_replace('__set_state(array(', '__set_state([', $dump);
            $dump = preg_replace('#\)\)$#', "])", $dump);
        } else { 
            $dump = preg_replace('#\)$#', "]", $dump);
        }
    
        if ($return===true) {
            return $dump;
        } else {
            echo $dump;
        }
    }
}
