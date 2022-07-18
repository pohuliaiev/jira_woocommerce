<div class="wrapper">
	<h1 class="text-center">Jira Options</h1>

	<div class="jira-admin-form">
		<form action="options.php" method="post">
			<?php
			settings_fields( 'jira_example_plugin_settings' );
			do_settings_sections( 'jira_example_plugin' );
			?>
			<input
				type="submit"
				name="submit"
				class="button button-primary"
				value="<?php esc_attr_e( 'Save' ); ?>"
				/>
		</form>
	</div>

<div class="jira-admin-form">
	<form action="options.php" method="post">
		<?php
		settings_fields( 'jira_example_plugin_settings_2' );
		do_settings_sections( 'jira_example_plugin_2' );
		?>
		<input
			type="submit"
			name="submit"
			class="button button-primary"
			value="<?php esc_attr_e( 'Update' ); ?>"
			/>
	</form>
</div>


	<div class="jira-admin-form">
		<form action="options.php" method="post">
			<?php
			settings_fields( 'jira_example_plugin_settings_3' );
			do_settings_sections( 'jira_example_plugin_3' );
			?>
			<input
				type="submit"
				name="submit"
				class="button button-primary"
				value="<?php esc_attr_e( 'Delete' ); ?>"
				/>
		</form>
	</div>





</div>
