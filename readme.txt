=== Plugin Name ===
Plugin Name: IFS Simple Mailing List Mailer
Contributors: guusifs
Donate link: http://www.inspiration-for-success.com/pay/
Tags: mailer, mailing list, simple mailer, mass mailer, mass mail
Requires at least: 3.0
Tested up to: 5.4.1
Version: 1.72
Stable tag: 1.71
License: GPLv2

Use this plugin to maintain a simple mass mailing list and send simple html e-mails to the people in the list.

== Description ==

With this plugin you can maintain a simple mailing list and send html e-mails to people in this list straight from your own server.

People can sign up to your list and a confirmation e-mail is supported for that.

The plugin supports 'batches' so if your hosting provider limits the number of e-mails you can send in a certain amount of time you can split your sending up.

Users can subscribe from the front-end. Just add the string [ifsmailersignupbox] in any post or a div with id "frontendaddemail" in any post or theme area: &lt;div id="frontendaddemail"&gt;&lt;/div&gt; to add the subscription form.

A confirmation e-mail is being sent to the person signing up to verify the e-mail address.

Users can unsubscribe with the the unsubscribe feature.

== Installation ==

The IFS mailinglist plugin doesn't require any specific installation. Just unpack the zip file, upload the files to the plugins folder and activate the plugin.

Easiest way to include the signup box is to include it with the string [ifsmailersignupbox] in the content. For advanced use like in your theme just add a div with id "frontendaddemail" in your theme.

For sending e-mails just set the configuration options first like your e-mail address that will be used as the 'To' and 'From' addresses (E-mails are sent with the BCC option).

Technical: you can create a constant _DO_NOT_USE_WP_MAIL to use the standard php mail function to get bounced messages return. This may not work on all hosting servers.

== Frequently Asked Questions ==

= How do I include the e-mail box for signup in a post =
 
Just add the text [ifsmailersignupbox] anywhere in the text of your post.

= How many e-mail addresses can I maintain? =

The number of people in the e-mail list is unlimited.

= What is a batch =

Batches are intended to bypass the limit of the number of mails you can send per hour through your (shared) hosting account.
A batch is nothing more or less than a grouping for the people in your mailing list.
Of course you could use this functionality in different ways, but the origin was just to bypass the server limit of maximum number of e-mails per hour.

= Can users easily unsubscribe? =
Users can easily unsubscribe by using the unsubscribe feature. In order to use this you need to configure the plugin to send individual e-mails. After that you can use [unsubscribe] or [Unsubscribe] or [unsubscribelink] to add an unsubscribe link in your message.

== Screenshots ==

1. This is how the screen should look like just before sending an e-mail to your recipients. The 'subject' field should hold the subject for your e-mail. The message should hold the message in html. You can also just put plain text. With clicking on the color selectors you can select foreground and background colors.

2. This is how the configuration screen look like just after updating.

== Upgrade notice ==

No special requirements for upgrades.

== Changelog ==

= 1.8 =
* Development version

= 1.72 =
* Upgraded to latest version of 'mini-lib'.
* Updated working up to Wordpress 5.4.1.

= 1.71 =
* Fixed a nasty bug related to mySQL maximum key size in InnoDB on installation.

= 1.7 =
* Changed the location of the batch selection.
* Updated working up to Wordpress 4.7.3

= 1.61 =
* Fixed a weird issue with mysql_real_escape_string not working anymore in version 4.5.2 (and maybe one earlier version) in the IFS version storing the daily quotes. Also affects fetching for e-mail probably in all versions.
* Updated working up to Wordpress 4.5.2

= 1.6 =
* Made back-end more adaptive, especially for IFS version.
* Fixed bug 'From address' when using wp_mail().

= 1.53 =
* No change. Just updated 'tested up to version'.

= 1.52 =
* No change. Just updated 'tested up to version'.

= 1.51 =
* Some minor textual changes.

= 1.5 =
* Included options for unsubscribe links: [unsubscribe], [Unsubscribe] and [unsubscribelink].
* Options for quote based on the constant _IS_IFS. _IS_IFS is a constant related to the IFS website and not supported for use by third parties. It is mainly used for sending quotes in an easy way.

= 1.4 =
* Solved sending duplicate e-mails when _DO_NOT_USE_WP_MAIL was set.

= 1.31 =
* Included firstname support with [first-name] in message.
* Prepared for e-mail bouncing support

= 1.3 =
* Included option to send individual e-mails instead of BCC as was the only option until now.
* Fixed minor error with checkbox in configuration screen.
* Added bounce address for own IFS version.

= 1.2 =
* Include signup with [ifsmailersignupbox] string instead of a div.
* Improved the color chooser.
* Added option for wysiwyg editing.

= 1.1 =
* Added selection of color and background color with color picker.
* Some minor improvements in the description, lay-out and text

= 1.0 =
* just made ready for publishing on Wordpress
* Fixed some minor issues

= 0.43 =
* Enhanced e-mail functionality and more option fixes
* Initial version without mailer

= 0.42 = 
*  Fixed initialization issue with default batch

= 0.41 = 
*  Fixed naming issue= 0.41 = 

= 0.4 =
* Added functionality for subscription in front-end including confirmation e-mail.
* Added configuration option for default batch. Batch can also be any string, not only numeric.
* E-mails are sent to people with status 'active'.

= 0.3 =
* Added configuration options for sender name and sender address.

= 0.2 =
* Added mailer option.


