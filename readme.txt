=== Event Tickets HubSpot Integration ===
Contributors: theeventscalendar, brianjessee
Donate link: https://evnt.is/29
Tags: events, calendar
Requires at least: 5.8.6
Stable tag: 1.0.3
Tested up to: 6.1.1
Requires PHP: 7.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

[Extension Description]

== Description ==

Updates to a user's attendee meta or RSVP status will be synced with HubSpot.

When a user is checked into an event, that event will sync to the HubSpot contact and timeline.

== Installation ==

Install and activate like any other plugin!

* You can upload the plugin zip file via the *Plugins ‣ Add New* screen
* You can unzip the plugin and then upload to your plugin directory (typically _wp-content/plugins_) via FTP
* Once it has been installed or uploaded, simply visit the main plugin list and activate it

== Frequently Asked Questions ==

= Where can I find more extensions? =

Please visit our [extension library](https://theeventscalendar.com/extensions/) to learn about our complete range of extensions for The Events Calendar and its associated plugins.

= What if I experience problems? =

We're always interested in your feedback and our [Help Desk](https://support.theeventscalendar.com/) are the best place to flag any issues. Do note, however, that the degree of support we provide for extensions like this one tends to be very limited.

== Changelog ==

= [1.0.3] 2023-01-04 =

* Fix - Modify the redirect url for app authorization to support the latest standards from Hubspot. [EXT-283]

= [1.0.2] 2022-05-23 =

* Tweak - Utilize new settings class to link to the integration tab for authorization, disconnect, and notices. [EXT-283]

= [1.0.1] 2022-03-15 =

* Fix - Prevent authorization error by changing to the crm.schemas.contacts.read and crm.schemas.contacts.write scopes. [EXT-246]
* Tweak - Update code to get organizer names following update ons The Events Calendar. [EXT-246]

= [1.0.0] 2019-12-17 =

* Initial release
