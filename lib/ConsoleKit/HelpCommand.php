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

class HelpCommand extends Command
{
    public function execute(array $args, array $options = array())
    {
        if (empty($args)) {
            $formater = new TextFormater(array('quote' => ' * '));
            $this->writeln('Available commands:', Colors::BLACK | Colors::BOLD);
            foreach ($this->console->getCommands() as $name => $fqdn) {
                if ($fqdn !== __CLASS__) {
                    $this->writeln($formater->format($name));
                }
            }
            $scriptName = basename($_SERVER['SCRIPT_FILENAME']);
            $this->writeln("Use './$scriptName help command' for more info");
        } else {
            $commandFQDN = $this->console->getCommand($args[0]);
            $help = Help::fromFQDN($commandFQDN, Utils::get($args, 1));
            $this->writeln($help);
        }
    }
}