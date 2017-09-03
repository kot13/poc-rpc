<?php
namespace App\Validator\Rules;

class EmailRule implements IRule
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
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * @return string
     */
    public function error()
    {
        return 'Value must be a valid email address.';
    }
}