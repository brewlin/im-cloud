<?php declare(strict_types=1);


namespace Core\Config\Parser;


use Core\Config\Config;

/**
 * Interface ParserInterface
 */
interface ParserInterface
{
    /**
     * Parse files
     *
     * @param Config $config
     *
     * @return array
     */
    public function parse(Config $config):array ;
}