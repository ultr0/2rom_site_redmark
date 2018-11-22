<div class="wrap">
  <div class="left-content">
      
    <div class="icon32"><img src="<?php echo plugins_url( 'images/logo_32px_32px.png', dirname( __FILE__ ) ); ?>" /></div>
    <h2>Настройки ByteHand SMS</h2>
    
    <form method="post" action="options.php" id="bytehand_options_form">
    
    <?php
    foreach( array_unique( get_settings_errors( 'bytehand_options' ) ) as $error ) {
      if( $error['type'] == 'updated' ) {
        print '<div id="message" class="updated fade"><p><strong>' . $error['message'] . '</strong></p></div>';        
      } else {
        print '<div id="message" class="error"><p><strong>' . $error['message'] . '</strong></p></div>';                
      }
    }
    
    settings_fields( 'bytehand_options' );
    do_settings_sections( 'bytehand' );
    submit_button();
    ?>
    
    </form>
    
  </div>
  
  <div class="right-content">
    <div class="innerbox">
      <h2>Проверить</h2>
      
      <p>Вы можете проверить корректно работы плагина. </p>
	  <p>Укажите свой номер в международном формате (+79111111111) и нажмите кнопку "Тест".</p>
      
      <form method="admin.php?page=bytehand_test_message" method="post" accept-charset="utf-8">      
      <input type="hidden" name="page" value="bytehand_test_message">
      <p><input type="text" size="14" maxlength="14" class="bytehand_number_field" name="to" value="" placeholder="Введите ваш номер"> <button type="submit" class="button">Тест</a></p>
      </form>
    </div>
      
    <img src="<?php echo plugins_url( 'images/badrobot.png', dirname( __FILE__ ) ); ?>" />
  </div>

</div>