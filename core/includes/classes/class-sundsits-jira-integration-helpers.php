<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Sundsits_Jira_Integration_Helpers
 *
 * This class contains repetitive functions that
 * are used globally within the plugin.
 *
 * @package		SUNDSITSJI
 * @subpackage	Classes/Sundsits_Jira_Integration_Helpers
 * @author		developmentAlina
 * @since		1.0.0
 */
class Sundsits_Jira_Integration_Helpers{

	/**
	 * ######################
	 * ###
	 * #### CALLABLE FUNCTIONS
	 * ###
	 * ######################
	 */

}
require_once SUNDSITSJI_PLUGIN_DIR . "core/includes/classes/plugin_parts/jira_api/vendor/autoload.php"; 

require_once SUNDSITSJI_PLUGIN_DIR . 'core/includes/classes/plugin_parts/user_custom_fields.php';

require_once SUNDSITSJI_PLUGIN_DIR . 'core/includes/classes/plugin_parts/plugin_admin_pages.php';

require_once SUNDSITSJI_PLUGIN_DIR . 'core/includes/classes/plugin_parts/jira_api_call.php';

require_once SUNDSITSJI_PLUGIN_DIR . 'core/includes/classes/plugin_parts/woocommerce_product_type.php';

require_once SUNDSITSJI_PLUGIN_DIR . 'core/includes/classes/plugin_parts/account_tab_page.php';

require_once SUNDSITSJI_PLUGIN_DIR . 'core/includes/classes/plugin_parts/user_time_update.php';

require_once SUNDSITSJI_PLUGIN_DIR . 'core/includes/classes/plugin_parts/create_db_tables.php';

require_once SUNDSITSJI_PLUGIN_DIR . 'core/includes/classes/plugin_parts/jira_ajax_request.php';

//require_once SUNDSITSJI_PLUGIN_DIR . 'core/includes/classes/plugin_parts/jira_cron_job.php';

