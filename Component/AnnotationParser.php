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

        return $this;
    }

    public function parse()
    {
        $configuration = null;
        $annotations = $this->annotationReader->getClassAnnotations($this->reflectionClass);

        foreach ($annotations as $annotation) {
            if ($annotation instanceof PorterStemmer) {
                $configuration = new Configuration();

                $configuration->setObjectClass($annotation->class);
                $configuration->setMappedField(strtolower($this->getName($this->classMetadata->name)));
                $configuration->setMappedClass($this->classMetadata->name);

                foreach ($this->reflectionClass->getProperties() as $property) {
                    $propertyAnnotations = $this->annotationReader->getPropertyAnnotations($property);

                    if (count($propertyAnnotations) > 0) {
                        foreach ($propertyAnnotations as $annotation) {
                            if ($annotation instanceof Stem) {
                                $configuration->addField(array(
                                    'name'   => $property->name,
                                    'weight' => $annotation->weight
                                ));
                            }
                        }
                    }
                }
            }
        }

        return $configuration;
    }

    /**
     * Configures Metadata.
     * Checks and sets ReflectionClass
     *
     * @param  ClassMetadata $classMetadata
     * @return AnnotationParser
     */
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
