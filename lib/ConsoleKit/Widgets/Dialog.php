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

namespace ConsoleKit\Widgets;

class Dialog extends AbstractWidget
{
    /**
     * Writes $text and reads the user's input
     *
     * @param string $text
     * @param string $default
     * @param bool $displayDefault
     * @return string
     */
    public function ask($text, $default = '', $displayDefault = true)
    {
        if ($displayDefault && !empty($default)) {
            $defaultText = $default;
            if (strlen($defaultText) > 30) {
                $defaultText = substr($default, 0, 30) . '...';
            }
            $text .= " [$defaultText]";
        }
        $this->textWriter->write("$text ");
        return trim(fgets(STDIN)) ?: $default;
    }

    /**
     * Writes $text (followed by the list of choices) and reads the user response. 
     * Returns true if it matches $expected, false otherwise
     *
     * <code>
     * if($dialog->confirm('Are you sure?')) { ... }
     * if($dialog->confirm('Your choice?', null, array('a', 'b', 'c'))) { ... }
     * </code>
     *
     * @param string $text
     * @param string $expected
     * @param array $choices
     * @param string $default
     * @param string $errorMessage
     * @return bool
     */
    public function confirm($text, $expected = 'y', array $choices = array('Y', 'n'), $default = 'y', $errorMessage = 'Invalid choice')
    {
        $text = $text . ' [' . implode('/', $choices) . ']';
        $choices = array_map('strtolower', $choices);
        $expected = strtolower($expected);
        $default = strtolower($default);
        do {
            $input = strtolower($this->ask($text));
            if (in_array($input, $choices)) {
                return $input === $expected;
            } else if (empty($input) && !empty($default)) {
                return $default === $expected;
            }
            $this->textWriter->writeln($errorMessage);
        } while (true);
    }
}