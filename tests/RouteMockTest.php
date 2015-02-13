<?php

namespace Creads\Tests\Api2Symfony;

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
     * @expectedExceptionMessage    You must provide a route's path
     */
    public function testNoPath()
    {
        new RouteMock('', 'foo');
    }

    /**
     * @expectedException           Exception
     * @expectedExceptionMessage    You must provide a route's name
     */
    public function testNoName()
    {
        new RouteMock('/foo', '');
    }
}