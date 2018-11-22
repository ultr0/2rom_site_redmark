<div class="wrap orion-otp-mega-wrapper">
	<div class="jumbotron">
		<h6 class="mb-0 text-white lh-100">Orion SMS OTP Verification</h6>
		<small><?php echo __( 'by', 'orion-sms-orion-sms-otp-verification' ); ?> Imran Sayed, Smit Patadiya</small>
	</div>

	<!--Plugin Description-->
	<div class="my-3 p-3 bg-white rounded box-shadow ihs-api-config-cont">
		<h6 class="border-bottom border-gray pb-2 mb-0"><?php echo __( 'Description', 'orion-sms-orion-sms-otp-verification' ); ?></h6>
		<div class="media text-muted pt-3">
			<div class="d-sm-flex media-body ihs-input-wrap pb-3 mb-0 small lh-125 border-bottom border-gray">
				<ul>
					<li><?php echo __( 'This plugin allows you to verify mobile number by sending a one time OTP to the user entered mobile number.', 'orion-sms-orion-sms-otp-verification' ); ?></li>
					<li><?php echo __( 'You can verify mobile number on Contact form 7 and any registration form. It will not allow the form to be submitted before completing the OTP verification.', 'orion-sms-orion-sms-otp-verification' ); ?></li>
					<li><?php echo __( 'This plugin uses a third party API call called MSG91 to send messages (', 'orion-sms-orion-sms-otp-verification' ); ?> <a href="<?php echo esc_url( 'https://msg91.com' ) ?>">https://msg91.com</a> )<?php echo __( '. All you have to do is get your auth key from MSG91 to send messages from the below link:', 'orion-sms-orion-sms-otp-verification' ); ?>
						<a href="<?php echo esc_url( 'https://msg91.com/signup' ) ?>" target="_blank">https://msg91.com/signup</a></li>
					<li><?php echo __( 'User can also reset his/her password using mobile OTP', 'orion-sms-orion-sms-otp-verification' ); ?></li>
					<li><?php echo __( 'The free version of the plugin uses', 'orion-sms-orion-sms-otp-verification' );?> <strong>OTP route</strong><?php echo __( '. So you need to buy OTP credits from MSG 91 plugin.', 'orion-sms-orion-sms-otp-verification' ); ?>
						<?php echo __( 'If you would like to use it with', 'orion-sms-orion-sms-otp-verification' ); ?> <strong>Transactional credit</strong> <?php echo __( 'then you would need to buy the', 'orion-sms-orion-sms-otp-verification' );?>
						<a href="<?php echo esc_url( 'https://imransayed.com/orion/' ); ?>" target="_blank"><?php echo __( 'premium version', 'orion-sms-orion-sms-otp-verification' ); ?></a></li>
					<li class="ihs-you-tube-link">
						<?php echo ihs_get_tell_me_how_link( 'Tell me how to use this plugin', 'https://youtu.be/hvDkuZowZfM?list=PLD8nQCAhR3tR2N5k3wy8doceQCyVLQEOf' )?>
					</li>
				</ul>
			</div>
		</div>
	</div>

	<!--Form-->
	<form method="post" action="options.php">
		<?php settings_fields( 'ihs-otp-plugin-settings-group' ); ?>
		<?php do_settings_sections( 'ihs-otp-plugin-settings-group' ); ?>


	<!--1. API Configuration-->
	<!--Heading-->
	<div class="d-sm-flex align-items-center p-3 my-3 text-white-50 bg-purple rounded box-shadow" style="background-color: #6f42c1; box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, .05);">
		<div class="lh-100 ihs-admin-head-cont">
			<h6 class="mb-0 text-white lh-100"><?php echo __( 'Api Configuration', 'orion-sms-orion-sms-otp-verification' );?></h6>
			<small><?php echo __( 'Api settings required for plugin to function', 'orion-sms-orion-sms-otp-verification' ); ?></small>
		</div>
	</div>
	<div class="my-3 p-3 bg-white rounded box-shadow ihs-api-config-cont">
		<p class="border-bottom border-gray pb-2 mb-0"><?php echo __( 'You can get the Auth Key from', 'orion-sms-orion-sms-otp-verification' ); ?> MSG91.
			<?php echo ihs_get_tell_me_how_link( __( 'Tell me how', 'orion-sms-orion-sms-otp-verification' ), 'https://youtu.be/od7f82A7RMw?list=PLD8nQCAhR3tR2N5k3wy8doceQCyVLQEOf' )?>
		</p>
		<!--Auth Key Input Field-->
		<div class="media text-muted pt-3">
			<div class="ihs-input-icon ihs-bg-blue d-flex"><i class="fa fa-key" aria-hidden="true"></i></div>
			<?php echo ihs_get_text_input( __( 'AUTH KEY', 'orion-sms-orion-sms-otp-verification' ), 'ihs_otp_auth_key',
				'text', true, '', true, __( 'Get the auth key from MSG91', 'orion-sms-orion-sms-otp-verification' ) ); ?>
		</div>
		<!--Sender's ID-->
		<div class="media text-muted pt-3">
			<div class="ihs-input-icon ihs-bg-pink d-flex"><i class="fa fa-id-badge" aria-hidden="true"></i></div>
			<?php echo ihs_get_text_input( __( 'SENDER\'S ID ( 6 characters )', 'orion-sms-orion-sms-otp-verification' ), 'ihs_otp_sender_id', 'text',
				true, '', true, 'e.g. IBAZAR', 6 ); ?>
		</div>
		<!--Country Code-->
		<div class="media text-muted pt-3">
			<div class="ihs-input-icon ihs-bg-purple d-flex"><i class="fa fa-globe" aria-hidden="true"></i></div>
			<?php echo ihs_get_text_input( __( 'COUNTRY CODE', 'orion-sms-orion-sms-otp-verification' ), 'ihs_otp_country_code', 'select' ); ?>
		</div>
		<!--Mobile No length-->
		<div class="media text-muted pt-3">
			<div class="ihs-input-icon ihs-bg-pink d-flex"><i class="ihs-my-icons fas fa-phone-square" aria-hidden="true"></i></div>
			<?php echo ihs_get_text_input( __( 'MOBILE NO LENGTH', 'orion-sms-orion-sms-otp-verification' ), 'ihs_mobile_length', 'text',
				false, '', true, __( 'How many digits excluding country code? For e.g. for India enter 10', 'orion-sms-orion-sms-otp-verification' ), 2 ); ?>
		</div>
		<!--Route-->
		<div class="media text-muted pt-3">
			<?php $route_text = __( 'MSG91 Route Name', 'orion-sms-orion-sms-otp-verification' ); ?>
			<div class="ihs-input-icon ihs-bg-purple d-flex"><i class="fas fa-map-signs ihs-my-icons"></i></div>
			<?php echo ihs_get_route_drop_down( __( 'ROUTE', 'orion-sms-orion-sms-otp-verification' ), 'ihs_mgs_route', true, true, $route_text ); ?>
		</div>
		<!--Upgrade to Pro-->
		<div class="media text-muted pt-3">
			<div class="ihs-for-transac-route"><?php echo __( 'For Transactional Route and Multiple Countries feature,', 'orion-sms-orion-sms-otp-verification' ); ?>
				<a href="<?php echo esc_url( 'https://imransayed.com/orion/product/orion-otp-premium-plugin/' ); ?>" target="_blank"><?php echo __( 'Upgarde to Pro', 'orion-sms-orion-sms-otp-verification' ); ?></a>
			</div>
		</div>
		<!--Rating-->
		<?php echo ihs_get_rate_us_content(); ?>
	</div>

	<!--2. Form Settings-->
	<!--Heading-->
	<div class="d-sm-flex align-items-center p-3 my-3 text-white-50 ihs-bg-blue rounded box-shadow" style="background-color: #6f42c1; box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, .05);">
		<div class="lh-100 ihs-admin-head-cont">
			<h6 class="mb-0 text-white lh-100"><?php echo __( 'Form Settings', 'orion-sms-orion-sms-otp-verification' ); ?></h6>
			<small>User Registration Form/ Contact Form 7/ Ultimate Member/ Profile Builder/ Profile Press/ Registration Magic/ Comment Form/ <?php echo __( 'Any Other Form', 'orion-sms-orion-sms-otp-verification' ); ?></small>
		</div>
	</div>
	<div class="my-3 p-3 bg-white rounded box-shadow ihs-api-config-cont">
		<h6 class="border-bottom border-gray pb-2 mb-0"><?php echo __( 'Form Settings', 'orion-sms-orion-sms-otp-verification' ); ?>
			<?php echo ihs_get_tell_me_how_link( __( 'Tell me how', 'orion-sms-orion-sms-otp-verification' ), 'https://youtu.be/3EX1p05pEv0?list=PLD8nQCAhR3tR2N5k3wy8doceQCyVLQEOf' )?>
		</h6>
		<!--Contact form Selector-->
		<div class="media text-muted pt-3">
			<div class="ihs-input-icon ihs-bg-blue d-flex"><i class="ihs-my-icons fab fa-wpforms" aria-hidden="true"></i></div>
			<?php echo ihs_get_text_input( __( 'CONTACT FORM SELECTOR', 'orion-sms-orion-sms-otp-verification' ),
				'ihs_otp_form_selector', 'text', false,
				'e.g .bodyclassname #divclassname', true,
				__( 'Please enter a unique bodyclassname followed by classname or id name parent div of the form element. Please prefix a . (dot) for class name and # for ID before the selector', 'orion-sms-orion-sms-otp-verification' ) ); ?>
		</div>
		<!--Submit Btn Selector-->
		<div class="media text-muted pt-3">
			<div class="ihs-input-icon ihs-bg-pink d-flex"><i class="ihs-my-icons fab fa-wpforms" aria-hidden="true"></i></div>
			<?php echo ihs_get_text_input( __( 'SUBMIT BUTTON SELECTOR', 'orion-sms-orion-sms-otp-verification' ), 'ihs_otp_submit_btn-selector',
				'text', true, 'e.g .body-classname #submit-btn-id',
				true, __( 'Please enter a unique body classname followed by submit button id or classname. The two selectors need to be separated by space. Also prefix a . (dot) for class name and # for an ID', 'orion-sms-orion-sms-otp-verification' ) ); ?>
		</div>
		<!--New Mobile Input field and preexisting One-->
		<?php echo ihs_get_mobile_input_fields();?>

		<!--OTP template-->
		<?php $textarea_placeholder = 'Your One Time Password is {OTP}. This OTP is valid for today and please don\'t share this OTP with anyone for security'; ?>
		<div class="media text-muted pt-3">
			<div class="ihs-input-icon ihs_otp_template_textarea ihs-bg-pink d-flex"><i class="ihs-my-icons fas fa-envelope" aria-hidden="true"></i></div>
			<?php echo ihs_get_text_input( __( 'OTP TEMPLATE', 'orion-sms-orion-sms-otp-verification' ), 'ihs_otp_msg_template',
				'textarea', true, $textarea_placeholder,
				true, 'Please make sure you follow the format given in placeholder along with <b>{OTP}</b>.'); ?>
		</div>
		<!--Rating-->
		<?php echo ihs_get_rate_us_content(); ?>
	</div>

	<!--3. Password Reset-->
	<!--Heading-->
	<div class="d-sm-flex align-items-center p-3 my-3 text-white-50 ihs-bg-light-pink rounded box-shadow" style="background-color: #6f42c1; box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, .05);">
		<div class="lh-100 ihs-admin-head-cont">
			<h6 class="mb-0 text-white lh-100"><?php echo __( 'Forgot Password Settings', 'orion-sms-orion-sms-otp-verification' ); ?></h6>
			<small><?php echo __( 'Send forgot Password SMS Settings ( Add these settings if you want forgot password field to be added in Login form )', 'orion-sms-orion-sms-otp-verification' ); ?></small>
		</div>
	</div>
	<div class="my-3 p-3 bg-white rounded box-shadow ihs-api-config-cont">
		<h6 class="border-bottom border-gray pb-2 mb-0"><?php echo __( 'Form Settings', 'orion-sms-orion-sms-otp-verification' ); ?>
			<?php echo ihs_get_tell_me_how_link( __( 'Tell me how', 'orion-sms-orion-sms-otp-verification' ), 'https://youtu.be/3EX1p05pEv0?list=PLD8nQCAhR3tR2N5k3wy8doceQCyVLQEOf&t=925' )?>
		</h6>
		<!--Login form Selector-->
		<div class="media text-muted pt-3">
			<div class="ihs-input-icon ihs-bg-blue d-flex"><i class="ihs-my-icons fab fa-wpforms" aria-hidden="true"></i></div>
			<?php echo ihs_get_text_input( 'FORM/PARENT SELECTOR',
				'ihs_otp_login_form_selector', 'text', false,
				'e.g .classname or #idname', true,
				'Enter a unique body classname followed by form\'s parent selector of the login form. Please prefix a . (dot) for class name and # for ID before the login form selector' ); ?>
		</div>
		<!--Input Name-->
		<div class="media text-muted pt-3">
			<div class="ihs-input-icon ihs-bg-purple d-flex"><i class="ihs-my-icons fab fa-wpforms" aria-hidden="true"></i></div>
			<?php echo ihs_get_text_input( 'INPUT NAME',
				'ihs_otp_login_form_input_name', 'text', false,
				'e.g user-name', true,
				'Enter any one input name inside the login form. e.g. name' ); ?>
		</div>
		<!--Meta Key for Mobile No-->
		<div class="media text-muted pt-3">
			<div class="ihs-input-icon ihs-bg-pink d-flex"><i class="ihs-my-icons fas fa-code" aria-hidden="true"></i></div>
			<?php echo ihs_get_text_input( 'META_KEY FOR MOBILE NO', 'ihs_otp_mob_meta_key',
				'text', false, '',
				true, 'Enter meta_key for mobile number provided mobile no. is being saved in wp_usermeta table'); ?>
		</div>
		<!--Is Mobile No Saved with Country Code-->
		<div class="media text-muted pt-3">
			<div class="ihs-input-icon ihs-bg-pink d-flex"><i class="ihs-my-icons fa fa-globe" aria-hidden="true"></i></div>
			<?php echo ihs_is_saved_with_country_code( 'SAVED WITH COUNTRY CODE', 'ihs_no_saved_with_country',
				false, true, 'If mobile no is being saved with country code in the database, select yes, no 
				otherwise.' ) ?>
		</div>
		<!--Forgot Password Country Code-->
		<div class="media text-muted pt-3">
			<div class="ihs-input-icon ihs-bg-blue d-flex"><i class="fa fa-globe" aria-hidden="true"></i></div>
			<?php echo ihs_get_text_input( 'COUNTRY CODE', 'ihs_otp_mob_country_code',
				'select', false, '',
				true, 'If mobile number is being saved with the country code. Please enter the country code ( e.g. if the mobile number is saved as +919960119780 then enter <b>+91</b> )'); ?>
		</div>


		<!--Message template-->
		<?php $textarea_placeholder = __( 'Your New Password is {OTP}. Please don\'t share this OTP with anyone for security', 'orion-sms-orion-sms-otp-verification' ); ?>
		<div class="media text-muted pt-3">
			<div class="ihs-input-icon ihs_otp_template_textarea ihs-bg-pink d-flex"><i class="ihs-my-icons fas fa-envelope" aria-hidden="true"></i></div>
			<?php echo ihs_get_text_input( 'Msg Template', 'ihs_otp_reset_template',
				'textarea', false, $textarea_placeholder,
				true, 'Please make sure you follow the format given in placeholder along with <b>{OTP}</b>.'); ?>
		</div>
		<!--Rating-->
		<?php echo ihs_get_rate_us_content(); ?>
	</div>

		<!--Submit Button-->
		<?php submit_button(); ?>
	</form>

	<!--1- Tutorial Section-->
	<!--Heading-->
	<div class="d-sm-flex align-items-center p-3 my-3 text-white-50 ihs-bg-light-purple rounded box-shadow" style="background-color: #6f42c1; box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, .05);">
		<div class="lh-100 ihs-admin-head-cont">
			<h6 class="mb-0 text-white lh-100"><?php echo __( 'How to use the Plugin?', 'orion-sms-orion-sms-otp-verification' );?></h6>
			<small><?php echo __( 'Watch below demo tutorials to have a better understanding', 'orion-sms-orion-sms-otp-verification' ); ?></small>
		</div>
	</div>
	<div class="">
		<div class="row">
			<div class="col-md-4 col-sm-6 col-12">
				<?php ihs_get_video_cards( 'Plugin Demo', 'New Orion OTP SMS WordPress Plugin Demo | msg91', 'https://www.youtube.com/embed/hvDkuZowZfM' ); ?>
			</div>
			<div class="col-md-4 col-sm-6">
				<?php ihs_get_video_cards( __( 'New Features', 'orion-sms-orion-sms-otp-verification' ), __( 'Whats new in the Orion SMS OTP MSG91 WordPress Plugin V 1.0.2', 'orion-sms-orion-sms-otp-verification' ), 'https://www.youtube.com/embed/VzrnXY6i-J8' ); ?>
			</div>
			<div class="col-md-4 col-sm-6">
				<?php ihs_get_video_cards( 'Auth Key & Routes', __( 'Get the Auth Key from MSG 91 | OTP and Transactional Route', 'orion-sms-orion-sms-otp-verification' ), 'https://www.youtube.com/embed/od7f82A7RMw' ); ?>
			</div>
			<div class="col-md-4 col-sm-6">
				<?php ihs_get_video_cards( 'With Contact Form 7', __( 'How to use the Orion SMS OTP WordPress plugin with Contact Form 7', 'orion-sms-orion-sms-otp-verification' ), 'https://www.youtube.com/embed/xkafUWOaIL8' ); ?>
			</div>
			<div class="col-md-4 col-sm-6">
				<?php ihs_get_video_cards( 'With Ultimate Member', __( 'How to use Orion SMS OTP Plugin with Ultimate Member Plugin', 'orion-sms-orion-sms-otp-verification' ), 'https://www.youtube.com/embed/3EX1p05pEv0' ); ?>
			</div>
			<div class="col-md-4 col-sm-6">
				<?php ihs_get_video_cards( 'With User Registration', __( 'How to use Orion SMS OTP Plugin with User Registration Plugin', 'orion-sms-orion-sms-otp-verification' ), 'https://www.youtube.com/embed/8G8Vq0tadoE' ); ?>
			</div>
			<div class="col-md-4 col-sm-6">
				<?php ihs_get_video_cards( 'With Registration Magic', __( 'How to Use Orion SMS OTP Plugin with Registration Magic Plugin', 'orion-sms-orion-sms-otp-verification' ), 'https://www.youtube.com/embed/P7zHEEZyqlg' ); ?>
			</div>
			<div class="col-md-4 col-sm-6">
				<?php ihs_get_video_cards( 'With Profile Press', __( 'How to use Orion SMS OTP WordPress Plugin with Profile Press', 'orion-sms-orion-sms-otp-verification' ), 'https://www.youtube.com/embed/ppsnfUQuFDM' ); ?>
			</div>
			<div class="col-md-4 col-sm-6">
				<?php ihs_get_video_cards( 'With Profile Builder', __( 'How to use the Orion SMS OTP WordPress Plugin | Profile Builder', 'orion-sms-orion-sms-otp-verification' ), 'https://www.youtube.com/embed/gDh8oP-zoBA' ); ?>
			</div>
		</div>
	</div>
</div>