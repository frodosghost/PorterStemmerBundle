<?php

namespace Manhattan\PorterStemmerBundle\Tests\Tools;

use Manhattan\PorterStemmerBundle\Tools\PorterStemmer;

/**
 * PorterStemmerTest
 *
 * @author James Rickard <james@frodosghost.com>
 */
class PorterStemmerTest extends \PHPUnit_Framework_TestCase
{

    private $porter;

    public function setUp()
    {
        $this->porter = $this->getMock('Porter');
    }

    /**
     * @covers Atom\LoggerBundle\Handler\CatchErrorHandler::__construct
     */
    public function testConstruct()
    {
        $porterStemmer = new PorterStemmer($this->porter);

        $this->assertInstanceOf('Manhattan\PorterStemmerBundle\Tools\PorterStemmer', $porterStemmer);
    }

}
