<?php

namespace MBDI\Templates;

class Image extends Base {
	/**
	 * Return first attachment URL so that it can be used in Divi.
	 *
	 * @return string
	 */
	public function render(): string {
		$value = $this->get_value();
		$value = array_keys( $value );
		
		$url = wp_get_attachment_url( intval( $value[0] ) );

		return $this->raw ? $url : '<img src="' . esc_url( $url ) . '" alt="' . esc_attr( $value['alt'] ?? '' ) . '" />';
	}
}
