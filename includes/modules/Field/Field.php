<?php

use MBDI\FieldQuery;
use MBDI\Output;

class MBDI_Field extends ET_Builder_Module
{
    public $slug       = 'mbdi_field';

    public $vb_support = 'on';

    protected $module_credits = [
        'module_uri' => 'https://metabox.io/plugins/mbdi',
        'author'     => '',
        'author_uri' => '',
    ];

    public function init()
    {
        $this->name = esc_html__('Meta Box Field', 'mbdi');
    }

    public function get_fields()
    {
        $query = new MBDI\FieldQuery();
        $fields = $query->pluck('name', 'id');
        $options = array_merge(['' => esc_html__('Select a field', 'mbdi')], $fields);

        return [
            'metabox_field_id' => [
                'label' => esc_html__('Meta Box Field', 'mbdi'),
                'type' => 'select',
                'options' => $options,
                'option_category' => 'basic_option',
                'description' => esc_html__('Select a field to display.', 'mbdi'),
                'toggle_slug' => 'main_content',
            ],
        ];
    }

    public function render($attrs, $content = null, $render_slug)
    {
        global $wp_query;

        $index = $attrs['index'] ?? null;
        $array = $attrs['array'] ?? null;

        $meta_key = $this->props['metabox_field_id'];
        $post_id = $wp_query->get_queried_object_id();

        $post_type   = get_post_type($post_id);
        $object_type = 'post';
        $sub_type    = $post_type;
        $identifier  = $post_id;
        $args = [];
        $field_registry = rwmb_get_registry('field');

        // Single field.
        if (is_null($index)) {
            $field_value = rwmb_meta($meta_key, $args, $identifier);
            $field  = $field_registry->get($meta_key, $object_type, $sub_type);
        }

        // Cloneable field.
        if (is_numeric($index)) {
            $array_field = $field_registry->get($array, $object_type, $sub_type);

            if ($array_field['type'] === 'group') {
                $field_value = rwmb_meta($array, $args, $identifier);
                $field_value = $field_value[$index][$meta_key];

                foreach ($array_field['fields'] as $f) {
                    if ($f['id'] === $meta_key) {
                        $field = $f;
                        break;
                    }
                }
            } else {
                $field_value = rwmb_meta($meta_key, $args, $identifier);
                $field_value = $field_value[$index];
                $field  = $field_registry->get($meta_key, $object_type, $sub_type);
            }
        }

        return Output::from($field_value, $field);
    }
}

new MBDI_Field;
