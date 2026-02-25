<?php

if (! defined('ABSPATH')) {
    exit;
}

class DOM_Divi_Module
{
    public static function init(): void
    {
        add_action('et_builder_ready', array(__CLASS__, 'register_module'));
    }

    public static function register_module(): void
    {
        if (! class_exists('ET_Builder_Module')) {
            return;
        }

        if (! class_exists('DOM_Program_Card_Module')) {
            require_once DOM_DIR . 'includes/class-dom-program-card-module.php';
        }

        new DOM_Program_Card_Module();
    }
}
