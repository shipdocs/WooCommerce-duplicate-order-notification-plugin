=== WooCommerce Duplicate Order Notification ===
Contributors: martinsplinter
Donate link: none
Tags: woocommerce, orders, notification, duplicate-orders, order-management, e-commerce
Requires at least: 5.0
Tested up to: 6.2
Stable tag: 1.0.0
Requires PHP: 7.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Prevents duplicate purchases by notifying customers when they attempt to order products that are already in pending orders or recently purchased.

== Description ==

The WooCommerce Duplicate Order Notification plugin helps prevent accidental duplicate purchases by alerting customers during checkout if they're trying to order products that are either:

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
* **Responsive Design** - Looks great on all devices
* **Performance Optimized** - Minimal impact on checkout speed

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

1. Upload the plugin files to `/wp-content/plugins/wc-order-notification` directory
2. Activate the plugin through the 'Plugins' screen in WordPress
3. No configuration needed - works automatically with WooCommerce checkout

= Requirements =

* WordPress 5.0 or higher
* WooCommerce 3.0 or higher
* PHP 7.2 or higher

== Frequently Asked Questions ==

= Does this work with guest checkout? =

No, the plugin requires users to be logged in as it needs to check their order history.

= Can customers still place duplicate orders if they want to? =

Yes, the plugin shows a warning but includes an "Ignore and Proceed" option for cases where duplicate orders are intentional.

= Will this slow down my checkout? =

No, the plugin is optimized to perform checks efficiently and only on the checkout page.

= Does it work with all WooCommerce themes? =

Yes, the plugin uses standard WooCommerce hooks and modern CSS (Tailwind) for compatibility.

== Screenshots ==

1. Duplicate order detection modal
2. Order links in notification
3. Mobile view of notification
4. Checkout page integration

== Changelog ==

= 1.0.0 =
* Initial release
* Core duplicate detection functionality
* Modern notification modal
* Order history integration

== Upgrade Notice ==

= 1.0.0 =
Initial release with core duplicate order detection and notification features.

== Privacy Policy ==

This plugin does not collect any personal data beyond what WooCommerce already stores for order management.
