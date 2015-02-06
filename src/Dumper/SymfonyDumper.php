<?php

namespace Creads\Api2Symfony\Dumper;

use Symfony\Component\Filesystem\Filesystem;
use Creads\Api2Symfony\SymfonyController;
use Twig_Loader_Filesystem;
use Twig_Environment;

/**
 * Dump a symfony controller
 *
 * @author Quentin <q.pautrat@creads.org>
 * @author Damien Pitard <d.pitard@creads.org>
 */
class SymfonyDumper extends AbstractFileDumper
{
    /**
     * Twig
     *
     * @var Twig_Environment
     */
    protected $twig;

    /**
     * Initialize dumper.
     * We don't use injection because we don't want the engine to be overriden
     */
    public function __construct()
    {
        $loader = new Twig_Loader_Filesystem(__DIR__ . '/../Resources/templates/symfony');
        $this->twig = new Twig_Environment($loader);

        parent::__construct();
    }

    /**
     * {@inheritDoc}
     */
    protected function render(SymfonyController $controller)
    {
        $template = $this->twig->loadTemplate('controller.php.twig');
        if (!$template) {
            throw new \Exception('Unable to find template');
        }

        return $template->render(array('controller' => $controller));
    }
}
