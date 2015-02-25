<?php

namespace Creads\Tests\Api2Symfony\Converter;

use Creads\Api2Symfony\Dumper\SymfonyDumper;
use Creads\Api2Symfony\Mock\ControllerMock;
use Symfony\Component\Filesystem\Filesystem;


use Gnugat\Medio\PrettyPrinter;
use Gnugat\Medio\TwigExtension\Line\Line;
use Gnugat\Medio\TwigExtension\Line\ContractLineStrategy;
use Gnugat\Medio\TwigExtension\Line\FileLineStrategy;
use Gnugat\Medio\TwigExtension\Line\ObjectLineStrategy;
use Gnugat\Medio\TwigExtension\Phpdoc;
use Gnugat\Medio\TwigExtension\Type;
use Gnugat\Medio\TwigExtension\Whitespace;

class SymfonyDumperTest extends \PHPUnit_Framework_TestCase
{
    protected $dumper;

    protected function setUp()
    {
        $line = new Line();
        $line->add(new ContractLineStrategy());
        $line->add(new FileLineStrategy());
        $line->add(new ObjectLineStrategy());

        $loader = new \Twig_Loader_Filesystem(__DIR__.'/../../vendor/gnugat/medio/templates');
        $twig = new \Twig_Environment($loader);
        $twig->addExtension(new Phpdoc());
        $twig->addExtension(new Type());
        $twig->addExtension(new Whitespace($line));



        $prettyPrinter = new PrettyPrinter($twig);
        $this->dumper = new SymfonyDumper($prettyPrinter);

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
        $controller = new ControllerMock('MyController');

        @unlink($this->dir . '/MyController.php');

        $this->assertFalse($this->dumper->exists($controller, $this->dir));

        @touch($this->dir . '/MyController.php');

        $this->assertTrue($this->dumper->exists($controller, $this->dir));
    }

    public function testDump()
    {
        $controller = new ControllerMock('MyController');
        $filename = $this->dumper->dump($controller, $this->dir);

        $this->assertTrue(file_exists($this->dir . '/MyController.php'));
        $this->assertEquals($this->dir . '/MyController.php', $filename);
    }

    public function testDumpWithRestore()
    {
        @file_put_contents($this->dir . '/MyController.php', 'test');

        $controller = new ControllerMock('MyController');
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

        $controller = new ControllerMock('MyController');
        $filename = $this->dumper->dump($controller, $this->dir);

        $this->assertTrue(file_exists($this->dir . '/MyController.php.old~2'), 'A new copy of the old file has been created');
        $this->assertEquals('test', @file_get_contents($this->dir . '/MyController.php.old~2'));
    }
}