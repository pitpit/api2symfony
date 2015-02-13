<?php

namespace Creads\Tests\Api2Symfony\Mock;

use Creads\Api2Symfony\Mock\RouteMock;

class RouteMockTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $route = new RouteMock('a/path/to', 'myroute');

        $this->assertEquals('myroute', $route->getName());
        $this->assertEquals('a/path/to', $route->getPath());
    }

    /**
     * @expectedException           Exception
     */
    public function testNoPath()
    {
        new RouteMock('', 'foo');
    }

    /**
     * @expectedException           Exception
     */
    public function testNoName()
    {
        new RouteMock('/foo', '');
    }
}