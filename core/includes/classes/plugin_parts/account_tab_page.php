<?php
/**
 * Add New Tab on the My Account page
 */


if( get_option( 'jira_example_plugin_settings' )['shortcode_checkbox'] == '1' ){

	// Add Shortcode
	function jira_shortcode() {
		ob_start();
		include SUNDSITSJI_PLUGIN_DIR . '/templates/hourly_page.php';
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}
	add_shortcode( 'jira-integration', 'jira_shortcode' );
}
else{
	add_action( 'woocommerce_account_dashboard', 'custom_account_dashboard_content',10, 0 );
	function custom_account_dashboard_content(){
		require_once SUNDSITSJI_PLUGIN_DIR . '/templates/hourly_page.php';
	}

	add_filter( 'woocommerce_locate_template', 'woo_adon_plugin_template', 1, 3 );
	function woo_adon_plugin_template( $template, $template_name, $template_path ) {
		global $woocommerce;
		$_template = $template;
		if ( ! $template_path )
			$template_path = $woocommerce->template_url;

		$plugin_path  = SUNDSITSJI_PLUGIN_DIR . '/templates/woocommerce/';

		// Look within passed path within the theme - this is priority
		$template = locate_template(
			array(
				$template_path . $template_name,
				$template_name
			)
		);

		if( ! $template && file_exists( $plugin_path . $template_name ) )
			$template = $plugin_path . $template_name;

		if ( ! $template )
			$template = $_template;

		return $template;
	}
}


