<?php
namespace App\Validator\Rules;

class PhoneRule implements IRule
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
        $value = preg_replace('/[^0-9]/', '', $value);

        return strlen($value) === 11;
    }

    /**
     * @return string
     */
    public function error()
    {
        return 'Value must be a valid phone number.';
    }
}