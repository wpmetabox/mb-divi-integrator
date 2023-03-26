<?php
namespace MBDI\Templates;

class SingleImage extends Base
{
    /**
     * Return attachment URL so that it can be used in Divi.
     * 
     * @return string
     */
    public function render()
    {
        $value = $this->get_value();
        
        return esc_url(wp_get_attachment_url(intval($value['ID'])));
    }
}
