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

/**
 * Progress bar
 *
 * ```
 * $total = 100;
 *
 * $progress = new ProgressBar($textWriter, $total);
 *
 * for ($i = 0; $i < $total; $i++) {
 *   $progress->incr();
 *   usleep(10000);
 * }
 *
 * $progress->stop();
 * ```
 */
class ProgressBar extends AbstractWidget {
  /** @var int */
  protected $value = 0;

  /** @var int */
  protected $total = 0;

  /** @var int */
  protected $size = 0;

  /** @var bool */
  protected $showRemainingTime = true;

  /** @var int */
  protected $startTime;

  /**
   * @param TextWriter $writer
   * @param int $total
   * @param int $size
   */
  function __construct(TextWriter $writer = null, $total = 100, $size = 50, $showRemainingTime = true) {
    parent::__construct($writer);
    $this->textWriter = $writer;
    $this->size = $size;
    $this->showRemainingTime = $showRemainingTime;
    $this->start($total);
  }

  /**
   * @param int $size
   * @return ProgressBar
   */
  function setSize($size) {
    $this->size = $size;
    return $this;
  }

  /**
   * @return int
   */
  function getSize() {
    return $this->size;
  }

  /**
   * @param bool $show
   */
  function setShowRemainingTime($show = true) {
    $this->showRemainingTime = $show;
  }

  /**
   * @return bool
   */
  function getShowRemainingTime() {
    return $this->showRemainingTime;
  }

  /**
   * @param number $value
   */
  function setValue($value) {
    $this->value = $value;
  }

  /**
   * @return number
   */
  function getValue() {
    return $this->value;
  }

  /**
   * @param int $total
   * @return ProgressBar
   */
  function start($total = 100) {
    $this->value = 0;
    $this->total = $total;
    $this->startTime = time();
    return $this;
  }

  /**
   * Increments the value and calls {@see write()}
   *
   * @param int $increment
   * @return ProgressBar
   */
  function incr($increment = 1) {
    $this->value += $increment;
    $this->write();
    return $this;
  }

  /**
   * Writes a new line
   *
   * @return ProgressBar
   */
  function stop() {
    $this->textWriter->writeln();
    return $this;
  }

  /**
   * Generates the text to write for the current values
   *
   * @return string
   */
  function render() {
    $percentage = (float) ($this->value / $this->total);

    $progress = floor($percentage * $this->size);
    $output = "\r[" . str_repeat('=', $progress);
    if ($progress < $this->size) {
      $output .= ">" . str_repeat(' ', $this->size - $progress);
    } else {
      $output .= '=';
    }
    $output .= sprintf('] %s%% %s/%s', round($percentage * 100, 0), $this->value, $this->total);

    if ($this->showRemainingTime) {
      $speed = (time() - $this->startTime) / $this->value;
      $remaining = number_format(round($speed * ($this->total - $this->value), 2), 2);
      $output .= " - $remaining sec remaining";
    }

    return $output;
  }

  /**
   * Writes the rendered progress bar to the text writer
   *
   * @return ProgressBar
   */
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
