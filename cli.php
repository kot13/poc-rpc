#!/usr/bin/env php
<?php
date_default_timezone_set("Europe/Moscow");

include_once __DIR__ . '/vendor/autoload.php';

class Cli
{
    /**
     * @var int exit code - all went well
     */
    const EXIT_CODE_OK = 0;

    /**
     * @var int exit code - incorrect CLI arguments list
     */
    const EXIT_CODE_ARGS_LIST = 10;

    /**
     * @var array copy of system's argv
     */
    private $args;

    /**
     * Main function - class's entry point
     *
     * @return int
     */
    public function run()
    {
        if (count($this->args) <= 1) {
            $this->help();
            return static::EXIT_CODE_ARGS_LIST;
        }

        switch ($this->args[1]) {
            case "help":
            case "--help":
            default:
                $this->help();
                return static::EXIT_CODE_OK;
        }
    }

    /**
     * Display usage/help screen
     */
    private function help()
    {
        echo PHP_EOL;
        echo "syntaxis: php cli <command> [<args>]", PHP_EOL;
        echo PHP_EOL;
        echo "Commands: ", PHP_EOL;
        echo "php cli --help                             -->   Displays the help menu.",          PHP_EOL;
        echo "php cli generate                           -->   Generate endpoints by contracts.", PHP_EOL;
        echo "php cli runTests <args>                    -->   Run test suite.",                  PHP_EOL;
        echo PHP_EOL;
    }
}

$cli = new Cli($argv);
exit ($cli->run());


