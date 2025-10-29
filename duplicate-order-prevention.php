<?php
/**
 * Plugin Name: Duplicate Order Prevention for WooCommerce
 * Plugin URI:  https://github.com/shipdocs/WooCommerce-duplicate-order-notification-plugin
 * Description: Helps prevent duplicate purchases by notifying customers when they attempt to order products that are already in pending orders or recently purchased.
 * Version:     1.1.0
 * Author:      Martin Splinter
 * Author URI:  https://github.com/shipdocs
 * License:     GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: duplicate-order-prevention-for-woocommerce
 * Domain Path: /languages
 *
 * Requires at least: 6.0
 * Tested up to: 6.7
 * Requires PHP: 7.4
 * WC requires at least: 7.0
 * WC tested up to: 9.4
 *
 * @package Duplicate_Order_Prevention
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define plugin constants.
define( 'DUPLICATE_ORDER_PREVENTION_VERSION', '1.1.0' );
define( 'DUPLICATE_ORDER_PREVENTION_PLUGIN_FILE', __FILE__ );
define( 'DUPLICATE_ORDER_PREVENTION_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'DUPLICATE_ORDER_PREVENTION_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

if ( ! class_exists( 'Duplicate_Order_Prevention' ) ) :

final class Duplicate_Order_Prevention {

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	public $version = '1.1.0';

	/**
	 * Singleton instance.
	 *
	 * @var Duplicate_Order_Prevention
	 */
	private static $instance = null;

	/**
	 * Main Duplicate_Order_Prevention Instance.
	 *
	 * Ensures only one instance of Duplicate_Order_Prevention is loaded or can be loaded.
	 *
	 * @return Duplicate_Order_Prevention Main instance.
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
	 *
	 * Cloning and unserializing instances of this class is forbidden.
	 */
	private function __construct() {
		// Intentionally left empty.
	}

	/**
	 * Prevent cloning.
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cloning is forbidden.', 'duplicate-order-prevention-for-woocommerce' ), '1.0.0' );
	}

	/**
	 * Prevent unserializing.
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Unserializing instances is forbidden.', 'duplicate-order-prevention-for-woocommerce' ), '1.0.0' );
	}

	/**
	 * Initialize hooks.
	 */
	private function init_hooks() {
		// Load plugin textdomain for translations.
		add_action( 'init', array( $this, 'load_textdomain' ) );

		// Check WooCommerce dependency.
		add_action( 'admin_notices', array( $this, 'check_woocommerce_active' ) );

		// Declare HPOS compatibility.
		add_action( 'before_woocommerce_init', array( $this, 'declare_hpos_compatibility' ) );

		// Include required files.
		$this->includes();

		// Initialize classes.
		add_action( 'plugins_loaded', array( $this, 'init_classes' ) );
	}

	/**
	 * Declare compatibility with WooCommerce High-Performance Order Storage (HPOS).
	 */
	public function declare_hpos_compatibility() {
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', DUPLICATE_ORDER_PREVENTION_PLUGIN_FILE, true );
		}
	}

	/**
	 * Load plugin textdomain.
	 */
	public function load_textdomain() {
		load_plugin_textdomain(
			'duplicate-order-prevention-for-woocommerce',
			false,
			dirname( plugin_basename( DUPLICATE_ORDER_PREVENTION_PLUGIN_FILE ) ) . '/languages/'
		);
	}

	/**
	 * Check if WooCommerce is active.
	 */
	public function check_woocommerce_active() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			?>
			<div class="notice notice-error">
				<p>
					<strong><?php esc_html_e( 'Duplicate Order Prevention for WooCommerce', 'duplicate-order-prevention-for-woocommerce' ); ?></strong>
					<?php esc_html_e( 'requires WooCommerce to be installed and active.', 'duplicate-order-prevention-for-woocommerce' ); ?>
				</p>
			</div>
			<?php
		}
	}

	/**
	 * Include required files.
	 */
	private function includes() {
		require_once DUPLICATE_ORDER_PREVENTION_PLUGIN_DIR . 'includes/class-duplicate-checker.php';
		require_once DUPLICATE_ORDER_PREVENTION_PLUGIN_DIR . 'includes/class-notification-handler.php';
	}

	/**
	 * Initialize classes.
	 */
	public function init_classes() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		if ( class_exists( 'Duplicate_Order_Checker' ) && class_exists( 'Duplicate_Order_Handler' ) ) {
			$duplicate_checker    = new Duplicate_Order_Checker();
			$notification_handler = new Duplicate_Order_Handler( $duplicate_checker );
		}
	}
}

/**
 * Returns the main instance of Duplicate_Order_Prevention.
 *
 * @return Duplicate_Order_Prevention Main instance.
 */
function duplicate_order_prevention() {
	return Duplicate_Order_Prevention::instance();
}

// Initialize the plugin.
duplicate_order_prevention();

endif;
