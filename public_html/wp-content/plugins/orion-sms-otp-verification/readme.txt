=== Orion SMS OTP Verification ===
Contributors: gsayed786, smitpatadiya
Tags: otp, mobile verification, verification, mobile, phone, sms, one time, password
Requires at least: 4.6
Tested up to: 4.9.2
Stable tag: 4.9.2
Requires PHP: 5.2.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

This plugin allows you to verify mobile number by sending a one time OTP to the mobile number.
It works with Contact Form 7 and any registration form. You can also reset password using mobile number OTP.

== Description ==

This plugin allows you to verify mobile number by sending a one time OTP to the user entered mobile number.
You can verify mobile number on Contact form 7 and any registration form. It will not allow the form to be submitted before completing the OTP verification.
This plugin uses a third party API call called MSG91 to send messages ( http://control.msg91.com ). All you have to do is get your auth key from MSG91 to send messages from the below link:
https://msg91.com/signup
You can also use this plugin to send OTP to international numbers( One country at a time ).
User can also reset his/her password using mobile OTP.
If for some reason you want to switch back to the older version of the plugin you can download it on https://imransayed.com/orion/download-prev-versions/
This plugin has been tested with WordPress default theme Twentyseventeen along with the top 6 forms plugins( with their versions available at the time of release ) and works successfully:
1-Contact Form 7
2-User Registration -User Profile, Membership and More
3-Ultimate Member
4-Profile Builder -User registration & user profile
5-Profile Press
6-RegistrationMagic.


== Demo Videos ==

Please check the demo videos

[2018-06-25] Plugin Demo.

[youtube https://youtu.be/hvDkuZowZfM]

[2018-06-25] Whats New in the Version 1.0.2 ?

[youtube https://youtu.be/VzrnXY6i-J8]

[2018-06-25] How to use the auth key from MSG91 | OTP Route & Transactional Route.

[youtube https://youtu.be/od7f82A7RMw]

[2018-06-25] How to use the Plugin with Contact Form 7.

[youtube https://youtu.be/xkafUWOaIL8]

[2018-06-25] How to use the Plugin with Ultimate Member Plugin.

[youtube https://youtu.be/3EX1p05pEv0]

[2018-06-25] How to use the Plugin with User Registration Plugin.

[youtube https://youtu.be/8G8Vq0tadoE]

[2018-06-25] How to use the Plugin with Registration Magic Plugin.

[youtube https://youtu.be/P7zHEEZyqlg]

[2018-06-25] How to use the Plugin with Profile Press Plugin.

[youtube https://youtu.be/ppsnfUQuFDM]

[2018-06-25] How to use the Plugin with Profile Builder Plugin.

[youtube https://youtu.be/gDh8oP-zoBA]


== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Go to WordPress admin Dashboard under Orion OTP Menu and fill the required fields. Watch demo video for better explanation.
4. Create the auth key for MSG91 Api. Please watch the demo video how to do that. Please find the link to create auth key in the demo video description.
== Frequently Asked Questions ==

= Its not working.

Step 1. Check if your Plugin is activated.
Step 2. Test it with WordPress default plugin Twentyseventeen. Deactivate all plugins and reactivate the plugin.
Step 3. Go through all the tutorials and check if you have entered all required inputs correctly.
Step 4. Ensure there you have used dot/hash before selectors where applicable and there are no unnecessary spaces in the input field.
Step 5. Check Auth Key and the type of route, this plugin only works with OTP Route, if you have bought the transactional route credit then you need to buy the premium version on https://imransayed.com/orion
Step 6. If you want multiple international countries feature you need to buy the pro version on https://imransayed.com/orion.
Step 7. If for some reason you want to switch back to the older version of the plugin you can download it on https://imransayed.com/orion/download-prev-versions/
Step 8. Check more on FAQ's on https://imransayed.com/orion/faq/

== Screenshots ==

1-Backend Settings. screenshot-2.png
2-Use OTP verification during user registration with your own pre-existing form. screenshot-2.png
3-You get send OTP button in your existing form. screenshot-3.png
4-Get a success message when OTP is successfully verified. screenshot-4.png
5-You can get a new OTP on user's mobile and which resets the password to the new one. screenshot-5.png