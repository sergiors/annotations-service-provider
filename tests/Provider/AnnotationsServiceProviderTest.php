<?php

namespace Sergiors\Pimple\Tests\Provider;

use Pimple\Container;
use Sergiors\Pimple\Provider\DoctrineCacheServiceProvider;
use Sergiors\Pimple\Provider\AnnotationsServiceProvider;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Annotations\AnnotationReader;

class AnnotationsServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function register()
    {
        $container = new Container();
        $container->register(new DoctrineCacheServiceProvider());
        $container->register(new AnnotationsServiceProvider());

        $this->assertInstanceOf(AnnotationReader::class, $container['annotation_reader']);
        $this->assertInstanceOf(CachedReader::class, $container['annotations']);
    }
}
