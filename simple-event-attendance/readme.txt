=== Plugin Name ===
Contributors: sourcez
Donate link: http://www.3cc.org/
Tags: events, attendance list, attendance, event management, sign-up, registration
Requires at least: 3
Tested up to: 4.3.1
Stable tag: 1.3.0

Simple attendance list, multiple lists can be added to any post or page and subscribed members can be edited through the admin panel.

== Description ==

Simple attendance list, multiple lists can be added to any post or page and subscribed members can be edited.

Add an event in the admin panel with a name, description, closing date (for signing up) and registration limit (for number of users who can signup). You can then embed a form into any post or page using the wordpress shorttag [seatt-form event_id=x] (x = the event id).

From the admin panel you can read comments left by people who have registered, as well as boot them off the list or delete the list altogether.

Please note you have to allow user registration for this plugin to function. If you allow non-registered users access you open the form up to spam and also numerous issues when people want to change their status, enter incorrect details, or submit all their friends. As a result the plugin will remain registration-only for the time being.

Uninstalling removes all traces of plugin, including from the database. This means any events you have will be lost. Updates will not affect existing events nor their attendees.

Comments are always welcome, it's through feedback that we improve.

== Installation ==

Simple to install, automatically adds relevant information to the database.

1. Upload `simple-events-attendance` directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Set up an event in the admin panel (link on the sidebar)
4. Place `[seatt-form event_id=x]` (where x = the event id) in your pages/posts.
5. If you haven't already, enable "Anyone can register" in Settings>General.

== Frequently Asked Questions ==

Q: Why can't we add un-registered users?
A: This has been left out due to the large number of signups we experienced when testing from spam bots. It can be done quite easily, but it will require putting a captcha into the code for most use cases.

== Screenshots ==

1. screenshot-1.png shows the admin panel event browser
2. screenshot-2.png shows the admin panel event add page, this has a start and finish time from v1.2
3. screenshot-3.png shows the admin panel event edit page, this has a start and finish time from v1.2
4. screenshot-4.png shows a post with the signup form, this has a start and finish time from v1.2
5. screenshot-5.png shows a post with the signup form after registering, this has a start and finish time from v1.2

== Changelog ==

= 1.3.0 = 
Updated all SQL queries to use wpdb->prepare for additional security, and updated security as per feedback from J.D. Grimes and Ipstensu to remove a SQL injection risk. Also fixed a template issue when the register form is shown in new wordpress templates, and tested compatibility with 4.3.1. Fixed an issue where character encoding would display comments incorrectly on the user-side. Apologies for the delay in updating this plugin.

= 1.2.7 = 
Updated version numbers, fixed problems with apostrophes being escaped with numerous backspaces in admin panel and in the comment box. Removed first+last name from page template as this is rarely used, with list users no longer in a table format, but now in an ordered list. Admins can now signup registered users simply by supplying a username in the admin panel. Fixed problems with wp_prepare() causing errors in wordpress 3.5. Deleting the plugin now removes all database tables.

= 1.2.6 = 
Updated incorrect link in seatt_header.php and version number.

= 1.2.5 = 
Update version numbers, change time() for current_time() to correct offsets in control panel in all files. Also added expire date to summary table in admin. Added a list of signed up user emails to allow copying and emailing of everyone at once. Also added register and login links to the form for guest users.

= 1.2.4 =
Update version number in simple-events-attendance.php and change line 15 in seatt_events_include.php.

= 1.2.3 =
Version 1.2.2 did not upload correctly, so this patches simple-events-attendance.php and seatt_events_include.php (the latter just to solve errors when debugging is enabled).

= 1.2.2 =
Fixed error with adding new events, for new plugin users. Also fixed reference to undeclared variables and changed menu layout. Database updated to v1.1.1 to solve new installation problems.

= 1.2.1 =
Fixed error with adding new events.

= 1.2 =
Updated the layout so the form is clearer on pages, have added a link to remove ALL participants from an event at once (so you don't have to click through every one), have added an open and close date for registration rather than the basic hour system there was before, and have updated the database to allow reserved places in the near future. Also fixed broken link to project page.

= 1.1 =
Fixed errors with multiple forms on the same page (thanks to mhobach) and form now displays where you post it on the page rather than at the bottom.

= 1.0 =
* Initial Release Version

== Upgrade Notice ==

= 1.3.0 =
Changes in all files (see changelog). Recommend upgrading to improve security and patch possible SQL injection by users with author+ accounts.

= 1.2.7 =
Changes in files & database (see changelog).

= 1.2.6 =
Changes in files (see changelog).

= 1.2.5 =
Changes in files (see changelog).

= 1.2.4 =
Changes in files (see changelog).

= 1.2.3 =
Changes in files (see changelog).

= 1.2.2 =
Changes in all files & database for fresh installations, to v1.1.1.

= 1.2.1 =
Changes in events-add and readme.txt

= 1.2 =
Changes in all files, plus addition of two new database fields (to db version 1.1) on seatt_events called event_start and event_reserve for the starting timestamp and number of reserves respectively. You will need to check all your events after updating to ensure they have a start date.

= 1.1 =
Changes in simple-events-attendance.php, seatt_events_include.php and readme.txt