<?php

set_include_path(__DIR__ . '/lib' . PATH_SEPARATOR . get_include_path());
spl_autoload_register(function($className) {
    $filename = str_replace('\\', DIRECTORY_SEPARATOR, trim($className, '\\')) . '.php';
    require_once $filename;
});

use ConsoleKit\Console,
    ConsoleKit\Command,
    ConsoleKit\Text,
    ConsoleKit\ProgressBar;

class HelloWorldCommand extends Command
{
    /**
     * Prints hello world
     */
    public function execute(array $args, array $options = array())
    {
        $this->writeln('hello world!');
    }
}

class SayHelloCommand extends Command
{
    /**
     * Says hello to someone
     *
     * @arg The name of the person to say hello to
     * @opt color The color in which to print the text
     */
    public function execute(array $args, array $options = array())
    {
        $textOptions = array();
        if (isset($options['color'])) {
            $textOptions['fgcolor'] = strtolower($options['color']);
        }
        $this->writeln(sprintf('hello %s!', $args[0]), $textOptions);
    }
}

class SayCommand extends Command
{
    /**
     * Says hello to someone
     *
     * @arg Name The name of the person to say hello to
     */
    public function executeHello(array $args, array $options = array())
    {
        $this->writeln(sprintf('hello %s!', $args[0]));
    }

    /**
     * Says hi to someone
     *
     * @arg Name The name of the person to say hello to
     */
    public function executeHi(array $args, array $options = array())
    {
        $this->writeln(sprintf('hi %s!', $args[0]));
    }
}

$console = new Console(array(
    'hello' => 'HelloWorldCommand',
    'say-hello' => 'SayHelloCommand',
    'say' => 'SayCommand'
));

$console->run();
