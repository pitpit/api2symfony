<?php

namespace Creads\Api2Symfony\Converter;

/**
 * @author Quentin <q.pautrat@creads.org>
 */
interface ConverterInterface
{
    /**
     * Generate controller from a given file
     *
     * @param  string  $filepath  Specification filepath
     * @param  string  $namespace Base namespace for controllers
     *
     * @return array                A list of ControllerDefinition
     */
    public function generate($filepath, $namespace);
}
