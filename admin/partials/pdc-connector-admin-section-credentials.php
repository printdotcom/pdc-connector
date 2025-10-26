<?php
/**
 * Credentials settings section partial
 *
 * Renders the settings fields for API credentials and environment.
 *
 * @package Pdc_Connector
 * @subpackage Admin\Partials
 * @since 1.0.0
 */

 if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$pdc_connector_api_key = get_option( $this->plugin_name . '-api_key' );
$pdc_connector_env     = get_option( $this->plugin_name . '-env' );
$pdc_connector_app_url = ( 'prod' === $pdc_connector_env ) ? 'app.print.com' : 'app.stg.print.com';
?>

<p>
	<?php esc_html_e( 'You can create an API key in your Print.com account settings. Visit', 'pdc-connector' ); ?>
	<a data-testid="pdc-environment-link" target="_blank" href="<?php echo esc_url( 'https://' . $pdc_connector_app_url . '/account' ); ?>">
		<?php echo esc_html( $pdc_connector_app_url . '/account' ); ?>
	</a>,
	<?php esc_html_e( 'create an API key and paste it in the input field below.', 'pdc-connector' ); ?>
</p>

<div class="notice notice-success hidden" id="js-<?php echo esc_attr( $this->plugin_name ); ?>-auth-success">
	<p><?php esc_html_e( 'API Key verified. You are now connected!', 'pdc-connector' ); ?></p>
</div>
<div class="notice notice-error hidden" id="js-<?php echo esc_attr( $this->plugin_name ); ?>-auth-failed">
	<p><?php esc_html_e( 'API Key is not valid. Check your environment and API Key', 'pdc-connector' ); ?></p>
</div>

<table class="form-table">
	<tbody>
		<tr>
			<th scope="row"><label for="pdc_api_key"><?php esc_html_e( 'API Key', 'pdc-connector' ); ?></label></th>
			<td>
				<input id="pdc_api_key" data-testid="pdc-apikey" name="<?php echo esc_attr( $this->plugin_name ); ?>-api_key" type="text" value="<?php echo esc_attr( $pdc_connector_api_key ); ?>" class="regular-text" />
				<span id="js-<?php echo esc_attr( $this->plugin_name ); ?>-verify_loader" class="spinner"></span>
				<button data-testid="pdc-verify-key" type="button" id="js-<?php echo esc_attr( $this->plugin_name ); ?>-verify_key" class="button button-secondary">
					<span><?php esc_html_e( 'Verify', 'pdc-connector' ); ?></span>
				</button>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="pdc_env"><?php esc_html_e( 'Environment', 'pdc-connector' ); ?></label></th>
			<td>
				<select data-testid="pdc-environment" name="<?php echo esc_attr( $this->plugin_name ); ?>-env" id="pdc_env">
					<option value="stg" <?php selected( $pdc_connector_env, 'stg' ); ?>><?php esc_html_e( 'Test', 'pdc-connector' ); ?></option>
					<option value="prod" <?php selected( $pdc_connector_env, 'prod' ); ?>><?php esc_html_e( 'Live', 'pdc-connector' ); ?></option>
				</select>
			</td>
		</tr>
	</tbody>
</table>
