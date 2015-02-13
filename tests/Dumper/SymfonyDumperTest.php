<?php

namespace Creads\Tests\Api2Symfony\Converter;

use Raml\Parser;
use Creads\Api2Symfony\Dumper\SymfonyDumper;
use Creads\Api2Symfony\Mock\ControllerDefinition;
use Symfony\Component\Filesystem\Filesystem;

class SymfonyDumperTest extends \PHPUnit_Framework_TestCase
{
    protected $dumper;

    protected function setUp()
    {
        $this->dumper = new SymfonyDumper();

        $this->dir = sys_get_temp_dir() . '/api2symfony.test';
        $fs = new Filesystem();
        $fs->mkdir($this->dir);
    }

    protected function tearDown()
    {
      $fs = new Filesystem();
      $fs->remove($this->dir);
    }

    public function testExists()
    {
        $controller = new ControllerDefinition('MyController');

        @unlink($this->dir . '/MyController.php');

        $this->assertFalse($this->dumper->exists($controller, $this->dir));

        @touch($this->dir . '/MyController.php');

        $this->assertTrue($this->dumper->exists($controller, $this->dir));
    }

    public function testDump()
    {
        $controller = new ControllerDefinition('MyController');
        $filename = $this->dumper->dump($controller, $this->dir);

        $this->assertTrue(file_exists($this->dir . '/MyController.php'));
        $this->assertEquals($this->dir . '/MyController.php', $filename);
    }

    public function testDumpWithRestore()
    {
        @file_put_contents($this->dir . '/MyController.php', 'test');

        $controller = new ControllerDefinition('MyController');
        $filename = $this->dumper->dump($controller, $this->dir);

        $this->assertTrue(file_exists($this->dir . '/MyController.php'));
        $this->assertNotEquals('test', @file_get_contents($this->dir . '/MyController.php'));

        $this->assertTrue(file_exists($this->dir . '/MyController.php.old'), 'A copy of the old file has been created');
        $this->assertEquals('test', @file_get_contents($this->dir . '/MyController.php.old'));
    }

    public function testDumpWithSeveralRestore()
    {
        @file_put_contents($this->dir . '/MyController.php', 'test');
        @file_put_contents($this->dir . '/MyController.php.old', 'test2');

        $controller = new ControllerDefinition('MyController');
        $filename = $this->dumper->dump($controller, $this->dir);

        $this->assertTrue(file_exists($this->dir . '/MyController.php.old~2'), 'A new copy of the old file has been created');
        $this->assertEquals('test', @file_get_contents($this->dir . '/MyController.php.old~2'));
    }
}