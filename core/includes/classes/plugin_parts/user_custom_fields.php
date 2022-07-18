<?php
add_action( 'show_user_profile', 'jira_add_extra_social_links' );
add_action( 'edit_user_profile', 'jira_add_extra_social_links' );

function jira_add_extra_social_links( $user )
{
	?>
	<h3>Users Jira Fields</h3>

	<table class="form-table" id="jira_fields">

		<tr>
			<th><label for="jira_filter">Jira Filter Id</label></th>
			<td><input type="text" name="jira_filter" value="<?php echo esc_attr(get_the_author_meta( 'jira_filter', $user->ID )); ?>" class="regular-text" /></td>
		</tr>
	</table>
	<?php
}

add_action( 'personal_options_update', 'jira_save_extra_social_links' );
add_action( 'edit_user_profile_update', 'jira_save_extra_social_links' );

function jira_save_extra_social_links( $user_id )
{

	update_user_meta( $user_id,'jira_filter', sanitize_text_field( $_POST['jira_filter'] ) );
}

