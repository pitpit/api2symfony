<?php

namespace Creads\Api2Symfony\Loader;

use Symfony\Component\HttpFoundation\Request;
use Creads\Api2Symfony\Definition\Definition;
use Creads\Api2Symfony\Definition\Method;
use Creads\Api2Symfony\Definition\Property;

use DocDigital\Lib\SourceEditor\TokenParser;
use DocDigital\Lib\SourceEditor\PhpClassEditor;
/**
 * Load definition from an existent controllers
 *
 * @author Damien Pitard <damien.pitard@gmail.com>
 */
class SymfonyLoader implements LoaderInterface
{
    /**
     * @param string $class Fully qualified controller class name
     *
     * @return boolean
     */
    public function exists($filepath)
    {
        return (file_exists($filepath));
    }

    /**
     * @param string $filepath Filepath to class
     *
     * @throws InvalidArgumentException If the class does not exist
     *
     * @return DocDigital\Lib\SourceEditor\ClassStructure\ClassElement
     */
    public function load($filepath)
    {
        if (!$this->exists($filepath)) {

            throw new \InvalidArgumentException(sprintf('File %s does not exist', $filepath));
        }

        //....

        return ;
    }
}
