<?php
namespace Inbep\Silex\Provider;

use Silex\Application;
use Silex\WebTestCase;
use Doctrine\Common\Annotations\AnnotationReader;

class AnnotationServiceProviderTest extends WebTestCase
{
    /**
     * @test
     */
    public function register()
    {
        $app = $this->createApplication();
        $app->register(new AnnotationServiceProvider());

        $this->assertInstanceOf(AnnotationReader::class, $app['annotations.reader']);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function shouldReturnInvalidArgumentException()
    {
        $app = $this->createApplication();
        $app->register(new AnnotationServiceProvider(), [
            'annotations.options' => [
                'cache_driver' => 'filesystem'
            ]
        ]);
        $app['annotation_reader'];
    }

    public function createApplication()
    {
        $app = new Application();
        $app['debug'] = true;
        $app['exception_handler']->disable();
        return $app;
    }
}
