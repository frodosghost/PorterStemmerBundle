<?php

namespace Manhattan\PorterStemmerBundle\Tests\Component;

use Manhattan\PorterStemmerBundle\Component\Configuration;

/**
 * ConfigurationTest
 *
 * @author James Rickard <james@frodosghost.com>
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers Manhattan\PorterStemmerBundle\Component\Configuration::__construct
     */
    public function testConstruct()
    {
        $configuration = new Configuration();

        $this->assertInstanceOf('Manhattan\PorterStemmerBundle\Component\Configuration', $configuration, 'New instance of Configuration is created');
    }

    public function testIsMappedClassObject()
    {
        $config = new Configuration();

        $dateTime = $this->getMock('DateTime');
        $config->setMappedClass(get_class($dateTime));

        // Test Object passed into isMappedClass
        $this->assertTrue($config->isMappedClass($dateTime), '->isMappedClass() the mapped class matches set Mapped Class');

        $this->assertTrue($config->isMappedClass(get_class($dateTime)), '->isMappedClass() the mapped class does not match the passed string');

        $this->assertFalse($config->isMappedClass('Date Time'), '->isMappedClass() does not match where string is incorrect');
    }

    public function testIsMappedClassString()
    {
        $config = new Configuration();

        $dateTime = new \DateTime();
        $config->setMappedClass(get_class($dateTime));

        $this->assertTrue($config->isMappedClass('DateTime'), '->isMappedClass() the mapped class matches the passed string');

        $this->assertTrue($config->isMappedClass($dateTime), '->isMappedClass() the mapped class matches the passed object');

        $this->assertTrue($config->isMappedClass(get_class($dateTime)), '->isMappedClass() the mapped class matches the passed object');
    }
}

