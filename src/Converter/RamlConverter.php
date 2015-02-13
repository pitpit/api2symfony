<?php

namespace Creads\Api2Symfony\Converter;

use Symfony\Component\HttpFoundation\Request;
use Raml\ApiDefinition;
use Raml\Parser;
use Raml\Resource;

use Creads\Api2Symfony\SymfonyController;
use Creads\Api2Symfony\SymfonyAction;
use Creads\Api2Symfony\SymfonyRoute;
use Creads\Api2Symfony\SymfonyResponse;
use Creads\Api2Symfony\SymfonyResponseContent;

/**
 * Provide a way to convert a RAML specification to a list of SymfonyController
 *
 * @author Quentin <q.pautrat@creads.org>
 */
class RamlConverter implements ConverterInterface
{
    /**
     * Parser
     *
     * @var Parser
     */
    private $parser;

    /**
     * Options
     *
     * @var array
     */
    protected $options;

    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->parser = new Parser();

        $this->options = array(
            'version_in_namespace' => false
        );

        $this->options = array_merge($this->options, $options);
    }

    /**
     * {@inheritDoc}
     */
    public function generate($filepath, $namespace)
    {
        $definition = $this->parser->parse($filepath);

        return $this->generateControllers($definition, $namespace);
    }

    /**
     * {@inheritDoc}
     */
    public function update($newFilepath, $oldFilepath, $namespace)
    {
        $newDefinition = $this->parser->parse($newFilepath);
        $oldDefinition = $this->parser->parse($oldFilepath);

        throw new \Exception('Not implemented');
    }

    /**
     * Generate controller according to definition
     *
     * @return array of SymfonyController
     */
    protected function generateControllers(ApiDefinition $definition, $namespace)
    {
        $controllers = array();

        $namespace = $this->buildNamespace($definition, $namespace);

        if ($definition->getResources()) {
            foreach ($definition->getResources() as $resource) {
                $controller = new SymfonyController(ucfirst($resource->getDisplayName()) . 'Controller', $namespace, $resource->getDescription());
                $this->generateActions($controller, $resource);
                $controllers[] = $controller;
            }
        }

        return $controllers;
    }

    /**
     * Recursive method which converts raml resource into action and add it to controller
     *
     * @param  SymfonyController $controller Controller where actions will be added
     * @param  Resource $resource
     * @param  string   $chainName
     */
    protected function generateActions(SymfonyController $controller, Resource $resource, $chainName = '')
    {
        $actions = array();

        $chainName = $chainName . '_' . strtolower($resource->getDisplayName());

        $request = new Request();

        foreach ($resource->getMethods() as $method) {
            $actionName = strtolower($method->getType()) . str_replace(' ', '', ucwords(str_replace('_', ' ', $chainName))) . 'Action';
            $route = new SymfonyRoute($resource->getUri(), strtolower($method->getType() . $chainName));
            $action = new SymfonyAction($actionName, $route, $method->getType(), $method->getDescription());

            preg_match_all('/\{[a-zA-Z]+\}/', $resource->getUri(), $parameters);
            foreach ($parameters[0] as $parameter) {
                $action->addParameter(substr($parameter, 1, strlen($parameter) - 2));
            }

            if ($method->getResponses()) {
                foreach ($method->getResponses() as $code => $response) {
                    $headers = array();
                    foreach ($response->getHeaders() as $key => $value) {
                        if (isset($value['required']) && $value['required']) {
                            $headers[$key] = isset($value['example']) ? $value['example'] : '';
                        }
                    }
                    $mimeTypes = $response->getTypes();
                    if (!count($mimeTypes)) {
                        $mimeTypes = ['application/json'];
                    }

                    foreach ($mimeTypes as $mimeType) {
                        $format = $request->getFormat($mimeType);
                        $body = $response->getExampleByType($mimeType);

                        if (null == $body) {
                            if ('xml' === $format) {
                                $body = <<< EOT
<response>
    <message>RAML had no response information for {$mimeType}</message>
</response>
EOT;
                            } else {
$body = <<< EOT
{
  "message": "RAML had no response information for {$mimeType}"
}
EOT;
                            }
                        }

                        $action->addResponse(new SymfonyResponse(
                            $code,
                            $body,
                            $format,
                            $headers,
                            $response->getDescription()?$response->getDescription():$method->getDescription()
                        ));
                    }
                }
            }

            $controller->addAction($action);
        }

        foreach ($resource->getResources() as $subresource) {
            $this->generateActions($controller, $subresource, $chainName);
        }
    }

    /**
     * Build namespace given ApiDefinition and base namespace
     *
     * @param  ApiDefinition $definition Checks if definition has version
     * @param  string        $namespace  Base
     * @return string
     */
    protected function buildNamespace(ApiDefinition $definition, $namespace)
    {
        if ($this->options['version_in_namespace'] && $definition->getVersion()) {
            $namespace .= '\\' . preg_replace(
                array('/(^[0-9])/', '/[^a-zA-Z0-9]/'),
                array('Version\1', '_'),
                $definition->getVersion()
            );
        }

        return $namespace;
    }
}
