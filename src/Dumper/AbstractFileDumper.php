<?php

namespace Creads\Api2Symfony\Dumper;

use Symfony\Component\Filesystem\Filesystem;
use Creads\Api2Symfony\Mock\ControllerMock;
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
     * Get the controller PSR-0 filepath
     *
     * @return string
     */
    protected function getFilepath(ControllerMock $controller, $destination)
    {
        return $destination . '/' .$controller->getShortClassName() . '.php';
    }

    /**
     * {@inheritDoc}
     */
    public function exists(ControllerMock $controller, $destination = '.')
    {
        $filepath = $this->getFilepath($controller, $destination);

        return $this->filesystem->exists($filepath);
    }

    /**
     * Render a controller
     *
     * @retirn string
     */
    abstract protected function render(ControllerMock $controller);

    /**
     * {@inheritDoc}
     */
    public function dump(ControllerMock $controller, $destination = '.')
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
