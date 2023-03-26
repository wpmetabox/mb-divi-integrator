<?php
namespace MBDI;

class Output
{
    public static function from($value, $field)
    {
        $template = self::get_template($field['type']);
        $template = new $template($value, $field);
        $rendered = $template->render();

        return et_core_esc_previously($rendered);
    }

    protected static function get_template($type)
    {
        $type = str_replace(' ', '', ucwords(str_replace('_', ' ', $type)));
        $template = __NAMESPACE__ . '\\Templates\\' . $type;
        
        if (class_exists($template)) {
            return $template;
        }

        return __NAMESPACE__ . '\\Templates\\Text';
    }
}