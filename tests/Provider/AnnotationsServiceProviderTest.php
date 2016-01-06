<?php
namespace Sergiors\Silex\Provider;

use Silex\Application;
use Silex\WebTestCase;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Annotations\AnnotationReader;

class AnnotationsServiceProviderTest extends WebTestCase
{
    /**
     * @test
     */
    public function register()
    {
        $app = $this->createApplication();
        $app->register(new DoctrineCacheServiceProvider());
        $app->register(new AnnotationsServiceProvider());

        $this->assertInstanceOf(AnnotationReader::class, $app['annotations.reader']);
        $this->assertInstanceOf(CachedReader::class, $app['annotation_reader']);
    }

    public function createApplication()
    {
        $app = new Application();
        $app['debug'] = true;
        $app['exception_handler']->disable();
        return $app;
    }
}
