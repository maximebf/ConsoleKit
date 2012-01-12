<?php

namespace ConsoleKit\Tests;

class TestComputedCommand extends \ConsoleKit\Command
{
    /**
     * @compute-params
     */
    public function execute($name, $color = 'green', $_args = array(), $_opts = array())
    {
        $this->writeln(sprintf("hello %s!", $name), $color);
        return array($_args, $_opts);
    }
}
