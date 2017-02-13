<?php

namespace Sergiors\Silex\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Annotations\AnnotationReader;

/**
 * @author Sérgio Rafael Siqueira <sergio@inbep.com.br>
 */
class AnnotationsServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['annotation_reader'] = function () use ($app) {
            return new AnnotationReader();
        };

        $app['annotations.cached_reader.factory'] = $app->protect(function ($options) use ($app) {
            if (!isset($app['cache'])) {
                throw new \LogicException(
                    'You must register the DoctrineCacheServiceProvider to use the AnnotationServiceProvider.'
                );
            }

            return $app['cache_factory']($options['cache_driver'], $options);
        });

        $app['annotations.cached_reader'] = function () use ($app) {
            return new CachedReader(
                $app['annotation_reader'],
                $app['annotations.cached_reader.factory']($app['annotations.options']),
                $app['annotations.debug']
            );
        };

        $app['annotations'] = function () use ($app) {
            return $app['annotations.cached_reader'];
        };

        $app['annotations.debug'] = false;
        $app['annotations.options'] = [
            'cache_driver' => 'array',
            'cache_dir' => null,
        ];
    }
}
