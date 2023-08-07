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
	public static function from($value, array $field, $raw = true ): string {
		$template = self::get_template( $field['type'] );
		$template = new $template( $value, $field, $raw );
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
	protected static function get_template( string $type ): string {
		$type     = str_replace( ' ', '', ucwords( str_replace( '_', ' ', $type ) ) );
		$template = __NAMESPACE__ . '\\Templates\\' . $type;
		
		if ( class_exists( $template ) ) {
			return $template;
		}

		return __NAMESPACE__ . '\\Templates\\Text';
	}
}
