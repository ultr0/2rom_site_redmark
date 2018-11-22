<?php
/**
 * Custom functions for creating admin menu settings for the plugin.
 *
 * @package Orion SMS OTP Verification
 */

add_action( 'admin_menu', 'ihs_otp_create_menu' );

if ( ! function_exists( 'ihs_otp_create_menu' ) ) {
	/**
	 * Creates Menu for Orion Plugin in the dashboard.
	 */
	function ihs_otp_create_menu() {

		$menu_plugin_title = __( 'Orion OTP', 'orion-sms-orion-sms-otp-verification' );

		// Create new top-level menu.
		add_menu_page( __( 'Orion OTP Plugin Settings', 'orion-sms-orion-sms-otp-verification' ), $menu_plugin_title, 'administrator', __FILE__, 'ihs_otp_plugin_settings_page', 'dashicons-email' );


		// Add submenu Page.
		$parent_slug = __FILE__;
		$page_title = __( 'Woocommerce SMS Settings', 'orion-sms-orion-sms-otp-verification' );
		$menu_title = __( 'Woocommerce Settings', 'orion-sms-orion-sms-otp-verification' );
		$capability = 'manage_options';
		$menu_slug = 'ihs_otp_plugin_woocommerce_settings_page';
		$function = 'ihs_otp_woocommerce_setting_page_func';
		add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );

		// Call register settings function.
		add_action( 'admin_init', 'register_ihs_otp_plugin_settings' );
	}
}

if ( ! function_exists( 'register_ihs_otp_plugin_settings' ) ) {

	/**
	 * Register our settings.
	 */
	function register_ihs_otp_plugin_settings() {
		register_setting( 'ihs-otp-plugin-settings-group', 'ihs_otp_auth_key' );
		register_setting( 'ihs-otp-plugin-settings-group', 'ihs_otp_sender_id' );
		register_setting( 'ihs-otp-plugin-settings-group', 'ihs_otp_country_code' );
		register_setting( 'ihs-otp-plugin-settings-group', 'ihs_otp_form_selector' );
		register_setting( 'ihs-otp-plugin-settings-group', 'ihs_otp_submit_btn-selector' );
		register_setting( 'ihs-otp-plugin-settings-group', 'ihs_otp_mobile_input_required' );
		register_setting( 'ihs-otp-plugin-settings-group', 'ihs_otp_mobile_input_name' );
		register_setting( 'ihs-otp-plugin-settings-group', 'ihs_otp_msg_template' );
		register_setting( 'ihs-otp-plugin-settings-group', 'ihs_otp_mob_meta_key' );
		register_setting( 'ihs-otp-plugin-settings-group', 'ihs_otp_login_form_selector' );
		register_setting( 'ihs-otp-plugin-settings-group', 'ihs_otp_mob_country_code' );
		register_setting( 'ihs-otp-plugin-settings-group', 'ihs_mgs_route' );
		register_setting( 'ihs-otp-plugin-settings-group', 'ihs_mobile_length' );
		register_setting( 'ihs-otp-plugin-settings-group', 'ihs_otp_login_form_input_name' );
		register_setting( 'ihs-otp-plugin-settings-group', 'ihs_no_saved_with_country' );
		register_setting( 'ihs-otp-plugin-settings-group', 'ihs_otp_reset_template' );
	}
}

if ( ! function_exists( 'ihs_get_checked_val' ) ) {

	/**
	 * Find the value of checked mobile input value and return an array.
	 *
	 * @return {array} $checked_array Array containing values yes or no.
	 */
	function ihs_get_checked_val() {
		$checked_array = array(
			'checked-yes' => '',
			'checked-no' => '',
		);
		$checkbox_val = esc_attr( get_option( 'ihs_otp_mobile_input_required' ) );
		if ( 'Yes' === $checkbox_val ) {
			$checked_array['checked-yes'] = 'checked';
		} else if ( 'No' === $checkbox_val ) {
			$checked_array['checked-no'] = 'checked';
		}
		return $checked_array;
	}
}

if ( ! function_exists( 'ihs_otp_plugin_settings_page' ) ) {

	/**
	 * Settings Page for Orion Plugin.
	 */
	function ihs_otp_plugin_settings_page() {

		include_once IHS_OTP_PATH . '/inc/otp-form-template.php';
	}
}

if ( ! function_exists( 'ihs_otp_woocommerce_setting_page_func' ) ) {
	/**
	 * Woo Commerce Setting tab contents
	 */
	function ihs_otp_woocommerce_setting_page_func() {
	?>
		<div class="jumbotron woo-comm-upgrade-pro-wrap">
			<div class="woo-com-header">
				<h1 class="display-5"><?php echo __( 'Upgrade to Pro to get Woocommerce Support!', 'orion-sms-orion-sms-otp-verification' ) ?></h1>
				<p class="lead"><?php echo __( 'PRO FEATURES:', 'orion-sms-orion-sms-otp-verification' ); ?></p>
			</div>
			<div class="woo-com-wrap">
				<ul class="list-group list-group-flush">
					<li class="list-group-item">
						<i class="fa fa-check" aria-hidden="true"></i>
						<?php echo __( 'Transactional Route', 'orion-sms-orion-sms-otp-verification' ); ?>
					</li>
					<li class="list-group-item">
						<i class="fa fa-check" aria-hidden="true"></i>
						<?php echo __( ' Woocommerce Checkout Mobile OTP Verification', 'orion-sms-orion-sms-otp-verification' ); ?>
					</li>
					<li class="list-group-item">
						<i class="fa fa-check" aria-hidden="true"></i>
						<?php echo __( 'Woocommerce Order SMS', 'orion-sms-orion-sms-otp-verification' ); ?>
					</li>
					<li class="list-group-item">
						<i class="fa fa-check" aria-hidden="true"></i>
						<?php echo __( 'Plugin Customization', 'orion-sms-orion-sms-otp-verification' ); ?>
					</li>
				</ul>
				<p class="lead">
					<a class="woo-upgrade-pro-button" href="<?php echo esc_url( 'https://imransayed.com/orion/' )?>" target="_blank" role="button">Upgrade to Pro</a>
				</p>
			</div>
		</div>
	<?php
	}
}
