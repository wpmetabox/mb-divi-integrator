<?php

namespace MBDI\Templates;
defined( 'ABSPATH' ) || die;

class TaxonomyAdvanced extends Base {
	public function render(): string {
		$value = $this->get_value();

		if ( empty( $value ) ) {
			return '';
		}

		if ( ! is_array( $value ) ) {
			$value = [ $value ];
		}

		return et_builder_list_terms( $value, true, ', ' );
	}
}
