<?php

namespace MBDI\Templates;

class Text extends Base {
	public function render(): string {
		return $this->get_value();
	}
}
