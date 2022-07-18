<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Sundsits_Jira_Integration_Run
 *
 * Thats where we bring the plugin to life
 *
 * @package		SUNDSITSJI
 * @subpackage	Classes/Sundsits_Jira_Integration_Run
 * @author		developmentAlina
 * @since		1.0.0
 */
class Sundsits_Jira_Integration_Run{

	/**
	 * Our Sundsits_Jira_Integration_Run constructor 
	 * to run the plugin logic.
	 *
	 * @since 1.0.0
	 */
	function __construct(){
		$this->add_hooks();
	}

	/**
	 * ######################
	 * ###
	 * #### WORDPRESS HOOKS
	 * ###
	 * ######################
	 */

	/**
	 * Registers all WordPress and plugin related hooks
	 *
	 * @access	private
	 * @since	1.0.0
	 * @return	void
	 */
	private function add_hooks(){
	
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_backend_scripts_and_styles' ), 20 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_scripts_and_styles' ), 0  );
	
	}

	/**
	 * ######################
	 * ###
	 * #### WORDPRESS HOOK CALLBACKS
	 * ###
	 * ######################
	 */

	/**
	 * Enqueue the backend related scripts and styles for this plugin.
	 * All of the added scripts andstyles will be available on every page within the backend.
	 *
	 * @access	public
	 * @since	1.0.0
	 *
	 * @return	void
	 */
	public function enqueue_backend_scripts_and_styles() {
		$screen = get_current_screen();

		if ( is_plugin_page('sundsits-jira-integration') ){
			wp_enqueue_style( 'sundsitsji-backend-styles-bootstrap', SUNDSITSJI_PLUGIN_URL . 'core/includes/assets/css/bootstrap.min.css', array(), SUNDSITSJI_VERSION, 'all' );
			wp_enqueue_style( 'sundsitsji-backend-styles', SUNDSITSJI_PLUGIN_URL . 'core/includes/assets/css/backend-styles.css', array(), SUNDSITSJI_VERSION, 'all' );
			wp_enqueue_script( 'sundsitsji-bootstrap-scripts', SUNDSITSJI_PLUGIN_URL . 'core/includes/assets/js/bootstrap.bundle.js', array(), SUNDSITSJI_VERSION, false );
			wp_enqueue_script( 'sundsitsji-data-tables', 'https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js', array(), SUNDSITSJI_VERSION, false );
			wp_enqueue_script( 'sundsitsji-backend-scripts', SUNDSITSJI_PLUGIN_URL . 'core/includes/assets/js/backend-scripts.js', array(), SUNDSITSJI_VERSION, false );
			wp_localize_script( 'sundsitsji-bootstrap-scripts', 'sundsitsji', array(
				'plugin_name'   	=> __( SUNDSITSJI_NAME, 'sundsits-jira-integration' ),
			));
			wp_localize_script( 'sundsitsji-data-tables', 'sundsitsji', array(
				'plugin_name'   	=> __( SUNDSITSJI_NAME, 'sundsits-jira-integration' ),
			));
			wp_localize_script( 'sundsitsji-backend-scripts', 'sundsitsji', array(
				'plugin_name'   	=> __( SUNDSITSJI_NAME, 'sundsits-jira-integration' ),
			));
		}

	}

	public function enqueue_frontend_scripts_and_styles(){
		//global $wp;
		//$request = explode( '/', $wp->request );
		if( ( is_account_page() ) ){
			wp_register_style( 'sundsitsji-frontend-styles-bootstrap', SUNDSITSJI_PLUGIN_URL . 'core/includes/assets/css/bootstrap.min.css', array(), SUNDSITSJI_VERSION,'all' );
			wp_enqueue_style( 'sundsitsji-frontend-styles-bootstrap' );
			wp_register_style( 'sundsitsji-frontend-styles', SUNDSITSJI_PLUGIN_URL . 'core/includes/assets/css/frontend-styles.css', array(), SUNDSITSJI_VERSION, 'all' );
			wp_enqueue_style( 'sundsitsji-frontend-styles' );
			wp_enqueue_script( 'sundsitsji-bootstrap-front-scripts', SUNDSITSJI_PLUGIN_URL . 'core/includes/assets/js/bootstrap.bundle.js', array(), SUNDSITSJI_VERSION, true );
			wp_enqueue_script( 'sundsitsji-frontend-scripts', SUNDSITSJI_PLUGIN_URL . 'core/includes/assets/js/frontend-scripts.js', array(), SUNDSITSJI_VERSION, true );
			wp_localize_script( 'sundsitsji-bootstrap-front-scripts', 'sundsitsji', array(
				'plugin_name'   	=> __( SUNDSITSJI_NAME, 'sundsits-jira-integration' ),
			));
			wp_localize_script( 'sundsitsji-frontend-scripts', 'sundsitsji', array(
				'plugin_name'   	=> __( SUNDSITSJI_NAME, 'sundsits-jira-integration' ),
			));
		}
	}

}
