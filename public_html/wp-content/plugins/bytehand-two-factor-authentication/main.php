<?php
class Bytehand_Two_Factor_Plugin extends Bytehand_Plugin {  

  protected $plugin_name = 'ByteHand двух-факторная авторизация';  
  protected $language_string = 'bytehand_two_factor';
  protected $prefix = 'bytehand_two_factor';
  protected $folder = '';
  
  protected $forms = array();
  
  
  public function __construct() {
    parent::__construct();
    
    $this->plugin_callback = array( $this, 'bytehand_two_factor' );    
    $this->plugin_dir = basename( dirname( __FILE__ ) );
    
    add_action( 'show_user_profile', array( $this, 'show_user_profile' ) );
    add_action( 'edit_user_profile', array( $this, 'show_user_profile' ) );
    add_action( 'personal_options_update', array( $this, 'save_user_profile' ) );
    add_action( 'edit_user_profile_update', array( $this, 'save_user_profile' ) );
    add_action( 'clear_auth_cookie', array( $this, 'destroy_code' ) );
    
    add_action( 'wp_login', array( $this, 'send_code' ), 10, 2 );

	if (isset($_GET['bh_resend'])) {
		 $user = wp_get_current_user();
		 if ($user) {
			$this->send_code(true, $user );
		 }
	}
  }
  

  public function setup_admin_navigation() {
    parent::setup_admin_navigation();
  }
  

  public function setup_admin_message() {
    parent::setup_admin_message();
    

    $user = wp_get_current_user();
    $mobile = get_user_meta( $user->ID, 'mobile', true );
    
    if( !isset( $mobile ) || $mobile == '' ) {
      $this->show_admin_message( 'Bytehand двухфакторная авторизация: если Вы <a href="profile.php#mobile">не указали свой телефон</a> у вас могут возникнуть проблемы при работе', true);      
    }
  }
  
  
  public function setup_admin_head() {
    echo '<link rel="stylesheet" type="text/css" href="' . plugins_url( 'css/bytehand.css', __FILE__ ) . '">';
  }
  
  
  public function setup_admin_init() {    
    // Register main Bytehand functions
    parent::setup_admin_init();
    
    // Plugin options
    register_setting( 'bytehand_two_factor_user', 'bytehand_two_factor_user', array( $this, 'validate_bytehand_user_options' ) );
    add_settings_section( 'bytehand_two_factor_user', 'Настройки', array( $this, 'user_settings_text' ), 'bytehand_two_factor_user' );
    add_settings_field( 'enabled', 'Активно для', array( $this, 'bytehand_two_factor_enabled_input' ), 'bytehand_two_factor_user', 'bytehand_two_factor_user' );
    register_setting( 'bytehand_two_factor_credit', 'bytehand_two_factor_credit' );
    add_settings_section( 'bytehand_two_factor_credit', 'Настройки баланса', array( $this, 'credit_settings_text' ), 'bytehand_two_factor_credit' );
    add_settings_field( 'credit', 'Баланс', array( $this, 'bytehand_two_factor_credit_options' ), 'bytehand_two_factor_credit', 'bytehand_two_factor_credit' );
    
    if( !get_option( 'bytehand_two_factor_credit' ) ) {
      update_option( 'bytehand_two_factor_credit', array( 'credit' => 'disable_plugin' ) );
    }
    
    $this->handle_two_factor_authentication();
  }
  
  
  public function send_code( $wp_login, $user ) {
	 
    // Get the mobile number
    $mobile_number = get_user_meta( $user->ID, 'mobile', true );
    
    // Do we have a Bytehand API key set?
    $options = get_option( 'bytehand_options' );
    if( !is_array( $options ) || !isset( $options['api_key'] ) || empty( $options['api_key'] ) ) {
      print 1;
      return;
    }
    
    if( defined( 'BYTEHAND_TWOFACTOR' ) ) {
      if( constant( 'BYTEHAND_TWOFACTOR' ) === false ) {
        return;
      }
    }
    
    // Get the enabled groups and credit options
    if( is_array( get_option( 'bytehand_two_factor_user' ) ) ) {
      $options = @array_merge( $options, get_option( 'bytehand_two_factor_user' ) );
    }
    if( is_array( get_option( 'bytehand_two_factor_credit' ) ) ) {
      $options = @array_merge( $options, get_option( 'bytehand_two_factor_credit' ) );
    }
    
    // Have we enabled any groups?
    if( !isset( $options['enabled'] ) ) {
      return;
    }
    
    // Is this user not in a group which we're using two-factor authentication for?
    if( count( array_intersect( array_values( $user->roles ), array_keys( $options['enabled'] ) ) ) == 0 ) {
      return;
    }
    
    // Do we have any credit?
    $bytehand = new WordPressBytehand( $options['api_id'], $options['api_key'] );
    $balance = $bytehand->checkBalance();
    
    // If we're out of credit, check what the option is
    if( floatval( $balance['balance'] ) == 0.0 ) {
      if( !isset( $options['credit'] ) || $options['credit'] == 'disable_plugin' ) {
        return;
      } elseif( $options['credit'] == 'disable_wordpress' ) {
        $message = '<p>Вы не можете войти в WordPress, т.к. у вас закончились средства на счету ByteHand.</p>';
        wp_logout();
        $this->die_with_message( $message );
        die();
      }
    }
      
    // Does this user have a mobile number set?
    if( !isset( $mobile_number ) || ( $mobile_number == '' ) ) {
      $this->render_template( 'required-number-form' );
      wp_logout();
      die();
    }
    
    // Send them the code
    $code = $this->generate_code();
    $message = 'Ваш Bytehand SMS код для ' . get_bloginfo('name') . ': ' . $code . '.';
  	update_user_meta( $user->ID, 'bytehand_code', $code );
  	update_user_meta( $user->ID, 'bytehand_time', time() );
      
    // Send the message
    try {
      $messages = array( array( 'from' => $options['from'], 'to' => $user->mobile, 'message' => $message ) );
      $result = $bytehand->send( $messages );
      update_user_meta( $user->ID, 'bytehand_prevent_login', '1' );
    } catch( BytehandException $e ) {
      $result = "Error: " . $e->getMessage();
    } catch( Exception $e ) { 
      $result = "Error: " . $e->getMessage();
    }
  }
  
 
  public function handle_two_factor_authentication() {
    // Do we have the disable override setup in wp-config.php?
    if( defined( 'BYTEHAND_TWOFACTOR' ) ) {
      if( constant( 'BYTEHAND_TWOFACTOR' ) === false ) {
        return;
      }
    }
    
    $user = wp_get_current_user();
    
    // Have we entered a code?
    if( isset( $_GET['code'] ) ) {
      $code = get_user_meta( $user->ID, 'bytehand_code', true );
      if( $code == $_GET['code'] ) {
        $this->destroy_code();
        update_user_meta( $user->ID, 'bytehand_prevent_login', '0' );
        return;
      } else {
        $this->render_template( 'code-form', array( 'error_message' => "Код введен неправильно. Пожалуйста, попробуйте ещё раз." ) );
        die(); 
      }
    }    
    
    // If no code entered, can we login?
    $meta = get_user_meta( $user->ID, 'bytehand_prevent_login', true );
    if( ( isset( $meta ) && $meta == 1 ) || !isset( $meta ) ) {
      $this->render_template( 'code-form', array( 'user' => $user, 'message' => "Введите 4-значный код, отправленный вам на телефон." ) );
      die();
    }
    
  }
  
 
  public function generate_code() {
    mt_srand( time() );
    return mt_rand( 1000, 9999 );
  }
  
 
  public function destroy_code() {    
    $user = wp_get_current_user();
  	update_user_meta( $user->ID, 'bytehand_time', '0' );
  	update_user_meta( $user->ID, 'bytehand_code', '0' );
    return;
  }
  
 
  public function die_with_message( $message ) {
    $title = get_bloginfo('name') . ' - двухфакторная аутентификация';
    wp_die( $message, $title );
    return;
  }
  
  public function user_settings_text() {
	 print '<p>Вы можете указать роли, где будет использоваться двух-факторная аутентификация</p>';

  }
  
  
  public function credit_settings_text() {
    print '<p>Настройки работы с балансом</p>';    
  }
  
 
  public function bytehand_two_factor_enabled_input() {
    $options = get_option( 'bytehand_two_factor_user' );
    if( $_GET['settings-updated'] && isset( $options['error_message'] ) ) {
      print '<div id="message" class="error"><p><strong>' . $options['error_message'] . '</strong></p></div>';
    }
    
    $roles = get_editable_roles();
    foreach( $roles as $tag => $data ) {
      if( isset( $options['enabled'][$tag] ) ) {
        print '<label><input type="checkbox" checked="checked" name="bytehand_two_factor_user[enabled][' . $tag . ']" id="bytehand_two_factor_user_enabled" value="1">&nbsp;&nbsp;&nbsp;' . $data['name'] . '</label><br />';
      } else {
        print '<label><input type="checkbox" name="bytehand_two_factor_user[enabled][' . $tag . ']" id="bytehand_two_factor_user_enabled" value="1">&nbsp;&nbsp;&nbsp;' . $data['name'] . '</label><br />';        
      }
    }
  }
  
  
  public function validate_bytehand_user_options( $val ) {
    $all_users = new WP_User_Query( array( 'role' => 'Administrator', 'fields' => 'all_with_meta' ) );
    $mobile_number_set = 0;
    
    foreach( $all_users->results as $u ) {
      if( $u->has_prop( 'mobile' ) ) {
        $mobile_number_set++;
      }
    }
    
    if( $mobile_number_set == 0 ) {
      $val = array();
      $val['error_message'] = 'Как минимум один администратор должен указать номер мобильного телефона';
      return $val;        
    }
    
    return $val;
  }
  
  public function bytehand_two_factor_require_number_input() {
    $options = get_option( 'bytehand_two_factor_user' );
    if( isset( $options['require_number'] ) ) {
      print '<input type="checkbox" name="bytehand_two_factor_user[require_number]" checked="checked" id="bytehand_two_factor_user_require_number" value="1">';
    } else {
      print '<input type="checkbox" name="bytehand_two_factor_user[require_number]" id="bytehand_two_factor_user_require_number" value="1">';      
    }
  }
  
  public function bytehand_two_factor_credit_options() {
    $options = get_option( 'bytehand_two_factor_credit' );
    if( !isset( $options ) || !isset( $options['credit'] ) || $options['credit'] == 'disable_plugin' ) { 
      print '<label><input type="radio" name="bytehand_two_factor_credit[credit]" value="disable_plugin" checked="checked">&nbsp;&nbsp;&nbsp;Отключить плагин, когда у вас закончатся средства на счету.</strong></label><br />';
      print '<label><input type="radio" name="bytehand_two_factor_credit[credit]" value="disable_wordpress">&nbsp;&nbsp;&nbsp;Отключить доступ к Wordpress, когда у вас закончатся средства на счету.</strong></label><br />';
    } else {
      print '<label><input type="radio" name="bytehand_two_factor_credit[credit]" value="disable_plugin">&nbsp;&nbsp;&nbsp;Отключить плагин, когда у вас закончатся средства на счету.</label><br />';
      print '<label><input type="radio" name="bytehand_two_factor_credit[credit]" value="disable_wordpress" checked="checked">&nbsp;&nbsp;&nbsp;Отключить доступ к Wordpress, когда у вас закончатся средства на счету.</label><br />';      
    }
  }
  
 
  public function show_user_profile( $user ) {
    $this->render_template( 'user-options', $user );
  }
  
 
  public function save_user_profile( $user_id ) {
  	if ( !current_user_can( 'edit_user', $user_id ) ) {
  		return false;
    }
    
  	update_user_meta( $user_id, 'mobile', $_POST['mobile'] );
  }
  

  public function bytehand_two_factor() {
    $this->render_template( 'two-factor-options' );
  }
  
  
  public function get_existing_username_and_password() { }
  
}

$cp = new Bytehand_Two_Factor_Plugin();