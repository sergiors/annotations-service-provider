<?php
namespace Inbep\Silex\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\RedisCache;
use Doctrine\Common\Cache\ApcCache;
use Doctrine\Common\Cache\XcacheCache;

/**
 * @author Sérgio Rafael Siqueira <sergio@inbep.com.br>
 */
class AnnotationServiceProvider implements ServiceProviderInterface
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
            switch ($options['cache_driver']) {
                case 'array':
                    return $app['annotations.cached_reader.array']();
                    break;
                case 'filesystem':
                    return $app['annotations.cached_reader.filesystem']($options);
                    break;
                case 'apc':
                    return $app['annotations.cached_reader.apc']();
                    break;
                case 'xcache':
                    return $app['annotations.cached_reader.xcache']();
                    break;
                case 'redis':
                    return $app['annotations.cached_reader.redis']($options);
                    break;
            }

            throw new \RuntimeException();
        });

        $app['annotations.cached_reader.filesystem'] = $app->protect(function ($options) {
            if (empty($options['cache_dir']) || !is_dir($options['cache_dir'])) {
                throw new \RuntimeException(
                    'You must specify "cache_dir" for Filesystem.'
                );
            }

            return new FilesystemCache($options['cache_dir']);
        });

        $app['annotations.cached_reader.apc'] = $app->protect(function () {
            return new ApcCache();
        });

        $app['annotations.cached_reader.xcache'] = $app->protect(function () {
            return new XcacheCache();
        });

        $app['annotations.cached_reader.array'] = $app->protect(function () {
            return new ArrayCache();
        });

        $app['annotations.cached_reader.redis'] = $app->protect(function ($options) {
            if (empty($options['host']) || empty($options['port'])) {
                throw new \RuntimeException('You must specify "host" and "port" for Redis.');
            }

            $redis = new \Redis();
            $redis->connect($options['host'], $options['port']);

            if (isset($options['password'])) {
                $redis->auth($options['password']);
            }

            $cache = new RedisCache();
            $cache->setRedis($redis);
            return $cache;
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
