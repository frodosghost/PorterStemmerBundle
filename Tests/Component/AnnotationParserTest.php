<?php

namespace Manhattan\PorterStemmerBundle\Tests\Tools;

use Manhattan\PorterStemmerBundle\Component\AnnotationParser;

/**
 * AnnotationParserTest
 *
 * @author James Rickard <james@frodosghost.com>
 */
class AnnotationParserTest extends \PHPUnit_Framework_TestCase
{
    private $annotationReader;

    public function setUp()
    {
        $this->annotationReader = $this->getMock('Doctrine\Common\Annotations\AnnotationReader');
    }

    /**
     * @covers Manhattan\PorterStemmerBundle\Component\AnnotationParser::__construct
     */
    public function testConstruct()
    {
        $annotationParser = new AnnotationParser($this->annotationReader);

        $this->assertInstanceOf('Manhattan\PorterStemmerBundle\Component\AnnotationParser', $annotationParser);
    }

    public function testIncorrectConfigureMetadata()
    {
        $annotationParser = new AnnotationParser($this->annotationReader);

        $this->setExpectedException('\Exception');
        $annotationParser->configureMetadata(null);
    }

    public function testIncompleteConfigureMetadata()
    {
        $annotationParser = new AnnotationParser($this->annotationReader);

        $classMetadata = $this->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadata')
            ->disableOriginalConstructor()
            ->getMock();

        $this->setExpectedException('\Manhattan\PorterStemmerBundle\Exception\ConfigurationException');
        $annotationParser->configureMetadata($classMetadata);
        //$this->assertInstanceOf('Manhattan\PorterStemmerBundle\Component\AnnotationParser', $annotationParser->setClassMetadata($classMetadata));
    }

    public function testConfigureMetadata()
    {
        $annotationParser = new AnnotationParser($this->annotationReader);
        $test = $this;

        $classMetadata = $this->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadata')
            ->disableOriginalConstructor()
            ->getMock();

        $classMetadata->expects($this->any())
            ->method('getReflectionClass')
            ->will($this->returnCallback(function () use ($test) {
                return $test->getMockBuilder('ReflectionClass')
                        ->disableOriginalConstructor()
                        ->getMock();
            }));

        $this->assertInstanceOf('Manhattan\PorterStemmerBundle\Component\AnnotationParser', $annotationParser->configureMetadata($classMetadata));
    }

}

