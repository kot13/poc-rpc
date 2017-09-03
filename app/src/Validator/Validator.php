<?php
namespace App\Validator;

use App\Validator\Rules\IRule;

class Validator
{
    /**
     * @var array
     */
    public $errors = [];

    /**
     * @var array
     */
    protected $input = [];

    /**
     * @param array $data
     * @param array $rules
     *
     * @return $this
     */
    public function validate(array $data, array $rules)
    {
        $this->errors = [];
        $this->input = $data;

        $fields = array_unique(array_merge(array_keys($data), array_keys($rules)));

        foreach ($fields as $field) {
            $value = isset($data[$field]) ? $data[$field] : null;
            $param = $rules[$field];

            if ($param['isRequired'] && is_null($value)) {
                die('required');
            }

            if (gettype($value) !== $param['type'] && !is_null($value)) {
                die('type');
            }

            if (!in_array($param['type'], ['object', 'array'])) {
                foreach ($param['rules'] as $rule) {
                    $continue = $this->validateAgainstRule($field, $value, $this->getRuleName($rule), $this->getRuleArgs($rule));
                    if (!$continue) {
                        break;
                    }
                }
            } else {
                // TODO: Иначе перебираем свойства вложенного объекта/массива
            }
        }
        return $this;
    }

    /**
     * @return bool
     */
    public function passes()
    {
        return empty($this->errors);
    }

    /**
     * @return bool
     */
    public function fails()
    {
        return !$this->passes();
    }

    /**
     * @return array
     */
    public function errors()
    {
        return $this->errors;
    }

    /**
     * @param  string $rule
     *
     * @return bool
     */
    protected function ruleHasArgs($rule)
    {
        return isset(explode(':', $rule)[1]);
    }

    /**
     * @param  string $rule
     *
     * @return array
     */
    protected function getRuleArgs($rule)
    {
        if (!$this->ruleHasArgs($rule)) {
            return [];
        }

        $args = explode(':', $rule)[1];
        $args = preg_replace('/\s+/', '', $args);
        $args = explode(',', $args);

        return $args;
    }

    /**
     * @param  string $rule
     *
     * @return string
     */
    protected function getRuleName($rule)
    {
        return explode(':', $rule)[0];
    }

    /**
     * @param  string $field
     * @param  string $value
     * @param  string $rule
     * @param  array $args
     *
     * @return boolean
     */
    protected function validateAgainstRule($field, $value, $rule, array $args)
    {
        $ruleClass = 'App\\Validator\\Rules\\' . ucfirst($rule) . 'Rule';
        /** @var IRule $ruleObject */
        $ruleObject = new $ruleClass();

        $passed = $ruleObject->run($value, $this->input, $args);
        if (!$passed) {
            $this->errors[$field][] = $ruleObject->error();

            return false;
        }

        return true;
    }
}