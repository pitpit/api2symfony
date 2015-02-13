<?php

namespace Creads\Api2Symfony\Mock;

/**
 * @author Quentin <q.pautrat@creads.org>
 */
class ResponseMock
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

    /**
     *
     * @var string
     */
    private $format;

    /**
     * Constructor
     *
     * @param integer   $code           HTTP Code
     * @param array header
     */
    public function __construct($code, $body, $format = 'json', array $headers = array(), $description = null)
    {
        $this->code         = $code;
        $this->headers      = $headers;
        $this->body         =  $body;
        $this->format       =  $format;
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
     * Gets the value of format.
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }
}
