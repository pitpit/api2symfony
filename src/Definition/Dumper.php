<?php

namespace Creads\Api2Symfony\Definition;

/**
 * The dumper.
 *
 * This file was part of Mondator (https://github.com/mandango/mondator)
 * (c) Pablo Díez <pablodip@gmail.com>
 * See LICENSE file
 *
 * @author Pablo Díez <pablodip@gmail.com>
 * @author Damien Pitard <damien.pitard@gmail.com>
 */
class Dumper
{
    const INDENT_SPACES = 4;

    protected $definition;

    /**
     * Constructor.
     *
     * @param Definition $definition The definition.
     *
     * @api
     */
    public function __construct(Definition $definition)
    {
        $this->setDefinition($definition);
    }

    /**
     * Set the definition.
     *
     * @param Definition $definition The definition.
     *
     * @api
     */
    public function setDefinition(Definition $definition)
    {
        $this->definition = $definition;
    }

    /**
     * Returns the definition
     *
     * @return Definition The definition.
     *
     * @api
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * Dump the definition.
     *
     * @return string The PHP code of the definition.
     *
     * @api
     */
    public function dump()
    {
        return
            $this->startFile().
            $this->addNamespace().
            $this->addUses().
            $this->startClass().
            $this->addProperties().
            $this->addMethods().
            $this->endClass()
        ;
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
        // return var_export($array, true);

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

    protected function startFile()
    {
        return <<<EOF
<?php

EOF;
    }

    protected function addNamespace()
    {
        if (!$namespace = $this->definition->getNamespace()) {
            return '';
        }

        return <<<EOF

namespace $namespace;

EOF;
    }

    protected function addUses()
    {
        $code = '';
        $uses = $this->definition->getUses();
        if (count($uses)) {
            $code .= "\n";

            foreach ($uses as $class => $alias) {
                $code .= "use $class";
                if ($alias) {
                    $code .= "as $alias";
                }
                $code .= ";\n";
            }
        }

        return $code;
    }

    protected function startClass()
    {
        $code = "\n";

        // doc comment
        if ($docComment = $this->definition->getDocComment()) {
            $code .= $docComment."\n";
        }

        /*
         * declaration
         */
        $declaration = '';

        // abstract
        if ($this->definition->isAbstract()) {
            $declaration .= 'abstract ';
        }

        // class
        $declaration .= 'class '.$this->definition->getClassName();

        // parent class
        if ($parentClass = $this->definition->getParentClass()) {
            $declaration .= ' extends '.$parentClass;
        }

        // interfaces
        if ($interfaces = $this->definition->getInterfaces()) {
            $declaration .= ' implements '.implode(', ', $interfaces);
        }

        $code .= <<<EOF
$declaration
{
EOF;

        return $code;
    }

    protected function addProperties()
    {
        $code = '';

        $properties = $this->definition->getProperties();
        foreach ($properties as $property) {
            $code .= "\n";

            if ($docComment = $property->getDocComment()) {
                $code .= $docComment."\n";
            }
            $isStatic = $property->isStatic() ? 'static ' : '';

            $value = $property->getValue();
            if (null === $value) {
                $code .= <<<EOF
    $isStatic{$property->getVisibility()} \${$property->getName()};
EOF;
            } else {
                $value = is_array($property->getValue()) ? self::renderArray($property->getValue()) : var_export($property->getValue(), true);

                $code .= <<<EOF
    $isStatic{$property->getVisibility()} \${$property->getName()} = $value;
EOF;
            }
        }
        if ($properties) {
            $code .= "\n";
        }

        return $code;
    }

    protected function addMethods()
    {
        $code = '';

        foreach ($this->definition->getMethods() as $method) {
            $code .= "\n";

            // doc comment
            if ($docComment = $method->getDocComment()) {
                $code .= self::renderIndent($docComment, 1)."\n";
            }

            // isFinal
            $isFinal = $method->isFinal() ? 'final ' : '';

            // isStatic
            $isStatic = $method->isStatic() ? 'static ' : '';

            // abstract
            if ($method->isAbstract()) {
                $code .= <<<EOF
    abstract $isStatic{$method->getVisibility()} function {$method->getName()}({$method->getArguments()});
EOF;
            } else {
                $methodCode = $method->getCode();
                if ($methodCode) {
                    $methodCode = (($method->getAutoIndentation())?self::renderIndent($methodCode, 1):$methodCode)."\n";
                }
                $code .= <<<EOF
    $isFinal$isStatic{$method->getVisibility()} function {$method->getName()}({$method->getArguments()})
    {
{$methodCode}
    }
EOF;
            }
            $code =  $code . "\n";
        }

        return $code;
    }

    protected function endClass()
    {
        $code = '';

        if (!$this->definition->getProperties() && !$this->definition->getMethods()) {
            $code .= "\n";
        }

        $code .= <<<EOF
}

EOF;

        return $code;
    }
}
