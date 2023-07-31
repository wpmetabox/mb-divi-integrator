<?php

namespace MBDI\Templates;

class FileAdvanced extends Base {
	/**
	 * Convert selected value to labels, separated by comma
	 *
	 * @return string
	 */
	public function render(): string {
		$value = $this->get_value();
		
		if ( empty( $value ) || !is_array($value) ) {
			return '';
		}

		$value = array_map( function ( $value ) {
            return "<a href='{$value['url']}' target='_blank'>{$value['title']}</a>\n";
        }, $value );

        return implode( ', ', $value );
	}
}
