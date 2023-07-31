<?php

namespace MBDI\Templates;

class ImageAdvanced extends Text {
	public function render() {
        $value = $this->get_value();

        if (is_array($value)) {
            $value = $value[0];
        }

        return $value['url'] ?? '';
    }
}
