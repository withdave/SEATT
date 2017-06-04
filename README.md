# SEATT
Simple Event Attendance Plugin for Wordpress - https://wordpress.org/plugins/simple-event-attendance/

Updates will be posted on my blog - https://withdave.com/category/seatt/

This repo will hold the latest development versions, which will then be pushed to WP once complete and tested.


## Requests:
- Add categories to event list shortcode to allow selection of which types of events are displayed
- Repeatable/recurring events – Some sort of functionality to allow repeatable events – whether this be decoupling of event details from dates, or some other mechanism.
- Register for events without requiring an account – I’m currently planning to do this via email confirmation and with a captcha, but need to test it.
- Email notification – More broad email notification, both upon registration (to user and admin), and also allowing admin to email users.
- Custom list pages and fields – Allow admins to change what information the plugin lists, and where it draws usernames and names from.
- Additional columns in database to capture event details.
- Internationalisation, and custom locale options – This includes the option to allow the user to call an “Event” a “Ride” or similar.
- Custom redirect to put user back at entry page after login.
- Capturing of timestamp when user registers for event (logging).
- Update list page to give the flexibility to add category filters.

### Requests fulfilled in 1.5.0
- Event calendar shortcode and layout – Allow you to group events into categories and display all relevant events in a list view on a post.
- Allow other users to see comments on short-code form.

### Requests fulfilled in 1.4.0
- Allow admin to use tinymce content editor.
- Made compatible with PHP 5.6+
