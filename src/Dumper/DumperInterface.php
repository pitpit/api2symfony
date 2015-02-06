<?php

namespace Creads\Api2Symfony\Dumper;

use Creads\Api2Symfony\SymfonyController;

interface DumperInterface
{
    /**
     * Does a file exist in $destination directory for $controller
     *
     * @param  SymfonyController $controller
     * @param  string            $destination
     *
     * @return boolean
     */
    public function exists(SymfonyController $controller, $destination = '.');

    /**
     * Dump $controller as a file into $destinaton directory
     *
     * @param  SymfonyController $controller
     * @param  string            $destination
     *
     * @return array where key is a filename and value the output
     */
    public function dump(SymfonyController $controller, $destination = '.');
}
