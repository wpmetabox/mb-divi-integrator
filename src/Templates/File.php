<?php

namespace MBDI\Templates;

class File extends Base {
	public function render(): string {
		$value = $this->get_value();
		
		if ( empty( $value ) || !is_array($value) ) {
			return '';
		}

		if (is_array($value)) {
			$value = reset($value);
		}
		
        return "<a href='{$value['url']}' target='_blank'>{$value['title']}</a>\n";
	}
}
