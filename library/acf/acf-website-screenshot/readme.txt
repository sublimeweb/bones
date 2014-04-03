=== Advanced Custom Fields: Website Screenshot ===
Contributors: polevaultweb
Author: polevaultweb
Author URI: http://www.polevaultweb.com
Plugin URI: http://www.polevaultweb.com
Tags: advanced custom fields, acf, screenshot, website screenshot, screenshot generator
Requires at least: 3.0
Tested up to: 3.7.1
Stable tag: 1.0
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

== Description ==

This plugin adds a new ACF field that automatically generates a screenshot of a website from a link provided from an ACF text field and saves it to your WordPress media library.

This plugin requires the free plugin [Advanced Custom Fields](http://wordpress.org/plugins/advanced-custom-fields/)

== Installation ==

This software can be treated as both a WP plugin and a theme include.
However, only when activated as a plugin will updates be available.

= Plugin =
1. Copy the 'acf-website-screenshot' folder into your plugins folder
2. Activate the plugin via the Plugins admin page

= Include =
1. Copy the 'acf-website-screenshot' folder into your theme folder (can use sub folders)
   * You can place the folder anywhere inside the 'wp-content' directory
2. Edit your functions.php file and add the following code to include the field:

`
add_action('acf/register_fields', 'my_register_fields');

function my_register_fields()
{
	include_once('acf-website-screenshot/includes/website-screenshot.php');
}
`

3. Make sure the path is correct to include the website-screenshot.php file

== Changelog ==

= 1.0 =

* Initial Release
