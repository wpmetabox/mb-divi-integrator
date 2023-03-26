<?php

namespace MBDI\Templates;

class Text extends Base {
	public function render() {
		return $this->get_value();
	}
}
