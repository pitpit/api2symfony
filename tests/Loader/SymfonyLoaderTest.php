<?php

namespace Creads\Tests\Api2Symfony\Loader;

use Creads\Api2Symfony\Loader\SymfonyLoader;
use Creads\Tests\Api2Symfony\Fixtures\Controller\Test1LoaderController;

class SymfonyControllerLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testExists()
    {
        $loader = new SymfonyLoader();

        $this->assertTrue($loader->exists(__DIR__ . '/../Fixtures/Controller/Test1LoaderController.php'));
        $this->assertFalse($loader->exists(__DIR__ . '/../unknown.php'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testLoadException()
    {
        $loader = new SymfonyLoader();

        $definition = $loader->load('This\Class\Does\Not\Exists');
    }

    public function testLoad()
    {
        $loader = new SymfonyLoader();

        $classElement = $loader->load(__DIR__ . '/../Fixtures/Controller/Test1LoaderController.php');

        $this->assertInstanceOf('DocDigital\Lib\SourceEditor\ClassStructure\ClassElement', $classElement);


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
