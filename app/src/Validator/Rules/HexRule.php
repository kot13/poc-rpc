<?php
namespace App\Validator\Rules;

class HexRule implements IRule
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
        return ctype_xdigit($value);
    }

    /**
     * @return string
     */
    public function error()
    {
        return 'Value must be a valid hex.';
    }
}