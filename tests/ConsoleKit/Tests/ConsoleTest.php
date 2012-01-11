<?php

namespace ConsoleKit\Tests;

use ConsoleKit\Console,
    ConsoleKit\DefaultOptionsParser,
    ConsoleKit\EchoTextWriter;

class ConsoleTest extends ConsoleKitTestCase
{
    public function setUp()
    {
        $this->console = new Console();
        $this->console->setTextWriter(new EchoTextWriter());
    }

    public function testAddCommand()
    {
        $this->console->addCommand('ConsoleKit\Tests\TestCommand');
        $this->assertArrayHasKey('test', $this->console->getCommands());
        $this->assertEquals('ConsoleKit\Tests\TestCommand', $this->console->getCommand('test'));

        $this->console->addCommand('ConsoleKit\Tests\TestCommand', 'test-alias');
        $this->assertArrayHasKey('test-alias', $this->console->getCommands());
        $this->assertEquals('ConsoleKit\Tests\TestCommand', $this->console->getCommand('test-alias'));

        $this->console->addCommand('var_dump');
        $this->assertArrayHasKey('var-dump', $this->console->getCommands());
        $this->assertEquals('var_dump', $this->console->getCommand('var-dump'));

        $this->console->addCommand(array(new TestCommand($this->console), 'execute'), 'test-callback');
        $this->assertArrayHasKey('test-callback', $this->console->getCommands());
        $this->assertInternalType('array', $this->console->getCommand('test-callback'));
        
        $this->console->addCommand(function($args, $opts) { echo 'hello!'; }, 'hello');
        $this->assertArrayHasKey('hello', $this->console->getCommands());
        $this->assertInstanceOf('Closure', $this->console->getCommand('hello'));
    }

    public function testAddCommands()
    {
        $this->console->addCommands(array(
            'ConsoleKit\Tests\TestCommand',
            'test-alias' => 'ConsoleKit\Tests\TestCommand'
        ));

        $this->assertArrayHasKey('test', $this->console->getCommands());
        $this->assertEquals('ConsoleKit\Tests\TestCommand', $this->console->getCommand('test'));
        $this->assertArrayHasKey('test-alias', $this->console->getCommands());
        $this->assertEquals('ConsoleKit\Tests\TestCommand', $this->console->getCommand('test-alias'));
    }

    public function testAddCommandsFromDir()
    {
        $this->console->addCommandsFromDir(__DIR__, 'ConsoleKit\Tests');
        $this->assertArrayHasKey('test', $this->console->getCommands());
        $this->assertEquals('ConsoleKit\Tests\TestCommand', $this->console->getCommand('test'));
    }

    public function testExecute()
    {
        $this->expectOutputString("foobar\n");
        $this->console->addCommand('ConsoleKit\Tests\TestCommand');
        $this->console->execute('test');
    }

    public function testRun()
    {
        $this->expectOutputString("foobar\n");
        $this->console->addCommand('ConsoleKit\Tests\TestCommand');
        $this->console->run(array('test'));
    }

    public function testDefaultCommand()
    {
        $this->expectOutputString("foobar\n");
        $this->console->addCommand('ConsoleKit\Tests\TestCommand', null, true);
        $this->console->run(array());
    }
}