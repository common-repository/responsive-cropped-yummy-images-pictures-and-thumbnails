<?php
namespace YaroRaci;

class Constant
{
    const PREFIX         = 'yraci_';
    const PLUG_SLUG      = 'yaro-raci';
    const PLUG_DIR       = __DIR__ . '/../..';
    const CONTENT_DIR    = self::PLUG_DIR . '/content';
    const TEMPLATES_DIR  = self::PLUG_DIR . '/templates';
    const CONFIG_DIR     = self::PLUG_DIR . '/config';
    const CACHE_DIR      = self::PLUG_DIR . '/../../pug_cache';

    const DEV_MODE = true;

}