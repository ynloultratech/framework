<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\Component\FileReader\Matcher;

use Doctrine\DBAL\Types\Type;

class Column
{
    private $name;
    private $label;
    private $index;
    private $required;
    private $type;
    private $restricted;
    private $supportedTypes = [
        Type::STRING,
        Type::DECIMAL,
        Type::INTEGER,
        Type::DATE,
        Type::DATETIME,
    ];

    /**
     * @param string $name       Name of the column to get values later
     * @param string $label      Human label to display
     * @param bool   $required   Is required
     * @param string $type       Column type
     * @param bool   $restricted Restricted column
     */
    public function __construct($name, $label = null, $required = false, $type = Type::STRING, $restricted = false)
    {
        $this->name = $name;
        $this->label = $label;
        $this->required = $required;
        $this->type = $type;
        $this->restricted = $restricted;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getLabel()
    {
        return $this->label ?: $this->name;
    }

    /**
     * @param null|string $label
     *
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return int
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @param int $index
     *
     * @return $this
     */
    public function setIndex($index)
    {
        $this->index = $index;

        return $this;
    }

    /**
     * @return bool
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * @param bool $required
     *
     * @return $this
     */
    public function setRequired($required)
    {
        $this->required = $required;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        if (!in_array($type, $this->supportedTypes)) {
            throw new \InvalidArgumentException(sprintf('Unsupported column type: %s, these types are supported only: (%s)', $type, implode(', ', $this->supportedTypes)));
        }

        $this->type = $type;
    }

    /**
     * @return bool
     */
    public function isRestricted()
    {
        return $this->restricted;
    }

    /**
     * @param bool $restricted
     */
    public function setRestricted($restricted)
    {
        $this->restricted = $restricted;
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->getName();
    }
}
