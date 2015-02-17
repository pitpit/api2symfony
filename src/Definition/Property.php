<?php

namespace Creads\Api2Symfony\Definition;

/**
 * Represents a property of a class.
 *
 * This file was part of Mondator (https://github.com/mandango/mondator)
 * (c) Pablo Díez <pablodip@gmail.com>
 * See LICENSE file
 *
 * @author Pablo Díez <pablodip@gmail.com>
 * @author Damien Pitard <damien.pitard@gmail.com>
 */
class Property
{
    private $visibility;
    private $name;
    private $value;
    private $static;
    private $docComment;

    /**
     * Constructor.
     *
     * @param string $visibility The visibility.
     * @param string $name       The name.
     * @param mixed  $value      The value.
     *
     * @api
     */
    public function __construct($visibility, $name, $value)
    {
        $this->setVisibility($visibility);
        $this->setName($name);
        $this->setValue($value);
        $this->static = false;
    }

    /**
     * Set the visibility.
     *
     * @param string $visibility The visibility.
     *
     * @api
     */
    public function setVisibility($visibility)
    {
        $this->visibility = $visibility;
    }

    /**
     * Returns the visibility.
     *
     * @return string The visibility.
     *
     * @api
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * Set the name.
     *
     * @param string $name The name.
     *
     * @api
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the name.
     *
     * @return string The name.
     *
     * @api
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value.
     *
     * @param mixed $value The value.
     *
     * @api
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Returns the value.
     *
     * @return mixed The value.
     *
     * @api
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set if the property is static.
     *
     * @param bool $static If the property is static.
     *
     * @api
     */
    public function setStatic($static)
    {
        $this->static = (bool) $static;
    }

    /**
     * Return if the property is static.
     *
     * @return bool Returns if the property is static.
     *
     * @api
     */
    public function isStatic()
    {
        return $this->static;
    }

    /**
     * Set the doc comment.
     *
     * @param string|null $docComment The doc comment.
     *
     * @api
     */
    public function setDocComment($docComment)
    {
        $this->docComment = $docComment;
    }

    /**
     * Returns the doc comment.
     *
     * @return string|null The doc comment.
     *
     * @api
     */
    public function getDocComment()
    {
        return $this->docComment;
    }
}