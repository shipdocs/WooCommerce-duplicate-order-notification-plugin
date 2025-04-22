<?php
/**
 * Plugin Name: Duplicate Order Prevention for WooCommerce
 * Plugin URI:  https://github.com/shipdocs/WooCommerce-duplicate-order-notification-plugin
 * Description: Helps prevent duplicate purchases by notifying customers when they attempt to order products that are already in pending orders or recently purchased.
 * Version:     1.0.0
 * Author:      Your Name
 * Author URI:  https://example.com
 * License:     GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: duplicate-order-prevention-for-woocommerce
 *
 * Requires at least: 5.0
 * Tested up to: 6.8
 * WC requires at least: 3.0
 * WC tested up to: 8.0
 *
 * @package Duplicate_Order_Prevention
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Create the required languages directory
if ( ! file_exists( plugin_dir_path( __FILE__ ) . 'languages' ) ) {
    mkdir( plugin_dir_path( __FILE__ ) . 'languages', 0755 );
}

if ( ! class_exists( 'Duplicate_Order_Prevention' ) ) :

final class Duplicate_Order_Prevention {

    /**
     * Plugin version.
     *
     * @var string
     */
    public $version = '1.0.0';

    /**
     * Singleton instance.
     *
     * @var Duplicate_Order_Prevention
     */
    private static $instance = null;

    /**
     * Main Duplicate_Order_Prevention Instance.
     *
     * @return Duplicate_Order_Prevention
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
            self::$instance->init_hooks();
        }
        return self::$instance;
    }

    /**
     * Constructor.
     */
    public function __construct() {
        // Intentionally left empty.
    }

    /**
     * Initialize hooks.
     */
    private function init_hooks() {
        // Load plugin textdomain for translations.
        add_action( 'init', array( $this, 'load_textdomain' ) );

        // Check WooCommerce dependency.
        add_action( 'admin_notices', array( $this, 'check_woocommerce_active' ) );

        // Include required files.
        $this->includes();

        // Initialize classes.
        add_action( 'plugins_loaded', array( $this, 'init_classes' ) );
    }

    /**
     * Load plugin textdomain.
     */
    public function load_textdomain() {
        load_plugin_textdomain( 'duplicate-order-prevention-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }

    /**
     * Check if WooCommerce is active.
     */
    public function check_woocommerce_active() {
        if ( ! class_exists( 'WooCommerce' ) ) {
            echo '<div class="error"><p><strong>' . esc_html__( 'Duplicate Order Prevention for WooCommerce requires WooCommerce to be installed and active.', 'duplicate-order-prevention-for-woocommerce' ) . '</strong></p></div>';
        }
    }

    /**
     * Include required files.
     */
    private function includes() {
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-duplicate-checker.php';
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-notification-handler.php';
    }

    /**
     * Initialize classes.
     */
    public function init_classes() {
        if ( class_exists( 'Duplicate_Order_Checker' ) && class_exists( 'Duplicate_Order_Handler' ) ) {
            $duplicate_checker = new Duplicate_Order_Checker();
            $notification_handler = new Duplicate_Order_Handler( $duplicate_checker );
        }
    }
}

/**
 * Returns the main instance of Duplicate_Order_Prevention.
 *
 * @return Duplicate_Order_Prevention
 */
function Duplicate_Order_Prevention() {
    return Duplicate_Order_Prevention::instance();
}

// Initialize the plugin.
Duplicate_Order_Prevention();

endif;
