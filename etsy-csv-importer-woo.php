<?php
/**
 * Plugin Name: Etsy CSV Importer for WooCommerce
 * Plugin URI: https://github.com/mhoffmann2670/etsy-csv-importer-woo
 * Description: Import Etsy CSV files directly into WooCommerce
 * Version: 1.0.0
 * Author: Developer
 * License: GPL v2 or later
 */

if (!defined('ABSPATH')) exit;

define('ECIM_VERSION', '1.0.0');
define('ECIM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ECIM_PLUGIN_URL', plugin_dir_url(__FILE__));

function ecim_check_woocommerce() {
      if (current_user_can('manage_options') && !class_exists('WooCommerce')) {
                echo '<div class="error"><p>Etsy CSV Importer requires WooCommerce.</p></div>';
      }
}
add_action('admin_notices', 'ecim_check_woocommerce');

if (class_exists('WooCommerce')) {
      require_once ECIM_PLUGIN_DIR . 'includes/class-ecim-admin.php';
      require_once ECIM_PLUGIN_DIR . 'includes/class-ecim-importer.php';
      add_action('plugins_loaded', function() { new ECIM_Admin(); });
}

register_activation_hook(__FILE__, function() {
      $upload_dir = wp_upload_dir();
      wp_mkdir_p($upload_dir['basedir'] . '/etsy-csv-imports');
});
?>
