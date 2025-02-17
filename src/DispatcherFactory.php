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

use Hyperf\Apidog\Annotation\ApiController;
use Hyperf\Apidog\Annotation\ApiVersion;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Mapping;
use Hyperf\HttpServer\Router\DispatcherFactory as HyperfDispatcherFactory;

class DispatcherFactory extends HyperfDispatcherFactory
{
    protected function apiController(string $className, Controller $annotation, array $methodMetadata, array $middlewares = []): void
    {
        if (! $methodMetadata) {
            return;
        }
        $router = $this->getRouter($annotation->server);

        /** @var ApiVersion $version */
        $version = AnnotationCollector::list()[$className]['_c'][ApiVersion::class] ?? null;
        foreach ($methodMetadata as $methodName => $values) {
            $methodMiddlewares = $middlewares;
            // Handle method level middlewares.
            if (isset($values)) {
                $methodMiddlewares = array_merge($methodMiddlewares, $this->handleMiddleware($values));
                $methodMiddlewares = array_unique($methodMiddlewares);
            }

            foreach ($values as $mapping) {
                if (! ($mapping instanceof Mapping)) {
                    continue;
                }
                if (! isset($mapping->methods)) {
                    continue;
                }

                $tokens = [$version ? $version->version : null, $annotation->prefix, $mapping->path];
                $tokens = array_map(function ($item) {
                    return ltrim($item, '/');
                }, array_filter($tokens));
                $path = '/' . implode('/', $tokens);
                $router->addRoute($mapping->methods, $path, [$className, $methodName], [
                    'middleware' => $methodMiddlewares,
                ]);
                //别名
                if($mapping->alias){
                	$aliases = explode(',',$mapping->alias);
                	foreach ($aliases as $alias){
		                $tokens = [$version ? $version->version : null, $annotation->prefix, $alias];
		                $tokens = array_map(function ($item) {
			                return ltrim($item, '/');
		                }, array_filter($tokens));
		                $alias = '/' . implode('/', $tokens);
		                $router->addRoute($mapping->methods, $alias, [$className, $methodName], [
			                'middleware' => $methodMiddlewares,
		                ]);
	                }
                }
            }
        }
        

    }

    protected function initAnnotationRoute(array $collector): void
    {
        foreach ($collector as $className => $metadata) {
            if (isset($metadata['_c'][ApiController::class])) {
                $middlewares = $this->handleMiddleware($metadata['_c']);
                $this->apiController($className, $metadata['_c'][ApiController::class], $metadata['_m'] ?? [], $middlewares);
            }
        }
        parent::initAnnotationRoute($collector);
    }
}
