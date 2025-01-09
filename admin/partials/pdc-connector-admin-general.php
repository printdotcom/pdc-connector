<div class="wrap">
    <h2><?php echo esc_html(get_admin_page_title()); ?></h2>
    <p><?php _e('This plugin allows you to connect your WooCommerce store to Print.com.', 'pdc-connector'); ?></p>
    <form method="post" action="options.php" id="js-<?= $this->plugin_name; ?>-general-form">
        <?php
        settings_fields($this->plugin_name . '-options');
        do_settings_sections($this->plugin_name);
        submit_button('Save Settings');
        ?>
    </form>
</div>