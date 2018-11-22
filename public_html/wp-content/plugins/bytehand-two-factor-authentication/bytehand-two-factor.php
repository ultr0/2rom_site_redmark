<?php
/*
Plugin Name: Two-Factor Authentication - Bytehand SMS
Description: Proper security for your Wordpress site. Text message-based two factor authentication for logins.
Version: 1.0.0
Author: ByteHand.com
Author URI: http://www.bytehand.com/
*/
 
// Version of the Bytehand plugin in use
$GLOBALS['bytehand_plugins'][ basename( dirname( __FILE__ ) ) ] = '1.2.0';

if( !function_exists( 'bytehand_loader' ) ) {

  function bytehand_loader() {
    $versions = array_flip( $GLOBALS['bytehand_plugins'] );
    uksort( $versions, 'version_compare' );
    $versions = array_reverse( $versions );
    $first_plugin = reset( $versions );
    
    // Require Bytehand plugin architecture
    if( !class_exists( 'Bytehand_Plugin' ) ) {
      require_once( dirname( dirname( __FILE__ ) ) . '/' . $first_plugin . '/lib/class-bytehand-plugin.php' );
    }

    // Require each plugin, unless major version doesn't match
    $version_nums = array_keys($versions);
    preg_match( '/([0-9]+)\./', reset($version_nums), $matches );
    $major_version = intval( $matches[1] );

    foreach( $GLOBALS['bytehand_plugins'] as $plugin => $version ) {
      preg_match( '/([0-9]+)\./', $version, $matches );
      
      if( intval( $matches[1] ) < $major_version ) {
        // If it's a major version behind, automatically deactivate it
        require_once( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/wp-admin/includes/plugin.php' );
        $plugin_path = dirname( dirname( __FILE__ ) ) . '/' . $plugin . '/' . $plugin . '.php';
        $plugin_data = get_plugin_data( $plugin_path );
        deactivate_plugins( $plugin_path );
        
        // Output a message to tell the admin what's going on
        $message = '<div id="message" class="error"><p><strong>The plugin ' . $plugin_data['Name'] . ' has an important update available. It has been disabled until it has been updated.</strong></p></div>';
        print $message;
      } else {
        require_once( dirname( dirname( __FILE__ ) ) . '/' . $plugin . '/main.php' );
      }
      
    }
  }
  
}

add_action( 'plugins_loaded', 'bytehand_loader' );
