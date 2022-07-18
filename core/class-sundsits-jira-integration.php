<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( 'Sundsits_Jira_Integration' ) ) :

	/**
	 * Main Sundsits_Jira_Integration Class.
	 *
	 * @package		SUNDSITSJI
	 * @subpackage	Classes/Sundsits_Jira_Integration
	 * @since		1.0.0
	 * @author		developmentAlina
	 */

	final class Sundsits_Jira_Integration {

		/**
		 * The real instance
		 *
		 * @access	private
		 * @since	1.0.0
		 * @var		object|Sundsits_Jira_Integration
		 */
		private static $instance;

		/**
		 * SUNDSITSJI helpers object.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @var		object|Sundsits_Jira_Integration_Helpers
		 */
		public $helpers;

		/**
		 * SUNDSITSJI settings object.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @var		object|Sundsits_Jira_Integration_Settings
		 */
		public $settings;

		/**
		 * Throw error on object clone.
		 *
		 * Cloning instances of the class is forbidden.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @return	void
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'You are not allowed to clone this class.', 'sundsits-jira-integration' ), '1.0.0' );
		}

		/**
		 * Disable unserializing of the class.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @return	void
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( 'You are not allowed to unserialize this class.', 'sundsits-jira-integration' ), '1.0.0' );
		}

		/**
		 * Main Sundsits_Jira_Integration Instance.
		 *
		 * Insures that only one instance of Sundsits_Jira_Integration exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @access		public
		 * @since		1.0.0
		 * @static
		 * @return		object|Sundsits_Jira_Integration	The one true Sundsits_Jira_Integration
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Sundsits_Jira_Integration ) ) {
				self::$instance					= new Sundsits_Jira_Integration;
				self::$instance->base_hooks();
				self::$instance->includes();
				self::$instance->helpers		= new Sundsits_Jira_Integration_Helpers();
				self::$instance->settings		= new Sundsits_Jira_Integration_Settings();

				//Fire the plugin logic
				new Sundsits_Jira_Integration_Run();

				/**
				 * Fire a custom action to allow dependencies
				 * after the successful plugin setup
				 */
				do_action( 'SUNDSITSJI/plugin_loaded' );
			}

			return self::$instance;
		}

		/**
		 * Include required files.
		 *
		 * @access  private
		 * @since   1.0.0
		 * @return  void
		 */
		private function includes() {
			require_once SUNDSITSJI_PLUGIN_DIR . 'core/includes/classes/class-sundsits-jira-integration-helpers.php';
			require_once SUNDSITSJI_PLUGIN_DIR . 'core/includes/classes/class-sundsits-jira-integration-settings.php';

			require_once SUNDSITSJI_PLUGIN_DIR . 'core/includes/classes/class-sundsits-jira-integration-run.php';
		}

		/**
		 * Add base hooks for the core functionality
		 *
		 * @access  private
		 * @since   1.0.0
		 * @return  void
		 */
		private function base_hooks() {
			add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );
		}

		/**
		 * Loads the plugin language files.
		 *
		 * @access  public
		 * @since   1.0.0
		 * @return  void
		 */
		public function load_textdomain() {
			load_plugin_textdomain( 'sundsits-jira-integration', FALSE, dirname( plugin_basename( SUNDSITSJI_PLUGIN_FILE ) ) . '/languages/' );
		}

	}

endif; // End if class_exists check.