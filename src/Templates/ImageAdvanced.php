<?php

namespace MBDI\Templates;

class ImageAdvanced extends Base {
	public function render(): string {
        $value = $this->get_value();

        if (is_array($value) && !isset($value['url'])) {
            $value = reset($value);
        }

        return $value['url'] ?? '';
    }
}
