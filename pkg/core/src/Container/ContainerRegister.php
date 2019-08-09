<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/7/6
 * Time: 22:10
 */

namespace Core\Container;


use Core\Annotation\AnnotationRegister;
use Core\Container\Mapping\Bean;

class ContainerRegister
{
    /**
     * parse annotaion register to container
     */
    public static function parse()
    {
        $annotation = AnnotationRegister::getAnnotations();
        if(empty($annotation)){
            return;
        }
        foreach ($annotation as $componentNs){
            foreach ($componentNs as $ns => $parseObj) {
                $anno = $parseObj["annotation"];
                foreach ($anno as $annoObj) {
                    if ($annoObj instanceof Bean) {
                        Container::getInstance()->create($ns);
                    }
                }
            }
        }

    }

}