<?php declare(strict_types=1);

namespace Core\Http\Request;

/**
 * Class RequestParserInterface
 */
interface RequestParserInterface
{
    /**
     * @param string $content
     *
     * @return mixed
     */
    public function parse(string $content);
}
