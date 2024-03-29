( function ( $ ) {
	"use strict";

	var resetOtp = {

		/**
		 * Init Function.
		 */
		init: function () {
			if ( ! reset_pass_obj.form_selector ) {
				return;
			}
			resetOtp.Select = reset_pass_obj.form_selector;
			resetOtp.loginInputName = reset_pass_obj.login_input_name;

			resetOtp.mobileLength = reset_pass_obj.ihs_mobile_length;
			resetOtp.countryCode = reset_pass_obj.ihs_otp_mob_country_code;
			resetOtp.Selector = resetOtp.buildFormSelector();
			resetOtp.addRequiredInputFields();
			resetOtp.bindEvents();
		},

		/**
		 * Set values for form selector and submit button selector.
		 */
		buildFormSelector: function () {
			resetOtp.userFormSelector = reset_pass_obj.form_selector;
			resetOtp.loginInputName = reset_pass_obj.login_input_name;

			var formSelector = resetOtp.userFormSelector + ' input[name="' + resetOtp.loginInputName + '"]';
			/**
			 * Find the parent form element for the submit button selector.
			 * And if the parent form element is found add a class 'ihs_si_form' to it.
			 */
			resetOtp.formElement = $( formSelector ).parents( 'form' );
			if ( resetOtp.formElement.length ) {
				resetOtp.formElement.addClass( 'ihs_log_form' );
			}

			resetOtp.formSelector = 'form.ihs_log_form';
			return resetOtp.formSelector;
		},

		/**
		 * Add required Input Fields.
		 */
		addRequiredInputFields: function () {
			resetOtp.resetPassLink = '<a class="ihs-otp-password-reset-link btn" href="javascript:void(0)">Reset Password</a>';
			$( resetOtp.Selector ).append( resetOtp.resetPassLink );
		},

		/**
		 * Bind Events.
		 */
		bindEvents: function () {
			$( '.ihs-otp-password-reset-link' ).on( 'click', function () {
				var sendPassBtn, content,
					countryCode = ( resetOtp.countryCode && 'ALL' !== resetOtp.countryCode ) ? '+' + resetOtp.countryCode : '',
					readOnly = ( countryCode ) ? 'readonly' : '';
				if ( ! resetOtp.countryCode ) {
					resetOtp.countryCode = '+91';
				}

				var mobileInputEl = '<br>' +
									'<label id="ihs-otp-reset-pass-input"> Mobile Number (required)<br>\n' +
										'<div id="ihs-country-code" class="ihs-country-code-exis-mob">' +
											'<div class="ihs-country-inp-wrap">' +
											'<span class="">' +
											'   <input type="text" name="' + 'ihs-country-code' + '" value="' + countryCode + '" class="wpcf7-form-control ihs-country-code ihs-reset-country-code" required placeholder="+91" aria-invalid="false" ' + readOnly + ' maxlength="5">' +
											'</span> ' +
											'</div>' +
										'</div>' +
										'<div>' +
											'<span class="">' +
												'<input type="number" name="ihs-otp-reset-pass-input" value="" class="ihs-otp-reset-pass-input" aria-required="true" aria-invalid="false">' +
											'</span> ' +
										'</div>' +
									'</label>';
					sendPassBtn = '<div class="ihs-otp-send-pass-btn" id="ihs-otp-send-pass-btn">Send New Password</div>';
					content = mobileInputEl + sendPassBtn;
				$( resetOtp.Selector ).append( content );
				$( '.ihs-otp-password-reset-link' ).remove();
			} );
			$( resetOtp.Selector ).on( 'click', '.ihs-otp-send-pass-btn', function () {
				var mobileNumber = $( '.ihs-otp-reset-pass-input' ).val(),
					isNoError,
					countryCodeEl = $( '.ihs-reset-country-code' ),
					countryCodeElVal = countryCodeEl.val(),
					mobileLengthDatabase = parseInt( reset_pass_obj.ihs_mobile_length, 10 ),
					isAllSelected = ( resetOtp.countryCode && 'ALL' === resetOtp.countryCode ) ? true : '',
					errorArray = [];

				// Validate for no error
				isNoError = resetOtp.mobileAndCountryCodeValidation( mobileNumber, isAllSelected, mobileLengthDatabase, countryCodeElVal, errorArray );

				// If no error call ajax request function.
				if ( ! isNoError ) {
					resetOtp.sendNewPassAjaxRequest( mobileNumber, countryCodeElVal );
				}
			} );
		},
		/**
		 * Return true if there are errors.
		 *
		 * @param mobElVal
		 * @param isAllSelected
		 * @param mobileLengthDatabase
		 * @param countryCodeElVal
		 * @param errorArray
		 */
		mobileAndCountryCodeValidation: function ( mobElVal, isAllSelected, mobileLengthDatabase, countryCodeElVal, errorArray ) {
			if ( ! mobElVal ) {
				errorArray.push( 'Enter the mobile Number' );
			}

			if ( mobElVal && ! isAllSelected ) {
				// Checks the mobile digit needs to be at least no. of digit user has entered
				if ( mobileLengthDatabase && mobileLengthDatabase !== mobElVal.length ) {
					console.log( 'yes' );
					errorArray.push( 'Enter the correct Mobile Number' );
				}
				if ( ! mobileLengthDatabase && mobElVal.length < 5 ) {
					errorArray.push( 'Enter the correct Mobile Number' );
				}
			}

			if ( ! countryCodeElVal ) {
				errorArray.push( 'Enter the Country Code' );
			}

			if ( errorArray.length ) {
				var errorMessages = errorArray.join( '</br>' );
				alerts.error(
					errorMessages,'',{
						displayDuration: 3000
					});
			}

			return errorArray.length;
		},

		/**
		 * Send New Password Ajax Request.
		 *
		 * @param {int} mobileNumber
		 * @param {string} countryCodeElVal
		 */
		sendNewPassAjaxRequest: function ( mobileNumber, countryCodeElVal ) {
			var request = $.post(
				reset_pass_obj.ajax_url,   // this url till admin-ajax.php  is given by functions.php wp_localoze_script()
				{
					action: 'ihs_otp_reset_ajax_hook',
					security: reset_pass_obj.ajax_nonce,
					data: {
						mob: mobileNumber,
						country_code: countryCodeElVal
					}
				}
			);

			request.done( function ( response ) {
				if ( response.data.otp_pin_sent_to_js ) {
					alerts.info(
						'New password sent to your mobile',
						{
							displayDuration: 0
						});
					$( '#ihs-otp-reset-pass-input' ).hide();
					$( '#ihs-otp-send-pass-btn' ).hide();
				}
			} );
		}
	},

	selector = 'form' + reset_pass_obj.form_selector;
	if ( $( selector ) ) {
		resetOtp.init();
	}

})( jQuery );
