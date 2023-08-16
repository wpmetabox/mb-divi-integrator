<?php

namespace MBDI\Templates;

class ImageAdvanced extends Base {
	public function render(): string {
        $value = $this->get_value();
        $url = '';

        if (is_array($value)) {
            // In group or not in group
            if (isset($value[0]) && is_numeric($value[0])) {
                $url = wp_get_attachment_url($value[0]);
            } else {
                $value = reset($value);
                $url = $value['full_url'] ?? '';
            }
        }

        return $this->raw ? $url : '<img src="' . esc_url( $url ) . '" alt="' . esc_attr( $value['alt'] ?? '' ) . '" />';
    }
}
