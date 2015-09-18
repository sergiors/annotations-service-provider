<?php
namespace Inbep\Silex\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\ApcCache;
use Doctrine\Common\Cache\XcacheCache;

/**
 * @author Sérgio Rafael Siqueira <sergio@inbep.com.br>
 */
class AnnotationServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['annotations.cache_dir'] = null;
        $app['annotations.cache_type'] = null;
        $app['annotations.debug'] = false;

        $app['annotations.reader'] = $app->share(function () {
            return new AnnotationReader();
        });

        $app['annotations.cached_reader.default'] = $app->share(function (Application $app) {
            $type = $app['annotations.cache_type'];

            if ('filesystem' === $type) {
                return $app['annotations.cached_reader.filesystem'];
            }

            if ('apc' === $type) {
                return $app['annotations.cached_reader.apc'];
            }
            
            if ('xcache' === $type) {
                return $app['annotations.cached_reader.xcache'];
            }

            return new ArrayCache();
        });

        $app['annotations.cached_reader.filesystem'] = $app->share(function (Application $app) {
            if (!is_dir($app['annotations.cache_dir'])) {
                throw new \InvalidArgumentException('The parameter "annotations.cache_dir" must be a directory.');
            }

            return new FilesystemCache($app['annotations.cache_dir']);
        });

        $app['annotations.cached_reader.apc'] = $app->share(function () {
            return new ApcCache();
        });

        $app['annotations.cached_reader.xcache'] = $app->share(function () {
            return new XcacheCache();
        });

        $app['annotations.cached_reader'] = $app->share(function (Application $app) {
            return new CachedReader(
                $app['annotations.reader'],
                $app['annotations.cached_reader.default'],
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
