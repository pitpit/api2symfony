<?php

namespace Creads\Api2Symfony\Dumper;

use Creads\Api2Symfony\Mock\ControllerMock;
use Creads\Api2Symfony\Mock\ActionMock;
use Creads\Api2Symfony\Mock\ResponseMock;

use DocDigital\Lib\SourceEditor\TokenParser;
use DocDigital\Lib\SourceEditor\PhpClassEditor;
use DocDigital\Lib\SourceEditor\ElementBuilder;
use DocDigital\Lib\SourceEditor\ClassStructure\ClassElement;
use DocDigital\Lib\SourceEditor\ClassStructure\DocBlock;
use DocDigital\Lib\SourceEditor\ClassStructure\MethodElement;

use Pitpit\Component\Diff\DiffEngine;
use Pitpit\Component\Diff\Diff;
/**
 * Dump a symfony controller
 *
 * @author Damien Pitard <d.pitard@creads.org>
 */
class SymfonyDumper extends AbstractFileDumper
{
    const INDENT_SPACES = 4;

    protected $test;

    /**
     * {@inheritDoc}
     */
    protected function render(ControllerMock $controller)
    {
        $class = $this->getClass($controller);

        //we need to clone the class becase $class->render destroy it contents
        $this->test = clone $class;

        return $class->render(false);
    }

    public function dump(ControllerMock $controller, $destination = '.')
    {
        $filepath = parent::dump($controller, $destination);

        //*** BEGIN TEST ***
        $editor = new PhpClassEditor(new TokenParser());
        $editor->parseFile($filepath);
        $name = basename(str_replace('\\', '/', $this->test->getName()));
        $class = $editor->getClass($name);

        $engine = new DiffEngine(
            null,
            array(
                'DocDigital\Lib\SourceEditor\ClassStructure\MethodElement' => array('parentClass', 'elements'),
                'DocDigital\Lib\SourceEditor\ClassStructure\ClassElement' => array('elements')
            ),
            array(
                'DocDigital\Lib\SourceEditor\ElementBuilder' => function($diff) {
                    $new = $diff->getNew();
                    $old = $diff->getOld();

                    if ($new->__toString() !== $old->__toString()) {
                        $diff->setStatus(Diff::STATUS_MODIFIED);
                    }
                }
            )
        );

        $diff = $engine->compare($this->test, $class);

        $trace = function($diff, $tab = '') use (&$trace) {
            foreach ($diff as $element) {
                $c = $element->isTypeChanged()?'T':($element->isModified()?'M':($element->isCreated()?'+':($element->isDeleted()?'-':'=')));

                // print_r(sprintf("%s* %s [%s -> %s] (%s)\n", $tab, $element->getIdentifier(), is_object($element->getOld())?get_class($element->getOld()):gettype($element->getOld()), is_object($element->getNew())?get_class($element->getNew()):gettype($element->getNew()), $c));
                print_r(sprintf("%s* %s [%s -> %s] (%s)\n", $tab, $element->getIdentifier(), gettype($element->getOld()), gettype($element->getNew()), $c));

                if ($element->isModified()) {
                    $trace($element, $tab . '  ');
                }
                if ($element->isModified() && is_object($element->getNew()) && get_class($element->getNew()) === 'DocDigital\Lib\SourceEditor\ElementBuilder') {
                    var_dump($element->getOld());
                    var_dump($element->getNew());die();
                }
            }
        };

        $trace($diff);

        return $filepath;
    }

    /**
     * Get class definition corresponding to ControllerMock
     *
     * @param ControllerMock $controller
     *
     * @return ClassElement
     */
    protected function getClass(ControllerMock $controller)
    {
        $class = new ClassElement();

        // $class->addElement(new ElementBuilder("<?php\n"));

        // $class->addElement(new ElementBuilder("\n"));   //@todo how can we remove this ?

        $class->setName($controller->getShortClassName());

        $class->setClassDef(new ElementBuilder('class ' . $controller->getShortClassName() . ' extends Controller'));

        $namespace = new ElementBuilder('namespace ' . $controller->getNamespace() . ';');
        $class->setNameSpace($namespace);

        // $class->addElement($namespace); //@todo how can we remove this ?

        // $class->addElement(new ElementBuilder("\n\n")); //@todo how can we remove this ?

        $class->addUse(new ElementBuilder('use Symfony\Bundle\FrameworkBundle\Controller\Controller;'));
        $class->addUse(new ElementBuilder('use Symfony\Component\HttpFoundation\Request;'));
        $class->addUse(new ElementBuilder('use Symfony\Component\HttpFoundation\Response;'));
        $class->addUse(new ElementBuilder('use Symfony\Component\HttpKernel\Exception\HttpException;'));
        $class->addUse(new ElementBuilder('use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;'));
        $class->addUse(new ElementBuilder('use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;'));
        $class->addUse(new ElementBuilder('use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;'));

        $description = $controller->getDescription()?$controller->getDescription():'<no description>';
        $docblock = new ElementBuilder(
<<< EOT
/**
* {$description}
*
* This class has been auto-generated by Api2Symfony.
* @see https://github.com/creads/api2symfony
*/
EOT
        );

        $class->addDocBlock(new DocBlock($docblock));
        // $class->addElement($docblock);

        foreach ($controller->getActions() as $action) {
            $method = $this->getMethod($action, $class);
            $class->addMethod($method);
        };

        return $class;
    }

    /**
     * Get method definition corresponding to action mock
     *
     * @param ActionMock $action
     *
     * @return MethodElement
     */
    protected function getMethod(ActionMock $action, ClassElement $class)
    {
        $method = new MethodElement($class);

        $method->setName($action->getName());

        $description = $action->getDescription()?$action->getDescription():'<no description>';

        // $method->addElement(new ElementBuilder("\n\n    ")); //@todo shall we remove this ?

        $docblock = new ElementBuilder(
<<< EOT
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
EOT
        );

        $method->addDocBlock(new DocBlock($docblock));

        // $method->addElement($docblock);//@todo shall we remove this ?

        // $method->addElement(new ElementBuilder("\n    "));  //@todo shall we remove this ?

        $signature = new ElementBuilder('public function ' . $action->getName() . '(Request $request)');
        $method->setSignature($signature);

        // $method->addElement($signature);    //@todo shall we remove this ?

        // $method->addElement(new ElementBuilder("{"));   //@todo shall we remove this ?

        $code = '';
        foreach ($action->getResponses() as $response) {
            $code .= $this->getResponse($response);
        }

        $code .=
<<< EOT

        //returns an exception if the api does not know how to handle the request
        throw new BadRequestHttpException("Don't know how to handle this request");
EOT
        ;


        $method->addBodyElement(new ElementBuilder($code));

        $method->addElement(new ElementBuilder("}"));

        return $method;
    }

    /**
     * Get code corresponding to ResponseMock
     *
     * @param ResponseMock $response
     *
     * @return string
     */
    protected function getResponse(ResponseMock $response)
    {
        $body = trim(addcslashes($response->getBody(), "'"));
        $description = $response->getDescription()?$response->getDescription():'<no description>';
        $headers = var_export($response->getHeaders(), true);
        if ($response->getCode() >= 200 && $response->getCode() < 300) { //valid response
            $headers = self::renderArray($response->getHeaders(), 4);
            $code =
<<< EOT

        if ('{$response->getFormat()}' === \$request->get('_format')) {

            return new Response(
'{$body}',
                {$response->getCode()},
                {$headers}
            );
        }

EOT
            ;
        } else { //invalid response
            $headers = self::renderArray($response->getHeaders(), 3);

            $code = self::renderComment(
<<< EOT

        throw new HttpException(
            {$response->getCode()},
'{$body}',
            null,
            {$headers}
        );

EOT
            );
        }

        return $code;
    }


    /**
     * Export an array.
     *
     * Based on Symfony\Component\DependencyInjection\Dumper\PhpDumper::exportParameters
     * http://github.com/symfony/symfony
     *
     * @param array $array  The array.
     *
     * @return string The array exported.
     */
    public static function renderArray(array $array, $tabs = 0)
    {
        if (count($array)) {

            $code = array();
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $value = self::exportArray($value, $tabs + 1);
                } else {
                    $value = null === $value ? 'null' : var_export($value, true);
                }

                $code[] = sprintf('%s%s => %s,', str_repeat(' ', ($tabs+1) * self::INDENT_SPACES), var_export($key, true), $value);
            }

            return sprintf("array(\n%s\n%s)", implode("\n", $code), str_repeat(' ', $tabs * self::INDENT_SPACES));
        } else {
            return 'array()';
        }
    }

    /**
     * Indents code with tabs (4 spaces)
     *
     * @return string
     */
    public static function renderIndent($code, $tabs = 0)
    {
        $output = '';
        $lines = preg_split('/\r\n|\r|\n/', $code);
        $count = count($lines);
        foreach ($lines as $i => $line) {
            $line = rtrim($line);
            if (!empty($line)) {
                str_repeat(' ', $tabs * self::INDENT_SPACES);
                $output .= $line;
            }
            if ($i < ($count - 1)) {
                $output .= "\n";
            }
        }

        return $output;
    }

    /**
     * Comments code with simple line comment (//)
     *
     * @return string
     */
    public static function renderComment($code)
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
     * Comments code in a docblock
     *
     * @return string
     */
    public static function renderDocblockComment($code)
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
     * Comments code with multiline comment
     *
     * @return string
     */
    public static function renderMultiComment($code)
    {
        $output = "/*\n";
        $lines = preg_split('/\r\n|\r|\n/', $code);
        foreach ($lines as $i => $line) {
            $output .= $line . "\n";
        }
        $output .= "*/";

        return $output;
    }
}
