<?php
/**
 * Plugin Name: Divi Offering Module
 * Description: Offerings content type + Divi module for editable offering cards.
 * Version: 1.0.20
 * Author: Your Team
 * Text Domain: divi-offering-module
 */

if (! defined('ABSPATH')) {
    exit;
}

define('DOM_VERSION', '1.0.20');
define('DOM_FILE', __FILE__);
define('DOM_DIR', plugin_dir_path(__FILE__));
define('DOM_URL', plugin_dir_url(__FILE__));

require_once DOM_DIR . 'includes/class-dom-programs.php';
require_once DOM_DIR . 'includes/class-dom-divi-module.php';

DOM_Programs::init();
DOM_Divi_Module::init();

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'dom-module-styles',
        DOM_URL . 'assets/css/dom-module.css',
        array(),
        DOM_VERSION
    );
});

add_action('admin_enqueue_scripts', function () {
    wp_enqueue_style(
        'dom-module-styles-admin',
        DOM_URL . 'assets/css/dom-module.css',
        array(),
        DOM_VERSION
    );
});
