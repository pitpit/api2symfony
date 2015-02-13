<?php

namespace Creads\Tests\Api2Symfony\Mock;

use Creads\Api2Symfony\Mock\RouteDefinition;

class RouteDefinitionTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $route = new RouteDefinition('a/path/to', 'myroute');

        $this->assertEquals('myroute', $route->getName());
        $this->assertEquals('a/path/to', $route->getPath());
    }

    /**
     * @expectedException           Exception
     */
    public function testNoPath()
    {
        new RouteDefinition('', 'foo');
    }

    /**
     * @expectedException           Exception
     */
    public function testNoName()
    {
        new RouteDefinition('/foo', '');
    }
}