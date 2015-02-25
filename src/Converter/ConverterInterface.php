<?php

namespace Creads\Api2Symfony\Converter;

/**
 * Convert a speck to Mock model (see Mock/*)
 *
 * @author Quentin <q.pautrat@creads.org>
 */
interface ConverterInterface
{
    /**
     * Generate mocked controllers in a $namespace from a given file
     *
     * @param string $filepath  Specification filepath
     * @param string $namespace Base namespace for controllers
     *
     * @return ControllerMock[]
     */
    public function generate($filepath, $namespace);
}
