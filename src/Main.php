<?php
namespace MBDI;

class Main
{
    public function __construct()
    {
        // add_action('plugins_loaded', [$this, 'init']);

        // add_filter( 'et_builder_dynamic_content_meta_value', array( $this, 'maybe_filter_dynamic_content_meta_value' ), 10, 3 );
		add_filter( 'et_builder_custom_dynamic_content_fields', array( $this, 'maybe_filter_dynamic_content_fields' ), 10, 3 );
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
	public function maybe_filter_dynamic_content_meta_value( $meta_value, $meta_key, $post_id ) {
		global $wp_query;

		$post_type  = get_post_type( $post_id );
		$identifier = $post_id;

		if ( et_theme_builder_is_layout_post_type( $post_type ) ) {
			return $this->format_placeholder_value( $meta_key, $post_id );
		}

		$is_blog_query = isset( $wp_query->et_pb_blog_query ) && $wp_query->et_pb_blog_query;

		if ( ! $is_blog_query && ( is_category() || is_tag() || is_tax() ) ) {
			$term       = get_queried_object();
			$identifier = "{$term->taxonomy}_{$term->term_id}";
		} elseif ( is_author() ) {
			$user       = get_queried_object();
			$identifier = "user_{$user->ID}";
		}

		$meta_box_value = rwmb_meta( $meta_key, array(), $identifier );

		if ( false === $meta_box_value ) {
			return $meta_value;
		}

		$acf_field = get_field_object( $meta_key, $post_id, array( 'load_value' => false ) );
		$meta_box_value = $this->format_field_value( $meta_box_value, $acf_field );

		if ( is_array( $meta_box_value ) || is_object( $meta_box_value ) ) {
			// Avoid exposing unformatted values.
			$meta_box_value = '';
		}

		return (string) $meta_box_value;
	}


    /**
	 * Format a placeholder value based on the field type.
	 *
	 * @param string  $meta_key
	 * @param integer $post_id
	 *
	 * @return mixed
	 */
	protected function format_placeholder_value( $meta_key, $post_id ) {
		if ( function_exists( 'acf_get_field' ) ) {
			$field = acf_get_field( $meta_key );
		} else {
			$field = get_field_object( $meta_key, false, array( 'load_value' => false ) );
		}

		if ( ! is_array( $field ) || empty( $field['type'] ) ) {
			return esc_html__( 'Your ACF Field Value Will Display Here', 'et_builder' );
		}

		$value = esc_html(
			sprintf(
				// Translators: %1$s: ACF Field name
				__( 'Your "%1$s" ACF Field Value Will Display Here', 'et_builder' ),
				$field['label']
			)
		);

		switch ( $field['type'] ) {
			case 'image':
				$value = ET_BUILDER_PLACEHOLDER_LANDSCAPE_IMAGE_DATA;
				break;

			case 'taxonomy':
				$value = esc_html(
					implode(
						', ',
						array(
							__( 'Category 1', 'et_builder' ),
							__( 'Category 2', 'et_builder' ),
							__( 'Category 3', 'et_builder' ),
						)
					)
				);
				break;
		}

		return $value;
	}


    public function maybe_filter_dynamic_content_fields( $custom_fields, $post_id, $raw_custom_fields ) {
		if ( ! $post_id || et_theme_builder_is_layout_post_type( get_post_type( $post_id ) ) ) {
			$post_id = 0;
		}

		return $this->maybe_filter_dynamic_content_fields_from_groups( $custom_fields, $post_id, $raw_custom_fields );
	}
    
    public function maybe_filter_dynamic_content_fields_from_groups( $custom_fields, $post_id, $raw_custom_fields ) {
        $meta_box_registry = rwmb_get_registry( 'meta_box' );
        $meta_boxes = $meta_box_registry->all();
        
        if ( ! $meta_boxes ) {
            return $custom_fields;
        }
     

        foreach ($meta_boxes as $name => $meta_box) {
            $meta_box = $meta_box->meta_box;

            foreach ($meta_box['fields'] as $field) {
                // if (isset($field['type']) && $field['type'] === 'group') {
                //     $custom_fields = array_merge(
                //         $this->maybe_filter_dynamic_content_fields_from_groups($custom_fields, $post_id, $field['fields']), $custom_fields
                //     );
                // }

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

                if ( current_user_can( 'unfiltered_html' ) ) {
					$settings['fields']['enable_html'] = array(
						'label'   => esc_html__( 'Enable raw HTML', 'et_builder' ),
						'type'    => 'yes_no_button',
						'options' => array(
							'on'  => et_builder_i18n( 'Yes' ),
							'off' => et_builder_i18n( 'No' ),
						),
						// Set enable_html default to `on` for taxonomy fields so builder
						// automatically renders taxonomy list properly as unescaped HTML.
						'default' => 'taxonomy' === $field['type'] ? 'on' : 'off',
						'show_on' => 'text',
					);
				}

				$custom_fields[ "custom_meta_{$field['id']}" ] = $settings;
            }
        }

		return $custom_fields;
	}

    public function expand_fields( $fields, $name_prefix = '', $label_prefix = '' ) {
		$expanded = array();

		foreach ( $fields as $field ) {
			$expanded[] = array(
				array_merge(
					$field,
					array(
						'name'  => $name_prefix . $field['id'],
						'label' => $label_prefix . $field['label'],
					)
				),
			);

			if ( 'group' === $field['type'] ) {
				$expanded[] = $this->expand_fields(
					$field['sub_fields'],
					$name_prefix . $field['id'] . '_',
					$label_prefix . $field['label'] . ': '
				);
			}
		}

		if ( empty( $expanded ) ) {
			return array();
		}
        
		return call_user_func_array( 'array_merge', $expanded );
	}
}