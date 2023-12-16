
<?php 
$env = get_option($this->plugin_name . '-env_baseurl');
$prodchecked = $env == 'https://api.print.com/' ? 'checked' : '';
$stgchecked = $env == 'https://api.print.beer/' ? 'checked' : '';
?>
<div class="environment">
    <input type="radio" <?= $prodchecked; ?> name="<?= $this->plugin_name; ?>-env_baseurl" value="https://api.print.com/" /> Production
</div>
<div class="environment">
    <input type="radio" <?= $stgchecked; ?> name="<?= $this->plugin_name; ?>-env_baseurl" value="https://api.print.beer/" /> Staging
</div>