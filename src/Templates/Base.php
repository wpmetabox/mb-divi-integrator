<?php
namespace MBDI\Templates;

abstract class Base
{
    /**
     * Value retrieved from meta box.
     * 
     * @var string|array|object
     */
    protected $value;

    /**
     * Field settings.
     * 
     * @var array
     */
    protected $field;

    /**
     * Constructor.
     * 
     * @param string|array|object $value Value retrieved from meta box.
     * @param array $field Field settings.
     */
    public function __construct($value, $field)
    {
        $this->value = $value;
        $this->field = $field;
    }

    /**
     * Get field value.
     * 
     * @return string|array|object
     */
    public function get_value()
    {
        return $this->value;
    }

    /**
     * Get meta box field.
     * 
     * @return array
     */
    public function get_field()
    {
        return $this->field;
    }

    /**
     * Render output.
     * 
     * @return string
     */
    abstract public function render();
}
