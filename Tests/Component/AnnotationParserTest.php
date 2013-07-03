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

    public function testIncorrectReflectionClass()
    {
        $annotationParser = new AnnotationParser($this->annotationReader);

        $this->setExpectedException('\Exception');
        $annotationParser->setReflectionClass(null);
    }

    public function testSetReflectionClass()
    {
        $annotationParser = new AnnotationParser($this->annotationReader);

        $reflectionClass = $this->getMockBuilder('ReflectionClass')
            ->disableOriginalConstructor()
            ->getMock();
        $this->assertInstanceOf('Manhattan\PorterStemmerBundle\Component\AnnotationParser', $annotationParser->setReflectionClass($reflectionClass));
    }

}
