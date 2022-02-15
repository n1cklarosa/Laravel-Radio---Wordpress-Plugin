<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://allclassweb.com
 * @since      1.0.0
 *
 * @package    Acw_Radio
 * @subpackage Acw_Radio/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Acw_Radio
 * @subpackage Acw_Radio/includes
 * @author     Nick La Rosa <nick@allclassweb.com>
 */
class Acw_Radio_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {

		/**
		 * This only required if custom post type has rewrite!
		 */
		flush_rewrite_rules();
	}

}
