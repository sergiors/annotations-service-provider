<?php

namespace Sergiors\Silex\Tests\Provider;

use Pimple\Container;
use Sergiors\Silex\Provider\DoctrineCacheServiceProvider;
use Sergiors\Silex\Provider\AnnotationsServiceProvider;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Annotations\AnnotationReader;

class AnnotationsServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function register()
    {
        $app = $this->createApplication();
        $app->register(new DoctrineCacheServiceProvider());
        $app->register(new AnnotationsServiceProvider());

        $this->assertInstanceOf(AnnotationReader::class, $app['annotation_reader']);
        $this->assertInstanceOf(CachedReader::class, $app['annotations']);
    }

    public function createApplication()
    {
        return new Container();
    }
}
