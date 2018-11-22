( function ( $ ) {
	"use strict";

	var otp = {

		mobileInputElement: '',
		mobileInputSelector: '',
		submitBtnSelector: '',
		mobileOtpInputEl: '',
		sendOtpBtnEl: '',
		verifyOtpBtnEl: '',
		otpPinSent: '',
		countryCodeSelector: '#ihs-country-code .ihs-country-code',
		mobileVerified: false,

		/**
		 * Init function.
		 */
		init: function () {
			this.buildFormSelector();

			// If neither submit button selector or form button selector is filled , then return
			if (  '' === otp.formElement && '' === otp.formSelector  ) {
				return;
			}
			
			otp.addRequiredInputFields();
			otp.bindEvents();
		},

		/**
		 * Set values for form selector and submit button selector.
		 */
		buildFormSelector: function () {
			otp.userFormSelector = otp_obj.form_selector;
			otp.submitBtnSelector = otp_obj.submit_btn_selector;

			/**
			 * Find the parent form element for the submit button selector.
			 * And if the parent form element is found add a class 'ihs_si_form' to it.
			 */
			otp.formElement = $( otp.submitBtnSelector ).parents( 'form' );
			if ( otp.formElement.length ) {
				otp.formElement.addClass( 'ihs_si_form' );
			}

			/**
			 * If user has not entered form selector then, add selector 'form.ihs_si_form',
			 * otherwise use the one provided by him.
			 * @type {string}
			 */
			otp.formSelector = ( ! otp.userFormSelector ) ? 'form.ihs_si_form' : otp.userFormSelector;
			console.log( otp.formSelector );
		},

		/**
		 * Binds Events.
		 */
		bindEvents: function () {
			if ( otp.submitBtnSelector ) {
				$( otp.submitBtnSelector ).on( 'click', function () {
					console.log( otp.submitBtnSelector );
					if ( ! otp.mobileVerified ) {
						event.preventDefault();
						alerts.error(
							'Please verify OTP first','',{
								displayDuration: 3000
							});
						return false;
					}
				} );
			} else {
				$( otp.formSelector ).on( 'submit', function () {
					if ( ! otp.mobileVerified ) {
						event.preventDefault();
						alerts.error(
							'Please enter the required fields','',{
								displayDuration: 3000
							});
						return false;
					}
				} );
			}

			$( otp.formSelector ).on( 'click', '#ihs-send-otp-btn', function () {
				var mobEl = $( otp.mobileInputSelector ),
					mobElVal = mobEl.val(),
					countryCodeEl = $( otp.countryCodeSelector ),
					countryCodeElVal = countryCodeEl.val(),
					isNoError,
					isAllSelected = '',
					errorArray = [],
					mobileLengthDatabase = parseInt( otp_obj.ihs_mobile_length, 10 );
				console.log( typeof mobileLengthDatabase, typeof mobElVal.length  );
				console.log( otp.countryCodeSelector );

				isNoError = otp.mobileAndCountryCodeValidation( mobElVal, isAllSelected, mobileLengthDatabase, countryCodeElVal, errorArray );
				// If no errors send Ajax request for otp.
				if ( ! isNoError ) {
					$( '#ihs-mobile-otp' ).removeClass( 'ihs-otp-hide' );
					otp.sendOtpAjaxRequest( mobElVal, countryCodeElVal );
				}
			} );

			$( otp.formSelector ).on( 'click', '#ihs-submit-otp-btn', function () {
				var otpInputEl = $( '#ihs-mobile-otp' ),
					otpInputElVal = otpInputEl.val();
					// otpInputElVal = otpInputElVal;
				if ( otpInputElVal ) {
					if ( otp.otpPinSent == otpInputElVal ) {
						otp.mobileVerified = true;
						alerts.success(
							'Thanks for the verification',
							{
								displayDuration: 3000,
								pos: 'top'
							});
						$( '.ihs-otp-required' ).fadeOut( 500 );
						otp.verifyOtpBtnEl.fadeOut( 500 );
					} else {
						alerts.error(
							'OTP entered is incorrect','',{
								displayDuration: 3000
							});
					}
					// otp.verifyOtpAjaxRequest( otpInputElVal );
				} else {
					alerts.error(
						'Please enter OTP','',{
							displayDuration: 3000
						});
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
		 * Create and append the required input fields.
		 */
		addRequiredInputFields: function () {
			var mobileInputName = 'ihs-mobile',
				countryCodeInputName, countryCode,
				createOtpFieldsWithMobInput = otp_obj.input_required,
				htmlEl, countryCodeHtmlCont;
			countryCodeInputName = 'ihs-country-code';
			countryCode = ( otp_obj.ihs_country_code && 'ALL' !== otp_obj.ihs_country_code ) ? '+' + otp_obj.ihs_country_code : '';

			countryCodeHtmlCont =   '<div id="ihs-country-code" class="ihs-country-code-exis-mob">' +
										'<div class="ihs-country-inp-wrap">' +
											'<span class="">' +
												'<input type="text" name="' + countryCodeInputName + '" value="' + countryCode + '" class="ihs-country-code" required placeholder="+91" aria-invalid="false" ' + readOnly + ' maxlength="5">' +
											'</span> ' +
										'</div>' +
									'</div>';

			if ( 'Yes' === createOtpFieldsWithMobInput ) {
				createOtpFieldsWithMobInput = true;
			} else if ( 'No' === createOtpFieldsWithMobInput ) {
				createOtpFieldsWithMobInput = false;
			} else {
				createOtpFieldsWithMobInput = false;
			}
			otp.mobileInputName = mobileInputName;

			if ( ! createOtpFieldsWithMobInput ) {
				var mobileInputNm = otp_obj.mobile_input_name;

				if ( mobileInputNm ) {
					var mobInpSelector = otp.formSelector + ' input[name="' + mobileInputNm + '"]';
					console.log( mobInpSelector );
					htmlEl = otp.createMobileInputAndOtherFields( mobileInputNm );
					$( htmlEl.allOtpHtml ).insertAfter( mobInpSelector );
					otp.mobileInputSelector = htmlEl.mobileInputNameSelector;
					otp.mobileInputElement = otp.setInputElVariables( htmlEl.mobileInputNameSelector );
					otp.setOtpInputElementVar();

					// Add country code input field before existing mobile no. and add a class to existing mob input field
					$( countryCodeHtmlCont ).insertBefore( otp.mobileInputSelector );
					$( otp.mobileInputSelector  ).addClass( 'ihs-existing-mob-inp-fld' );
					$( otp.mobileInputSelector  ).css( 'width', 'calc(100% - 5rem)' );
				} else {
					htmlEl = otp.createMobileInputAndOtherFields( mobileInputName );
					$( htmlEl.allOtpHtml ).insertAfter( htmlEl.mobileInputNameSelector );
					otp.mobileInputSelector = htmlEl.mobileInputNameSelector;
					otp.mobileInputElement = otp.setInputElVariables( htmlEl.mobileInputNameSelector );
					otp.setOtpInputElementVar();

					// Add country code input field before existing mobile no. and add a class to existing mob input field
					$( countryCodeHtmlCont ).insertBefore( otp.mobileInputSelector );
					$( otp.mobileInputSelector  ).addClass( 'ihs-existing-mob-inp-fld' );
					$( otp.mobileInputSelector  ).css( 'width', 'calc(100% - 5rem)' );
				}

			} else {
				var readOnly,
					mobAndCountryCodeContent = '',
					countryCodeAndMobileInputEl, submitBtnSelector,
					mobileInpName = 'ihs-mobile';
					readOnly = ( countryCode ) ? 'readonly' : '';
					countryCodeAndMobileInputEl = '<label id="ihs-country-code" class="ihs-mobile-no-lab">Mobile Number with Country Code (required)<br>\n' +
														'<div class="ihs-country-inp-wrap">' +
															'<span class="">' +
															'<input type="text" name="' + countryCodeInputName + '" value="' + countryCode + '" class="ihs-country-code" required placeholder="+91" aria-invalid="false" ' + readOnly + ' maxlength="5">' +
															'</span> ' +
														'</div>' +
														'<div class="ihs-mob-inp-wrap">' +
															'<span class="">' +
															'<input type="number" name="' + mobileInpName + '" value="" class="ihs-mb-inp-field" aria-required="true" aria-invalid="false">' +
															'</span> ' +
														'</div>' +
												   '</label>',
					submitBtnSelector = otp.formSelector + ' input[type="submit"]';
				htmlEl = otp.createMobileInputAndOtherFields( mobileInputName );
				mobAndCountryCodeContent = '<div class="ihs-mob-country-wrapper">' + countryCodeAndMobileInputEl + '</div>';
				mobAndCountryCodeContent += htmlEl.allOtpHtml;
				otp.mobileInputSelector = '#ihs-country-code .ihs-mb-inp-field';
				otp.countryCodeSelector = '#ihs-country-code .ihs-country-code';
				// $( mobileInputEl ).insertBefore( submitBtnSelector );

				$( otp.formSelector ).append( mobAndCountryCodeContent );
				otp.setOtpInputElementVar();
				otp.mobileInputElement = otp.setInputElVariables( '#ihs-mobile-number' );
			}
		},

		setOtpInputElementVar: function () {
			otp.mobileOtpInputEl = otp.setInputElVariables( '#ihs-mobile-otp' );
			otp.mobileOtpHiddenInputEl = otp.setInputElVariables( '#ihs-otp-hidden' );
			otp.sendOtpBtnEl = otp.setInputElVariables( '#ihs-send-otp-btn' );
			otp.verifyOtpBtnEl = otp.setInputElVariables( '#ihs-submit-otp-btn' );
		},

		/**
		 * Sets the value of an element.
		 *
		 * @param elementSelector
		 * @return {*|HTMLElement} elementSelector Element Selector.
		 */
		setInputElVariables: function ( elementSelector ) {
			return $( elementSelector );
		},

		/**
		 * Creates markup for OTP input fields and submit button.
		 *
		 * @param mobileInputName
		 * @return {obj} htmlEl Contains markup for OTP input fields and submit button.
		 */
		createMobileInputAndOtherFields: function ( mobileInputName ) {
			var htmlEl = {},
				otpInputEl = '<br><label id="ihs-otp-required" class="ihs-otp-required ihs-otp-hide"> OTP (required)<br>\n' +
				'<span class="wrap ihs-otp">' +
				'<input type="number" id="ihs-mobile-otp" name="ihs-otp" value="" size="40" class="wpcf7-text wpcf7-validates-as-required ihs-otp-hide" aria-required="true" aria-invalid="false">' +
				'</span>' +
				'</label>',
				sendOtpBtn = '<div class="ihs-otp-btn" id="ihs-send-otp-btn">Send OTP</div>',
				submitOtpBtn = '<div class="ihs-otp-btn ihs-otp-hide" id="ihs-submit-otp-btn">Verify OTP</div>';
				htmlEl.allOtpHtml = otpInputEl + sendOtpBtn + submitOtpBtn;
				htmlEl.mobileInputNameSelector = otp.formSelector + ' input[name="' + mobileInputName + '"]';
			return htmlEl;
		},

		/**
		 * OTP function
		 * @param {} mobileNumber
		 */
		sendOtpAjaxRequest: function ( mobileNumber, countryCode ) {
			var request = $.post(
				otp_obj.ajax_url,   // this url till admin-ajax.php  is given by functions.php wp_localoze_script()
				{
					action: 'ihs_otp_ajax_hook',
					security: otp_obj.ajax_nonce,
					data: {
						mob: mobileNumber,
						country_code: countryCode
					}
				}
			);

			request.done( function ( response ) {
				otp.otpPinSent = response.data.otp_pin_sent_to_js;
				if ( response.data.otp_pin_sent_to_js ) {
					alerts.info(
						'OTP sent to your mobile',
						{
							displayDuration: 0
						});

					// Hide the Send OTP button once OTP is sent and disable moble input field
					$( '#ihs-send-otp-btn' ).hide();
					$( otp.verifyOtpBtnEl ).removeClass( 'ihs-otp-hide' );
					$( '.ihs-otp-required' ).removeClass( 'ihs-otp-hide' );
					$( otp.mobileInputSelector ).attr( 'readonly', true );
					$( otp.countryCodeSelector ).attr( 'readonly', true );
					$( otp.countryCodeSelector ).css( 'color', '#b3b0b0' );
					$( otp.mobileInputSelector ).css( 'opacity', '0.5' );

				}
			} );
		}
	};
		if( 'undefined' !== typeof otp_obj ){
		var selector = 'form' + otp_obj.form_selector;
		if ( $( selector ) ) {
			otp.init();
		}
	}

})( jQuery );
