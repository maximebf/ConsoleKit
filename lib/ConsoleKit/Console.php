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
    private static $commands = array();
    
    /**
     * @param array $args
     * @return mixed Results of the command callback
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
        if (function_exists($classname)) {
            return call_user_func($classname, $args, $options);
        }
        $instance = new $classname();
        return $instance->execute($args, $options);
    }
    
    /**
     * Registers a command
     * 
     * @param array|string $command Command name to be used in the shell
     * @param string $class Associated class name, function name or Command instance
     */
    public static function register($command, $class = null)
    {
        if (is_array($command)) {
            foreach ($command as $k => $v) {
                self::register($k, $v);
            }
            return;
        }

        if ($class === null || (!class_exists($class) && !function_exists($class))) {
            throw new ConsoleException("'$class' must reference a class or a function");
        }
        if (class_exists($class) && !is_subclass_of($class, 'ConsoleKit\Command')) {
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