<?php

namespace Creads\Api2Symfony;

/**
 * @author Quentin <q.pautrat@creads.org>
 */
class SymfonyController
{
    /**
     * Class name
     *
     * @var string
     */
    private $class;

    /**
     * Base namespace
     *
     * @var string
     */
    private $namespace;

    /**
     * Description
     *
     * @var string
     */
    private $description;

    /**
     * Actions
     *
     * @var array
     */
    private $actions;

    /**
     * Constructor
     * @param string $class     Short class name
     * @param string $namespace     Base namespace
     * @param string $description   Description
     */
    public function __construct($class, $namespace = '', $description = '')
    {
        if (!$class || empty($class)) {
            throw new \Exception('Class cannot be empty');
        }

        $this->class = $class;
        $this->namespace = $namespace;
        $this->description = $description;
        $this->actions = array();
    }

    /**
     * Gets namespace
     *
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Returns the class name without namespace
     *
     * @return string
     */
    public function getShortClassName()
    {
        return $this->class;
    }

    /**
     * Returns the class name without namespace
     *
     * @deprecated
     *
     * @return string
     */
    public function getName()
    {
        return $this->getShortClassName();
    }

    /**
     * Returns the fully qualified name of the class
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->namespace . '\\' . $this->class;
    }

    /**
     * Gets description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Add an action to controller. If an action with the same name alreaday exists, it will erase it.
     *
     * @param SymfonyAction $action An array of SymfonyAction
     */
    public function addAction(SymfonyAction $action)
    {
        $this->actions[] = $action;
    }

    /**
     * Gets actions
     *
     * @return array An array of SymfonyAction
     */
    public function getActions()
    {
        return $this->actions;
    }
}
