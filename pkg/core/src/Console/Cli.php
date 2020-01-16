<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/8/23 0023
 * Time: 下午 5:08
 */

namespace Core\Console;


use Core\App;
use Core\Console\Input\Input;
use Core\Container\Mapping\Bean;
use Stdlib\Helper\Arr;
use Stdlib\Helper\Str;
use Toolkit\Cli\ColorTag;

/**
 * Class Cli
 * @package Core\Console
 * @Bean()
 */
class Cli
{
    /**
     * @var string
     */
    private $version = '2.2.0';

    /**
     * @var string
     */
    private $description = 'im-cloud console';

    /** @var array */
    private static $globalOptions = [
        '--start'      => 'start the server 启动服务',
        '--restart'    => 'restart the server 重启服务',
        '--stop'       => 'stop the server  kill服务',
        "--reload"     => 'reload the server 重启worker',
        '--d'         => 'with deamon      守护模式',
        '--debug'       => 'start debug     开启debug',
        '--log=bool'         => 'true start log,false stop log 是否开启日志记录',
        '--h'    => 'Display this help message',
        '--v' => 'Show application version information',
    ];
    /**
     * Display command list of the application
     *
     * @param bool $showLogo
     */
    public function showApplicationHelp(bool $showLogo = true): void
    {
        // show logo
        if ($showLogo) {
            Console::colored(App::FONT_LOGO, 'cyan');
        }
        $input = new Input();
        $script = $input->getScriptName();

        // Global options
        $globalOptions = self::$globalOptions;
        // Append expand option

        $appVer  = $this->getVersion();
        $appDesc = $this->getDescription();

        Console::startBuffer();
        Console::writeln(sprintf("%s%s\n", $appDesc, $appVer ? " (Version: <info>$appVer</info>)" : ''));

        self::showMList([
            'Usage:'   => "php $script <info>OPTIONS</info> --opt -v -h ...",
            'Options:' => self::alignOptions($globalOptions),
        ], [
            'sepChar' => '   ',
        ]);

        $router   = new Router\Router();
        $expand = $input->getBoolOpt('expand');
        $keyWidth = $router->getKeyWidth($expand ? 2 : -4);

//        Console::writeln('<comment>Available Commands:</comment>');

        $grpHandler = function (string $group, array $info) use ($keyWidth) {
            Console::writef(
                '  <info>%s</info>%s%s',
                Str::padRight($group, $keyWidth),
                $info['desc'] ?: 'No description message',
                $info['alias'] ? "(alias: <info>{$info['alias']}</info>)" : ''
            );
        };

        $cmdHandler = function (string $cmdId, array $info) use ($keyWidth) {
            Console::writef(
                '  <info>%s</info> %s%s',
                Str::padRight($cmdId, $keyWidth),
                $info['desc'] ?: 'No description message',
                $info['alias'] ? "(alias: <info>{$info['alias']}</info>)" : ''
            );
        };

        $router->sortedEach($grpHandler, $expand ? $cmdHandler : null);
//        Console::write("\nMore command information, please use: <cyan>$script COMMAND -h</cyan>");
        Console::flushBuffer();
    }
    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param array $data
     * @param array $opts
     */
    public static function showMList(array $data, array $opts = []): void
    {
        $stringList  = [];
        $ignoreEmpty = $opts['ignoreEmpty'] ?? true;
        $lastNewline = true;

        $opts['returned'] = true;
        if (isset($opts['lastNewline'])) {
            $lastNewline = $opts['lastNewline'];
            unset($opts['lastNewline']);
        }

        foreach ($data as $title => $list) {
            if ($ignoreEmpty && !$list) {
                continue;
            }

            $stringList[] = self::singleListShow($list, $title, $opts);
        }

        Console::write(implode("\n", $stringList), $lastNewline);
    }
    public static function singleListShow($data, string $title = '', array $opts = [])
    {
        $string = '';
        $opts   = array_merge([
            'leftChar'    => '  ',
            // 'sepChar' => '  ',
            'keyStyle'    => 'info',
            'keyMinWidth' => 8,
            'titleStyle'  => 'comment',
            'returned'    => false,
            'ucFirst'     => false,
            'lastNewline' => true,
        ], $opts);

        // title
        if ($title) {
            $title  = ucwords(trim($title));
            $string .= ColorTag::wrap($title, $opts['titleStyle']) . PHP_EOL;
        }

        // handle item list
        $string .= self::spliceKeyValue((array)$data, $opts);

        // return formatted string.
        if ($opts['returned']) {
            return $string;
        }

        return Console::write($string, $opts['lastNewline']);
    }
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
    public static function spliceKeyValue(array $data, array $opts = []): string
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
            $opts['keyMaxWidth'] = Arr::getKeyMaxWidth($data);
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
                $text .= ColorTag::wrap($key, $keyStyle) . $opts['sepChar'];
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
            $text  .= ColorTag::wrap($value, $opts['valStyle']) . "\n";
        }

        return $text;
    }
    /**
     * @param array $options
     * @return array
     */
    public static function alignOptions(array $options): array
    {
        $optKeys = array_keys($options);
        // e.g '-h, --help'
        $hasShort = (bool)strpos(implode('', $optKeys), ',');

        if (!$hasShort) {
            return $options;
        }

        $formatted = [];
        foreach ($options as $name => $des) {
            if (!$name = trim($name, ', ')) {
                continue;
            }

            if (!strpos($name, ',')) {
                // padding length equals to '-h, '
                $name = '    ' . $name;
            } else {
                $name = str_replace([' ', ','], ['', ', '], $name);
            }

            $formatted[$name] = $des;
        }

        return $formatted;
    }
}