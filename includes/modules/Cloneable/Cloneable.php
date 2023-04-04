<?php

class MBDI_Clonable extends ET_Builder_Module
{

	public $slug       = 'mbdi_cloneable';
	public $vb_support = 'on';
	
	protected $module_credits = [
		'module_uri' => 'https://metabox.io/plugins/mb-divi-integrator',
		'author'     => '',
		'author_uri' => '',
	];

	public function init()
	{
		$this->name = esc_html__('Meta Box Cloneable', 'mbdi');
	}

	public function get_fields()
	{
		// Get all divi layouts.
		$layouts = get_posts([
			'post_type' => 'et_pb_layout',
			'posts_per_page' => -1,
		]);

		$options = [];
		$options[''] = esc_html__('Select a layout', 'mbdi');

		foreach ($layouts as $layout) {
			$options[$layout->ID] = $layout->post_title;
		}

		// Get all cloneable fields from Meta Box.
		$query = new MBDI\FieldQuery([
			'clone' => true,
		]);

		$fields = $query->pluck('name', 'id');
		$fields = array_merge(['' => esc_html__('Select a field', 'mbdi')], $fields);

		return [
			'layout' => [
				'label' => esc_html__('Layout', 'mbdi'),
				'type' => 'select',
				'options' => $options,
				'option_category' => 'configuration',
				'description' => esc_html__('Select a layout to display.', 'mbdi'),
				'toggle_slug' => 'main_content',
			],
			'field' => [
				'label' => esc_html__('Cloneable Field', 'mbdi'),
				'type' => 'select',
				'options' => $fields,
				'option_category' => 'configuration',
				'description' => esc_html__('Select cloneable field which applied to that layout.', 'mbdi'),
				'toggle_slug' => 'main_content',
			],
		];
	}

	public function render($attrs, $content = null, $render_slug)
	{
		global $wp_query;
		$post_id = $wp_query->get_queried_object_id();

		$post_type   = get_post_type($post_id);
		$object_type = 'post';
		$sub_type    = $post_type;
		$identifier  = $post_id;
		$args = [];

		$cloneable_field = $this->props['field'];

		if (!$cloneable_field) {
			return;
		}

		$field_registry = rwmb_get_registry('field');

		// Check if the field is cloneable.
		$field = $field_registry->get($cloneable_field, $object_type, $sub_type);
	
		if (!$field || !$field['clone']) {
			return;
		}

		// Get all groups.
		$groups = rwmb_meta($cloneable_field, $args, $identifier);

		$layout = $this->props['layout'];

		if (!$layout) {
			return;
		}

		$layout = get_post($layout);

		$content = $layout->post_content;
		$output = '';

		// Loop through each group.
		foreach ($groups as $index => $group) {
			// Add index and cloneable field name to each field so children fields can know 
			// which group they belong to and current loop index.
			$append = ' index="' . $index . '" array="' . $cloneable_field . '"';
			$scoped = str_replace('[mbdi_field', "[mbdi_field {$append}", $content);
			$output .= $scoped;
		}

		return do_shortcode($output);
	}
}

new MBDI_Clonable;
