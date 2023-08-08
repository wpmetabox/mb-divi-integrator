<?php

namespace MBDI\Templates;

class FileInput extends Base {
	/**
	 * Convert selected value to labels, separated by comma
	 *
	 * @return string
	 */
	public function render(): string {
		$value = $this->get_value();
		
		return $this->raw ? esc_url($value) : "<a href='{$value}' target='_blank'>{$value}</a>\n";
	}
}
