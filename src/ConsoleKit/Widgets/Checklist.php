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

use ConsoleKit\Colors;

/**
 * Creates checklist
 *
 * ```
 * $checklist = new ConsoleKit\Widgets\Checklist($console);
 * $checklist->run(array(
 *   'Creating directory structure' => function() {
 *     // code
 *     return $success; // bool
 *   },
 *   'Downloading scripts' => function() {
 *     // code
 *     return $success; // bool
 *   }
 * ));
 * ```
 *
 * Will print:
 *
 * ```log
 * Creating directory structure OK
 * Downloading scripts          OK
 * ```
 */
class Checklist extends AbstractWidget {
  protected $maxMessageLength = 100;

  protected $successText = 'OK';

  protected $successColor = Colors::GREEN;

  protected $errorText = 'FAIL';

  protected $errorColor = Colors::RED;

  function setMaxMessageLength($length) {
    $this->maxMessageLength = $length;
  }

  function getMaxMessageLength() {
    return $this->maxMessageLength;
  }

  function setSuccessText($text, $color = Colors::GREEN) {
    $this->successText = $text;
    $this->successColor = $color;
  }

  function setErrorText($text, $color = Colors::RED) {
    $this->errorText = $text;
    $this->errorColor = $color;
  }

  function run(array $steps) {
    $maxMessageLength = min(array_reduce(array_keys($steps), function ($r, $i) {
      return max(strlen($i), $r);
    }, 0), $this->maxMessageLength);

    foreach ($steps as $message => $callback) {
      $this->step($message, $callback, $maxMessageLength);
    }
  }

  function runArray($array, $message, $callback, $useKeyInMessage = false) {
    $steps = array();
    foreach ($array as $k => $v) {
      $steps[sprintf($message, $useKeyInMessage ? $k : $v)] = function () use ($k, $v, $callback) {
        return $callback($v, $k);
      };
    }
    return $this->run($steps);
  }

  function step($message, $callback, $maxMessageLength = null) {
    $maxMessageLength = $maxMessageLength ?: $this->maxMessageLength;
    $this->textWriter->write(sprintf("%-{$maxMessageLength}s", $message));
    if (call_user_func($callback)) {
      $this->textWriter->write(Colors::colorize($this->successText, $this->successColor));
    } else {
      $this->textWriter->write(Colors::colorize($this->errorText, $this->errorColor));
    }
    $this->textWriter->write("\n");
  }
}
