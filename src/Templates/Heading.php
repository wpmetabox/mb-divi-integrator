<?php

namespace MBDI\Templates;
defined( 'ABSPATH' ) || die;

class Heading extends Base {
	public function render(): string {
		$field = $this->get_field();

		return "<h3>{$field['name']}</h3>\n";
	}
}
