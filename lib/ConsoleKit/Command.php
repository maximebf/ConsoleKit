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

use Closure;

/**
 * Base class for commands
 *
 * Can be used to represent a single command:
 *
 * <code>
 *     class MyCommand extends Command {
 *         public function execute(array $args, array $options = array()) {
 *             $this->writeln('hello world');
 *         }
 *     }
 * </code>
 *
 * If "execute()" is not overriden, the execution will be forwarded to subcommand
 * methods. The subcommand name will be the first argument value and the associated
 * method must be prefixed with "execute".
 *
 * <code>
 *     class MyCommand extends Command {
 *         public function executeSub1(array $args, array $options = array()) {
 *             $this->writeln('hello world');
 *         }
 *         public function executeSub2(array $args, array $options = array()) {
 *             $this->writeln('hello world');
 *         }
 *     }
 * </code>
 *
 */
abstract class Command
{
    /** @var Console */
    protected $console;

    /**
     * @param Console $console
     */
    public function __construct(Console $console)
    {
        $this->console = $console;
    }

    /** 
     * @return Console
     */
    public function getConsole()
    {
        return $this->console;
    }

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
     * Formats text using a {@see TextFormater}
     *
     * @param string $text
     * @param array $formatOptions
     * @return string
     */
    public function format($text, array $formatOptions = array())
    {
        $formater = new TextFormater($formatOptions);
        return $formater->format($text);
    }

    /**
     * Executes the closure with a {@see FormatedWriter} object as the first
     * argument, initialized with the $formatOptions array
     *
     * <code>
     * $this->context(array('quote' => ' * '), function($f) {
     *     $f->writeln('quoted text');
     * })
     * </code>
     *
     * @param array $formatOptions
     * @param Closure $closure
     */
    public function context(array $formatOptions, Closure $closure)
    {
        $formater = new FormatedWriter($this->console->getTextWriter(), $formatOptions);
        return $closure($formater);
    }
    
    /**
     * Writes some text to the text writer
     * 
     * @param string $text
     * @param array $formatOptions
     * @return Command
     */
    public function write($text, array $formatOptions = array())
    {
        $this->console->getTextWriter()->write($this->format($text, $formatOptions));
        return $this;
    }
    
    /**
     * Writes a line of text
     * 
     * @see write()
     * @param string $text
     * @param array $formatOptions
     * @return Command
     */
    public function writeln($text, array $formatOptions = array())
    {
        return $this->write("$text\n", $formatOptions);
    }
}