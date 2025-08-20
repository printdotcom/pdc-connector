<?php
/**
 * Admin general settings page
 *
 * Renders the Print.com general settings admin page.
 *
 * @package Pdc_Connector
 * @subpackage Pdc_Connector/admin/partials
 * @since 1.0.0
 */

?>
<div class="wrap">
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
	<p><?php esc_html_e( 'This plugin allows you to connect your WooCommerce store to Print.com.', 'pdc-connector' ); ?></p>
	<form method="post" action="options.php" id="js-<?php echo esc_attr( $this->plugin_name ); ?>-general-form">
		<?php
		settings_fields( $this->plugin_name . '-options' );
		do_settings_sections( $this->plugin_name );
		submit_button(
			'Save Settings',
			'primary',
			'submit',
			true,
			array(
				'test-id' => 'pdc-save-settings',
			)
		);
		?>
	</form>
</div>