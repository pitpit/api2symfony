<?php

namespace Creads\Api2Symfony\Dumper;

use Symfony\Component\Filesystem\Filesystem;
use Creads\Api2Symfony\SymfonyController;
use Creads\Api2Symfony\SymfonyAction;
use Creads\Api2Symfony\SymfonyResponse;
use Twig_Environment;

/**
 * Dump a symfony controller
 *
 * @author Damien Pitard <d.pitard@creads.org>
 */
class SymfonyDumper extends AbstractFileDumper
{
    /**
     * {@inheritDoc}
     */
    protected function render(SymfonyController $controller)
    {
        return $this->renderClass($controller);
    }

    /**
     * Render class for controller
     *
     * @return string
     */
    protected function renderClass(SymfonyController $controller)
    {
        $methods = '';
        foreach ($controller->getActions() as $action) {
            $methods .= $this->renderMethod($action);
        }

        $output = <<< EOD
<?php

namespace {$controller->getNamespace()};

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * {$controller->getDescription()}
 *
 * This class has been auto-generated by Api2Symfony.
 * @see https://github.com/creads/api2symfony
 */
class {$controller->getName()} extends Controller
{
{$methods}
}

EOD;

        return $output;
    }

    /**
     * Render class method
     *
     * @return string
     */
    protected function renderMethod(SymfonyAction $action)
    {
        $responses = '';
        foreach ($action->getResponses() as $response) {
            $responses .= $this->renderResponse($response);
        }

        $output = <<< EOD

{$this->renderMethodDocblock($action)}
public function {$action->getName()}(Request \$request)
{
{$responses}
    //returns an exception if the api does not know how to handle the request
    //you should remove it when implmenting this method
    throw new BadRequestHttpException("Don't know how to handle this request");
}
EOD;

        return $this->renderIndent($output, 1);
    }

    /**
     * Render method docblock
     *
     * @return string
     */
    protected function renderMethodDocblock(SymfonyAction $action)
    {
        $exceptions = '';
        foreach ($action->getResponses() as $response) {
            if ($response->getCode() < 200 || $response->getCode() >= 300) {
                $exceptions .= "@throws HttpException {$response->getDescription()}\n";
            }
        }

        $description = $action->getDescription()?$action->getDescription():'<no description>';
        $output = <<< EOD
{$description}

@Route("{$action->getRoute()->getPath()}", name="{$action->getRoute()->getName()}")
@Method({"{$action->getMethod()}"})

{$exceptions}
@return Response
EOD;

        $output = $this->renderDocblockComment($output);

        return $output;
    }

    /**
     * Render an API response
     *
     * @return string
     */
    protected function renderResponse(SymfonyResponse $response)
    {

        $body = addcslashes($response->getBody(), "'");
        $description = $response->getDescription()?$response->getDescription():'<no description>';

        if ($response->getCode() >= 200 && $response->getCode() < 300) { //valid response

                $output = <<< EOD

if ('{$response->getFormat()}' == \$request->get('_format')) {

    return new Response(
        '{$body}',
        {$response->getCode()},
    {$this->renderIndent($this->renderArray($response->getHeaders()), 1)}
    );
}
EOD;

            $output = $this->renderIndent($output, 1);
        } else { //invalid response
            $output = <<< EOD

//{$description}
throw new HttpException(
    {$response->getCode()},
    '{$body}',
    null,
{$this->renderIndent($this->renderArray($response->getHeaders()), 1)}
);
EOD;
            $output = $this->renderIndent($this->renderComment($output), 1);
        }


        return $output;
    }

    /**
     * Render an array
     *
     * @return string
     */
    protected function renderArray($value)
    {
        $output = var_export($value, true);

        return $output;
    }

    /**
     * Indents code (4 spaces)
     *
     * @return string
     */
    protected function renderIndent($code, $tabs = 0)
    {
        $output = '';
        $lines = preg_split('/\r\n|\r|\n/', $code);
        $count = count($lines);
        foreach ($lines as $i => $line) {

            $line = rtrim($line);
            if (!empty($line)) {
                for ($i = 0; $i < $tabs; $i++) {
                    $output .= '    ';
                }
                $output .= $line;
            }

            $last = ($i >= ($count - 1));
            if (!$last) {
                $output .= "\n";
            }
        }

        return $output;
    }

    /**
     * Comments code in a dockblock way
     *
     * @return string
     */
    protected function renderDocblockComment($code)
    {
        $output = "/**\n";
        $lines = preg_split('/\r\n|\r|\n/', $code);
        foreach ($lines as $i => $line) {
            $output .= ' * ' . $line . "\n";
        }
        $output .= " */";

        return $output;
    }

    /**
     * Comments code with simple line comment (//)
     *
     * @return string
     */
    protected function renderComment($code)
    {
        $output = '';
        $lines = preg_split('/\r\n|\r|\n/', $code);
        $count = count($lines);
        foreach ($lines as $i => $line) {

            $line = rtrim($line);
            if (!empty($line)) {
                $output .= '// ' . $line;
            }

            $last = ($i >= ($count - 1));
            if (!$last) {
                $output .= "\n";
            }
        }

        return $output;
    }

    /**
     * Comments code with multi line comment (//)
     *
     * @return string
     */
    protected function renderMultiComment($code)
    {
        $output = "/*\n";
        $lines = preg_split('/\r\n|\r|\n/', $code);
        foreach ($lines as $i => $line) {
            $output .= ' * ' . $line . "\n";
        }
        $output .= " */";

        return $output;
    }
}
