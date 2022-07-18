<?php
add_action('admin_menu', 'test_plugin_setup_menu');
function test_plugin_setup_menu(){
	add_menu_page( 'Jira Integration', 'Jira Integration', 'manage_options', 'jira-integration', 'test_init' );
	add_submenu_page('jira-integration', 'Customers', 'Customers', 'manage_options', 'jira-integration' );
}

function test_init(){
	require_once SUNDSITSJI_PLUGIN_DIR . '/templates/users_admin_page.php';

}

function jira_options_page()
{
	add_submenu_page(
		'jira-integration',
		'Settings',
		'Settings',
		'manage_options',
		'jira_settings',
		'jira_settings_page'
	);
}
add_action('admin_menu', 'jira_options_page');

function jira_logs_list()
{
	add_submenu_page(
		'jira-integration',
		'Log',
		'Log',
		'manage_options',
		'jira_logs_list',
		'jira_list_logs'
	);
}
add_action('admin_menu', 'jira_logs_list');

function jira_list_logs(){
	require_once SUNDSITSJI_PLUGIN_DIR . '/templates/jira_logs_list.php';
}
/*
function jira_logs_page()
{
	add_submenu_page( 'jira-integration', 'Logs Admin', 'Logs Admin', 'manage_options','edit.php?post_type=jira_logs');

}
add_action('admin_menu', 'jira_logs_page');
*/


function get_admin_page_url(string $menu_slug, $query = null, array $esc_options = []) : string
{
	$url = menu_page_url($menu_slug, false);

	if($query) {
		$url .= '&' . (is_array($query) ? http_build_query($query) : (string) $query);
	}

	return esc_url($url, ...$esc_options);
}

function jira_settings_page(){
	require_once SUNDSITSJI_PLUGIN_DIR . '/templates/jira_options_page.php';
}
function jira_register_settings() {
	register_setting(
		'jira_example_plugin_settings',
		'jira_example_plugin_settings',
		'jira_validate_example_plugin_settings'
	);
	add_settings_section(
		'section_one',
		'Jira API Settings',
		'jira_section_one_text',
		'jira_example_plugin'
	);
	add_settings_field(
		'api_key',
		'Jira Timeline API key',
		'jira_render_api_key',
		'jira_example_plugin',
		'section_one'
	);
	add_settings_field(
		'site_url',
		'Jira Timeline Url',
		'jira_render_site_url',
		'jira_example_plugin',
		'section_one'
	);
	add_settings_field(
		'jira_site_url',
		'Jira API Url',
		'jira_render_jira_site_url',
		'jira_example_plugin',
		'section_one'
	);
	add_settings_field(
		'jira_api_key',
		'Jira API Key',
		'jira_render_jira_api_key',
		'jira_example_plugin',
		'section_one'
	);
	add_settings_field(
		'jira_api_email',
		'Jira API Email',
		'jira_render_jira_api_email',
		'jira_example_plugin',
		'section_one'
	);
	add_settings_field(
		'shortcode_checkbox',
		'Manual insert',
		'jira_render_checkbox',
		'jira_example_plugin',
		'section_one'
	);
	add_settings_field(
		'cron_interval',
		'Cron interval',
		'jira_render_cron_interval',
		'jira_example_plugin',
		'section_one'
	);
	add_settings_field(
		'cron_checkbox',
		'Enable manual cron interval',
		'jira_render_cron_checkbox',
		'jira_example_plugin',
		'section_one'
	);
	register_setting(
		'jira_example_plugin_settings_2',
		'jira_example_plugin_settings_2',
		'jira_renew_data'
	);
	add_settings_section(
		'section_two',
		'Update Jira Data',
		'jira_section_two_text',
		'jira_example_plugin_2'
	);

	register_setting(
		'jira_example_plugin_settings_3',
		'jira_example_plugin_settings_3',
		'jira_delete_data'
	);
	add_settings_section(
		'section_three',
		'Delete',
		'jira_section_three_text',
		'jira_example_plugin_3'
	);




}
add_action( 'admin_init', 'jira_register_settings' );
function jira_validate_example_plugin_settings( $input ) {
	$output['api_key']  = sanitize_text_field( $input['api_key'] );
	$output['site_url'] = sanitize_text_field( $input['site_url'] );
	$output['jira_site_url'] = sanitize_text_field( $input['jira_site_url'] );
	$output['jira_api_key'] = sanitize_text_field( $input['jira_api_key'] );
	$output['jira_api_email'] = sanitize_text_field( $input['jira_api_email'] );
	$output['shortcode_checkbox'] = intval($input['shortcode_checkbox']);
	$output['cron_checkbox'] = intval($input['cron_checkbox']);
	$output['cron_interval'] = intval($input['cron_interval']);

	// ...
	return $output;
}


function jira_renew_data(){
	jira_update_user_tables();
}

function jira_delete_data(){
	jira_delete_tables();
}

function jira_section_one_text() {
	echo '<p>Enter your Jira Timeline API key, url and cron interval</p>';
}
function jira_section_two_text() {
	echo '<p>Update Jira Data</p>';
}
function jira_section_three_text() {
	echo '<p>Delete Jira Data</p>';
}

function jira_render_api_key() {
	$options = get_option( 'jira_example_plugin_settings' );
	printf(
		'<input type="text" name="%s" value="%s" />',
		esc_attr( 'jira_example_plugin_settings[api_key]' ),
		esc_attr( $options['api_key'] )
	);
}

function jira_render_site_url() {
	$options = get_option( 'jira_example_plugin_settings' );
	printf(
		'<input type="text" name="%s" value="%s" />',
		esc_attr( 'jira_example_plugin_settings[site_url]' ),
		esc_attr( $options['site_url'] )
	);
}
function jira_render_jira_site_url() {
	$options = get_option( 'jira_example_plugin_settings' );
	printf(
		'<input type="text" name="%s" value="%s" />',
		esc_attr( 'jira_example_plugin_settings[jira_site_url]' ),
		esc_attr( $options['jira_site_url'] )
	);
}
function jira_render_jira_api_key() {
	$options = get_option( 'jira_example_plugin_settings' );
	printf(
		'<input type="text" name="%s" value="%s" />',
		esc_attr( 'jira_example_plugin_settings[jira_api_key]' ),
		esc_attr( $options['jira_api_key'] )
	);
}
function jira_render_jira_api_email() {
	$options = get_option( 'jira_example_plugin_settings' );
	printf(
		'<input type="text" name="%s" value="%s" />',
		esc_attr( 'jira_example_plugin_settings[jira_api_email]' ),
		esc_attr( $options['jira_api_email'] )
	);
}
function jira_render_cron_interval() {
	$options = get_option( 'jira_example_plugin_settings' );
	printf(
		'<input type="number" id="cron-interval" name="%s" value="%s" />',
		esc_attr( 'jira_example_plugin_settings[cron_interval]' ),
		esc_attr( $options['cron_interval'] )
	);
}
function jira_render_checkbox() {
	$options = get_option( 'jira_example_plugin_settings' );
	printf(
		'<input type="checkbox" id="shortcode_checkbox" name="jira_example_plugin_settings[shortcode_checkbox]" value="1"' . checked( 1, $options['shortcode_checkbox'], false ) . '/>
<label for="shortcode_checkbox">Select this option to use timeline bar as shortcode</label>
<div class="shortcode_container">Just copy this shortcode <code>[jira-integration]</code></div>',
		esc_attr( 'jira_example_plugin_settings[shortcode_checkbox]' ),
		esc_attr( $options['shortcode_checkbox'] )
	);
}

function jira_render_cron_checkbox() {
	$options = get_option( 'jira_example_plugin_settings' );
	printf(
		'<input type="checkbox" id="cron_checkbox" name="jira_example_plugin_settings[cron_checkbox]" value="1"' . checked( 1, $options['cron_checkbox'], false ) . '/>
<label for="cron_checkbox">Enable cron</label>
<div class="shortcode_container">Check to enable cron. By default an interval is 5 minutes, but you can set it manually. If 5 minutes is ok - set field value to 0.</div>',
		esc_attr( 'jira_example_plugin_settings[cron_checkbox]' ),
		esc_attr( $options['cron_checkbox'] )
	);
}

