<?php declare(strict_types=1);


namespace Log\Handler;


use Monolog\Formatter\FormatterInterface;
use Monolog\Handler\AbstractProcessingHandler;

/**
 * Class CFileHandler
 *
 * @since 2.0
 */
class CFileHandler extends AbstractProcessingHandler
{
    /**
     * Write log levels
     *
     * @var array
     */
    protected $levels = [];

    /**
     * Write log file
     *
     * @var string
     */
    protected $logFile = '';

    /**
     * @param array $record
     */
    protected function write(array $record): void
    {
//        var_dump($record);
    }

    /**
     * @param FormatterInterface $formatter
     */
    public function setFormatter(FormatterInterface $formatter): void
    {
        $this->formatter = $formatter;
    }

    /**
     * @param array $levels
     */
    public function setLevels(array $levels): void
    {
        $this->levels = $levels;
    }

    /**
     * @param string $logFile
     */
    public function setLogFile(string $logFile): void
    {
        $this->logFile = $logFile;
    }
}