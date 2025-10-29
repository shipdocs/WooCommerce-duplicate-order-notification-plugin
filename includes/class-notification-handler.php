<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Class DOPWC_Notification_Handler
 *
 * Handles displaying notifications on checkout for duplicate products.
 */
class DOPWC_Notification_Handler {

    /**
     * Duplicate checker instance.
     *
     * @var DOPWC_Duplicate_Checker
     */
    private $duplicate_checker;

	/**
	 * Constructor.
	 *
	 * @param DOPWC_Duplicate_Checker $duplicate_checker Duplicate checker instance.
	 */
	public function __construct( $duplicate_checker ) {
		$this->duplicate_checker = $duplicate_checker;

		// Classic checkout hooks
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'woocommerce_checkout_process', array( $this, 'check_for_duplicates' ) );
		add_action( 'woocommerce_after_checkout_form', array( $this, 'print_modal_html' ) );
		add_action( 'woocommerce_review_order_before_submit', array( $this, 'add_nonce_field' ) );

		// Block checkout hooks
		add_action( 'woocommerce_store_api_checkout_update_order_meta', array( $this, 'check_for_duplicates_block' ) );
		add_action( 'woocommerce_add_to_cart', array( $this, 'check_duplicate_on_add_to_cart' ), 10, 6 );

		// Product page hooks
		add_action( 'woocommerce_before_add_to_cart_form', array( $this, 'show_duplicate_notice_on_product_page' ) );
	}

	/**
	 * Add nonce field to checkout form.
	 */
	public function add_nonce_field() {
		wp_nonce_field( 'duplicate_order_prevention_checkout', 'duplicate_order_prevention_nonce' );
	}

	/**
	 * Enqueue CSS and JS assets on checkout and product pages.
	 */
	public function enqueue_assets() {
		// Enqueue CSS on checkout and product pages
		if ( is_checkout() || is_product() ) {
			wp_enqueue_style(
				'duplicate-order-prevention-css',
				DOPWC_PLUGIN_URL . 'assets/css/duplicate-order-prevention.css',
				array(),
				DOPWC_VERSION
			);
		}

		// Enqueue JS only on checkout
		if ( is_checkout() ) {
			wp_enqueue_script(
				'duplicate-order-prevention-js',
				DOPWC_PLUGIN_URL . 'assets/js/duplicate-order-prevention.js',
				array( 'jquery' ),
				DOPWC_VERSION,
				true
			);

			// Load script translations if available.
			wp_set_script_translations(
				'duplicate-order-prevention-js',
				'duplicate-order-prevention-for-woocommerce',
				DOPWC_PLUGIN_DIR . 'languages'
			);
		}
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
			// Nonce verification failed. This could happen on initial checkout page load
			// or if the form is manipulated. Silently return to avoid breaking checkout.
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
	 * Check for duplicates when using block checkout.
	 *
	 * @param \WC_Order $order Order object.
	 */
	public function check_for_duplicates_block( $order ) {
		if ( ! is_user_logged_in() ) {
			return;
		}

		$user_id = get_current_user_id();
		$items   = $order->get_items();

		if ( empty( $items ) ) {
			return;
		}

		// Convert order items to cart-like format
		$cart_items = array();
		foreach ( $items as $item ) {
			$cart_items[] = array(
				'product_id' => $item->get_product_id(),
				'variation_id' => $item->get_variation_id(),
			);
		}

		$duplicates = $this->duplicate_checker->check_duplicates( $user_id, $cart_items );

		if ( ! empty( $duplicates ) ) {
			// Add order note about duplicates
			$order->add_order_note( __( 'Warning: This order contains products that are in other pending/recent orders.', 'duplicate-order-prevention-for-woocommerce' ) );
		}
	}

	/**
	 * Check for duplicates when adding to cart.
	 *
	 * @param string $cart_item_key Cart item key.
	 * @param int    $product_id    Product ID.
	 * @param int    $quantity      Quantity.
	 * @param int    $variation_id  Variation ID.
	 * @param array  $variation     Variation data.
	 * @param array  $cart_item_data Cart item data.
	 */
	public function check_duplicate_on_add_to_cart( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {
		if ( ! is_user_logged_in() ) {
			return;
		}

		$user_id = get_current_user_id();
		$cart_items = array(
			array(
				'product_id' => $product_id,
				'variation_id' => $variation_id,
			),
		);

		$duplicates = $this->duplicate_checker->check_duplicates( $user_id, $cart_items );

		if ( ! empty( $duplicates ) ) {
			// Build message with order links
			$product = wc_get_product( $variation_id ? $variation_id : $product_id );
			$product_name = $product ? $product->get_name() : __( 'This product', 'duplicate-order-prevention-for-woocommerce' );

			$order_links = array();
			foreach ( $duplicates as $product_id_key => $orders ) {
				foreach ( $orders as $order_info ) {
					$order_id = $order_info['order_id'];
					$order = wc_get_order( $order_id );
					if ( $order ) {
						$order_url = $order->get_view_order_url();
						$order_status = wc_get_order_status_name( $order->get_status() );
						$order_links[] = sprintf(
							'<a href="%s" target="_blank" rel="noopener">%s (#%d - %s)</a>',
							esc_url( $order_url ),
							__( 'Order', 'duplicate-order-prevention-for-woocommerce' ),
							$order_id,
							esc_html( $order_status )
						);
					}
				}
			}

			if ( count( $order_links ) === 1 ) {
				$message = sprintf(
					/* translators: 1: product name, 2: order link */
					__( 'Notice: %1$s is already in %2$s', 'duplicate-order-prevention-for-woocommerce' ),
					'<strong>' . esc_html( $product_name ) . '</strong>',
					$order_links[0]
				);
			} else {
				$message = sprintf(
					/* translators: 1: product name, 2: comma-separated order links */
					__( 'Notice: %1$s is already in these orders: %2$s', 'duplicate-order-prevention-for-woocommerce' ),
					'<strong>' . esc_html( $product_name ) . '</strong>',
					implode( ', ', $order_links )
				);
			}

			wc_add_notice( $message, 'notice' );
		}
	}

	/**
	 * Show duplicate notice on product page.
	 */
	public function show_duplicate_notice_on_product_page() {
		if ( ! is_user_logged_in() ) {
			return;
		}

		global $product;
		if ( ! $product ) {
			return;
		}

		$user_id = get_current_user_id();
		$product_id = $product->get_id();

		$cart_items = array(
			array(
				'product_id' => $product_id,
				'variation_id' => 0,
			),
		);

		$duplicates = $this->duplicate_checker->check_duplicates( $user_id, $cart_items );

		if ( empty( $duplicates ) ) {
			return;
		}

		// Build the notice HTML
		echo '<div class="woocommerce-info dopwc-product-notice">';
		echo '<strong>' . esc_html__( 'You already ordered this product:', 'duplicate-order-prevention-for-woocommerce' ) . '</strong>';

		foreach ( $duplicates as $product_id_key => $orders ) {
			foreach ( $orders as $order_info ) {
				$order_id = $order_info['order_id'];
				$order = wc_get_order( $order_id );

				if ( ! $order ) {
					continue;
				}

				$order_url = $order->get_view_order_url();
				$order_status = wc_get_order_status_name( $order->get_status() );
				$order_date = $order->get_date_created()->date_i18n( wc_date_format() );

				// Get quantity from this order
				$quantity = 0;
				foreach ( $order->get_items() as $item ) {
					if ( $item->get_product_id() === $product_id ) {
						$quantity = $item->get_quantity();
						break;
					}
				}

				echo '<div class="dopwc-order-item">';
				echo '<span class="dopwc-order-icon">📦</span>';
				printf(
					'<span class="dopwc-order-details"><a href="%s" target="_blank" rel="noopener">%s</a> – %s × %d (%s)</span>',
					esc_url( $order_url ),
					/* translators: %1$d: WooCommerce order ID. */
					sprintf( esc_html__( 'Order #%1$d', 'duplicate-order-prevention-for-woocommerce' ), intval( $order_id ) ),
					esc_html( $order_status ),
					intval( $quantity ),
					esc_html( $order_date )
				);
				echo '</div>';
			}
		}

		echo '</div>';
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
