<?php

namespace Manhattan\PorterStemmerBundle\Component;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Configuration
 *
 * @author James Rickard <james@frodosghost.com>
 */
class Configuration
{
    /**
     * @var String
     */
    private $objectClass;

    /**
     * @var String
     */
    private $mappedField;

    /**
     * @var String
     */
    private $mappedClass;

    /**
     * @var Array
     */
    private $fields;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->fields = new ArrayCollection();
    }

    /**
     * Set Object Class
     *
     * @param  String        $objectClass
     * @return Configuration
     */
    public function setObjectClass($objectClass)
    {
        $this->objectClass = $objectClass;

        return $this;
    }

    /**
     * Get Object Class
     *
     * @return String
     */
    public function getObjectClass()
    {
        return $this->objectClass;
    }

    /**
     * Set Mapped Field
     *
     * @param  String        $mappedField
     * @return Configuration
     */
    public function setMappedField($mappedField)
    {
        $this->mappedField = $mappedField;

        return $this;
    }

    /**
     * Get Mapped Field
     *
     * @return String
     */
    public function getMappedField()
    {
        return $this->mappedField;
    }

    /**
     * Set Mapped Class
     *
     * @param  String        $mappedClass
     * @return Configuration
     */
    public function setMappedClass($mappedClass)
    {
        $this->mappedClass = $mappedClass;

        return $this;
    }

    /**
     * Get Mapped Class
     *
     * @return String
     */
    public function getMappedClass()
    {
        return $this->mappedClass;
    }

    /**
     * Add Field
     *
     * @param  String $field
     * @return Configuration
     */
    public function addField($field)
    {
        $this->fields[] = $field;

        return $this;
    }

    /**
     * Returns Fields
     *
     * @return Doctrine\Common\Collections\ArrayCollection
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Checks to see if the Class is Mapped
     *
     * @param  (Object|String)  $entity
     * @return boolean
     */
    public function isMappedClass($entity)
    {
        $class = null;

        if (is_object($entity)) {
            $class = get_class($entity);
        } else {
            $class = $entity;
        }

        if ($class == $this->mappedClass) {
            return true;
        }

        return false;
    }

}
