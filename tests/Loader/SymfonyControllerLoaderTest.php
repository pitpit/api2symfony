<?php

namespace Creads\Tests\Api2Symfony\Loader;

use Creads\Api2Symfony\Loader\SymfonyControllerLoader;
use Creads\Tests\Api2Symfony\Fixtures\Controller\Test1LoaderController;

class SymfonyControllerLoaderLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testLoadException()
    {
        $loader = new SymfonyControllerLoader();

        $definition = $loader->load('This/Path/Does/Not/Exists');
    }

    public function testLoad()
    {
        $loader = new SymfonyControllerLoader();

        $file = $loader->load(__DIR__ . '/../Fixtures/Controller');

        $this->assertInstanceOf('Gnugat\Medio\Model\File', $file);


        // $this->assertEquals('Creads\Tests\Api2Symfony\Fixtures\Controller\Controller', $definition->getParentClass()->getName());

        // $methods = $definition->getMethods();
        // $this->assertEquals(1, count($methods));

        // $method = $methods[0];

        // $this->assertInstanceOf('Creads\Api2Symfony\Definition\Method', $method);
        // $this->assertEquals('public', $method->getVisibility());

        // $arguments = explode(',', $method->getArguments());
        // $this->assertEquals(1, count($arguments));

        // $argument = $arguments[0];
        // $this->assertEquals('Request $request', $argument);

        //@todo check doc comment
        //@todo check uses
    }
}
