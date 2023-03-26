<?php
namespace MBDI\Templates;

class Select extends Base
{
    public function render()
    {
        $value = $this->get_value();
        
        if (empty($value)) {
            return '';
        }

        if (!is_array($value)) {
            $value = [$value];
        }

        $field = $this->get_field();
        
        $options = $field['options'];

        $value = array_map(function($value) use ($options) {
            return isset($options[$value]) ? $options[$value] : '';
        }, $value);

        if (is_array($value)) {
            $value = implode(', ', $value);
        }

        return $value;
    }
}