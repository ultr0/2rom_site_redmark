jQuery(document).ready( function($) {
  
  if( $('input#bytehand_from').length > 0 ) {
    
    // Cut to 11 characters on blur if it contains alphanumeric characters
    $('input#bytehand_from').blur( function(e) {
      $('input#bytehand_from').val( trimToValid( $('input#bytehand_from').val() ) );
    });
    
    // Do the same on form submit
    $('form#bytehand_options_form').submit( function(e) {
      $('input#bytehand_from').val( trimToValid( $('input#bytehand_from').val() ) );      
    });
    
    // Input mask
    $("input#bytehand_from").keypress( function(e) {
      original_value = $(this).val();
      character = String.fromCharCode(e.keyCode ? e.keyCode : e.which);
      
      if( !character.match( /[0-9A-Za-z]/ ) ) {
        return false;
      }
    });
    
  }
  
});

function trimToValid( val ) {
  if( val.length > 11 && val.match(/[^d]/) ) {
    return val.substring( 0, 11 );
  }
  
  return val;
}