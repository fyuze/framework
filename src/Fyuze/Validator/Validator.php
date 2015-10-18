<?php
namespace Fyuze\Validator;

class Validator
{
    /**
     * @var array
     */
    protected $input;

    /**
     * @var array
     */
    protected $rules;

    /**
     * @var array
     */
    protected $errors = [];

    /**
     * Validator constructor.
     * @param array $input
     * @param array $rules
     */
    public function __construct(array $input = [], array $rules = [])
    {
        $this->input = $input + array_fill_keys(array_keys($rules), null);
        $this->rules = $rules;
    }

    /**
     * @return bool
     */
    public function passes()
    {
        return $this->validate();
    }

    /**
     * @return bool
     */
    public function failed()
    {
        return !$this->validate();
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return bool
     */
    protected function validate()
    {
        foreach ($this->input as $key => $input) {
            if (array_key_exists($key, $this->rules) && $rule = $this->parseRule($key, $input)) {
                $this->errors[] = $rule;
            }
        }

        return count($this->errors) === 0;
    }

    /**
     * @param $key
     * @param $value
     * @return bool|string
     */
    protected function parseRule($key, $value)
    {
        $params = [$value];

        foreach (explode('|', $this->rules[$key]) as $rule) {

            list($rule, $params) = $this->buildRule($rule, $params);

            if (call_user_func_array([$this, $rule], $params) === false) {
                return sprintf('Error with rule %s on field %s with %s', $rule, $key, implode(',', $params));
            }
        }

        return false;
    }

    /**
     * @param $rule
     * @param $params
     * @return array
     */
    protected function buildRule($rule, $params)
    {
        if (strpos($rule, ':') !== false) {
            $data = explode(':', $rule);
            $params[] = array_pop($data);
            $rule = reset($data);
        }

        return [$rule, $params];
    }

    /**
     * @param $value
     * @return bool
     */
    protected function required($value)
    {
        return !empty($value);
    }

    /**
     * @param $value
     * @param $max
     * @return bool
     */
    protected function max($value, $max)
    {
        return strlen($value) <= $max;
    }

    /**
     * @param $value
     * @param $min
     * @return bool
     */
    protected function min($value, $min)
    {
        return strlen($value) >= $min;
    }
}
