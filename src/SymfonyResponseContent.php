<?php

namespace Creads\Api2Symfony;

/**
 * @author Quentin <q.pautrat@creads.org>
 */
class SymfonyResponseContent
{
    /**
     * Content type
     *
     * @var string
     */
    private $type;

    /**
     * Content
     *
     * @var string
     */
    private $body;

    /**
     * Constructor
     *
     * @param string $type    Content type
     * @param string $content Content
     */
    public function __construct($type, $body)
    {
        $this->type = $type;
        $this->body = $body;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Gets content
     *
     * @deprecated
     *
     * @return string
     */
    public function getContent()
    {
        return $this->getBody();
    }

    /**
     * Gets body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }
}
