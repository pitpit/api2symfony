<?php

namespace Creads\Tests\Api2Symfony\Mock;

use Creads\Api2Symfony\Mock\ActionMock;

class ActionMockTest extends \PHPUnit_Framework_TestCase
{
    protected function getMockedRoute()
    {
        return $this->getMock('Creads\Api2Symfony\Mock\RouteMock', array(), array('/route_path', 'route_name'));
    }

    protected function getMockedResponse()
    {
        $mock = $this->getMock('Creads\Api2Symfony\Mock\ResponseMock', array(), array(200, ''));
        $mock->method('getCode')->willReturn(200);

        return $mock;
    }

    public function testConstruct()
    {
        $route = $this->getMockedRoute();
        $action = new ActionMock('myname', $route, 'get', 'my description');

        $this->assertEquals('myname', $action->getName());
        $this->assertSame($route, $action->getRoute());
        $this->assertEquals('get', $action->getMethod());
        $this->assertEquals('my description', $action->getDescription());
    }

    public function testConstructDefaultValue()
    {
        $route = $this->getMockedRoute();
        $action = new ActionMock('myname', $route, 'get');
        $this->assertEquals('', $action->getDescription());
        $this->assertEquals(array(), $action->getParameters());
    }

    public function testAddParameterAndGetParameters()
    {
        $route = $this->getMockedRoute();
        $action = new ActionMock('myname', $route, 'get', 'my description');
        $action->addParameter('my-parameter');

        $this->assertEquals(array('my-parameter'), $action->getParameters());
    }

    public function testSetResponseAndGetResponse()
    {
        $route = $this->getMockedRoute();
        $action = new ActionMock('myname', $route, 'get', 'my description');
        $response = $this->getMockedResponse();
        $action->addResponse($response);

        $this->assertSame($response, $action->getResponses()[0]);
    }

    /**
     * @expectedException           Exception
     */
    public function testNoName()
    {
        $route = $this->getMockedRoute();
        new ActionMock('', $route, 'get');
    }

    /**
     * @expectedException           Exception
     */
    public function testNoMethod()
    {
        $route = $this->getMockedRoute();
        new ActionMock('test', $route, '');
    }
}