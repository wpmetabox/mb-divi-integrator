<?php

namespace MBDI;

class Output {
	/**
	 * Get output from field value.
	 *
	 * @param mixed $value Field value.
	 * @param array $field Field settings.
	 *
	 * @return string        Output.
	 */
	public static function from( $value, $field ) {
		$template = self::get_template( $field['type'] );
		$template = new $template( $value, $field );
		$rendered = $template->render();

		return et_core_esc_previously( $rendered );
	}

	/**
	 * Get template class name from field type.
	 *
	 * @param string $type Field type.
	 *
	 * @return string       Template class name.
	 */
	protected static function get_template( $type ) {
		$type     = str_replace( ' ', '', ucwords( str_replace( '_', ' ', $type ) ) );
		$template = __NAMESPACE__ . '\\Templates\\' . $type;

		if ( class_exists( $template ) ) {
			return $template;
		}

		return __NAMESPACE__ . '\\Templates\\Text';
	}
}
