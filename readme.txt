=== Monitor Logins ===
Contributors: mtekk
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=FD5XEU783BR8U&lc=US&item_name=Monitor%20Logins%20Donation&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
Tags: login, security, notification
Requires at least: 3.5
Tested up to: 3.6
Stable tag: 0.2.0
License: GPLv2 or later
Notifies users of failed login attempts to their account, and successful login attempts by previously unseen devices.

== Description ==

Monitor login allows you to monitor login attempts to both real and non-existent user accounts on your WordPress site. Attempts to login to non-existent accounts sends alerts to the admin email for the WordPress install. Failed attempts to login to user accounts cause email notices to be sent to that user. That is, if they have notifications enabled. Notifications are enabled on a per user basis, and are off by default.

Additionally, Monitor Logins will remember devices used to login with, should a device be "new" upon successful login a notice will be sent to the user. Devices are forgotten if they have not been seen for a few months.

= Translations =

Monitor Logins distributes with translations for the following languages:

* English - default -

Don't see your language on the list? Stop by [Monitor Login's translation project](http://translate.mtekk.us/projects/monitor-login "Go to Monitor Login's GlotPress based translation project").

== Installation ==


1. Install Monitor Logins either via the WordPress.org plugin directory, or by uploading the files to your server
1. Activate Monitor Logins
1. That's it. You're ready to go!

Please visit [Monitor Login's](http://mtekk.us/code/monior-logins/#installation "Go to Monitor Login's project page's installation section.") project page for usage instructions.

== Changelog ==

= 0.2.0 =
* New feature: Added option to turn off monitoring of non-existent accounts.
= 0.1.0 =
* Initial release