<?php
if (!defined('ABSPATH')) exit;

class ECIM_Admin {
      public function __construct() {
                add_action('admin_menu', array($this, 'add_admin_menu'));
                add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
                add_action('wp_ajax_ecim_import_csv', array($this, 'handle_csv_upload'));
      }

    public function add_admin_menu() {
              add_menu_page('Etsy CSV Importer', 'Etsy Importer', 'manage_woocommerce', 'etsy-csv-importer', array($this, 'render_admin_page'), 'dashicons-upload', 56);
    }

    public function enqueue_scripts($hook) {
              if (strpos($hook, 'etsy-csv-importer') === false) return;
              wp_enqueue_style('ecim-admin-style', ECIM_PLUGIN_URL . 'assets/admin-style.css', array(), ECIM_VERSION);
              wp_enqueue_script('ecim-admin-script', ECIM_PLUGIN_URL . 'assets/admin-script.js', array('jquery'), ECIM_VERSION, true);
              wp_localize_script('ecim-admin-script', 'ecimVars', array('ajaxurl' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('ecim_import_nonce')));
    }

    public function render_admin_page() { ?>
                                                 <div class="wrap ecim-container">
                                                     <h1>Etsy CSV Importer for WooCommerce</h1>h1>
                                                             <div class="ecim-main-content">
                                                                               <div class="ecim-upload-section">
                                                                                                     <h2>Import Etsy Products</h2>h2>
                                                                                                   <p>Upload your Etsy CSV export file to import products into WooCommerce.</p>p>
                                                                                                   <form id="ecim-upload-form" enctype="multipart/form-data">
                                                                                                                             <div class="form-group">
                                                                                                                                                           <label for="csv-file">Select CSV File:</label>label>
                                                                                                                                                           <input type="file" id="csv-file" name="csv_file" accept=".csv" required>
                                                                                                                               </div>div>
                                                                                                                             <div class="form-group">
                                                                                                                                                           <h3>Import Options</h3>h3>
                                                                                                                                                           <label><input type="checkbox" name="create_new_products" value="1" checked> Create new products</label>label>
                                                                                                                                                           <label><input type="checkbox" name="publish_products" value="1" checked> Publish products immediately</label>label>
                                                                                                                               </div>div>
                                                                                                                             <button type="submit" class="button button-primary button-large">Import Products</button>button>
                                                                                                     </form>form>
                                                                               </div>div>
                                                                               <div id="ecim-results" class="ecim-results" style="display: none;"><div id="ecim-results-content"></div>div></div>div>
                                                             </div>div>
                                                 </div>div>
    <?php }

      public function handle_csv_upload() {
                check_ajax_referer('ecim_import_nonce', 'nonce');
                if (!current_user_can('manage_woocommerce')) wp_send_json_error('Insufficient permissions');
                          if (!isset($_FILES['csv_file'])) wp_send_json_error('No file uploaded');

                                    $file = $_FILES['csv_file'];
                if ($file['error'] !== UPLOAD_ERR_OK) wp_send_json_error('File upload error');

                          $csv_data = array_map('str_getcsv', file($file['tmp_name']));
                if (empty($csv_data)) wp_send_json_error('CSV file is empty');

                          $importer = new ECIM_Importer();
                $results = $importer->import_csv($csv_data);
                wp_send_json_success($results);
      }
}
?>
                                                                                                   </p></h2>
                                                             </h1>
