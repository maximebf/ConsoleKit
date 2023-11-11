<?php

namespace ConsoleKit\Tests;

use ConsoleKit\Console,
  ConsoleKit\EchoTextWriter,
  ConsoleKit\Colors;
use ErrorException;

class ConsoleTest extends ConsoleKitTestCase {
  private Console $console;

  function setUp(): void {
    $this->console = new Console();
    $this->console->setTextWriter(new EchoTextWriter());
    $this->console->setExitOnException(false);
  }

  function testAddCommand() {
    set_error_handler(function (int $errno, string $errstr, string $errfile, int $errline) {
      if ($errno === E_DEPRECATED) {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
      }
    });

    $this->console->addCommand(TestCommand::class);
    $this->assertArrayHasKey('test', $this->console->getCommands());
    $this->assertEquals(TestCommand::class, $this->console->getCommand('test'));

    $this->console->addCommand(TestCommand::class, 'test-alias');
    $this->assertArrayHasKey('test-alias', $this->console->getCommands());
    $this->assertEquals(TestCommand::class, $this->console->getCommand('test-alias'));

    $this->console->addCommand([TestStatic::class, 'sayHello'], 'say-hello');
    $this->assertArrayHasKey('say-hello', $this->console->getCommands(), print_r($this->console->getCommands(), true));
    $this->assertEquals(TestStatic::class . '::sayHello', $this->console->getCommand('say-hello'));

    $this->console->addCommand('var_dump');
    $this->assertArrayHasKey('var-dump', $this->console->getCommands());
    $this->assertEquals('var_dump', $this->console->getCommand('var-dump'));

    $this->console->addCommand(array(new TestCommand($this->console), 'execute'), 'test-callback');
    $this->assertArrayHasKey('test-callback', $this->console->getCommands());
    self::assertIsArray($this->console->getCommand('test-callback'));

    $this->console->addCommand(function () {
      echo 'hello!';
    }, 'hello');
    $this->assertArrayHasKey('hello', $this->console->getCommands());
    $this->assertInstanceOf('Closure', $this->console->getCommand('hello'));
  }

  function testAddCommands() {
    $this->console->addCommands(array(
      'ConsoleKit\Tests\TestCommand',
      'test-alias' => 'ConsoleKit\Tests\TestCommand'
    ));

    $this->assertArrayHasKey('test', $this->console->getCommands());
    $this->assertEquals('ConsoleKit\Tests\TestCommand', $this->console->getCommand('test'));
    $this->assertArrayHasKey('test-alias', $this->console->getCommands());
    $this->assertEquals('ConsoleKit\Tests\TestCommand', $this->console->getCommand('test-alias'));
  }

  function testAddCommandsFromDir() {
    $this->console->addCommandsFromDir(__DIR__, 'ConsoleKit\Tests');
    $this->assertArrayHasKey('test', $this->console->getCommands());
    $this->assertEquals('ConsoleKit\Tests\TestCommand', $this->console->getCommand('test'));
  }

  function testExecute() {
    $this->expectOutputString("hello unknown!\n");
    $this->console->addCommand('ConsoleKit\Tests\TestCommand');
    $this->console->execute('test');
  }

  function testExecuteWithArgs() {
    $this->expectOutputString("hello foo bar!\n");
    $this->console->addCommand('ConsoleKit\Tests\TestCommand');
    $this->console->execute('test', array('foo', 'bar'));
  }

  function testExecuteWithOption() {
    $this->expectOutputString("hello foobar!\n");
    $this->console->addCommand('ConsoleKit\Tests\TestCommand');
    $this->console->execute('test', array(), array('name' => 'foobar'));
  }

  function testExecuteSubCommand() {
    $this->console->addCommand('ConsoleKit\Tests\TestSubCommand', 'test');
    $this->assertEquals('hello foobar!', $this->console->execute('test', array('say-hello', 'foobar')));
    $this->assertEquals('hi foobar!', $this->console->execute('test', array('say-hi', 'foobar')));
  }

  function testExecuteFunction() {
    $this->expectOutputString("\033[31mhello foobar!\033[0m\n");
    $this->console->addCommand(function ($args, $opts, $console) {
      $console->writeln(Colors::colorize(sprintf("hello %s!", $args[0]), $opts['color']));
    }, 'test');
    $this->console->addCommand('test2', function ($args, $opts, $console) {
      return "success";
    });
    $this->console->execute('test', array('foobar'), array('color' => 'red'));
    $this->assertEquals("success", $this->console->execute('test2'));
  }

  function testRun() {
    $this->expectOutputString("hello unknown!\n");
    $this->console->addCommand('ConsoleKit\Tests\TestCommand');
    $this->console->run(array('test'));
  }

  function testDefaultCommand() {
    $this->expectOutputString("hello unknown!\n");
    $this->console->addCommand('ConsoleKit\Tests\TestCommand', null, true);
    $this->console->run(array());
  }

  function testOneCommandWithArguments() {
    $this->expectOutputString("hello foobar!\n");
    $this->console->addCommand('ConsoleKit\Tests\TestCommand', null, true);
    $this->console->setSingleCommand(true);
    $this->console->run(array('foobar'));
  }
}
