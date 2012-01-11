<?php
/**
 * ConsoleKit
 * Copyright (c) 2012 Maxime Bouroumeau-Fuseau
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author Maxime Bouroumeau-Fuseau
 * @copyright 2012 (c) Maxime Bouroumeau-Fuseau
 * @license http://www.opensource.org/licenses/mit-license.php
 * @link http://github.com/maximebf/ConsoleKit
 */
 
namespace ConsoleKit;

/**
 * Registry of available commands and command runner
 */
class Console
{
    /** @var array */
    protected $commands = array();

    /** @var OptionsParser */
    protected $optionsParser;

    /** @var TextWriter */
    protected $textWriter;

    /** @var bool */
    protected $exitOnException = true;

    /** @var string */
    protected $helpCommand = 'help';

    /** @var string */
    protected $helpCommandClass = 'ConsoleKit\HelpCommand';

    /**
     * @param array $commands
     */
    public function __construct(array $commands = array(), OptionsParser $parser = null, TextWriter $writer = null)
    {
        $this->optionsParser = $parser ?: new DefaultOptionsParser();
        $this->textWriter = $writer ?: new StdTextWriter();
        $this->addCommand($this->helpCommandClass, $this->helpCommand);
        $this->addCommands($commands);
    }

    /**
     * @param OptionsParser $parser
     * @return Console
     */
    public function setOptionsParser(OptionsParser $parser)
    {
        $this->optionsParser = $parser;
        return $this;
    }

    /**
     * @return OptionsParser
     */
    public function getOptionsParser()
    {
        return $this->optionsParser;
    }

    /**
     * @param TextWriter $writer
     * @return Console
     */
    public function setTextWriter(TextWriter $writer)
    {
        $this->textWriter = $writer;
        return $this;
    }

    /**
     * @return TextWriter
     */
    public function getTextWriter()
    {
        return $this->textWriter;
    }

    /**
     * Sets whether to call exit(1) when an exception is caught
     *
     * @param bool $exit
     * @return Console
     */
    public function setExitOnException($exit = true)
    {
        $this->exitOnException = $exit;
        return $this;
    }

    /**
     * @return bool
     */
    public function exitsOnException()
    {
        return $this->exitOnException;
    }

    /**
     * Adds multiple commands at once
     *
     * @see addCommand()
     * @param array $commands
     * @return Console
     */
    public function addCommands(array $commands)
    {
        foreach ($commands as $name => $command) {
            $this->addCommand($command, is_numeric($name) ? null : $name);
        }
        return $this;
    }
    
    /**
     * Registers a command
     * 
     * @param string $class Associated class name, function name or Command instance
     * @param string $alias Command name to be used in the shell
     * @return Console
     */
    public function addCommand($class, $alias = null)
    {
        if (!function_exists($class) && !class_exists($class)) {
            throw new ConsoleException("'$class' must reference a class or a function");
        }
        $name = $class;
        if (strtolower(substr($name, -7)) === 'command') {
            $name = substr($name, 0, -7);
        }
        if (class_exists($class, false)) {
            if (!is_subclass_of($class, 'ConsoleKit\Command')) {
                throw new ConsoleException("'$class' must be a subclass of 'ConsoleKit\Command'");
            }
            $name = Utils::dashized($name);
        } else {
            $name = strtolower(trim(str_replace('_', '-', $name), '-'));
        }
        $this->commands[$alias ?: $name] = $class;
        return $this;
    }

    /**
     * Registers commands from a directory
     * 
     * @param string $dir
     * @param string $namespace
     * @param bool $includeFiles
     * @return Console
     */
    public function addCommandsFromDir($dir, $namespace = '', $includeFiles = false)
    {
        foreach (new DirectoryIterator($dir) as $file) {
            if ($file->isDir() || substr($file->getFilename(), 0, 1) === '.' 
                || strtolower(substr($file->getFilename(), -4)) !== '.php') {
                    continue;
            }
            if ($includeFiles) {
                include $file->getPathname();
            }
            $className = trim($namespace . '\\' . substr($file->getFilename(), 0, -4), '\\');
            $this->addCommand($className);
        }
        return $this;
    }

    /**
     * @param string $name
     * @return string
     */
    public function getCommand($name)
    {
        if (!isset($this->commands[$name])) {
            throw new ConsoleException("Command '$name' does not exist");
        }
        return $this->commands[$name];
    }

    /**
     * @return array
     */
    public function getCommands()
    {
        return $this->commands;
    }
    
    /**
     * @param array $args
     * @return mixed Results of the command callback
     */
    public function run(array $argv = null)
    {
        try {
            if ($argv === null) {
                $argv = isset($_SERVER['argv']) ? array_slice($_SERVER['argv'], 1) : array();
            }

            list($args, $options) = $this->getOptionsParser()->parse($argv);
            if (!count($args)) {
                $this->textWriter->writeln(Colors::red("Missing command name"));
                $args[] = $this->helpCommand;
            }

            $command = array_shift($args);
            return $this->execute($command, $args, $options);

        } catch (\Exception $e) {
            $this->writeException($e);
            if ($this->exitOnException) {
                exit(1);
            }
            throw $e;
        }
    }

    /**
     * Executes a command
     *
     * @param string $command
     * @param array $args
     * @param array $options
     * @return mixed
     */
    public function execute($command, array $args = array(), array $options = array())
    {
        if (!isset($this->commands[$command])) {
            throw new ConsoleException("Command '$command' does not exist");
        }
        
        $classname = $this->commands[$command];
        if (function_exists($classname)) {
            return call_user_func($classname, $args, $options, $this);
        }
        $instance = new $classname($this);
        return $instance->execute($args, $options);
    }

    /**
     * Writes an error message to stderr
     *
     * @param \Exception $e
     */
    public function writeException(\Exception $e)
    {
        $text = sprintf("Uncaught exception '%s' with message '%s' in %s:%s\nStack trace:\n%s", 
            get_class($e),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString()
        );

        $box = new Widgets\Box($this->textWriter, $text);
        $this->textWriter->writeln(Colors::colorize($box, Colors::RED | Colors::BOLD));
    }
}