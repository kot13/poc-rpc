<?php
namespace App\Validator\Rules;

class EnumRule implements IRule
{
    /**
     * @param mixed $value
     * @param array $input
     * @param array $args
     *
     * @return bool
     */
    public function run($value, $input, $args)
    {
        return in_array($value, $args);
    }

    /**
     * @return string
     */
    public function error()
    {
        return 'Value must be a valid email address.';
    }
}