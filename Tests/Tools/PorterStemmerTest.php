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

    public function testRemoveExcludedWords()
    {
        $porterStemmer = new PorterStemmer($this->porter);

        $words = array('why', 'before', 'during', 'doing', 'Itself', 'him', 'yours', 'above', 'very', 'daunting');
        $this->assertEquals(array(9 => 'daunting'), $porterStemmer->removeExcludedWords($words), '->removeExcludedWords() removes all but one words from given array.');

        $words = array('I', 'am', 'unsure', 'why', 'I', 'am', 'doing', 'this', 'writing', 'maybe', 'it', 'is', 'because', 'I', 'need', 'to', 'test', 'it', 'all');
        $this->assertEquals(array(
            2 => 'unsure',
            8 => 'writing',
            9 => 'maybe',
            14 => 'need',
            16 => 'test'
        ), $porterStemmer->removeExcludedWords($words), '->removeExcludedWords() removes all of the required words from given array.');

        $words = array('which', 'was', 'be', 'been', 'being', 'and', 'but', 'if', 'or', 'because', 'as', 'until', 'into', 'before', 'after', 'above');
        $this->assertEquals(array(), $porterStemmer->removeExcludedWords($words), '->removeExcludedWords() removes all words from given array.');
    }

    public function testStemPhrase()
    {
        $porterStemmer = new PorterStemmer($this->porter);

        $sentance = 'I am unsure why I am doing this writing maybe it is because I need to test it all.';
        $this->assertEquals(array('unsur', 'write', 'mayb', 'need', 'test'), $porterStemmer->stemPhrase($sentance), '->stemPhrase() returns stemmed words');
    }

}
