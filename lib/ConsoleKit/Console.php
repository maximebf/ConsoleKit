<?php
 
namespace ConsoleKit;

class Console
{
    /** @var array */
    private static $commands = array();
    
    /**
     * @param array $args
     */
    public static function run(array $argv = null, $optionsParserClassName = 'ConsoleKit\OptionsParser')
    {
        if ($argv === null) {
            $argv = isset($_SERVER['argv']) ? array_slice($_SERVER['argv'], 1) : array();
        }

        $optionParser = new $optionsParserClassName();
        list($args, $options) = $optionParser->parse($argv);
        if (!count($args)) {
            throw new ConsoleException("Missing command name");
        }
        
        $command = array_shift($args);
        if (!isset(self::$commands[$command])) {
            throw new ConsoleException("Command '$command' does not exist");
        }
        
        $classname = self::$commands[$command];
        $instance = new $classname();
        return $instance->execute($args, $options);
    }
    
    /**
     * Registers a command
     * 
     * @param array|string $command
     * @param string $class
     */
    public static function register($command, $class = null)
    {
        if (is_array($command)) {
            foreach ($command as $k => $v) {
                self::register($k, $v);
            }
            return;
        }

        if ($class === null || !is_subclass_of($class, 'ConsoleKit\Command')) {
            throw new ConsoleException("'$class' must be a subclass of 'ConsoleKit\Command'");
        }
        self::$commands[$command] = $class;
    }

    /**
     * Registers commands from a directory
     * 
     * @param string $dir
     * @param string $namespace
     * @param bool $includeFiles
     */
    public static function registerFromDir($dir, $namespace = '', $includeFiles = false)
    {
        foreach (new DirectoryIterator($dir) as $file) {
            if ($file->isDir() || substr($file->getFilename(), 0, 1) === '.' 
                || strtolower(substr($file->getFilename(), -4)) !== '.php') {
                    continue;
            }
            $name = substr($file->getFilename(), 0, -4);
            $className = trim($namespace . '\\' . $name, '\\');
            $name = strtolower(preg_replace('/(?<=[a-z])([A-Z])/', '-$1', $name));

            if ($includeFiles) {
                include $file->getPathname();
            }
            self::register($name, $className);
        }
    }
}