<?php

namespace MBDI\Templates;
defined( 'ABSPATH' ) || die;

class Oembed extends Text {
	public function render(): string {
		$value = $this->get_value();

		if ( is_array( $value ) ) {
			$value = $value[0];
		}

		return $value ?? '';
	}
}
