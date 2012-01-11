<?php

namespace ConsoleKit\Tests;

class TestCommand extends \ConsoleKit\Command
{
    public function execute(array $args, array $opts)
    {
        $this->writeln("foobar");
    }
}