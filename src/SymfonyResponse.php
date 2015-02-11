<?php

namespace Creads\Api2Symfony;

/**
 * @author Quentin <q.pautrat@creads.org>
 */
class SymfonyResponse
{
    /**
     * HTTP Code
     *
     * @var integer
     */
    private $code;

    /**
     * Headers
     *
     * @var array
     */
    private $headers;

    /**
     * Description
     *
     * @var string
     */
    private $description;

    /**
     * Contents
     *
     * @var string
     */
    private $body;


    private $type;

    /**
     * Constructor
     *
     * @param integer   $code           HTTP Code
     * @param array header
     */
    public function __construct($code, $body, $type, array $headers = array(), $description = null)
    {
        $this->code         = $code;
        $this->headers      = $headers;
        $this->body         =  $body;
        $this->type         =  $type;
        $this->description  = $description;
    }

    /**
     * Gets code
     *
     * @return integer
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Get headers
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Get contents
     *
     * @return array
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Gets the Description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Gets the value of type.
     *
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }
}
