<?php
/*
 Plugin Name: The Events Calendar: Agenda View
 Description: This plugin adds an agenda view to your Tribe The Events Calendar suite.
 Version: 1.0
 Author: Modern Tribe, Inc.
 Author URI: http://www.tri.be
 Text Domain: 'tribe-event-agenda-view'
 License: GPLv2 or later

Copyright 2009-2013 by Modern Tribe Inc and the contributors

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

if ( ! class_exists( 'Tribe__Events__Main' ) ) {
	return;
}

if( ! class_exists('Tribe__Events__Agenda_View') ) {
	class Tribe__Events__Agenda_View {

		function tribe_events_agenda_init () {
			/* Include our other files */
			require_once( 'template-tags.php' );
			require_once( 'tribe-events-agenda-template-class.php' );

			/* Add Hooks */

			// hook in to add the rewrite rules
			add_action( 'generate_rewrite_rules', array ( $this, 'tribe_events_agenda_add_routes' ) );

			// specify the template class
			add_filter( 'tribe_events_current_template_class', array ( $this, 'tribe_events_agenda_setup_template_class' ) );

			// load the proper template for agenda view
			add_filter( 'tribe_events_current_view_template', array ( $this, 'tribe_events_agenda_setup_view_template' ) );

			// inject agenda view into events bar & (display) settings
			add_filter( 'tribe-events-bar-views', array ( $this, 'tribe_events_agenda_setup_in_bar' ), 40, 1 );

		}

		/**
		 * Add the agenda view rewrite rule
		 *
		 * @param $wp_rewrite the WordPress rewrite rules object
		 * @return void
		 **/
		function tribe_events_agenda_add_routes( $wp_rewrite ) {
			// Get the instance of the TribeEvents plugin, and the rewriteSlug that the plugin uses
			$tec = Tribe__Events__Main::instance();
			$tec_rewrite_slug = trailingslashit( $tec->rewriteSlug );

			// create new rule for the agenda view
			$newRules = array(
				$tec_rewrite_slug . 'agenda/?$' => 'index.php?post_type=' . Tribe__Events__Main::POSTTYPE . '&eventDisplay=agenda',
			);

			// Add the new rule to the global rewrite rules array
			$wp_rewrite->rules = $newRules + $wp_rewrite->rules;
		}

		/**
		 * Specify the template class for agenda view
		 *
		 * @param $class string containing the current template classname
		 * @return string
		 **/
		function tribe_events_agenda_setup_template_class( $class ) {
			if ( tribe_is_agenda() ) {
				$class = 'Tribe__Events__Agenda_Template';
			}
			return $class;
		}

		/**
		 * Specify the template for agenda view
		 *
		 * @param $template string containing the current template file
		 * @return string
		 **/
		function tribe_events_agenda_setup_view_template( $template ){

			error_log(">>> tribe_events_agenda_setup_view_template");
			// agenda view
			if( tribe_is_agenda() ) {
				$template = Tribe__Events__Templates::getTemplateHierarchy('agenda');
			}
			return $template;
		}


		/**
		 * Register the Agenda view alongside the other views
		 *
		 * @param $views array of registered views
		 * @return array
		 **/
		function tribe_events_agenda_setup_in_bar( $views ) {

			error_log(">>> tribe_events_agenda_setup_in_bar");
			$views[] = array(
				'displaying' => 'agenda',
				'anchor'     => 'Agenda',
				'url'        => tribe_get_agenda_permalink()
			);
			return $views;
		}
	}
}
// Fatal error: Class 'Tribe__Events__Template_Factory' not found in /var/www/wordpress/wp-content/plugins/tribe-events-agenda-view/tribe-events-agenda-template-class.php on line 15
/*
$tribe_events_agenda = new Tribe__Events__Agenda_View();
$tribe_events_agenda->tribe_events_agenda_init();
*/


// Warning: call_user_func_array() expects parameter 1 to be a valid callback, first array member is not a valid class name or object in /var/www/wordpress/wp-includes/plugin.php on line 235
//add_action( 'plugins_loaded', array ( 'Tribe__Events__Agenda_View', 'tribe_events_agenda_init' ) );//, 40, 1 );

// Try to delay loading, work but don't actually work :)
add_action('plugins_loaded', 'tribe_events_agenda_instantiate', 40, 1 );

function tribe_events_agenda_instantiate() {
	$tribe_events_agenda = new Tribe__Events__Agenda_View();
	$tribe_events_agenda->tribe_events_agenda_init();
}

