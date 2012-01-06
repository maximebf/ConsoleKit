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
 * Functions to colorize text
 *
 * Text can be colorized (foreground only) by using a static method named after the color code.
 *
 * <code>
 * $text = Colors::colorize('hello world', Colors::RED)
 * $text = Colors::colorize('hello world', 'red')
 * $text = Colors::colorize('hello world', Colors::RED | Colors::BOLD)
 * $text = Colors::colorize('hello world', 'red+bold')
 * $text = Colors::red('hello world');
 * </code>
 */
class Colors
{
    const RESET = "\033[0m";
    const BOLD = 10;

    const BLACK = 0;
    const RED = 1;
    const GREEN = 2;
    const YELLOW = 3;
    const BLUE = 4;
    const MAGENTA = 5;
    const CYAN = 6;
    const WHITE = 7;

    /** @var array */
    private static $colors = array(
        'black' => 0, 
        'red' => 1, 
        'green' => 2, 
        'yellow' => 3, 
        'blue' => 4, 
        'magenta' => 5, 
        'cyan' => 6, 
        'white' => 7
    );

    /**
     * Returns a colorized string
     *
     * @param string $text
     * @param string $fgcolor (a key from the $foregroundColors array)
     * @param string $bgcolor (a key from the $backgroundColors array)
     * @return string
     */
    public static function colorize($text, $fgcolor = null, $bgcolor = null)
    {
        $colors = '';
        if ($bgcolor) {
            $colors .= self::getBgColorString(self::getColorCode($bgcolor));
        }
        if ($fgcolor) {
            $colors .= self::getFgColorString(self::getColorCode($fgcolor));
        }
        return $colors . $text . self::RESET;
    }

    /**
     * Returns a color code
     *
     * $color can be a string with the color name, or one of the color constants.
     *
     * @param int|string $color
     * @param bool $bold
     * @return int
     */
    public static function getColorCode($color, $bold = false)
    {
        $code = (int) $color;
        if (is_string($color)) {
            $color = strtolower($color);
            if (strpos($color, '+bold') !== false) {
                list($color, $_) = explode('+', $color, 2);
                $bold = true;
            }
            if (!isset(self::$colors[$color])) {
                throw new ConsoleException("Color name '$color' does not exist");
            }
            $code = self::$colors[$color];
        }
        if ($bold) {
            $code = $code | self::BOLD;
        }
        return $code;
    }

    /**
     * Returns a foreground color string
     *
     * @param int $color
     * @return string
     */
    public static function getFgColorString($colorCode)
    {
        $bold = '';
        if (($colorCode & self::BOLD) === self::BOLD) {
            $colorCode = $colorCode & ~self::BOLD;
            $bold = '1;';
        }
        return "\033[{$bold}3{$colorCode}m";
    }

    /**
     * Returns a background color string
     *
     * @param int $color
     * @return string
     */
    public static function getBgColorString($colorCode)
    {
        return "\033[4{$colorCode}m";
    }
    
    public static function __callStatic($method, $args)
    {
        return self::colorize($args[0], $method);
    }
}