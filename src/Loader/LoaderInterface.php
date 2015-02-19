<?php

namespace Creads\Api2Symfony\Loader;

/**
 * A loader will load a resource and return the correspondind class definition
 *
 * @author Damien Pitard <damien.pitard@gmail.com>
 */
interface LoaderInterface
{
    /**
     * Does the resource exist
     *
     * @param mixed $resource
     *
     * @return boolean
     */
    public function exists($resource);

    /**
     * Load specification from existing controllers
     *
     * @param mixed $resource
     *
     * @return Creads\Api2Symfony\Definition\Definition
     */
    public function load($resource);
}
