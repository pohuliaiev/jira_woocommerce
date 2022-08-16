<?php
/**
 * Sundsits Jira Integration
 *
 * @package       SUNDSITSJI
 * @author        Andrii Pohuliaiev
 * @version       1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:   Sundsits Jira Integration
 * Plugin URI:    #
 * Description:   This is some demo short description...
 * Version:       1.0.0
 * Author:        Andrii Pohuliaiev
 * Author URI:    #
 * Text Domain:   sundsits-jira-integration
 * Domain Path:   /languages
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
// Plugin name
define( 'SUNDSITSJI_NAME',			'Sundsits Jira Integration' );

// Plugin version
define( 'SUNDSITSJI_VERSION',		'1.0.0' );

// Plugin Root File
define( 'SUNDSITSJI_PLUGIN_FILE',	__FILE__ );

// Plugin base
define( 'SUNDSITSJI_PLUGIN_BASE',	plugin_basename( SUNDSITSJI_PLUGIN_FILE ) );

// Plugin Folder Path
define( 'SUNDSITSJI_PLUGIN_DIR',	plugin_dir_path( SUNDSITSJI_PLUGIN_FILE ) );

// Plugin Folder URL
define( 'SUNDSITSJI_PLUGIN_URL',	plugin_dir_url( SUNDSITSJI_PLUGIN_FILE ) );

/**
 * Load the main class for the core functionality
 */
require_once SUNDSITSJI_PLUGIN_DIR . 'core/class-sundsits-jira-integration.php';

/**
 * The main function to load the only instance
 * of our master class.
 *
 * @author  developmentAlina
 * @since   1.0.0
 * @return  object|Sundsits_Jira_Integration
 */
function SUNDSITSJI() {
	return Sundsits_Jira_Integration::instance();
}

SUNDSITSJI();