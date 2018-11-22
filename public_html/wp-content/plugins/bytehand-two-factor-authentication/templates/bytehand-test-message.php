<div class="wrap">
  <div class="left-content">
      
    <h2>bytehand SMS – отправка тестового сообщения</h2>
    
    <?php if( $_GET['to'] == '' ) { ?>
		<p>Введите телефон для теста!.</p>
    <?php } else { ?>
    
		 <p>Вы доджны получить тестовое сообщение на телефон <?php print $_GET['to']; ?>. Если Вы не получили это сообщение, то отправьте информацию ниже в службу поддержи bytehand.co.</p>
    
    <p><a href="<?php echo self::SUPPORT_URL; ?>" class="button" target="_blank">Bytehand.com</a></p>
    
    <br />
    <textarea name="log" rows="25" cols="90" id="log"><?php print $data['log']; ?></textarea>
    <?php } ?>
    
  </div>
  
      
  
  </div>
</div>