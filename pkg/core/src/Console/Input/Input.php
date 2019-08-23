<?php declare(strict_types=1);

namespace Core\Console\Input;

use function array_map;
use function array_shift;
use Core\Container\Mapping\Bean;
use function fgets;
use function fwrite;
use function implode;
use function preg_match;
use const STDIN;
use const STDOUT;
use Toolkit\Cli\Flags;
use function trim;

/**
 * Class Input - The input information. by parse global var $argv.
 * @Bean()
 */
class Input extends AbstractInput
{
    /**
     * @var resource
     */
    protected $inputStream = STDIN;

    /**
     * Input constructor.
     * @param null|array $args
     * @param bool       $parsing
     */
    public function __construct(array $args = null, bool $parsing = true)
    {
        if (null === $args) {
            $args = (array)$_SERVER['argv'];
        }

        $this->pwd        = $this->getPwd();
        $this->tokens     = $args;
        $this->script     = array_shift($args);
        $this->fullScript = implode(' ', $args);

        if ($parsing) {
            // list($this->args, $this->sOpts, $this->lOpts) = InputParser::fromArgv($args);
            [$this->args, $this->sOpts, $this->lOpts] = Flags::parseArgv($args);

            // find command name
            $this->findCommand();
        }
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        $tokens = array_map(function ($token) {
            if (preg_match('{^(-[^=]+=)(.+)}', $token, $match)) {
                return $match[1] . Flags::escapeToken($match[2]);
            }

            if ($token && $token[0] !== '-') {
                return Flags::escapeToken($token);
            }

            return $token;
        }, $this->tokens);

        return implode(' ', $tokens);
    }

    /**
     * Read input information
     * @param  string $question 若不为空，则先输出文本消息
     * @param  bool   $nl true 会添加换行符 false 原样输出，不添加换行符
     * @return string
     */
    public function read(string $question = '', bool $nl = false): string
    {
        if ($question) {
            fwrite(STDOUT, $question . ($nl ? "\n" : ''));
        }

        return trim(fgets($this->inputStream));
    }

    /***********************************************************************************
     * getter/setter
     ***********************************************************************************/

    /**
     * @return string
     */
    public function getFullCommand(): string
    {
        return $this->script . ' ' . $this->command;
    }

    /**
     * @return string
     */
    public function getScriptName(): string
    {
        return $this->script;
    }

    /**
     * @return string
     */
    public function getBinName(): string
    {
        return $this->script;
    }

    /**
     * @return resource
     */
    public function getInputStream()
    {
        return $this->inputStream;
    }
}
