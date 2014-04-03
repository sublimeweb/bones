<?php
/*
Plugin Name: Advanced Custom Fields: Table Editor Field
Plugin URI: http://www.polevaultweb.com/
Description: Adds a new ACF field that is an Excel like grid table editor
Version: 1.1
Author: polevaultweb
Author URI: http://www.polevaultweb.com/

Copyright 2013  polevaultweb  (email : info@polevaultweb.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 3, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

class acf_table_editor_field_plugin
{
	/*
	*  Constructor
	*
	*  @description:
	*  @since 1.0
	*  @created: 29/11/13
	*/

	function __construct()
	{

		// create remote update
		if( is_admin() )
		{
			require_once('includes/wp-updates-plugin.php');
			new WPUpdatesPluginUpdater_284( 'http://wp-updates.com/api/2/plugin', plugin_basename(__FILE__));
		}

		// actions
		add_action('acf/register_fields', array($this, 'register_fields'));

	}

	/*
	*  register_fields
	*
	*  @description:
	*  @since: 1.0
	*  @created: 29/11/13
	*/

	function register_fields()
	{
		include_once('includes/table-editor-field.php');
	}

}
new acf_table_editor_field_plugin();