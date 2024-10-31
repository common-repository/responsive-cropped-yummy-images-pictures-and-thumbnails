<?php
namespace YaroRaci;

use YaroRaci\PugApp;
use YaroRaci\Config;
use YaroRaci\Constant;

class PugWp
{
    /**
     * @var array $data Data for pug template
     *
     */
    private $data = [];

    /**
     * @var string $template Template name
     *
     */
    private $template;

    private $config;

    public function __construct()
    {
        $this->config = 'production';
        if (Constant::DEV_MODE) {
            $this->config = 'development';
        }
    }

    public function getUrl()
    {
        $url = (filter_input(INPUT_SERVER, 'HTTPS') === 'on' ?
            'https' : 'http') . "://" . filter_input(INPUT_SERVER, 'HTTP_HOST') .
            filter_input(INPUT_SERVER, 'REQUEST_URI');
        return esc_url_raw($url);
    }

    public function render()
    {
        $html = $this->getHtml();
        header('Content-Type: text/html;charset: utf8');
        echo $html;
    }

    public function getHtml()
    {
        $app = new PugApp($this->config);

        $url = $this->getUrl();
        $html = null;
        if ($app->has($url)) {
            $html = $app->show($url);
        } else {
            $html = $app->render($this->getTemplate(), $this->data);
            $app->cache($url, $html);
        }
        return $html;
    }

    public function renderBlock()
    {
        $app = new PugApp($this->config);

        $url = $this->getUrl();

        $html = null;
        if ($app->has($url)) {
            $html = $app->show($url);
        } else {
            $html = $app->renderSections([['id' => $this->getTemplate(), 'data' => $this->data]]);
            $app->cache($url, $html);
        }
        return $html;
    }

    public function renderJSON()
    {
        $app = new PugApp($this->config);
        $url = $this->getUrl();
        $html = null;
        if ($app->has($url)) {
            $html = $app->show($url);
        } else {
            $file = $app->getJSON($this->getTemplate());
            if ($file) {
                $json = json_decode(file_get_contents($file), true);
                $html = $app->renderSections($json['sections']);
                $app->cache($url, $html);
            }
        }
        return $html;
    }

    public function outputBufferContents($function, $args = array())
    {
        ob_start();
        if (!is_array($args)) {
            $args = [$args];
        }
        call_user_func_array($function, $args);
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }

    public function setTemplate($template)
    {
        $this->template = $template;
    }

    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Sets the value for data by it's name.
     * Name can include dots which allow to set value deep item.
     *
     * @example $this->set('root.style', '/assets/css/bundle.css')
     *          this will set data root to
     *          data['root']['style'] = '/assets/css/bundle.css'
     *          if data['root'] did not exist it will be created
     * @access private
     * @param string $name The name to set.
     * @param mixed $value The value to set.
     */
    public function set($name, $value = null)
    {
        if (false === strpos($name, '.')) {
            $this->data[$name] = $value;
        } else {
            $current = &$this->data;
            $arr = explode('.', $name);
            foreach ($arr as $i => $part) {
                if ($i == count($arr) - 1) {
                    $current[$part] = $value;
                    break;
                }
                if (!isset($current[$part])) {
                    $current[$part] = [];
                }
                $current = &$current[$part];
            }
        }
    }

    public function get($name, $default = null)
    {
        if (true === $name) {
            return $this->data;
        }
        if (false === strpos($name, '.')) {
            return isset($this->data[$name]) ? $this->data[$name] : $default;
        }
        $current = $this->data;
        $arr = explode('.', $name);
        foreach ($arr as $i => $part) {
            if (isset($current[$part])) {
                $current = $current[$part];
            } else {
                return $default;
            }
        }
        return $current;
    }
}
