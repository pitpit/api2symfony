<?php

namespace Creads\Api2Symfony\Dumper;

use Creads\Api2Symfony\Mock\ControllerMock;

interface DumperInterface
{
    /**
     * Does a file exist in $destination directory for $controller
     *
     * @param  ControllerMock $controller
     * @param  string            $destination
     *
     * @return boolean
     */
    public function exists(ControllerMock $controller, $destination = '.');

    /**
     * Dump $controller as a file into $destinaton directory
     *
     * @param  ControllerMock $controller
     * @param  string            $destination
     *
     * @return array where key is a filename and value the output
     */
    public function dump(ControllerMock $controller, $destination = '.');
}
