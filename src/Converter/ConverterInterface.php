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
     * @return array                A list of SymfonyController
     */
    public function generate($filepath, $namespace);

    /**
     * Generate controller from a given file
     *
     * @param  string   $newFilepath New specification filepath
     * @param  string   $oldFilepath Old specification filepath
     * @param  string   $namespace   Base namespace for controllers
     *
     * @return array                A list of SymfonyController
     */
    public function update($newFilepath, $oldFilepath, $namespace);
}
