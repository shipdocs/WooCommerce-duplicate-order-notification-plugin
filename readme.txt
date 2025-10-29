=== Duplicate Order Prevention for WooCommerce ===
Contributors: martinsplinter
Donate link: https://www.paypal.com/ncp/payment/WQUXP4L7KQKRE
Tags: woocommerce, orders, duplicate-orders, order-management, e-commerce
Requires at least: 6.0
Tested up to: 6.8
Stable tag: 1.2.3
Requires PHP: 7.4
WC requires at least: 7.0
WC tested up to: 9.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Prevents duplicate purchases by showing smart notifications when customers view or order products already in their pending or recent orders.

== Description ==

The Duplicate Order Prevention for WooCommerce plugin helps prevent accidental duplicate purchases by showing smart, contextual notifications when customers are viewing or ordering products that are already in their pending or recent orders.

= Where Notifications Appear =

1. **Product Pages** - Beautiful info box showing existing orders with:
	* Order number (clickable link)
	* Order status
	* Quantity ordered
	* Order date
2. **Add to Cart** - Instant notice when adding duplicate products
3. **Checkout** - Final check before order completion

= Key Features =

* **Smart Detection** - Automatically identifies duplicate products in:
	* Open orders (pending, on-hold, processing)
	* Orders completed within the last 3 months
* **Product Page Notifications** - Shows existing orders directly on product pages with:
	* Clickable order links (opens in new tab)
	* Order status and quantity
	* Clean, professional design
* **Add to Cart Warnings** - Immediate feedback when adding duplicates
* **Checkout Protection** - Final duplicate check with modal display
* **Seamless Integration** - Works with:
	* Classic WooCommerce checkout
	* WooCommerce Checkout Block
	* WooCommerce High-Performance Order Storage (HPOS)
* **Responsive Design** - Looks great on all devices
* **Performance Optimized** - Minimal impact on site speed
* **Security First** - CSRF protection with nonces and proper data sanitization

= Perfect For =

* E-commerce sites with subscription-like products
* Stores where accidental duplicate orders are common
* Businesses wanting to improve customer experience
* Sites needing to reduce order management overhead

= Pro Features (Coming Soon) =

* Customizable time period for duplicate checking
* Admin notification settings
* Custom messages and styling
* Advanced duplicate detection rules

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/duplicate-order-prevention-for-woocommerce` directory
2. Activate the plugin through the 'Plugins' screen in WordPress
3. No configuration needed - works automatically with WooCommerce checkout

= Requirements =

* WordPress 6.0 or higher
* WooCommerce 7.0 or higher
* PHP 7.4 or higher

== Frequently Asked Questions ==

= Does this work with guest checkout? =

No, the plugin requires users to be logged in as it needs to check their order history.

= Where do the notifications appear? =

Notifications appear in three places:
1. On product pages - shows existing orders with details
2. When adding to cart - instant notice about duplicates
3. During checkout - final check before order completion

= Can customers still place duplicate orders if they want to? =

Yes, the plugin shows warnings but customers can proceed if they intentionally want to order duplicates.

= Does it work with the new WooCommerce Checkout Block? =

Yes, the plugin supports both classic WooCommerce checkout and the new Checkout Block.

= Will this slow down my site? =

No, the plugin is optimized to perform checks efficiently with minimal performance impact.

= Does it work with all WooCommerce themes? =

Yes, the plugin uses standard WooCommerce hooks and modern CSS for compatibility.

== Screenshots ==

1. Product page notification showing existing orders
2. Add to cart duplicate warning
3. Checkout duplicate detection modal
4. Mobile view of notifications

== Changelog ==

= 1.2.1 =
* Fixed product page notification formatting with improved flexbox layout
* Better icon spacing and alignment

= 1.2.0 =
* Added product page notifications - shows existing orders directly on product pages
* Added add-to-cart duplicate warnings with order links
* Added support for WooCommerce Checkout Block
* Improved notification messages with clickable order links
* Enhanced UX with order status, quantity, and date information
* Better visual design with flexbox layout and proper spacing
* Removed debug logging for production readiness
* Performance improvements

= 1.1.0 =
* Updated for WordPress 6.7 compatibility
* Updated for WooCommerce 9.4 compatibility
* Added WooCommerce High-Performance Order Storage (HPOS) compatibility
* Added CSRF protection with nonces
* Improved security: replaced deprecated date() with gmdate()
* Enhanced internationalization support with wp_set_script_translations()
* Added proper singleton pattern with __clone and __wakeup protection
* Fixed text domain consistency throughout the plugin
* Improved session handling with null checks
* Added uninstall.php for proper cleanup
* Updated minimum requirements to WordPress 6.0, WooCommerce 7.0, PHP 7.4
* Code quality improvements following WordPress coding standards
* Better error handling and validation

= 1.0.0 =
* Initial release
* Core duplicate detection functionality
* Modern notification modal
* Order history integration

== Upgrade Notice ==

= 1.2.1 =
Fixed product page notification formatting for better visual appearance.

= 1.2.0 =
Major UX improvement! Now shows duplicate order warnings directly on product pages with clickable order links. Added support for WooCommerce Checkout Block.

= 1.1.0 =
Updated for WordPress 6.7 and WooCommerce 9.4 with HPOS support, improved security, and better internationalization.

= 1.0.0 =
Initial release with core duplicate order detection and notification features.

== Privacy Policy ==

This plugin does not collect any personal data beyond what WooCommerce already stores for order management.
