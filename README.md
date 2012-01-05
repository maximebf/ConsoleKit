# ConsoleKit

PHP 5.3+ library to create command line utilities

## Installation

Download and copy the lib/ConsoleKit folder into php's include path.
You can also add the folder to your include path using *set\_include\_path()*:

    set_include_path('/path/to/lib' . PATH_SEPARATOR . get_include_path());

You will also need to configure a class autoloader. You can use the following one:

    spl_autoload_register(function($className) {
        $filename = str_replace('\\', DIRECTORY_SEPARATOR, trim($className, '\\')) . '.php';
        require_once $filename;
    });

## Usage

    <?php

    class HelloWorldCommand extends ConsoleKit\Command
    {
        public function execute(array $args, array $options = array())
        {
            $this->writeln('hello world!');
        }
    }

    ConsoleKit\Console::register('hello', 'HelloWorldCommand');
    ConsoleKit\Console::run();
