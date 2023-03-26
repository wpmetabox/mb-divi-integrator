<?php
namespace MBDI\Templates;

abstract class Base
{
    protected $value;

    protected $field;

    public function __construct($value, $field)
    {
        $this->value = $value;
        $this->field = $field;
    }

    public function get_value()
    {
        return $this->value;
    }

    public function get_field()
    {
        return $this->field;
    }

    abstract public function render();
}