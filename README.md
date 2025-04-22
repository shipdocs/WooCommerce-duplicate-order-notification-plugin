# WC Order Notification

A WooCommerce plugin that notifies users if they attempt to order products that are already in open orders or have been ordered within the last three months.

## Features

- Checks for duplicate products in:
  - Open orders (pending, on-hold, processing)
  - Orders completed within the last 3 months
- Modern modal notification using Tailwind CSS
- Links to existing orders (opens in new tabs)
- Option to ignore and proceed with the order
- Fully responsive design

## Requirements

- WordPress 5.0+
- WooCommerce 3.0+
- PHP 7.2+

## Installation

1. Upload the `wc-order-notification` directory to your `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. WooCommerce will now automatically check for duplicate orders during checkout

## Usage

The plugin works automatically after installation. When a customer attempts to check out with products that are already in open orders or were ordered recently:

1. A modal notification appears showing the duplicate products
2. Each duplicate product shows links to the existing orders where it appears
3. The customer can:
   - Click the order links to review existing orders (opens in new tabs)
   - Click "Ignore and Proceed" to continue with the checkout
   - Close the modal (same as ignoring) to proceed

## Development

### File Structure

```
wc-order-notification/
├── assets/
│   ├── css/
│   │   └── wc-order-notification.css
│   └── js/
│       └── wc-order-notification.js
├── includes/
│   ├── class-duplicate-checker.php
│   └── class-notification-handler.php
└── wc-order-notification.php
```

### Classes

- `WC_Order_Notification`: Main plugin class
- `WC_Duplicate_Checker`: Handles checking for duplicate products
- `WC_Notification_Handler`: Manages the notification display and user interaction

## License

This plugin is licensed under the GPL v2 or later.

```txt
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
