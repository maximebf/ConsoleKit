<?php

namespace ConsoleKit;

class ProgressBar
{
    protected $position = 0;

    protected $total = 0;

    protected $size = 0;

    protected $textOptions = array();

    protected $startTime;

    public function __construct($total = 100, $size = 50, array $textOptions = array())
    {
        $this->size = $size;
        $this->textOptions = $textOptions;
        $this->start($total);
    }

    public function setSize($size)
    {
        $this->size = $size;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function setTextOptions(array $options)
    {
        $this->textOptions = $options;
    }

    public function getTextOptions()
    {
        return $this->textOptions;
    }

    public function start($total = 100)
    {
        $this->position = 0;
        $this->total = $total;
        $this->startTime = time();
    }

    public function step($increment = 1)
    {
        $this->position += $increment;
        return $this;
    }

    public function stop()
    {
        echo "\n";
    }

    public function render()
    {
        $percentage = (double) ($this->position / $this->total);
        $speed = (time() - $this->startTime) / $this->position;
        $remaining = number_format(round($speed * ($this->total - $this->position), 2), 2);

        $progress = floor($percentage * $this->size);
        $output = "\r[" . str_repeat('=', $progress);
        if ($progress < $this->size) {
            $output .= ">" . str_repeat(' ', $this->size - $progress);
        } else {
            $output .= '=';
        }
        $output .= sprintf('] %s%% %s/%s %s sec remaining', round($percentage * 100, 0), $this->position, $this->total, $remaining);
        return Text::format($output, $this->textOptions);
    }

    public function write()
    {
        echo $this->render();
    }

    public function __toString()
    {
        return $this->render();
    }
}