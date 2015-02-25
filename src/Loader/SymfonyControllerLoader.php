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
class SymfonyControllerLoader implements LoaderInterface
{
    /**
     * @param string $path Path to directory where to find controller classes
     *
     * @throws InvalidArgumentException If the path does not exists
     * @throws InvalidArgumentException If there is no class in the file
     *
     * @return Gnugat\Medio\Model\File[]
     */
    public function load($path)
    {
        if (!is_dir($path)) {

            throw new \InvalidArgumentException(sprintf('Directory %s does not exist', $path));
        }

    }
}
