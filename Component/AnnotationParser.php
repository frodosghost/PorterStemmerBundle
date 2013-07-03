<?php

namespace Manhattan\PorterStemmerBundle\Component;

use ReflectionClass;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Common\Annotations\Reader;
use Manhattan\PorterStemmerBundle\Exception\ConfigurationException;

use Manhattan\PorterStemmerBundle\Mapping\Annotation\PorterStemmer;
use Manhattan\PorterStemmerBundle\Mapping\Annotation\Stem;

/**
 * AnnotationParser
 *
 * @author James Rickard <james@frodosghost.com>
 */
class AnnotationParser
{
    /**
     * AnnotationReader
     *
     * @var Doctrine\Common\Annotations\AnnotationReader
     */
    private $annotationReader;

    /**
     * ClassMetadata
     *
     * @var Doctrine\ORM\Mapping\ClassMetadata
     */
    private $classMetadata;

    /**
     * ReflectionClass
     *
     * @var ReflectionClass
     */
    private $reflectionClass;

    /**
     * Constructor
     *
     * @param Reader $annotationReader
     */
    public function __construct(Reader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }

    public function parse()
    {
        $configuration = array();
        $annotations = $this->annotationReader->getClassAnnotations($this->reflectionClass);

        foreach ($annotations as $annotation) {
            if ($annotation instanceof PorterStemmer) {
                $configuration['objectClass'] = $annotation->class;
                $configuration['mappedField'] = strtolower($this->getName($this->classMetadata->name));

                foreach ($class->getProperties() as $property) {
                    $propertyAnnotations = $this->annotationReader->getPropertyAnnotations($property);

                    if (count($propertyAnnotations) > 0) {
                        foreach ($propertyAnnotations as $annotation) {
                            if ($annotation instanceof Stem) {
                                $configuration['fields'][] = array(
                                    'name'   => $property->name,
                                    'weight' => $annotation->weight
                                );
                            }
                        }
                    }
                }
            }
        }

        return $configuration;
    }

    public function configureMetadata(ClassMetadata $classMetadata)
    {
        $this->classMetadata = $classMetadata;

        if ($this->classMetadata->getReflectionClass() instanceof ReflectionClass) {
            $this->reflectionClass = $this->classMetadata->getReflectionClass();
        } else {
            throw new ConfigurationException();
        }

        return $this;
    }

    /**
     * Returns class name as determined from Namespace
     *
     * @return string
     */
    private function getName($name)
    {
        $parts = explode('\\', $name);
        return end($parts);
    }

}
