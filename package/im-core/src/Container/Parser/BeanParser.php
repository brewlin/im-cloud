<?php declare(strict_types=1);

namespace Core\Container\Parser;

use Core\Annotation\Mapping\AnnotationParser;
use Core\Annotation\Parser\Parser;
use Core\Container\Mapping\Bean;

/**
 * Class BeanParser
 *
 * @AnnotationParser(Bean::class)
 *
 */
class BeanParser extends Parser
{
    /**
     * Parse object
     *
     * @param int  $type
     * @param Bean $annotationObject
     *
     * @return array
     */
    public function parse(int $type, $annotationObject): array
    {
        // Only to parse class annotation with `@Bean`
        if ($type != self::TYPE_CLASS) {
            return [];
        }

        $name  = $annotationObject->getName();
        $scope = $annotationObject->getScope();
        $alias = $annotationObject->getAlias();

        return [$name, $this->className, $scope, $alias];
    }
}