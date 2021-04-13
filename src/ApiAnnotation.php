<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Hyperf\Apidog;

use Doctrine\Common\Annotations\AnnotationReader;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\Di\ReflectionManager;
use Hyperf\Di\BetterReflectionManager;
use Roave\BetterReflection\Reflection\Adapter;

class ApiAnnotation
{
    public static function methodMetadata($className, $methodName)
    {   
        $class = BetterReflectionManager::reflectClass($className);
        $reflectMethod = $class->getMethod($methodName);
        $reader = new AnnotationReader();
        return $reader->getMethodAnnotations(  new Adapter\ReflectionMethod($reflectMethod) );
    }

    public static function classMetadata($className)
    {
        return AnnotationCollector::list()[$className]['_c'] ?? [];
    }
}
