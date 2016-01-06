<?php
namespace Sergiors\Silex\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Annotations\AnnotationReader;

/**
 * @author Sérgio Rafael Siqueira <sergio@inbep.com.br>
 */
class AnnotationsServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['annotations.debug'] = false;
        $app['annotations.options'] = [
            'cache_driver' => 'array',
            'cache_dir' => null
        ];

        $app['annotations.reader'] = $app->share(function () {
            return new AnnotationReader();
        });

        $app['annotations.cached_reader.factory'] = $app->protect(function ($options) use ($app) {
            if (!isset($app['cache'])) {
                throw new \LogicException(
                    'You must register the DoctrineCacheServiceProvider to use the AnnotationServiceProvider.'
                );
            }

            return $app['cache.factory']($options['cache_driver'], $options);
        });

        $app['annotations.cached_reader'] = $app->share(function (Application $app) {
            return new CachedReader(
                $app['annotations.reader'],
                $app['annotations.cached_reader.factory']($app['annotations.options']),
                $app['annotations.debug']
            );
        });

        $app['annotation_reader'] = $app->share(function (Application $app) {
            return $app['annotations.cached_reader'];
        });
    }

    public function boot(Application $app)
    {
    }
}
