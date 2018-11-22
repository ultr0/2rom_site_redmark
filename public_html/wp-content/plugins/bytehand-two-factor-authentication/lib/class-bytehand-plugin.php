<?php
/*  Copyright 2015, ByteHand

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

// Require the bytehand API
if( !class_exists('bytehand') ) {
  require_once( 'bytehand/class-bytehand.php' );
}
if( !class_exists('WordPressbytehand') ) {
  require_once( 'bytehand/class-WordPressBytehand.php' );
}


/**
 * Base class for bytehand plugins
 *
 * @package bytehand
 */
abstract class bytehand_Plugin {
  
  /**
   * Version of the bytehand Wordpress wrapper
   */
    const VERSION = '1.0';
	/**
	 * URL to signup for a new bytehand account
	 */
	const SIGNUP_URL = 'https://www.bytehand.com/secure/settings';
	
	/**
	 * URL for support
	 */
	const SUPPORT_URL = 'https://www.bytehand.com/';
  
  /**
   * @param $callback Callback function for the plugin's menu item
   *
   */
  public $plugin_callback = null;
  
  /**
   * @param $plugin_dir Plugin directory name 
   *
   */
  public $plugin_dir = null;
  
  /**
	 * Instance of WordPressbytehand
	 *
	 * @var WordPressbytehand
	 */
  protected $bytehand = null;

  /**
   * Setup admin panel menu, notices and settings
   *
   */
  public function __construct() {
	   
    // Setup bytehand
    try {
      $options = get_option( 'bytehand_options' );
      if( is_array( $options ) && isset( $options['api_key'] )  && isset( $options['api_id'] )) {
        $this->bytehand = new WordPressbytehand( $options['api_id'], $options['api_key'] );
      }
    } catch( Exception $e ) {
    }
  
    // Register the activation hook to install
    register_activation_hook( __FILE__, array( $this, 'install' ) );
    
    add_action( 'admin_head', array( $this, 'setup_admin_head' ) );  
    add_action( 'admin_menu', array( $this, 'setup_admin_navigation' ) );
    add_action( 'admin_notices', array( $this, 'setup_admin_message' ) ); 
    add_action( 'admin_bar_menu', array( $this, 'setup_admin_bar' ), 999 );
    add_action( 'admin_init', array( $this, 'setup_admin_init' ) );
    add_action( 'admin_enqueue_scripts', array( $this, 'setup_bytehand_js' ) );
    
    $this->plugin_callback = array( $this, 'main' );
  }
    
  /**
   * Return the username and password from the plugin's existing options
   *
   * @return array Array of 'username' and 'password'
   */
  abstract public function get_existing_username_and_password();
  
  /**
   * Setup HTML for the admin <head>
   *
   * @return void
   */
  abstract public function setup_admin_head();
    
  
  /**
   * Called on plugin activation
   *
   * @return void
   */
  public function install() {
  }
  
  /**
   * Tell the user to update their bytehand options on every admin panel page if they haven't already
   *
   * @return void
   */
  public function setup_admin_message() {
    // Don't bother showing the "You need to set your bytehand options" message if it's that form we're viewing
    if( !isset( $this->bytehand ) && ( get_current_screen()->base != 'toplevel_page_bytehand_options' ) ) {
      $this->show_admin_message('You need to set your <a href="' . site_url() . '/wp-admin/admin.php?page=bytehand_options">bytehand options</a> before you can use ' . $this->plugin_name . '.');
    }
  }
  
  /**
	 * Add the bytehand balance to the admin bar
	 *
	 * @return void
	 */
  public function setup_admin_bar() {
		global $wp_admin_bar;
		if ( !is_super_admin() || !is_admin_bar_showing() ) {
			return;
		}

    $options = get_option( 'bytehand_options' );
    if( isset( $options['api_key'] ) &&  isset( $options['api_id'] )) {
  		// Display a low credit notification if there's no credit
      try {
        if( !isset( $this->bytehand ) ) {
          $bytehand = new WordPressbytehand( $options['api_id'], $options['api_key'] );
        }
		$balance = $this->bytehand->checkBalance();
    	if( $balance <= 0 ) {
    		$balance_string = '0 руб.'; 
    	} else {
    		$balance_string = $balance['balance'] . ' руб.';
    	}
    	$wp_admin_bar->add_node( array(
			'id' => 'bytehand_balance',
			'title' => 'Bytehand: ' . $balance_string
				)
		);	
      } catch( Exception $e ) {
        // Don't kill the entire admin panel because we can't get the balance
      }
    }
  }
  
  /**
   * Setup admin navigation: callback for 'admin_menu'
   *
   * @return void
   */
  public function setup_admin_navigation() {
    global $menu;
    
    $menu_exists = false;
    foreach( $menu as $k => $item ) {
      if( $item[0] == "ByteHand SMS" ) {
        $menu_exists = true;
		 break;
      }
    }

	

    // Setup global bytehand options
    if( !$menu_exists ) {    
      add_menu_page( __( 'ByteHand SMS', $this->language_string ), __( 'ByteHand SMS', $this->language_string ), 'manage_options', 'bytehand_options', array( $this, 'bytehand_options' ), plugins_url( 'images/logo_16px_16px.png', dirname( __FILE__ ) ) );
      add_submenu_page( 'bytehand_options', __( 'ByteHand Настройки', $this->language_string ), __( 'bytehand Настройки', $this->language_string ), 'manage_options', 'bytehand_options', array( $this, 'bytehand_options' ) );
      add_submenu_page( NULL, 'Test', 'Test', 'manage_options', 'bytehand_test_message', array( $this, 'bytehand_test_message' ) );
    }
    
    // Setup options for this plugin
    add_submenu_page( 'bytehand_options', __( $this->plugin_name, $this->language_string ), __( $this->plugin_name, $this->language_string ), 'manage_options', $this->plugin_callback[1], $this->plugin_callback );
  }
  
  /**
   * Set up javascript for the bytehand admin functions
   *
   * @return void
   */
  public function setup_bytehand_js() {
		wp_enqueue_script( 'bytehand_options', plugins_url( 'js/bytehand_options.js', dirname( __FILE__ ) ), array( 'jquery' ) );
  }
  
  /**
   * Register global bytehand settings for API keys 
   *
   * @return void
   */
  public function setup_admin_init() {
    register_setting( 'bytehand_options', 'bytehand_options', array( $this, 'bytehand_options_validate' ) );
    add_settings_section( 'bytehand_api_keys', 'Параметры API', array( $this, 'settings_api_key_text' ), 'bytehand' );

    add_settings_field( 'bytehand_api_id', 'ID', array( $this, 'settings_api_id_input' ), 'bytehand', 'bytehand_api_keys' );   
    add_settings_field( 'bytehand_api_key', 'Ключ', array( $this, 'settings_api_key_input' ), 'bytehand', 'bytehand_api_keys' );   
    
    add_settings_section( 'bytehand_defaults', 'Настройки отправителя', array( $this, 'settings_default_text' ), 'bytehand' );
    add_settings_field( 'bytehand_from', "Подпись отправителя", array( $this, 'settings_from_input' ), 'bytehand', 'bytehand_defaults' );    
  }
  
  /**
   * Introductory text for the API keys part of the form
   *
   * @return void
   */
  public function settings_api_key_text() {
		echo '<p>Получить ID и Ключ можно по адресу <a href="https://www.bytehand.com/secure/settings">https://www.bytehand.com/secure/settings</a></p>';
	}
  
  /**
   * Introductory text for the default part of the form
   *
   * @return void
   */
  public function settings_default_text() {
		echo '<p>По умолчанию Вы можете использовать подпись SMS-INFO</p>';
	}
  
  /**
   * Input box for the API key
   *
   * @return void

   */
  public function settings_api_key_input() {
    $options = get_option( 'bytehand_options' );
    
    if( isset( $options['api_key'] ) ) {      
      try {
        if( !isset( $this->bytehand ) ) {
			$this->bytehand = new WordPressbytehand( $options['api_id'], $options['api_key'] );
        }

		
        echo "<input id='bytehand_api_key' name='bytehand_options[api_key]' size='40' type='text' value='{$this->bytehand->key}' />";
      
        // Show balance
        $balance = $this->bytehand->checkBalance();
        if( $balance ) {
  	      echo '<p><strong>Баланс:</strong> ' . $balance['balance'] . ' руб.</p>';
  	    } else { // We can't get the credits for some reason
  		   
  	    } 
      
      } catch( bytehandException $e ) {
        echo "<input id='bytehand_api_key' name='bytehand_options[api_key]' size='40' type='text' value='' />";
        echo '<p><a href="' . self::SIGNUP_URL . '" class="button">Get An API Key</a></p>';        
      }
    
      return;
    } else {
      echo "<input id='bytehand_api_key' name='bytehand_options[api_key]' size='40' type='text' value='' />";
      echo '<p><a href="' . self::SIGNUP_URL . '" class="button">Get An API Key</a></p>';            
    }
  }

  public function settings_api_id_input() {
    $options = get_option( 'bytehand_options' );
    
    if( isset( $options['api_id'] ) ) {      
      try {
        if( !isset( $this->bytehand ) ) {
          $this->bytehand = new WordPressbytehand( $options['api_id'], $options['api_key'] );
        }
      
        echo "<input id='bytehand_api_id' name='bytehand_options[api_id]' size='40' type='text' value='{$this->bytehand->id}' />";
      
      } catch( bytehandException $e ) {
        echo "<input id='bytehand_api_id' name='bytehand_options[api_id]' size='40' type='text' value='' />";
      }
    
      return;
    } else {
      echo "<input id='bytehand_api_id' name='bytehand_options[api_id]' size='40' type='text' value='' />";
    }
  }
  
  /**
   * Input box for the from name
   *
   * @return void
   */
  public function settings_from_input() {
    $options = get_option( 'bytehand_options' );
    if( isset( $options['from'] ) ) {
      echo "<input id='bytehand_from' name='bytehand_options[from]' size='40' maxlength='14' type='text' value='{$options['from']}' />";
    } else {
      echo "<input id='bytehand_from' name='bytehand_options[from]' size='40' maxlength='14' type='text' value='' />";
    }
    
    echo "<p>Подпись отправителя. <a href=\"https://www.bytehand.com/secure/signatures\" target=\"_blank\">Создать</a></p>";
  }
  
  /**
   * Validation for the API key
   *
   * @return void
   */
  public function bytehand_options_validate( $val ) {
    // From santization
    $val['from'] = trim($val['from']);
    return $val;
  }
  
  /**
   * Render the main bytehand options page
   *
   * @return void
   */
  public function bytehand_options() {
    $this->render_template( 'bytehand-options' );
  }
  
  /**
   * Send a test SMS message
   *
   * @param string $to Mobile number to send to
   * @return void
   */
  public function bytehand_test_message( $to ) {
    $log = array();
    
    global $wp_version;
    $log[] = "Using Wordpress " . $wp_version;
    $log[] = "bytehand PHP wrapper initalised";
    $log[] = "Plugin wrapper initialised: using " . get_class($this) . ' ' . self::VERSION;
    $log[] = '';
    
    $options = get_option( 'bytehand_options' );
   
    // Check API key for sanity
    if (isset( $options['api_key'] )) {
      $log[] = "API key is set and appears valid – " . $options['api_key'];
    } else {
      $log[] = "API key is not set.";
      $log[] = "No credit has been used for this test";
      $this->output_test_message_log( $log );
      return;
    }

	if (isset( $options['api_id'] )) {
      $log[] = "API ID is set and appears valid – " . $options['api_id'];
    } else {
      $log[] = "API ID is not set.";
      $log[] = "No credit has been used for this test";
      $this->output_test_message_log( $log );
      return;
    }
    
    // Check originator for sanity
    if( isset( $options['from'] ) && strlen( $options['from'] ) <= 11 ) {
      $log[] = "Originator is set to " . $options['from'] . " and is below 11 characters";
            
    } else {
      $log[] = "Originator is not set, using your bytehand account default";
    }
        
    // Check if API key is valid
    $log[] = '';
    
    $bytehand = new WordPressbytehand($options['api_id'], $options['api_key'] );
	    
    // Check what the balance is
    $balance_resp = $bytehand->checkBalance();
    
    if( $balance_resp['balance'] > 0 ) {
      $log[] = 'Balance is ' . $balance_resp['balance'] . ' rub.';
    } else {
      $log[] = 'Balance is 0. You need to add more credit to your bytehand account';      
      $log[] = "No credit has been used for this test";
      $this->output_test_message_log( $log );
      return;
    }
        
    // Can we authenticate?
    $log[] = '';
    
    $message = 'This is a test message from bytehand.com';
    
    if( !$bytehand->is_valid_msisdn( $_GET['to'] ) ) {
      $log[] = $_GET['to'] . ' appears an invalid number to send to, this message may not send';
    }
    
    $log[] = 'Attempting test send with API key ' . $options['api_key'] . ' to ' . $_GET['to'];

    try {
      $message_data = array( array( 'from' => $options['from'], 'to' => $_GET['to'], 'message' => $message ) );
	 
      $result = $bytehand->send( $message_data );
      
      $log[] = '';
      
      if( isset( $result[0]['id'] ) && isset( $result[0]['success'] ) && ( $result[0]['success'] == '1' ) ) {
        $log[] = 'Message successfully sent with ID ' . $result[0]['id'];
       
        $balance_resp = $bytehand->checkBalance();
        $log[] = 'Your new balance is ' . $balance_resp['balance'] . ' rub';
      } else {
        $log[] = 'There was an error sending the message: error code ' . $result[0]['error_code'] . ' – ' . $result[0]['error_message'];
        $log[] = "No credit has been used for this test";
      }
    } catch( bytehandException $e ) {
      $log[] = "Error: " . $e->getMessage();
    } catch( Exception $e ) { 
      $log[] = "Error: " . $e->getMessage();
    }
    
    $this->output_test_message_log( $log );
  }
  
  protected function output_test_message_log( $log ) {
    $this->render_template( 'bytehand-test-message', array( 'log' => implode( "\r\n", $log ) ) );
  }
  
  /**
   * Show a message at the top of the administration panel
   *
   * @param string $message Error message to show (can include HTML) 
   * @param bool $errormsg True to display as a red 'error message'
   * @return void
   */
  protected function show_admin_message( $message, $errormsg = false ) {
    if( $errormsg ) {
      echo '<div id="message" class="error">';
    } else {
      echo '<div id="message" class="updated fade">';
    }
  
    echo "<p><strong>$message</strong></p></div>";
  }
  
  /**
   * Render a template file from the templates directory
   *
   * @param string $name Path to template file, excluding .php extension
   * @param array $data Array of data to include in template
   * @return void
   */
  protected function render_template( $name, $data = array() ) {
    include( WP_PLUGIN_DIR . '/' . $this->plugin_dir . '/templates/' . $name . '.php');
  }

}
