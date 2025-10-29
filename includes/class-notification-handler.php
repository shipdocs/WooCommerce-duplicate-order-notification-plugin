<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Class Duplicate_Order_Handler
 *
 * Handles displaying notifications on checkout for duplicate products.
 */
class Duplicate_Order_Handler {

    /**
     * Duplicate checker instance.
     *
     * @var Duplicate_Order_Checker
     */
    private $duplicate_checker;

	/**
	 * Constructor.
	 *
	 * @param Duplicate_Order_Checker $duplicate_checker Duplicate checker instance.
	 */
	public function __construct( $duplicate_checker ) {
		$this->duplicate_checker = $duplicate_checker;

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'woocommerce_checkout_process', array( $this, 'check_for_duplicates' ) );
		add_action( 'woocommerce_after_checkout_form', array( $this, 'print_modal_html' ) );
		add_action( 'woocommerce_review_order_before_submit', array( $this, 'add_nonce_field' ) );
	}

	/**
	 * Add nonce field to checkout form.
	 */
	public function add_nonce_field() {
		wp_nonce_field( 'duplicate_order_prevention_checkout', 'duplicate_order_prevention_nonce' );
	}

	/**
	 * Enqueue CSS and JS assets on checkout page.
	 */
	public function enqueue_assets() {
		if ( ! is_checkout() ) {
			return;
		}

		// Enqueue custom CSS (includes bundled Tailwind utilities).
		wp_enqueue_style(
			'duplicate-order-prevention-css',
			DUPLICATE_ORDER_PREVENTION_PLUGIN_URL . 'assets/css/duplicate-order-prevention.css',
			array(),
			DUPLICATE_ORDER_PREVENTION_VERSION
		);

		// Enqueue custom JS.
		wp_enqueue_script(
			'duplicate-order-prevention-js',
			DUPLICATE_ORDER_PREVENTION_PLUGIN_URL . 'assets/js/duplicate-order-prevention.js',
			array( 'jquery' ),
			DUPLICATE_ORDER_PREVENTION_VERSION,
			true
		);

		// Load script translations if available.
		wp_set_script_translations(
			'duplicate-order-prevention-js',
			'duplicate-order-prevention-for-woocommerce',
			DUPLICATE_ORDER_PREVENTION_PLUGIN_DIR . 'languages'
		);
	}

	/**
	 * Check for duplicate products during checkout process.
	 */
	public function check_for_duplicates() {
		if ( ! is_user_logged_in() ) {
			// For guests, skip duplicate check.
			return;
		}

		// Verify nonce for security.
		if ( ! isset( $_POST['duplicate_order_prevention_nonce'] ) ||
			! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['duplicate_order_prevention_nonce'] ) ), 'duplicate_order_prevention_checkout' ) ) {
			// Nonce verification failed, but allow checkout to proceed (fail open for compatibility).
			return;
		}

		$user_id = get_current_user_id();
		$cart    = WC()->cart;
		if ( ! $cart ) {
			return;
		}

		$cart_items = $cart->get_cart();

		// Check if user has chosen to ignore duplicates this session.
		if ( isset( $_POST['duplicate_order_prevention_ignore'] ) &&
			'yes' === sanitize_text_field( wp_unslash( $_POST['duplicate_order_prevention_ignore'] ) ) ) {
			// User chose to ignore, allow checkout.
			return;
		}

		$duplicates = $this->duplicate_checker->check_duplicates( $user_id, $cart_items );

		if ( ! empty( $duplicates ) ) {
			// Store duplicates in session for use in modal.
			WC()->session->set( 'duplicate_order_prevention_duplicates', $duplicates );

			// Add a WooCommerce notice to trigger modal via JS.
			wc_add_notice(
				__( 'Duplicate products detected in your order. Please review the notification.', 'duplicate-order-prevention-for-woocommerce' ),
				'notice'
			);

			// Prevent checkout from proceeding until user ignores or confirms.
			wc_add_notice(
				__( 'Please review the duplicate order notification below.', 'duplicate-order-prevention-for-woocommerce' ),
				'error'
			);
		} else {
			// Clear duplicates from session if none found.
			if ( WC()->session ) {
				WC()->session->__unset( 'duplicate_order_prevention_duplicates' );
			}
		}
	}

	/**
	 * Print the modal HTML after the checkout form.
	 */
	public function print_modal_html() {
		if ( ! is_checkout() || ! WC()->session ) {
			return;
		}

		$duplicates = WC()->session->get( 'duplicate_order_prevention_duplicates', array() );

		if ( empty( $duplicates ) ) {
			return;
		}

		// Prepare data for JS.
		$modal_data = array();

		foreach ( $duplicates as $product_id => $orders ) {
			$product = wc_get_product( $product_id );
			if ( ! $product ) {
				continue;
			}

			$order_links = array();

			foreach ( $orders as $order_info ) {
				$order_links[] = array(
					'order_id'  => $order_info['order_id'],
					'order_url' => esc_url( $order_info['order_url'] ),
					'status'    => sanitize_text_field( $order_info['status'] ),
				);
			}

			$modal_data[] = array(
				'product_id'   => $product_id,
				'product_name' => $product->get_name(),
				'orders'       => $order_links,
			);
		}

		// Localize data for JS.
		wp_localize_script( 'duplicate-order-prevention-js', 'duplicateOrderPreventionData', $modal_data );

		// Print modal container.
		?>
		<div id="duplicate-order-prevention-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
			<div class="bg-white rounded-lg shadow-lg max-w-lg w-full p-6 relative">
				<button id="duplicate-order-prevention-close" class="absolute top-3 right-3 text-gray-600 hover:text-gray-900" aria-label="<?php esc_attr_e( 'Close notification', 'duplicate-order-prevention-for-woocommerce' ); ?>">
					&times;
				</button>
				<h2 class="text-xl font-semibold mb-4"><?php esc_html_e( 'Duplicate Order Notification', 'duplicate-order-prevention-for-woocommerce' ); ?></h2>
				<div id="duplicate-order-prevention-content" class="space-y-4">
					<!-- Content populated by JS -->
				</div>
				<div class="mt-6 flex justify-end space-x-4">
					<button id="duplicate-order-prevention-ignore" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded">
						<?php esc_html_e( 'Ignore and Proceed', 'duplicate-order-prevention-for-woocommerce' ); ?>
					</button>
				</div>
			</div>
		</div>
		<?php
	}
}
