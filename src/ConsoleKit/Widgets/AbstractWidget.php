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

use ConsoleKit\TextWriter;

abstract class AbstractWidget {
  /** @var TextWriter */
  protected $textWriter;

  /**
   * @param TextWriter $writer
   */
  function __construct(TextWriter $writer) {
    $this->textWriter = $writer;
  }

  /**
   * @param TextWriter $writer
   * @return Dialog
   */
  function setTextWriter(TextWriter $writer) {
    $this->textWriter = $writer;
    return $this;
  }

  /**
   * @return TextWriter
   */
  function getTextWriter() {
    return $this->textWriter;
  }
}
