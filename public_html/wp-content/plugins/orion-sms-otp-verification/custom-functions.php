<?php
/**
 * Custom functions for the Orion Plugin.
 * Contains definition of constants, file includes and enqueuing stylesheets and scripts.
 *
 * @package Orion SMS OTP verification
 */

/* Define Constants */
define( 'IHS_OTP_URI', plugins_url( 'orion-sms-otp-verification' ) );
define( 'IHS_OTP_PATH', plugin_dir_path( __FILE__ ) );
define( 'IHS_OTP_JS_URI', plugins_url( 'orion-sms-otp-verification' ) . '/vendor/js' );
define( 'IHS_OTP_CSS_URI', plugins_url( 'orion-sms-otp-verification' ) . '/css' );


if ( ! function_exists( 'ihs_otp_enqueue_scripts' ) ) {
	/**
	 * Enqueue Styles and Scripts.
	 */
	function ihs_otp_enqueue_scripts() {
		wp_enqueue_style( 'ihs_otp_styles', IHS_OTP_URI . '/style.css' );
		wp_enqueue_script( 'ihs_otp_alert_js', IHS_OTP_JS_URI . '/alert.js', array( 'jquery' ), '', true );
		wp_enqueue_script( 'ihs_otp_main_js', IHS_OTP_JS_URI . '/main.js', array( 'jquery' ), '', true );
		wp_enqueue_script( 'ihs_otp_reset_password_js', IHS_OTP_JS_URI . '/reset-password.js', array( 'jquery' ), '', true );
		wp_localize_script(
			'ihs_otp_main_js', 'otp_obj', array(
				'ajax_url'   => admin_url( 'admin-ajax.php' ),
				'ajax_nonce' => wp_create_nonce( 'ihs_otp_nonce_action_name' ),
				'form_selector' => get_option( 'ihs_otp_form_selector' ),
				'submit_btn_selector' => get_option( 'ihs_otp_submit_btn-selector' ),
				'input_required' => get_option( 'ihs_otp_mobile_input_required' ),
				'mobile_input_name' => get_option( 'ihs_otp_mobile_input_name' ),
				'ihs_country_code' => get_option( 'ihs_otp_country_code' ),
				'ihs_mobile_length' => get_option( 'ihs_mobile_length' ),
			)
		);
		wp_localize_script(
			'ihs_otp_reset_password_js', 'reset_pass_obj', array(
				'ajax_url'   => admin_url( 'admin-ajax.php' ),
				'ajax_nonce' => wp_create_nonce( 'ihs_otp_nonce_reset_pass' ),
				'form_selector' => get_option( 'ihs_otp_login_form_selector' ),
				'country_code' => get_option( 'ihs_otp_mob_country_code' ),
				'ihs_mobile_length' => get_option( 'ihs_mobile_length' ),
				'login_input_name' => get_option( 'ihs_otp_login_form_input_name' ),
			)
		);
	}
}

add_action( 'wp_enqueue_scripts', 'ihs_otp_enqueue_scripts' );

if ( ! function_exists( 'ihs_otp_enqueue_admin_scripts' ) ) {
	/**
	 * Enqueue Styles and Scripts for admin.
	 *
	 * @param {string} $hook Hook.
	 */
	function ihs_otp_enqueue_admin_scripts( $hook ) {
		if ( 'toplevel_page_orion-sms-otp-verification/inc/admin-settings' === $hook
		|| 'orion-otp-pro_page_ihs_otp_plugin_woocommerce_settings_page' === $hook ) {
			wp_enqueue_style( 'ihs_otp_admin_font_awesome', '//use.fontawesome.com/releases/v5.0.13/css/all.css' );
			wp_enqueue_style( 'ihs_otp_admin_bootstrap_styles', '//stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css' );
			wp_enqueue_style( 'ihs_otp_admin_styles', IHS_OTP_CSS_URI . '/admin.css' );
			wp_enqueue_script( 'ihs_otp_admin_script', IHS_OTP_JS_URI . '/admin.js', array( 'jquery' ), '', true );
		}
	}
	add_action( 'admin_enqueue_scripts', 'ihs_otp_enqueue_admin_scripts' );
}


if ( ! function_exists( 'ihs_otp_ajax_handler' ) ) {
	/**
	 * Send OTP .
	 */
	function ihs_otp_ajax_handler() {
		if ( isset( $_POST['security'] ) ) {
			$nonce_val = esc_html( wp_unslash( $_POST['security'] ) );
		}

		if ( ! wp_verify_nonce( $nonce_val, 'ihs_otp_nonce_action_name' ) ) {
			wp_die();
		}
		$mobile_number = $_POST['data']['mob'];
		$country_code_from_form = $_POST['data']['country_code'];
		$country_code_from_form = str_replace( '+', '', $country_code_from_form );
		$mobile_number = ( isset( $mobile_number ) && is_numeric( $mobile_number ) ) ? wp_unslash( $mobile_number ) : '';
		$mobile_number = absint( $mobile_number );
		$message_template = get_option( 'ihs_otp_msg_template' );
		$otp_pin = ihs_generate_otp( $mobile_number, $message_template, $country_code_from_form );

		wp_send_json_success(
			array(
				'otp_pin_sent_to_js' => $otp_pin,
				'data_recieved_from_js'    => $_POST,
			)
		);
	}

	add_action( 'wp_ajax_ihs_otp_ajax_hook', 'ihs_otp_ajax_handler' );
	add_action( 'wp_ajax_nopriv_ihs_otp_ajax_hook', 'ihs_otp_ajax_handler' );
}

if ( ! function_exists( 'ihs_generate_otp' ) ) {
	/**
	 * Generates random OTP, Calls function ihs_send_otp to send otp and
	 * returns OTP if the message sent was successful.
	 *
	 * @param {int}    $mobile_number Mobile number.
	 * @param {string} $message_template Message template.
	 * @param {string} $country_code_form Country code entered in front end form.
	 *
	 * @return {bool|string} $otp_pin Otp Pin.
	 */
	function ihs_generate_otp( $mobile_number, $message_template, $country_code_form ) {
		$otp_pin = mt_rand( 100000, 500000 );
		$country_code = ( $country_code_form ) ? $country_code_form : get_option( 'ihs_otp_country_code' );
		$country_code_length = strlen( $country_code );

		$response = ihs_send_otp( $mobile_number, $country_code, $otp_pin, $message_template );
		return ( $response ) ? $otp_pin : '';
	}
}

if ( ! function_exists( 'ihs_send_otp' ) ) {
	/**
	 * Send Otp.
	 *
	 * @param {int}    $mob_number Mobile number.
	 * @param {int}    $country_code Country Code.
	 * @param {string} $otp_pin Otp pin.
	 * @param {string} $message_template Message Template.
	 *
	 * @return {mixed} $response Response or Error.
	 */
	function ihs_send_otp( $mob_number, $country_code, $otp_pin, $message_template ) {
		$auth_key = get_option( 'ihs_otp_auth_key' );
		$otp_length = strlen( $otp_pin );
		$message = str_replace( '{OTP}', $otp_pin, $message_template );
		$sender_id = get_option( 'ihs_otp_sender_id' );
		$country_code = str_replace( '+', '', $country_code );
		$mob_number_with_country_code = '+' . $country_code . $mob_number;
		$message = urlencode( $message );
		$route = get_option( 'ihs_mgs_route' );
		
		if ( 'otp-route' === $route ) {
			return ihs_send_otp_via_otp_route(
				$otp_length, $auth_key, $message,
				$sender_id, $mob_number_with_country_code, $otp_pin );
		}
	}
}

if ( ! function_exists( 'ihs_send_otp_via_otp_route' ) ) {
	function ihs_send_otp_via_otp_route( $otp_length, $auth_key, $message,
		$sender_id, $mob_number, $otp_pin ) {
		$url = "http://control.msg91.com/api/sendotp.php?otp_length=$otp_length&authkey=$auth_key&message=$message&sender=$sender_id&mobile=$mob_number&otp=$otp_pin";
		$response = wp_remote_post( $url, array(
				'method' => 'POST',
				'timeout' => 30,
				'redirection' => 10,
				'httpversion' => '1.1',
				'blocking' => true,
				'headers' => array(),
				'body' => array(),
				'cookies' => array()
			)
		);

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			echo "Something went wrong: $error_message";
		} else if ( 'OK' === $response['response']['message'] ) {
			return true;
		}
	}
}


if ( ! function_exists( 'ihs_otp_send_new_pass' ) ) {
	/**
	 * Send New Password.
	 */
	function ihs_otp_send_new_pass() {
		if ( isset( $_POST['security'] ) ) {
			$nonce_val = sanitize_text_field( wp_unslash( $_POST['security'] ) );
		}

		if ( ! wp_verify_nonce( $nonce_val, 'ihs_otp_nonce_reset_pass' ) ) {
			wp_die();
		}
		$mobile_number = $_POST['data']['mob'];
		$country_code_from_form = $_POST['data']['country_code'];
		$country_code_from_form = str_replace( '+', '', $country_code_from_form );
		$mobile_number = ( isset( $mobile_number ) && is_numeric( $mobile_number ) ) ? wp_unslash( $mobile_number ) : '';
		$mobile_number = absint( $mobile_number );
		$meta_key = get_option( 'ihs_otp_mob_meta_key' );
		$meta_key = sanitize_text_field( $meta_key );
		$message_template = get_option( 'ihs_otp_reset_template' );
		
		$country_code_prefix = get_option( 'ihs_otp_mob_country_code' );
		$is_saved_with_country_code = get_option( 'ihs_no_saved_with_country' );
		$new_password = ihs_generate_otp( $mobile_number, $message_template, $country_code_from_form );
		$database_mob_number = '';
		if ( 'Yes' === $is_saved_with_country_code && $new_password ) {
			$database_mob_number = '+' . $country_code_from_form . $mobile_number;
		} else if ( 'No' === $is_saved_with_country_code && $new_password ){
			$database_mob_number = $mobile_number;
		}
		$args = array(
			'meta_key' => $meta_key,
			'meta_value' => $database_mob_number,
		);
		$user_obj = get_users( $args );
		$user_id = $user_obj[0]->data->ID;

		// If user exists update the new password for him.
		if ( $user_id ) {
			wp_set_password( $new_password, $user_id );
		}

		wp_send_json_success(
			array(
				'otp_pin_sent_to_js' => true,
				'data_recieved_from_js'    => $_POST,
				'user_id' => $user_id,
				'mobile_no' => $mobile_number,
				'country_from_form' => $country_code_from_form,
				'msg' => $message_template,
			)
		);
	}

	add_action( 'wp_ajax_ihs_otp_reset_ajax_hook', 'ihs_otp_send_new_pass' );
	add_action( 'wp_ajax_nopriv_ihs_otp_reset_ajax_hook', 'ihs_otp_send_new_pass' );
}
