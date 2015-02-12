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
     * Config
     *
     * @var array
     */
    protected $config;

    /**
     * Constructor
     *
     * @param Parser $parser
     */
    public function __construct(array $config = array())
    {
        $this->parser = new Parser();

        $this->config = array(
            'version_in_namespace' => false
        );

        $this->config = array_merge($this->config, $config);
    }

    /**
     * Recursive method which converts raml resource into action and add it to controller
     *
     * @param  SymfonyController $controller Controller where actions will be added
     * @param  Resource $resource
     * @param  string   $chainName
     */
    protected function addActions(SymfonyController $controller, Resource $resource, $chainName = '')
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

                    foreach ($response->getTypes() as $mimeType) {
                        if (null !== $example = $response->getExampleByType($mimeType)) {
                            $format= $request->getFormat($mimeType);
                            $example = str_replace(array("\r\n", "\n", "\r", "\t", "  "), '', $example);
                            $action->addResponse(new SymfonyResponse(
                                $code,
                                $example,
                                $format,
                                $headers,
                                $response->getDescription()?$response->getDescription():$method->getDescription()
                            ));
                        }
                    }
                }
            }

            $controller->addAction($action);
        }

        foreach ($resource->getResources() as $subresource) {
            $this->addActions($controller, $subresource, $chainName);
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
        if ($this->config['version_in_namespace'] && $definition->getVersion()) {
            $namespace .= '\\' . preg_replace(
                array('/(^[0-9])/', '/[^a-zA-Z0-9]/'),
                array('Version\1', '_'),
                $definition->getVersion()
            );
        }

        return $namespace;
    }

    /**
     * {@inheritDoc}
     */
    public function convert($filename, $namespace)
    {
        $def = $this->parser->parse($filename);

        $namespace = $this->buildNamespace($def, $namespace);

        $controllers = array();

        if ($def->getResources()) {
            foreach ($def->getResources() as $resource) {

                $controller = new SymfonyController(ucfirst($resource->getDisplayName()) . 'Controller', $namespace, $resource->getDescription());
                $this->addActions($controller, $resource);
                $controllers[] = $controller;
            }
        }

        return $controllers;
    }
}
