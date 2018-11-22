<?php
/**
 * Customer new account email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/plain/customer-new-account.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates/Emails/Plain
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

echo "= " . $email_heading . " =\n\n";

echo sprintf( __( 'Thanks for creating an account on %1$s. Your username is %2$s', 'woocommerce' ), $blogname, '<strong>' . $user_login . '</strong>' ) . "\n\n";

if ( 'yes' === get_option( 'woocommerce_registration_generate_password' ) && $password_generated ) {
	echo sprintf( __( 'Your password is %s.', 'woocommerce' ), '<strong>' . $user_pass . '</strong>' ) . "\n\n";
}

// страшная история
//            text/xml\r\n".
//                "Authorization: Basic ".base64_encode("$https_user:$https_password")."\r\n",
    $json = '{"sender": "SMS-INFO", "receiver": "+'.$user_login.'", "text": "Спасибо за регистрацию! Ваш пароль - '.$user_pass.' RedMark."}';
    $opts = array('http' =>
        array(
            'method'  => 'POST',
            'header'  => "Content-Type: application/json; charset=UTF-8 \r\n X-Service-Key: AzkT2JTNWp7M4AXM0Z6S8yqJwm7r9kKy",
            'content' => $json,
            'timeout' => 60
        )
    );

    $context  = stream_context_create($opts);
    $url = 'https://api.bytehand.com/v2/sms/messages';
    $result = file_get_contents($url, false, $context, -1, 40000);
    echo $result;
    print $result;



echo sprintf( __( 'You can access your account area to view your orders and change your password here: %s.', 'woocommerce' ), wc_get_page_permalink( 'myaccount' ) ) . "\n\n";

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
