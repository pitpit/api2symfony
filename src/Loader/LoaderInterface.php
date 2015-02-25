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
     * Load specification from existing controllers
     *
     * @param mixed $resource
     *
     * @return
     */
    public function load($resource);
}
