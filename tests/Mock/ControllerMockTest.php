<?php

namespace Creads\Tests\Api2Symfony\Mock;

use Creads\Api2Symfony\Mock\ControllerMock;

class ControllerMockTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $controller = new ControllerMock('mycontroller', 'A\Namespace', 'a description');

        $this->assertEquals('mycontroller', $controller->getName());
        $this->assertEquals('mycontroller', $controller->getShortClassName());
        $this->assertEquals('A\Namespace', $controller->getNamespace());
        $this->assertEquals('a description', $controller->getDescription());
    }

    public function testConstructDefaultValues()
    {
        $controller = new ControllerMock('my controller');

        $this->assertEquals('', $controller->getNamespace());
        $this->assertEquals('', $controller->getDescription());
    }

    public function testAddActionAndGetActions()
    {
        $controller = new ControllerMock('my controller');
        $route = $this->getMock('Creads\Api2Symfony\Mock\RouteMock', array(), array('/route_path', 'route_name'));
        $action = $this->getMock('Creads\Api2Symfony\Mock\ActionMock', array(), array('myname', $route, 'get', 'my description'));

        $controller->addAction($action);

        $this->assertEquals(array($action), $controller->getActions());
    }

    /**
     * @expectedException           Exception
     */
    public function testNoName()
    {
        new ControllerMock('');
    }
}