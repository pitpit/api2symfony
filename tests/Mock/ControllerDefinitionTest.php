<?php

namespace Creads\Tests\Api2Symfony\Mock;

use Creads\Api2Symfony\Mock\ControllerDefinition;

class ControllerDefinitionTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $controller = new ControllerDefinition('mycontroller', 'A\Namespace', 'a description');

        $this->assertEquals('mycontroller', $controller->getName());
        $this->assertEquals('mycontroller', $controller->getShortClassName());
        $this->assertEquals('A\Namespace', $controller->getNamespace());
        $this->assertEquals('a description', $controller->getDescription());
    }

    public function testConstructDefaultValues()
    {
        $controller = new ControllerDefinition('my controller');

        $this->assertEquals('', $controller->getNamespace());
        $this->assertEquals('', $controller->getDescription());
    }

    public function testAddActionAndGetActions()
    {
        $controller = new ControllerDefinition('my controller');
        $route = $this->getMock('Creads\Api2Symfony\Mock\RouteDefinition', array(), array('/route_path', 'route_name'));
        $action = $this->getMock('Creads\Api2Symfony\Mock\ActionDefinition', array(), array('myname', $route, 'get', 'my description'));

        $controller->addAction($action);

        $this->assertEquals(array($action), $controller->getActions());
    }

    /**
     * @expectedException           Exception
     */
    public function testNoName()
    {
        new ControllerDefinition('');
    }
}