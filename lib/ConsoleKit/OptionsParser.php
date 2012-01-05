<?php

namespace ConsoleKit;

class OptionsParser
{
    public function parse(array $argv = null)
    {
        $args = array();
        $options = array();

        for ($i = 0, $c = count($argv); $i < $c; $i++) {
            $arg = $argv[$i];
            if ($arg === '--') {
                $args[] = implode(' ', array_slice($argv, $i + 1));
                break;
            }
            if (substr($arg, 0, 2) === '--') {
                $key = substr($arg, 2);
                $value = true;
                if (($sep = strpos($arg, '=')) !== false) {
                    $key = substr($arg, 2, $sep - 2);
                    $value = substr($arg, $sep + 1);
                }
                $options[$key] = $value;
            } else if (substr($arg, 0, 1) === '-') {
                foreach (str_split(substr($arg, 1)) as $key) {
                    $options[$key] = true;
                }
            } else {
                $args[] = $arg;
            }
        }

        return array($args, $options);
    }
}