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
 * Utility functions to format and output text
 */
class Text
{
    const RESET_COLORS = "\033[0m";

    /** @var array */
    public static $backgroundColors = array(
        'black' => "\033[40m",
        'red' => "\033[41m",
        'green' => "\033[42m",
        'yellow' => "\033[43m",
        'blue' => "\033[44m",
        'magenta' => "\033[45m",
        'cyan' => "\033[46m",
        'white' => "\033[47m"
    );

    /** @var array */
    public static $foregroundColors = array(
        'black' => "\033[30m",
        'red' => "\033[31m",
        'green' => "\033[32m",
        'yellow' => "\033[33m",
        'blue' => "\033[34m",
        'magenta' => "\033[35m",
        'cyan' => "\033[36m",
        'white' => "\033[37m",
        'bright_grey' => "\033[1;30m",
        'bright_red' => "\033[1;31m",
        'bright_green' => "\033[1;32m",
        'bright_yellow' => "\033[1;33m",
        'bright_blue' => "\033[1;34m",
        'bright_magenta' => "\033[1;35m",
        'bright_cyan' => "\033[1;36m",
        'bright_white' => "\033[1;37m"
    );

    /** @var int */
    public static $indentWidth = 2;

    /** @var int */
    public static $defaultIndent = 0;

    /** @var string */
    public static $defaultColor;

    /**
     * Formats $text according to $options
     * 
     * Available options:
     *  - indent: number of indentation
     *  - bgcolor: background color (a key from the $backgroundColors array)
     *  - fgcolor: foreground color (a key from the $foregroundColors array)
     * 
     * @param string $text
     * @param array $options
     */
    public static function format($text, array $options = array())
    {
        $indent = self::$defaultIndent;
        if (isset($options['indent'])) {
            $indent += $options['indent'];
        }
        if (isset($options['bgcolor'])) {
            $text = self::$backgroundColors[$options['bgcolor']] . $text;
        }
        if (isset($options['fgcolor'])) {
            $text = self::$foregroundColors[$options['fgcolor']] . $text;
        } else if (self::$defaultColor) {
            $text = self::$foregroundColors[self::$defaultColor] . $text;
        }
        return str_repeat(' ', $indent * self::$indentWidth) . $text . self::RESET_COLORS;
    }
    
    /**
     * Prints some formated text
     * 
     * @param string $text
     * @param array $options
     */
    public static function write($text, array $options = array())
    {
        echo self::format($text, $options);
    }
    
    /**
     * Prints a line of formated text
     * 
     * @param string $text
     * @param array $options
     */
    public static function writeln($text, array $options = array())
    {
        self::write($text, $options);
        echo "\n";
    }
}