<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/15
 * Time: 10:48
 */
use Core\Context\Context;
use \Core\Context\ContextInterface;
use Core\Server\Server;
use Toolkit\Cli\Cli;
use Core\Console\Console;

/**
 * 获取bean
 */
if (!function_exists("bean")){
    /**
     * @param $id
     * @return object
     */
    function bean($id){
        return \Core\Container\Container::getInstance()->get($id);
    }
}
/**
 * 获取容器 container
 */
if(!function_exists("container")){
    /**
     * @return \Core\Container\Container
     */
    function container(){
        return \Core\Container\Container::getInstance();
    }
}
/**
 * 获取 协程上下文  response 消息 助手
 */
if (!function_exists("response")){
    /**
     * @return \Core\Http\Response\Response
     */
    function response(){
        return Context::get()->getResponse();
    }
}
/**
 * 获取 协程上下文  response 消息 助手
 */
if (!function_exists("request")) {
    /**
     * @return \Core\Http\Request\Request
     */
    function request()
    {
        return Context::get()->getRequest();
    }
}
if (!function_exists('config')) {
    /**
     * Get value from config by key or default
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    function config(string $key = null, $default = null)
    {
        /** @var \Core\Config\Config $config */
        $config = \Core\Container\Container::getInstance()->get(\Core\Config\Config::class);
        if (!$config) {
            \Log\Helper\CLog::error("config instance is not exist");
            return;
        }
        return $config->get($key, $default);
    }
}
if (!function_exists('env')) {
    /**
     * Gets the value of an environment variable.
     *
     * @param  string $key
     * @param  mixed  $default
     *
     * @return mixed
     */
    function env(string $key = null, $default = null)
    {
        $value = getenv($key);

        if ($value === false) {
            return value($default);
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return null;
        }

        if (defined($value)) {
            $value = constant($value);
        }

        return $value;
    }
}


if (!function_exists('sgo')) {
    /**
     * Create coroutine like 'go()'
     * @param callable $callable
     * @param bool     $wait
     */
    function sgo(callable $callable, bool $wait = true)
    {
        \Core\Co::create($callable, $wait);
    }
}

if (!function_exists('context')) {
    /**
     * Get current context
     *
     */
    function context(): ?ContextInterface
    {
        return Context::get();
    }
}

if (!function_exists('server')) {
    /**
     * Get server instance
     *
     */
    function server(): Server
    {
        return Server::getServer();
    }
}
if (!function_exists('show')){
    /**
     * cnosole show
     * @param $data
     * @param string $title
     * @param array $opts
     * @return int
     */
    function show($data, string $title = 'Im-Could 中心节点', array $opts = []){
        if (!$data) {
            Console::write('<info>No data to display!</info>');
            return -2;
        }

        $opts = array_merge([
            'borderChar' => '*',
            'ucFirst'    => true,
        ], $opts);

        $data  = is_array($data) ? array_filter($data) : [trim($data)];
        $title = trim($title);

        $panelData  = []; // [ 'label' => 'value' ]
        $borderChar = $opts['borderChar'];

        $labelMaxWidth = 0; // if label exists, label max width
        $valueMaxWidth = 0; // value max width

        foreach ($data as $label => $value) {
            // label exists
            if (!is_numeric($label)) {
                $width = mb_strlen($label, 'UTF-8');
                // save max value
                $labelMaxWidth = $width > $labelMaxWidth ? $width : $labelMaxWidth;
            }

            // translate array to string
            if (is_array($value)) {
                $temp = '';

                /** @var array $value */
                foreach ($value as $key => $val) {
                    if (is_bool($val)) {
                        $val = $val ? 'True' : 'False';
                    } else {
                        $val = (string)$val;
                    }

                    $temp .= (!is_numeric($key) ? "$key: " : '') . "<info>$val</info>, ";
                }

                $value = rtrim($temp, ' ,');
            } elseif (is_bool($value)) {
                $value = $value ? 'True' : 'False';
            } else {
                $value = trim((string)$value);
            }

            // get value width
            /** @var string $value */
            $value = trim($value);
            $width = mb_strlen(strip_tags($value), 'UTF-8'); // must clear style tag

            $valueMaxWidth     = $width > $valueMaxWidth ? $width : $valueMaxWidth;
            $panelData[$label] = $value;
        }

        $border     = null;
        $panelWidth = $labelMaxWidth + $valueMaxWidth;
        Console::startBuffer();

        // output title
        if ($title) {
            $title       = ucwords($title);
            $titleLength = mb_strlen($title, 'UTF-8');
            $panelWidth  = $panelWidth > $titleLength ? $panelWidth : $titleLength;
            $lenValue    = (int)(ceil($panelWidth / 2) - ceil($titleLength / 2));
            $indentSpace = str_pad(' ', $lenValue + 2 * 2, ' ');
            Console::write("  {$indentSpace}<bold>{$title}</bold>");
        }

        // output panel top border
        if ($borderChar) {
            $border = str_pad($borderChar, $panelWidth + (3 * 3), $borderChar);
            Console::write('  ' . $border);
        }

        // output panel body
        $panelStr = spliceKeyValue($panelData, [
            'leftChar'    => "  $borderChar ",
            'sepChar'     => ' | ',
            'keyMaxWidth' => $labelMaxWidth,
            'ucFirst'     => $opts['ucFirst'],
        ]);

        // already exists "\n"
        Console::write($panelStr, false);

        // output panel bottom border
        if ($border) {
            Console::write("  $border\n");
        }

        Console::flushBuffer();
        unset($panelData);
        return 0;
    }
}

if(!function_exists("spliceKeyValue")){
    /**
 * splice Array
 * @param  array $data
 * e.g [
 *     'system'  => 'Linux',
 *     'version'  => '4.4.5',
 * ]
 * @param  array $opts
 * @return string
 */
 function spliceKeyValue(array $data, array $opts = []): string
{
    $text = '';
    $opts = array_merge([
        'leftChar'    => '',   // e.g '  ', ' * '
        'sepChar'     => ' ',  // e.g ' | ' OUT: key | value
        'keyStyle'    => '',   // e.g 'info','comment'
        'valStyle'    => '',   // e.g 'info','comment'
        'keyMinWidth' => 8,
        'keyMaxWidth' => null, // if not set, will automatic calculation
        'ucFirst'     => true,  // upper first char
    ], $opts);

    if (!is_numeric($opts['keyMaxWidth'])) {
        $opts['keyMaxWidth'] = getKeyMaxWidth($data);
    }

    // compare
    if ((int)$opts['keyMinWidth'] > $opts['keyMaxWidth']) {
        $opts['keyMaxWidth'] = $opts['keyMinWidth'];
    }

    $keyStyle = trim($opts['keyStyle']);

    foreach ($data as $key => $value) {
        $hasKey = !is_int($key);
        $text   .= $opts['leftChar'];

        if ($hasKey && $opts['keyMaxWidth']) {
            $key  = str_pad($key, $opts['keyMaxWidth'], ' ');
            $text .= wrap($key, $keyStyle) . $opts['sepChar'];
        }

        // if value is array, translate array to string
        if (is_array($value)) {
            $temp = '';

            /** @var array $value */
            foreach ($value as $k => $val) {
                if (is_bool($val)) {
                    $val = $val ? '(True)' : '(False)';
                } else {
                    $val = is_scalar($val) ? (string)$val : gettype($val);
                }

                $temp .= (!is_numeric($k) ? "$k: " : '') . "$val, ";
            }

            $value = rtrim($temp, ' ,');
        } elseif (is_bool($value)) {
            $value = $value ? '(True)' : '(False)';
        } else {
            $value = (string)$value;
        }

        $value = $hasKey && $opts['ucFirst'] ? ucfirst($value) : $value;
        $text  .= wrap($value, $opts['valStyle']) . "\n";
    }

    return $text;
}
}
if(!function_exists("getKeyMaxWidth")) {

    function getKeyMaxWidth(array $data, bool $expectInt = false): int
    {
        $keyMaxWidth = 0;

        foreach ($data as $key => $value) {
            // key is not a integer
            if (!$expectInt || !is_numeric($key)) {
                $width = mb_strlen((string)$key, 'UTF-8');
                $keyMaxWidth = $width > $keyMaxWidth ? $width : $keyMaxWidth;
            }
        }

        return $keyMaxWidth;
    }
}
if (!function_exists("wrap")){
    /**
     * wrap a color style tag
     * @param string $text
     * @param string $tag
     * @return string
     */
    function wrap(string $text, string $tag): string
    {
        if (!$text || !$tag) {
            return $text;
        }

        return "<$tag>$text</$tag>";
    }
}
