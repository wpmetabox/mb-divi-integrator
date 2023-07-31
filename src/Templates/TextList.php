<?php

namespace MBDI\Templates;

class TextList extends Base {
	public function render(): string {
        $value = $this->get_value();

        if ( empty( $value ) ) {
            return '';
        }

        if ( ! is_array( $value ) ) {
            $value = [ $value ];
        }

        if ( ! is_array( $value[0] ) ) {
            $value = [ $value ];
        }

        $texts = array_map(function($item) {
            return $item[0] . ': ' . $item[1];
        }, $value);

        return implode(', ', $texts);
    }
}
