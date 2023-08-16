<?php

namespace MBDI\Templates;

class SingleImage extends Base {
	/**
	 * Return attachment URL so that it can be used in Divi.
	 *
	 * @return string
	 */
	public function render(): string {
		$value = $this->get_value();
		
		$url = '';
		if (is_array($value) && isset($value['full_url'])) {
			$url = $value['full_url'];
		}

		if (is_numeric( $value )) {
			$url = wp_get_attachment_url( intval( $value ) );
		}

		return $this->raw ? $url : '<img src="' . esc_url( $url ) . '" alt="' . esc_attr( $value['alt'] ?? '' ) . '" />';
	}
}
