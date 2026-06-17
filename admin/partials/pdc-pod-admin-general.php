<?php
/**
 * Admin general settings page
 *
 * Renders the Print.com general settings admin page with tabbed navigation.
 *
 * @package Pdc_Pod
 * @subpackage Pdc_Pod/admin/partials
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$current_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'general';

$tabs = array(
	'general' => __( 'General', 'pdc-pod' ),
	'product' => __( 'Product', 'pdc-pod' ),
	'support' => __( 'Support', 'pdc-pod' ),
);

if ( ! array_key_exists( $current_tab, $tabs ) ) {
	$current_tab = 'general';
}

$active_tab_name = $tabs[ $current_tab ];
?>
<div class="wrap pdc-pod-settings">
	<div class="pdc-pod-settings-head">
		<strong><?php echo esc_html( get_admin_page_title() ); ?></strong>
	</div>
	<nav class="nav-tab-wrapper">
		<?php foreach ( $tabs as $tab_id => $tab_label ) : ?>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=' . PDC_POD_NAME . '&tab=' . $tab_id ) ); ?>"
			   class="nav-tab <?php echo $current_tab === $tab_id ? 'nav-tab-active' : ''; ?>">
				<?php echo esc_html( $tab_label ); ?>
			</a>
		<?php endforeach; ?>
	</nav>
	<form method="post" action="options.php" id="js-<?php echo esc_attr( PDC_POD_NAME ); ?>-general-form">
		<h2 class="screen-reader-text"><?php echo esc_html($active_tab_name); ?></h2>
		<?php
		if ( 'general' === $current_tab ) {
			settings_fields( PDC_POD_NAME . '-general-options' );
			do_settings_sections( PDC_POD_NAME . '-general' );
		} elseif ( 'product' === $current_tab ) {
			settings_fields( PDC_POD_NAME . '-product-options' );
			do_settings_sections( PDC_POD_NAME . '-product' );
		} elseif ( 'support' === $current_tab ) {
			settings_fields( PDC_POD_NAME . '-support-options' );
			do_settings_sections( PDC_POD_NAME . '-support' );
		}
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
