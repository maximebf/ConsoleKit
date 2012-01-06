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
 * A TextWriter proxy which formats text before writing it
 */
class FormatedWriter extends TextFormater implements TextWriter
{
    /** @var TextWriter */
    protected $textWriter;

    /**
     * @param TextWriter $writer
     * @param array $formatOptions
     */
    public function __construct(TextWriter $writer, array $formatOptions = array())
    {
        parent::__construct($formatOptions);
        $this->textWriter = $writer;
    }

    /**
     * @param TextWriter $writer
     * @return FormatedWriter
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
     * Writes some text to the text writer
     * 
     * @param string $text
     * @param array $formatOptions
     * @return Command
     */
    public function write($text, $pipe = TextWriter::STDOUT)
    {
        $this->textWriter->write($this->format($text), $pipe);
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
    public function writeln($text = '', $pipe = TextWriter::STDOUT)
    {
        return $this->write("$text\n", $pipe);
    }
}