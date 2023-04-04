<?php

namespace MBDI;

/**
 * The Divi Integrator extension.
 */
class Extension extends \DiviExtension
{

    /**
     * The gettext domain for the extension's translations.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $gettext_domain = 'mb-divi-integrator';

    /**
     * The extension's WP Plugin name.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $name = 'mb-divi-integrator';

    /**
     * The extension's version
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $version = '1.0.0';

    /**
     *
     * @param string $name
     * @param array  $args
     */
    public function __construct($name = 'mb-divi-integrator', $args = [])
    {
        // define('MBDI\EXTENSION_DEBUG', true);
        
        $this->plugin_dir     = MBDI_PATH . 'includes';
        $this->plugin_dir_url = plugin_dir_url($this->plugin_dir);
        
        parent::__construct($name, $args);
    }
}
