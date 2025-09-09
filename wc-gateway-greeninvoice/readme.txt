=== Morning for WooCommerce ===
Contributors: greeninvoice, dorzki
Tags: greeninvoice, invoices, invoice, business management, credit cards
Requires at least: 6.7
Tested up to: 6.8.2
Stable tag: 2.2.1
Requires PHP: 7.4
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Morning (Green Invoice) add-on for WooCommerce enables an easy and convenient connection between your morning account to your online store.

== Description ==

Morning add-on (Green Invoice) for WooCommerce enables a quick, easy and convenient connection between morning's business management system and your digital store. The connection delivers two highly important actions in one add-on: acquiring and receiving payment by credit, and automatic invoicing for each transaction.

= About Us =
Morning (Green Invoice) is Israel's leading business management platform. Our system offers diverse convenient, user-friendly digital tools that help you better understand your business, turning administrative tasks that feel complex into fun, simple ones.
For example: production of invoices, documents and financial reports, smart expense management, credit acquiring, E-commerce, automation vis-à-vis the CPA, interface with add-ons and external systems and more. In addition, morning offers rich, diverse content including a magazine, guides, a podcast and a supportive community, that together create a comprehensive, rich ecosystem for freelancers.

= Important to Know =

**Morning (Green Invoice) operates in Israel and in Hebrew only.**

== Frequently Asked Questions ==

= What is morning? =

Morning (Green Invoice) is a business management system that truly understands freelancers' needs. With morning, you can generate invoices in seconds, manage revenues and expenses, receive payment from customers and create reports with many vital tools that help your business grow and make its management a great experience.

= Do you need to open a morning account? =

Yes. To use the add-on, you need to open a morning (Green Invoice) account on Popular track (or above), connect to one of the acquiring add-ons, then add the WooCommerce add-on.

= Is the system registered with the Tax Authority? =

Sure. Morning (Green Invoice) is registered with the Tax Authority. Generated invoices are signed with a secure digital signature.

= Can I cancel a transaction? =

Of course. The transaction can be canceled manually both on WooCommerce and in the morning (Green Invoice) system.

= Can I design an invoice to suit my business? =

That's fun! You can choose between 12 different design templates and adjust their coloring. Template selection is done through your morning account (Green Invoice) in the settings menu.

== Screenshots ==

== Installation ==

First, please note that add-on activation is possible for morning *Basic* or *Extra* subscribers only, with an active “Digital Payments” add-on (0 NIS monthly cost + clearing fees according to the terms of service). Meshulam/Isracard add-on (fixed monthly cost + clearing fees) or PayPal add-on (free) on your morning account.

To install this add-on go to [the add-ons section](https://app.greeninvoice.co.il/market/plugin/woocommerce) in your morning account. Choose your preferred payment methods under add-on settings, and finally, copy the add-on key.

= In WordPress =

1. Install the morning add-on for WooCommerce from the add-on database.
1. To activate, access the add-on settings and enter the key information received from your morning account.
1. Go to WooCommerce &gt; Settings &gt; Payments; use the Toggle buttons to activate the payment methods (Credit Card | PayPal | bit) that will be offered to clients during checkout.

== Changelog ==

= 2.2.1 | 09.09.2025 =
[FEATURE] Added support for Tax ID field in WooCommerce Checkout Blocks.
[IMPROVE] Bumped the minimum required WooCommerce version to 8.9
[IMPROVE] Added compatibility for WooCommerce v10.1.2

= 2.2.0 | 10.08.2025 =
[FEATURE] New plugin setting for allowed payment gateways in invoicing mode.
[FEATURE] Add a `View Logs` button to the plugin settings page.

= 2.1.1 | 03.08.2025 =
[BUGFIX] Fix order status changed to processing for virtual products.

= 2.1.0 | 24.07.2025 =
[IMPROVE] Moved installments' field to the gateway selection screen.
[IMPROVE] Add support for installments with Apple Pay.
[IMPROVE] Added compatibility for WooCommerce v10.0.4

= 2.0.6 | 20.07.2025 =
[IMPROVE] Added compatibility for WooCommerce v10.0.2
[IMPROVE] Bumped the minimum required WordPress version to 6.7

= 2.0.5 | 29.06.2025 =
[IMPROVE] Added invoice creation button for invoice-only mode.
[IMPROVE] Added compatibility for WooCommerce v9.9.5

= 2.0.4 | 15.06.2025 =
[IMPROVE] Improved integration with PayPlus payment gateway.
[IMPROVE] Added compatibility for WooCommerce v9.9.3

= 2.0.3 | 16.04.2025 =
[FEATURE] Added refund documents creation for invoicing mode.
[IMPROVE] Added compatibility for WooCommerce v9.8.1

= 2.0.2 | 09.04.2025 =
[IMPROVE] Reverted WooCommerce Subscriptions limitation.

= 2.0.1 | 07.04.2025 =
[BUGFIX] Fixed plugin activation error due to migration error.

= 2.0.0 | 06.04.2025 =
[BUGFIX] Remove the payment gateway box if the description field is empty.
[FEATURE] Added payment gateway icon filter.
[FEATURE] Added invoicing only mode.
[IMPROVE] Rewritten the plugin to add support for future features.
[IMPROVE] Bumped the minimum required WordPress version to 6.6

= 1.6.5 | 19.03.2025 =
[IMPROVE] Added compatibility for WooCommerce v9.7.1
[IMPROVE] Bumped the minimum required WooCommerce version to 8.0

= 1.6.4 | 25.02.2025 =
[BUGFIX] Hide document metabox for subscription order with trial.
[IMPROVE] Added compatibility for WooCommerce v9.6.2

= 1.6.3 | 27.01.2025 =
[IMPROVE] Added compatibility for WooCommerce v9.6.0

= 1.6.2 | 26.12.2024 =
[IMPROVE] Added compatibility for WooCommerce v9.5.1

= 1.6.1 | 08.12.2024 =
[BUGFIX] Fixed Site Info report exporting on WooCommerce v9.1.0
[FEATURE] Added support for WooCommerce Subscriptions trial days.
[IMPROVE] Added compatibility for WooCommerce v9.4.3
[IMPROVE] Added WooCommerce a required plugin.
[IMPROVE] Reflect payment form url error better.

= 1.6.0 | 14.08.2024 =
[FEATURE] Added support for WooCommerce Subscriptions.

= 1.5.1 | 25.07.2024 =
[BUGFIX] Fixed Site Info report exporting on WooCommerce v9.0.0
[IMPROVE] Added compatibility for WooCommerce v9.1.2

= 1.5.0 | 29.05.2024 =
[FEATURE] Added the ability to perform refunds via order screen.
[FEATURE] Added integration with Pimwick Gift Cards.
[FEATURE] Added support for WooCommerce fees.
[IMPROVE] Added tax settings in site info export.

= 1.4.2 | 13.05.2024 =
[BUGFIX] Fixed plugin crash for an incompatible WooCommerce version.

= 1.4.1 | 13.05.2024 =
[BUGFIX] Fixed missing order screen metabox.
[FEATURE] Added links to view Document and Transaction to order screen.
[IMPROVE] Bumped the minimum required WordPress version to 6.4
[IMPROVE] Bumped the minimum required PHP version to 7.4
[IMPROVE] Bumped the minimum required WooCommerce version to 6.9

= 1.4.0 | 29.05.2024 =
[BUGFIX] Fixed issue with installments form and Polylang.
[BUGFIX] Fixed issue with gateways disappearing when saving settings.
[FEATURE] Added billing company name field support.
[FEATURE] Added the ability to inject tax id number field.
[FEATURE] Added new export site data and logs button.

= 1.3.1 | 25.12.2023 =
[BUGFIX] Fixed double payment form on checkout page.

= 1.3.0 | 24.12.2023 =
[BUGFIX] Added missing assets.
[FEATURE] Added support for WooCommerce Blocks (Cart & Checkout).

= 1.2.3 | 20.12.2023 =
[FEATURE] Added support for HPOS (High-Performance Order Storage).

= 1.2.2 | 13.12.2023 =
[BUGFIX] Fixed gateways sync.
[FEATURE] Added the ability to choose order status after IPN.

= 1.2.1 | 25.10.2023 =
[FEATURE] Added Apple Pay payment gateway.

= 1.2.0 | 08.08.2023 =
[BUGFIX] Fixed order status changes to on-hold or failed after payment is done.
[BUGFIX] Fixed stock being reduced twice after payment.
[FEATURE] Revamped plugin settings pages.
[FEATURE] Added dynamic gateways according to account plugin configuration.

= 1.1.5 | 04.06.2023 =
[FEATURE] Added Google Pay support for Digital Payments plugin.
[FEATURE] Added support for WooCommerce v7.6

= 1.1.4 | 18.04.2023 =
[BUGFIX] Fixed order status changes after purchase.
[FEATURE] Added compatibility with the latest WooCommerce version.

= 1.1.3 | 23.06.2021 =
[BUGFIX] Fixed credit card gateway issue upon returning to cart without error.
[FEATURE] Added support for WPML and Polylang.
[FEATURE] Added the ability to change order status to "completed" instead or "processing".

= 1.1.2 | 11.05.2021 =
[BUGFIX] Fixed Bit back button functionality.
[BUGFIX] Added credit card loader for Meshulam.

= 1.1.1 | 24.02.2021 =
[BUGFIX] Added default country if country field is missing.
[FEATURE] Added support for WooCommerce 5.*.

= 1.1.0 | 13.01.2021 =
[FEATURE] Added the options to activate and choose installments.
[FEATURE] Added the ability to pay via Bit.

= 1.0.2 | 14.10.2020 =
[BUGFIX] Fixed domain path issue.

= 1.0.1 | 20.08.2020 =
[BUGFIX] Ignore shipping if there isn't any.
[BUGFIX] Better handling of tax value.

= 1.0.0 | 06.08.2020 =
First version!

== Upgrade Notice ==
