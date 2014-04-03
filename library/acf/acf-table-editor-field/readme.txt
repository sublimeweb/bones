=== Advanced Custom Fields: Table Editor Field ===
Contributors: polevaultweb
Author: polevaultweb
Author URI: http://www.polevaultweb.com
Plugin URI: http://www.polevaultweb.com
Tags: advanced custom fields, acf, field, table, grid, html, editor, excel
Requires at least: 3.0
Tested up to: 3.7.1
Stable tag: 1.1
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

== Description ==

Adds a new ACF field that is an Excel like grid table editor. This allows you to set a table, define the number of columns and their names.

The table data can be populated in the WordPress admin area wherever the ACF field group is displayed.

The table data is rendered on the frontend as an HTML table element using get_field(), the styling of the table is controlled by your theme not the plugin.

This plugin requires the free plugin [Advanced Custom Fields](http://wordpress.org/plugins/advanced-custom-fields/)

== Installation ==

This software can be treated as both a WP plugin and a theme include.
However, only when activated as a plugin will updates be available.

= Plugin =
1. Copy the 'acf-table-editor-field' folder into your plugins folder
2. Activate the plugin via the Plugins admin page

= Include =
1. Copy the 'acf-table-editor-field' folder into your theme folder (can use sub folders)
   * You can place the folder anywhere inside the 'wp-content' directory
2. Edit your functions.php file and add the following code to include the field:

`
add_action('acf/register_fields', 'my_register_fields');

function my_register_fields()
{
	include_once('acf-table-editor-field/includes/table-editor-field.php');
}
`

3. Make sure the path is correct to include the table-editor-field.php file

== Changelog ==

= 1.1 =

* New - Compatibility with ACF Repeater & ACF Flexible Content Addons
* New - Shortcodes in cell content now rendered
* New - Table Class option
* Fix - HTML in cell content now saved and rendered correctly
* Fix - Double quotes in cell content now saved and rendered correctly

= 1.0 =

* Initial Release