<?php
/**
 * Plugin Name: Gravity Form Schedule Export
 * Plugin URI: http://vps.benlbot.ovh/blebot/gfse
 * Description: Schedule an export for your gravity form
 * Version: 1.0
 * Author: Benjamin Le Bot
 * Author URI: http://benlbot.ovh/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

define( 'GF_SCHEDULE_EXPORT', '1.0' );

add_action( 'gform_loaded', array( 'GF_Schedule_Export', 'load' ), 5 );

class GF_Schedule_Export {

	public static function load() {

		if ( ! method_exists( 'GFForms', 'include_addon_framework' ) ) {
			return;
		}

		require_once( 'class-gf-schedule-export.php' );

		GFAddOn::register( 'GFScheduleExport' );
	}

}

function gf_schedule_export() {
	return GFScheduleExport::get_instance();
}