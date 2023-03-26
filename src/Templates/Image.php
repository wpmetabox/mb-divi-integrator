<?php
namespace MBDI\Templates;

class Image extends Base
{
    public function render()
    {
        $value = $this->get_value();
        $value = array_keys($value);

        return esc_url(wp_get_attachment_url(intval($value[0])));
    }
}