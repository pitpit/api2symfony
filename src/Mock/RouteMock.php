<?php

namespace Creads\Api2Symfony\Mock;

/**
 * This class abstracts a route definition (related to an action) guessed from API specification
 *
 * @author Quentin <q.pautrat@creads.org>
 */
class RouteMock
{
    /**
     * Path
     *
     * @var string
     */
    private $path;

    /**
     * Name
     *
     * @var string
     */
    private $name;

    /**
     * Constructor
     *
     * @param string $path
     * @param string $name
     */
    public function __construct($path, $name)
    {
        if (!$path || empty($path)) {
            throw new \Exception("You must provide a path for the route");
        }

        if (!$name || empty($name)) {
            throw new \Exception("You must provide a name for the route");
        }

        $this->path = $path;
        $this->name = $name;
    }

    /**
     * Gets path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Gets name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
