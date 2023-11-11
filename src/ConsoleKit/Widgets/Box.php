<?php
/*
 * This file is part of the ConsoleKit package.
 *
 * (c) 2012 Maxime Bouroumeau-Fuseau
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ConsoleKit\Widgets;

use ConsoleKit\TextWriter,
  ConsoleKit\ConsoleException;

class Box extends AbstractWidget {
  /** @var string */
  protected $text;

  /** @var string */
  protected $lineCharacter;

  /** @var int */
  protected $padding;

  /**
   * @param TextWriter $writer
   * @param string $text
   */
  function __construct(TextWriter $writer = null, $text = '', $lineCharacter = '*', $padding = 2) {
    parent::__construct($writer);
    $this->textWriter = $writer;
    $this->text = $text;
    $this->lineCharacter = $lineCharacter;
    $this->padding = $padding;
  }

  /**
   * @param string text
   * @return Box
   */
  function setText($text) {
    $this->text = $text;
    return $this;
  }

  /**
   * @return string
   */
  function getText() {
    return $this->text;
  }

  /**
   * @param string lineCharacter
   * @return Box
   */
  function setLineCharacter($lineCharacter) {
    $this->lineCharacter = $lineCharacter;
    return $this;
  }

  /**
   * @return string
   */
  function getLineCharacter() {
    return $this->lineCharacter;
  }

  /**
   * @param int padding
   * @return Box
   */
  function setPadding($padding) {
    $this->padding = $padding;
    return $this;
  }

  /**
   * @return int
   */
  function getPadding() {
    return $this->padding;
  }

  /**
   * @return string
   */
  function render() {
    $lines = explode("\n", $this->text);
    $maxWidth = 0;
    foreach ($lines as $line) {
      if (strlen($line) > $maxWidth) {
        $maxWidth = strlen($line);
      }
    }

    $maxWidth += $this->padding * 2 + 2;
    $c = $this->lineCharacter;
    $output = str_repeat($c, $maxWidth) . "\n";
    foreach ($lines as $line) {
      $delta = $maxWidth - (strlen($line) + 2 + $this->padding * 2);
      $output .= $c . str_repeat(' ', $this->padding) . $line
        . str_repeat(' ', $delta + $this->padding) . $c . "\n";
    }
    $output .= str_repeat($c, $maxWidth);
    return $output;
  }

  function write() {
    if ($this->textWriter === null) {
      throw new ConsoleException('No TextWriter object specified');
    }
    $this->textWriter->write($this->render());
    return $this;
  }

  function __toString() {
    return $this->render();
  }
}
