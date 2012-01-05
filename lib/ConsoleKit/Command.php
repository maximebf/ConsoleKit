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