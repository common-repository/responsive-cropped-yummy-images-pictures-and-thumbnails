<?php

namespace YaroRaci;

use YaroRaci\Constant;
use Predis\Client as RedisClient;
use Pug\Pug;

class PugApp
{
    private $config = [];
    private $client;

    /**
     * Application constructor.
     *
     * @access public
     * @param mixed $config The set of array or a name of file in CONFIG_DIR.
     */
    public function __construct($config)
    {
        if (is_array($config)) {
            $this->config = $config;
        } else {
            $file = Constant::CONFIG_DIR . '/' . $config . '.json';
            $this->config = json_decode(file_get_contents($file), true);
        }
        if (!@$this->config['cache']) {
            return true;
        }
        $options = @$this->config['cache']['options'];
        if (empty($options)) {
            $options = '';
        }
        $this->client = new RedisClient($options);
        if (isset($this->config['cache']['database'])) {
            $this->client->select($this->config['cache']['database']);
        }
    }

    /**
     * Check if has unexpired page cached by url.
     *
     * @access public
     * @param string $url The URI string.
     * @return boolean TRUE if has unexpired page, otherwise FALSE.
     */
    public function has($url)
    {
        if (!$this->client) {
            return false;
        }
        $key = $this->key($url);
        $time = $this->client->get('time.' . $key);
        if (!$time) {
            return false;
        }
        $timeout = (int) $this->config['cache']['timeout'];
        if ($timeout > 0 && $time + $timeout < time()) {
            return false;
        }
        return true;
    }

    public function show($url)
    {
        $key = $this->key($url);
        return $this->client->get('html.' . $key);
    }

    public function render($url, $data)
    {
        $options = [
            'cache'   => Constant::CACHE_DIR,
        ];
        $pug = new Pug($options);
        if (!$url) {
            $url = 'index';
        }
        $jsonFile = $this->getJSON($url);
        if ($jsonFile) {
            return $this->renderJson($jsonFile);
        }
        $file = Constant::CONTENT_DIR . '/' . trim($url, '/') . '.pug';
        if ('/.pug' == substr($file, -5)) {
            $file = Constant::CONTENT_DIR . '/' . trim($url, '/') . '.index.pug';
        }
        if (!file_exists($file)) {
            $file = Constant::CONTENT_DIR . '/404.pug';
        }
        if (isset($data['sections'])) {
            $data['content'] = $content = $this->renderSections($data['sections']);
        }
        return $pug->render($file, $data);
    }

    public function getJSON($url)
    {
        $jsonFile = Constant::CONTENT_DIR . '/' . trim($url, '/') . '.json';
        if (file_exists($jsonFile)) {
            return $jsonFile;
        } else {
            return false;
        }
    }

    public function renderSections($sections)
    {
        $options = [
            'cache'   => Constant::CACHE_DIR,
        ];
        $pug = new Pug($options);
        $content = [];
        foreach ($sections as $item) {
            $template = Constant::TEMPLATES_DIR . '/blocks/' . $item['id'] . '.pug';
            $data = isset($item['data']) ? $item['data'] : [];
            $content[] = $pug->render($template, $data);
        }
        return implode("\n", $content);
    }

    private function renderJson($file)
    {
        $options = [
            'cache'   => Constant::CACHE_DIR,
        ];
        $pug = new Pug($options);
        $json = json_decode(file_get_contents($file), true);
        $content = $this->renderSections($json['sections']);
        $layout = Constant::TEMPLATES_DIR . '/layout/' . $json['$layout'] . '.pug';
        return $pug->render($layout, ['content' => $content]);
    }

    public function cache($url, $html)
    {
        if (!$this->client) {
            return false;
        }
        $key = $this->key($url);
        $this->client->set('time.' . $key, time());
        $this->client->set('html.' . $key, $html);
        if (@$this->config['cache']['includeGet']) {
            $raw = $this->key($url, true);
            $this->lpush('child.' . $raw, $key);
        }
    }

    /**
     * Returns key for current url.
     *
     * @access protected
     * @param string $Url The URI string.
     * @return string The key for current URI.
     */
    protected function key($url, $rawOnly = false)
    {
        if (!$url) {
            $url = '/';
        }
        $url = trim($url, '/');
        if (@$this->config['cache']['includeGet'] && !$rawOnly && !empty($_GET)) {
            $url .= '?' . http_build_query(filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING));
        }
        return rtrim(base64_encode($url), '=');
    }
}
