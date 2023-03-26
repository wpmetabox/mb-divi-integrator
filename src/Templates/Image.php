<?php
namespace MBDI\Templates;

class Image extends Base
{
    /**
     * Return first attachment URL so that it can be used in Divi.
     * 
     * @return string
     */
    public function render()
    {
        $value = $this->get_value();
        $value = array_keys($value);

        return esc_url(wp_get_attachment_url(intval($value[0])));
    }
}
