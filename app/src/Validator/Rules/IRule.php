<?php
namespace App\Validator\Rules;

interface IRule
{
    /**
     * @param  mixed $value
     * @param  array $input
     * @param  array $args
     *
     * @return bool
     */
    public function run($value, $input, $args);

    /**
     * @return string
     */
    public function error();
}