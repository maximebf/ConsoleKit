<?php

namespace ConsoleKit\Tests;

use ConsoleKit\Console;

class TestStatic {
  static function sayHello(array $args, array $opts, Console $console) {
    $name = 'unknown';

    if (!empty($args)) {
      $name = implode(' ', $args);
    } else if (isset($opts['name'])) {
      $name = $opts['name'];
    }

    $console->writeln(sprintf("hello %s!", $name));
  }
}
