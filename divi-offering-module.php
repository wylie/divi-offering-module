<?php
/**
 * Plugin Name: Divi Offering Module
 * Description: Programs content type + Divi module for editable program cards.
 * Version: 1.0.0
 * Author: Your Team
 * Text Domain: divi-offering-module
 */

if (! defined('ABSPATH')) {
    exit;
}

define('LCDM_VERSION', '1.0.0');
define('LCDM_FILE', __FILE__);
define('LCDM_DIR', plugin_dir_path(__FILE__));
define('LCDM_URL', plugin_dir_url(__FILE__));

require_once LCDM_DIR . 'includes/class-lcdm-programs.php';
require_once LCDM_DIR . 'includes/class-lcdm-divi-module.php';

LCDM_Programs::init();
LCDM_Divi_Module::init();

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'lcdm-module-styles',
        LCDM_URL . 'assets/css/lcdm-module.css',
        array(),
        LCDM_VERSION
    );
});

add_action('admin_enqueue_scripts', function () {
    wp_enqueue_style(
        'lcdm-module-styles-admin',
        LCDM_URL . 'assets/css/lcdm-module.css',
        array(),
        LCDM_VERSION
    );
});
