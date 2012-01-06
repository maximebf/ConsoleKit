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

/**
 * Renders a progress bar
 *
 * <code>
 * $total = 100;
 * $progress = new ProgressBar($total);
 * for ($i = 0; $i < $total; $i++) {
 *     $this->write($progress->incr());
 *     usleep(100000);
 * }
 * $this->writeln();
 * </code>
 */
class ProgressBar
{
    /** @var int */
    protected $value = 0;

    /** @var int */
    protected $total = 0;

    /** @var int */
    protected $size = 0;

    /** @var int */
    protected $startTime;

    /**
     * @param int $total
     * @param int $size
     * @param array $textOptions
     */
    public function __construct($total = 100, $size = 50)
    {
        $this->size = $size;
        $this->start($total);
    }

    /**
     * @param int $size
     * @return ProgressBar
     */
    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param int $total
     * @return ProgressBar
     */
    public function start($total = 100)
    {
        $this->value = 0;
        $this->total = $total;
        $this->startTime = time();
        return $this;
    }

    /**
     * @param number $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return number
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Increments the value
     *
     * @param int $increment
     * @return ProgressBar
     */
    public function incr($increment = 1)
    {
        $this->value += $increment;
        return $this;
    }

    /**
     * Generates the text to write for the current values
     *
     * @return string
     */
    public function render()
    {
        $percentage = (double) ($this->value / $this->total);
        $speed = (time() - $this->startTime) / $this->value;
        $remaining = number_format(round($speed * ($this->total - $this->value), 2), 2);

        $progress = floor($percentage * $this->size);
        $output = "\r[" . str_repeat('=', $progress);
        if ($progress < $this->size) {
            $output .= ">" . str_repeat(' ', $this->size - $progress);
        } else {
            $output .= '=';
        }
        $output .= sprintf('] %s%% %s/%s %s sec remaining', round($percentage * 100, 0), $this->value, $this->total, $remaining);
        return $output;
    }

    public function __toString()
    {
        return $this->render();
    }
}