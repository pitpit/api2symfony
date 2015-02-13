<?php

namespace Creads\Api2Symfony\Dumper;

use Creads\Api2Symfony\Mock\ControllerDefinition;

interface DumperInterface
{
    /**
     * Does a file exist in $destination directory for $controller
     *
     * @param  ControllerDefinition $controller
     * @param  string            $destination
     *
     * @return boolean
     */
    public function exists(ControllerDefinition $controller, $destination = '.');

    /**
     * Dump $controller as a file into $destinaton directory
     *
     * @param  ControllerDefinition $controller
     * @param  string            $destination
     *
     * @return array where key is a filename and value the output
     */
    public function dump(ControllerDefinition $controller, $destination = '.');
}
