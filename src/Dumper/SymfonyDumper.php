<?php

namespace Creads\Api2Symfony\Dumper;

use Creads\Api2Symfony\Mock\ControllerMock;
use Creads\Api2Symfony\Mock\ActionMock;
use Creads\Api2Symfony\Mock\ResponseMock;
use Mandango\Mondator\Dumper;
use Mandango\Mondator\Definition\Definition;
use Mandango\Mondator\Definition\Method;

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
    protected function render(ControllerDefinition $controller)
    {

        $definition = $this->getClassDefinition($controller);

        $dumper = new Dumper($definition);

        return $dumper->dump();
    }

    protected function getClassDefinition(ControllerDefinition $controller)
    {
        $definition = new Definition($controller->getClassName());
        $definition->setParentClass('Controller');
        $description = $controller->getDescription()?$controller->getDescription():'<no description>';
        $definition->setDocComment(
<<< EOF
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
* {$description}
*
* This class has been auto-generated by Api2Symfony.
* @see https://github.com/creads/api2symfony
*/
EOF
        );

        foreach ($controller->getActions() as $action) {
            $method = $this->getMethodDefinition($action);
            $definition->addMethod($method);
        };

        return $definition;
    }

    protected function getMethodDefinition(ActionDefinition $action)
    {

        $method = new Method('public', $action->getName(), 'Request $request', '');
        $method->setCode(
<<< EOF

//returns an exception if the api does not know how to handle the request
throw new BadRequestHttpException("Don't know how to handle this request");
EOF
        );
        $description = $action->getDescription()?$action->getDescription():'<no description>';
        $method->setDocComment(
<<<EOF
    /**
     * {$description}
     *
     * @Route(
     *   "{$action->getRoute()->getPath()}",
     *   name="{$action->getRoute()->getName()}"
     * )
     * @Method({"{$action->getMethod()}"})
     *
     * @return Response
     */
EOF
        );

        return $method;
    }

    /**
     * Render class for controller
     *
     * @return string
     */
    protected function renderClass(ControllerMock $controller)
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
class {$controller->getShortClassName()} extends Controller
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
    protected function renderMethod(ActionMock $action)
    {
        $responses = '';
        foreach ($action->getResponses() as $response) {
            $responses .= $this->renderResponse($response);
        }

        $output = <<< EOD

{$this->renderIndent($this->renderMethodDocblock($action), 1)}
    public function {$action->getName()}(Request \$request)
    {
    {$responses}

        //returns an exception if the api does not know how to handle the request
        throw new BadRequestHttpException("Don't know how to handle this request");
    }
EOD;

        return $output;
    }

    /**
     * Render method docblock
     *
     * @return string
     */
    protected function renderMethodDocblock(ActionMock $action)
    {
        $responses = $action->getResponses();

        $exceptions = "@throws BadRequestHttpException If the api does not know how to handle the request\n";
        foreach ($responses as $response) {
            if ($response->getCode() < 200 || $response->getCode() >= 300) {
                $exceptions .= "@throws HttpException {$response->getDescription()}\n";
            }
        }

        $description = $action->getDescription()?$action->getDescription():'<no description>';
        $output = <<< EOD
{$description}

@Route(
    "{$action->getRoute()->getPath()}",
    name="{$action->getRoute()->getName()}"
)
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
    protected function renderResponse(ResponseMock $response)
    {

        $body = trim(addcslashes($response->getBody(), "'"));
        $description = $response->getDescription()?$response->getDescription():'<no description>';

        if ($response->getCode() >= 200 && $response->getCode() < 300) { //valid response

                $output = <<< EOD


    if ('{$response->getFormat()}' == \$request->get('_format')) {

        \$body = <<< EOT
{$body}
EOT;

        return new Response(
            \$body,
            {$response->getCode()},
{$this->renderIndent($this->renderArray($response->getHeaders()), 3)}
        );
    }
EOD;

        } else { //invalid response
            $output = <<< EOD


    //ERROR: {$description}
    \$body = <<< EOT
{$body}
EOT;

    throw new HttpException(
        {$response->getCode()},
        \$body,
        null,
{$this->renderIndent($this->renderArray($response->getHeaders()), 2)}
    );
EOD;
            $output = $this->renderComment($output);
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
                for ($j = 0; $j < $tabs; $j++) {
                    $output .= '    ';
                }
                $output .= $line;
            }

            if ($i < ($count - 1)) {
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

            if ($i < ($count - 1)) {
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
