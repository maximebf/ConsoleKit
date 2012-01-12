<?php

namespace ConsoleKit\Tests;

use ConsoleKit\Colors;

class ColorsTest extends ConsoleKitTestCase
{
    public function testColorize()
    {
        $this->assertEquals("\033[31mred\033[0m", Colors::colorize('red', Colors::RED));
        $this->assertEquals("\033[1;31mred\033[0m", Colors::colorize('red', Colors::RED | Colors::BOLD));
        $this->assertEquals("\033[43m\033[31mred\033[0m", Colors::colorize('red', Colors::RED, Colors::YELLOW));
        $this->assertEquals("\033[31mred\033[0m", Colors::red('red'));
    }

    public function testGetColorCode()
    {
        $this->assertEquals(1, Colors::getColorCode(Colors::RED));
        $this->assertEquals(1, Colors::getColorCode('red'));
        $this->assertEquals(2, Colors::getColorCode('GREEN'));
    }

    public function testGetBoldColorCode()
    {
        $this->assertEquals(11, Colors::getColorCode(Colors::YELLOW | Colors::BOLD));
        $this->assertEquals(11, Colors::getColorCode('red+bold'));
        $this->assertEquals(10, Colors::getColorCode('green', true));
    }

    /**
     * @expectedException        ConsoleKit\ConsoleException
     * @expectedExceptionMessage Color name 'foobar' does not exist
     */
    public function testGetUnknownColorCode()
    {
        Colors::getColorCode('foobar');
    }

    public function testGetFgColorString()
    {
        $this->assertEquals("\033[34m", Colors::getFgColorString(Colors::BLUE));
        $this->assertEquals("\033[1;34m", Colors::getFgColorString(Colors::BLUE | Colors::BOLD));
    }

    public function testGetBgColorString()
    {
        $this->assertEquals("\033[45m", Colors::getBgColorString(Colors::MAGENTA));
    }
}
