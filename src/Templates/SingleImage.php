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
		
		$url = $value['url'] ?? '';

        return $this->raw ? $url : '<img src="' . esc_url( $url ) . '" alt="' . esc_attr( $value['alt'] ?? '' ) . '" />';
	}
}
