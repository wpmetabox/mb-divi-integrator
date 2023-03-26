<?php
namespace MBDI;

class Main
{
	public function __construct()
	{
		add_filter('et_builder_dynamic_content_meta_value', array($this, 'maybe_filter_dynamic_content_meta_value'), 10, 3);
		add_filter('et_builder_custom_dynamic_content_fields', array($this, 'maybe_filter_dynamic_content_fields'), 10, 3);
	}


	/**
	 * Format Meta Box meta values accordingly.
	 *
	 * @since 3.17.2
	 *
	 * @param string  $meta_value
	 * @param string  $meta_key
	 * @param integer $post_id
	 *
	 * @return string
	 */
	public function maybe_filter_dynamic_content_meta_value($meta_value, $meta_key, $post_id)
	{
		global $wp_query;

		$post_type = get_post_type($post_id);
		$object_type = 'post';
		$sub_type = $post_type;
		$identifier = $post_id;

		if (et_theme_builder_is_layout_post_type($post_type)) {
			return $this->format_placeholder_value($meta_key, $post_id);
		}

		$is_blog_query = isset($wp_query->et_pb_blog_query) && $wp_query->et_pb_blog_query;

		if (!$is_blog_query && (is_category() || is_tag() || is_tax())) {
			$object_type = 'term';
			$term = get_queried_object();
			$sub_type = $term->taxonomy;
			$identifier = "{$term->taxonomy}_{$term->term_id}";
		} elseif (is_author()) {
			$object_type = 'user';
			$sub_type = 'user';
			$user = get_queried_object();
			$identifier = "user_{$user->ID}";
		}

		$meta_box_value = rwmb_meta($meta_key, [], $identifier);

		if (false === $meta_box_value) {
			return $meta_value;
		}


		$field_registry = rwmb_get_registry('field');
		$field = $field_registry->get($meta_key, $object_type, $sub_type);

		$meta_box_value = $this->format_field_value($meta_box_value, $field);

		if (is_array($meta_box_value) || is_object($meta_box_value)) {
			// Avoid exposing unformatted values.
			$meta_box_value = '';
		}

		return (string) $meta_box_value;
	}

	/**
	 * Format a field value based on the field type.
	 *
	 * @param mixed $value
	 * @param array $field
	 *
	 * @return mixed
	 */
	protected function format_field_value($value, $field)
	{
		if (!is_array($field) || empty($field['type'])) {
			return $value;
		}

		switch ($field['type']) {
			case 'image':
				$format = isset($field['return_format']) ? $field['return_format'] : 'url';
				switch ($format) {
					case 'array':
						$value = esc_url(wp_get_attachment_url(intval($value['id'])));
						break;
					case 'id':
						$value = esc_url(wp_get_attachment_url(intval($value)));
						break;
				}
				break;

			case 'select':
			case 'checkbox':
				$value = is_array($value) ? $value : array($value);
				$value_labels = array();

				foreach ($value as $value_key) {
					$choice_label = isset($field['choices'][$value_key]) ? $field['choices'][$value_key] : '';
					if (!empty($choice_label)) {
						$value_labels[] = $choice_label;
					}
				}

				$value = implode(', ', $value_labels);
				break;

			case 'true_false':
				$value = et_builder_i18n($value ? 'Yes' : 'No');
				break;

			case 'taxonomy':
				// If taxonomy configuration exist, get HTML output of given value (ids).
				if (isset($field['taxonomy'])) {
					$terms = get_terms(
						array(
							'taxonomy' => $field['taxonomy'],
							'include' => $value,
						)
					);
					$link = 'on';
					$separator = ', ';

					if (is_array($terms)) {
						$value = et_builder_list_terms($terms, $link, $separator);
					}
				}
				break;

			default:
				// Handle multiple values for which a more appropriate formatting method is not available.
				if (isset($field['multiple']) && $field['multiple']) {
					$value = implode(', ', $value);
				}
				break;
		}

		// Value escaping left to the user to decide since some fields hold rich content.
		$value = et_core_esc_previously($value);

		return $value;
	}


	/**
	 * Format a placeholder value based on the field type.
	 *
	 * @param string  $meta_key
	 * @param integer $post_id
	 *
	 * @return mixed
	 */
	protected function format_placeholder_value($meta_key, $post_id)
	{
		$field_registry = rwmb_get_registry('field');
		$field = $field_registry->get($meta_key, 'post');

		if (!is_array($field) || empty($field['type'])) {
			return esc_html__('Your Meta Box Field Value Will Display Here', 'meta-box');
		}

		$value = esc_html(
			sprintf(
				__('Your "%1$s" Meta Box Field Value Will Display Here', 'meta-box'),
				$field['label']
			)
		);

		switch ($field['type']) {
			case 'image':
				$value = ET_BUILDER_PLACEHOLDER_LANDSCAPE_IMAGE_DATA;
				break;

			case 'taxonomy':
				$value = esc_html(
					implode(
						', ',
						array(
							__('Category 1', 'meta-box'),
							__('Category 2', 'meta-box'),
							__('Category 3', 'meta-box'),
						)
					)
				);
				break;
		}

		return $value;
	}


	public function maybe_filter_dynamic_content_fields($custom_fields, $post_id, $raw_custom_fields)
	{
		if (!$post_id || et_theme_builder_is_layout_post_type(get_post_type($post_id))) {
			$post_id = 0;
		}

		return $this->maybe_filter_dynamic_content_fields_from_groups($custom_fields, $post_id, $raw_custom_fields);
	}

	public function maybe_filter_dynamic_content_fields_from_groups($custom_fields, $post_id, $raw_custom_fields)
	{
		$meta_box_registry = rwmb_get_registry('meta_box');
		$meta_boxes = $meta_box_registry->all();

		if (!$meta_boxes) {
			return $custom_fields;
		}

		foreach ($meta_boxes as $name => $meta_box) {
			$meta_box = $meta_box->meta_box;

			$fields = $this->flatten($meta_box['fields']);

			foreach ($fields as $field) {
				$settings = [
					'label' => esc_html($field['name']),
					'type' => 'any',
					'fields' => [
						'before' => [
							'label' => et_builder_i18n('Before'),
							'type' => 'text',
							'default' => '',
							'show_on' => 'text',
						],
						'after' => [
							'label' => et_builder_i18n('After'),
							'type' => 'text',
							'default' => '',
							'show_on' => 'text',
						],
					],
					'meta_key' => $field['id'],
					'custom' => true,
					'group' => 'Meta Box: ' . $meta_box['title'],
				];

				if (current_user_can('unfiltered_html')) {
					$settings['fields']['enable_html'] = array(
						'label' => esc_html__('Enable raw HTML', 'et_builder'),
						'type' => 'yes_no_button',
						'options' => array(
							'on' => et_builder_i18n('Yes'),
							'off' => et_builder_i18n('No'),
						),
						// Set enable_html default to `on` for taxonomy fields so builder
						// automatically renders taxonomy list properly as unescaped HTML.
						'default' => 'taxonomy' === $field['type'] ? 'on' : 'off',
						'show_on' => 'text',
					);
				}

				$custom_fields["custom_meta_{$field['id']}"] = $settings;
			}
		}

		return $custom_fields;
	}

	public function flatten($fields, $label_prefix = '')
	{
		$output = [];

		foreach ($fields as $field) {
			if ($field['type'] === 'tab') {
				continue;
			}

			if (!in_array($field['type'], ['tab', 'group'])) {
				$field['name'] = $label_prefix . $field['name'];
			}

			if ('group' === $field['type']) {
				$children = $this->flatten($field['fields'], $label_prefix . $field['name'] . ': ');
				$output = array_merge($output, $children);
			}

			$output[] = $field;
		}

		return $output;
	}
}