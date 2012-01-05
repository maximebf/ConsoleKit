<?php

namespace ConsoleKit;

abstract class Command
{
    /**
     * If not overriden, will execute the command specified
     * as the first argument
     * 
     * Commands must be defined as methods named after the
     * command, prefixed with execute (eg. create -> executeCreate)
     * 
     * @param array $args
     * @param array $options
     */
    public function execute(array $args, array $options = array())
    {
        if (!count($args)) {
            throw new ConsoleException("Not enough arguments");
        }
        
        $command = str_replace(' ', '', ucwords(str_replace('-', ' ', array_shift($args))));
        $method = "execute$command";
        
        if (!method_exists($this, $method)) {
            throw new ConsoleException("Command '$command' does not exist");
        }
        
        return call_user_func(array($this, $method), $args, $options);
    }
    
    /**
     * Prints some text
     * 
     * @param string $text
     * @param array $options
     */
    public function write($text, array $options = array())
    {
        Text::write($text, $options);
    }
    
    /**
     * Prints a line of text
     * 
     * @param string $text
     * @param array $options
     */
    public function writeln($text, array $options = array())
    {
        Text::writeln($text, $options);
    }
}