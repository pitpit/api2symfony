<?php

namespace Creads\Api2Symfony\Loader;

use Symfony\Component\HttpFoundation\Request;
use Creads\Api2Symfony\Definition\Definition;
use Creads\Api2Symfony\Definition\Method;
use Creads\Api2Symfony\Definition\Property;

/**
 * Load definition from an existent controllers
 *
 * @author Damien Pitard <damien.pitard@gmail.com>
 */
class SymfonyLoader implements LoaderInterface
{
    /**
     * @param string $class Fully qualified controller class name
     *
     * @return boolean
     */
    public function exists($class)
    {
        return (class_exists($class));
    }

    /**
     * @param string $class Fully qualified controller class name
     *
     * @throws InvalidArgumentException If the class does not exist
     *
     * @return Definition
     */
    public function load($class)
    {
        if (!$this->exists($class)) {

            throw new \InvalidArgumentException(sprintf('Class %s does not exist', $class));
        }

        $reflectionClass = new \ReflectionClass($class);

        $definition = new Definition($class);
        $definition->setParentClass($reflectionClass->getParentClass());

        foreach ($reflectionClass->getMethods() as $reflectionMethod) {
            if ($reflectionMethod->isPublic()) {
                //@todo filters method that does not match pattern

                $parameters = [];
                foreach ($reflectionMethod->getParameters() as $parameter) {
                    $class = $parameter->getClass();

                    //@todo add the parameter class to uses

                    $parameters[] = $class->getShortName() . ' $' .$parameter->getName();
                }
                $parameters = implode(',', $parameters);

                $method = new Method('public', $reflectionMethod->getName(), $parameters, '');
                $definition->addMethod($method);
            }
        }

        return $definition;
    }
}
