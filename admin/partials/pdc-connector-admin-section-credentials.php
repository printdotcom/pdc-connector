<?php 
$username = get_option($this->plugin_name . '-user');
$pw = get_option($this->plugin_name . '-pw');
?>
<input class="hidden" id="js-<?= $this->plugin_name; ?>-testusername" test-username" value="<?= $username ?>" />
<input class="hidden" id="js-<?= $this->plugin_name; ?>-testpw" name="test-password" value="<?= $pw ?>" />
<button type="button" id="js-<?= $this->plugin_name; ?>-testcredentials" class="button button-hero">Test Login</button>

<div class="notice notice-success hidden" id="js-<?= $this->plugin_name; ?>-auth-success">
    <p><?php _e( 'Credentials verified. You are now connected.', 'pdc_connector' ); ?></p>
</div>
<div class="notice notice-error hidden" id="js-<?= $this->plugin_name; ?>-auth-failed">
    <p><?php _e( 'Credentials are not verified. Please contact your nearest Print.com partner.', 'pdc_connector' ); ?></p>
</div>