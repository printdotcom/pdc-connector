<?php
$api_key = get_option($this->plugin_name . '-api_key');
$pdc_env = get_option($this->plugin_name . '-env');
$app_url = $pdc_env === 'prod' ? 'app.print.com' : 'app.stg.print.com';
?>

<p>You can create an API key in your Print.com account settings. Visit <a data-testid="pdc-environment-link" target="_blank" href="https://<?= $app_url; ?>/account"><?= $app_url; ?>/account</a>, create an API key and paste it in the input field below.</p>

<div class="notice notice-success hidden" id="js-<?= $this->plugin_name; ?>-auth-success">
    <p><?php _e('API Key verified. You are now connected!', 'pdc-connector'); ?></p>
</div>
<div class="notice notice-error hidden" id="js-<?= $this->plugin_name; ?>-auth-failed">
    <p><?php _e('API Key is not valid. Check your environment and API Key', 'pdc-connector'); ?></p>
</div>

<table class="form-table">
    <tbody>
        <tr>
            <th scope="row"><label for="pdc_api_key"><?php _e('API Key', 'pdc-connector'); ?></label></th>
            <td>
                <input id="pdc_api_key" data-testid="pdc-apikey" name="<?php echo $this->plugin_name; ?>-api_key" type="text" value="<?php echo $api_key; ?>" class="regular-text" />
                <span id="js-<?php echo $this->plugin_name; ?>-verify_loader" class="spinner"></span>
                <button data-testid="pdc-verify-key" type="button" id="js-<?php echo $this->plugin_name; ?>-verify_key" class="button button-secondary">
                    <span><?php _e('Verify', 'pdc-connector'); ?></span>
                </button>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="pdc_env"><?php _e('Environment', 'pdc-connector'); ?></label></th>
            <td>
                <select data-testid="pdc-environment" name="<?php echo $this->plugin_name; ?>-env" id="pdc_env">
                    <option <?php if ($pdc_env === "stg") { ?> selected <?php } ?> value="stg"><?php _e('Test', 'pdc-connector'); ?></option>
                    <option <?php if ($pdc_env === "prod") { ?> selected <?php } ?> value="prod"><?php _e('Live', 'pdc-connector'); ?></option>
                </select>
            </td>
        </tr>
    </tbody>
</table>