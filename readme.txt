=== Duplicate Order Prevention for WooCommerce ===
Contributors: martinsplinter
Donate link: https://www.paypal.com/ncp/payment/WQUXP4L7KQKRE
Tags: woocommerce, orders, notification, duplicate-orders, order-management, e-commerce
Requires at least: 6.0
Tested up to: 6.7
Stable tag: 1.1.0
Requires PHP: 7.4
WC requires at least: 7.0
WC tested up to: 9.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Prevents duplicate purchases by notifying customers when they attempt to order products that are already in pending orders or recently purchased.

== Description ==

The Duplicate Order Prevention for WooCommerce plugin helps prevent accidental duplicate purchases by alerting customers during checkout if they're trying to order products that are either:

1. Currently in pending/processing orders
2. Already purchased within the last 3 months

= Key Features =

* **Smart Detection** - Automatically identifies duplicate products in:
	* Open orders (pending, on-hold, processing)
	* Orders completed within the last 3 months
* **User-Friendly Notifications** - Modern modal display showing:
	* List of duplicate products
	* Links to existing orders (opens in new tabs)
	* Option to proceed if duplicate order is intentional
* **Seamless Integration** - Works automatically with:
	* WooCommerce checkout
	* Customer order history
	* WooCommerce High-Performance Order Storage (HPOS)
* **Responsive Design** - Looks great on all devices
* **Performance Optimized** - Minimal impact on checkout speed
* **Security First** - Includes CSRF protection with nonces and proper data sanitization

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

= Can customers still place duplicate orders if they want to? =

Yes, the plugin shows a warning but includes an "Ignore and Proceed" option for cases where duplicate orders are intentional.

= Will this slow down my checkout? =

No, the plugin is optimized to perform checks efficiently and only on the checkout page.

= Does it work with all WooCommerce themes? =

Yes, the plugin uses standard WooCommerce hooks and modern CSS for compatibility.

== Screenshots ==

1. Duplicate order detection modal
2. Order links in notification
3. Mobile view of notification
4. Checkout page integration

== Changelog ==

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

= 1.1.0 =
Updated for WordPress 6.7 and WooCommerce 9.4 with HPOS support, improved security, and better internationalization.

= 1.0.0 =
Initial release with core duplicate order detection and notification features.

== Privacy Policy ==

This plugin does not collect any personal data beyond what WooCommerce already stores for order management.
