<?php

namespace ConsoleKit\Tests;

use ConsoleKit\Colors;
use ConsoleKit\ConsoleException;

class ColorsTest extends ConsoleKitTestCase {
  function testColorize() {
    $this->assertEquals("\033[31mred\033[0m", Colors::colorize('red', Colors::RED));
    $this->assertEquals("\033[1;31mred\033[0m", Colors::colorize('red', Colors::RED | Colors::BOLD));
    $this->assertEquals("\033[43m\033[31mred\033[0m", Colors::colorize('red', Colors::RED, Colors::YELLOW));
    $this->assertEquals("\033[31mred\033[0m", Colors::red('red'));
  }

  function testGetColorCode() {
    $this->assertEquals(Colors::RED, Colors::getColorCode(Colors::RED));
    $this->assertEquals(Colors::RED, Colors::getColorCode('red'));
    $this->assertEquals(Colors::GREEN, Colors::getColorCode('GREEN'));
  }

  function testGetBoldColorCode() {
    $this->assertEquals(Colors::YELLOW | Colors::BOLD, Colors::getColorCode(Colors::YELLOW | Colors::BOLD));
    $this->assertEquals(Colors::RED | Colors::BOLD, Colors::getColorCode('red+bold'));
    $this->assertEquals(Colors::GREEN | Colors::BOLD, Colors::getColorCode('green', array('bold')));
  }

  function testGetUnknownColorCode() {
    self::expectException(ConsoleException::class);
    self::expectExceptionMessage("Unknown color 'foobar'");
    Colors::getColorCode('foobar');
  }

  function testGetFgColorString() {
    $this->assertEquals("\033[34m", Colors::getFgColorString(Colors::BLUE));
    $this->assertEquals("\033[1;34m", Colors::getFgColorString(Colors::BLUE | Colors::BOLD));
  }

  function testGetBgColorString() {
    $this->assertEquals("\033[45m", Colors::getBgColorString(Colors::MAGENTA));
  }
}
