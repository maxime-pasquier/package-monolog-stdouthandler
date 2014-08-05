<?php

namespace Monolog\Formatter;

use Monolog\TestCase;
use Monolog\Logger;
use Monolog\Handler\StdoutHandler;

class ColorLineFormatterTest extends TestCase
{
    private $formatter;
    
    public function setUp()
    {
        $this->formatter = new ColorLineFormatter(StdoutHandler::FORMAT);
    }
    
    private function getFormattedMessage($colorName)
    {
        $message = sprintf('[error][c=%s]core dumped[/c].', $colorName);
        
        return $this->formatter->format(
            $this->getRecord(Logger::ERROR, $message)
        );
    }
    
    /**
     * @dataProvider providerTestColor
     */
    public function testRealColor($colorName, $colorValue)
    {
        $expected = sprintf("[error]\033[%dmcore dumped\033[0m.\n", $colorValue);
        $this->assertSame($expected, $this->getFormattedMessage($colorName));
    }
    
    public function providerTestColor()
    {
        return array(
            array('black',  30),
            array('red',    31),
            array('green',  32),
            array('yellow', 33),
            array('blue',   34),
            array('purple', 35),
            array('cyan',   36),
            array('white',  37),
        );
    }
    
    /**
     * @dataProvider providerTestUnknownColor
     */
    public function testUnknownColor($colorName)
    {
        $expected = "[error]core dumped\033[0m.\n";
        $this->assertSame($expected, $this->getFormattedMessage($colorName));
    }
    
    public function providerTestUnknownColor()
    {
        return array(
            array('foo'),
            array('bar'),
        );
    }
    
    /**
     * @dataProvider providerTestBadValue
     */
    public function testBadValue($colorName)
    {
        $expected = sprintf("[error][c=%s]core dumped\033[0m.\n", $colorName);
        $this->assertSame($expected, $this->getFormattedMessage($colorName));
    }
    
    public function providerTestBadValue()
    {
        return array(
            array(null),
            array(''),
            array(' '),
            array('red2'),
            array('red-light'),
            array('dark_red'),
        );
    }
}
