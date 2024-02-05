<?php

namespace MBDI\Templates;
defined( 'ABSPATH' ) || die;

class CustomHtml extends Base {
	public function render(): string {
		return $this->get_value();
	}
}
