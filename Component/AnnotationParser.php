<?php

namespace Manhattan\PorterStemmerBundle\Component;

use Doctrine\Common\Annotations\Reader;

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
     * ReflectionClass
     *
     * @var \ReflectionClass
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

    public function setReflectionClass(\ReflectionClass $reflectionClass)
    {
        $this->reflectionClass = $reflectionClass;

        return $this;
    }
}
