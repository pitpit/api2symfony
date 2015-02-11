<?php

namespace Creads\Api2Symfony\Dumper;

use Symfony\Component\Filesystem\Filesystem;
use Creads\Api2Symfony\SymfonyController;
use Twig_Environment;

/**
 * Dump a controller
 *
 * @author Damien Pitard <d.pitard@creads.org>
 */
abstract class AbstractFileDumper implements DumperInterface
{
    /**
     * Filesystem
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Initialize dumper.
     */
    public function __construct()
    {
        $this->filesystem = new Filesystem();
    }

    /**
     * Get the controller filepath
     *
     * @return string
     */
    protected function getFilepath(SymfonyController $controller, $destination)
    {
        return $destination . '/' .$controller->getName() . '.php';
    }

    /**
     * {@inheritDoc}
     */
    public function exists(SymfonyController $controller, $destination = '.')
    {
        $filepath = $this->getFilepath($controller, $destination);

        return $this->filesystem->exists($filepath);
    }

    /**
     * Render a controller
     *
     * @retirn string
     */
    abstract protected function render(SymfonyController $controller);

    /**
     * {@inheritDoc}
     */
    public function dump(SymfonyController $controller, $destination = '.')
    {
        $output = $this->render($controller);

        if (!$this->filesystem->exists($destination)) {
            throw new \InvalidArgumentException(sprintf('Folder %s does not exist', $destination));
        }

        $filepath = $this->getFilepath($controller, $destination);

        if ($this->filesystem->exists($filepath)) {

            $old = $filepath . '.old';
            $i = 1;
            while ($this->filesystem->exists($old)) {
                $i++;
                $old = $filepath . '.old~' .$i;
            }

            $this->filesystem->copy($filepath, $old);
        }

        $this->filesystem->dumpFile($filepath, $output);

        return $filepath;
    }
}
