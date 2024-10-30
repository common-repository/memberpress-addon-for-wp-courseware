=== WP Courseware for MemberPress ===
Contributors: flyplugins
Donate link: https://flyplugins.com/donate
Tags: learning management system, selling online courses
Requires at least: 4.9
Tested up to: 6.4.1
Stable tag: 2.0
Requires PHP: 5.2.4
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

This plugin adds integration between MemberPress and WP Courseware which allows you to associate course(s) to products for automatic enrollment.

== Description ==
[Fly Plugins](https://flyplugins.com) presents [WP Courseware](https://flyplugins.com/wp-courseware) for [MemberPress](https://flyplugins.com/memberpress).

= Would you like to sell an online course with MemberPress? =
The MemberPress Addon for WP Courseware will add full integration with WP Courseware. Simply assign WP Courseware course(s) to a MemberPress product. When a student purchases the product, they will automatically be enrolled into the associated course(s).

With this addon, you will be able to create a fully automated [Learning Management System](https://flyplugins.com/wp-courseware) and sell online courses.

= MemberPress Plugin Integration with WP Courseware Plugin =
[youtube https://www.youtube.com/watch?v=fKyF0PC4-ro]

= Basic Configuration Steps =
1. Create a course with WP Courseware and add module(s), unit(s), and quiz(zes)
2. Create a course outline page using [shortcode]
3. Create a product and set a price
4. Associate one or more WP Courseware courses with the product
5. New student pays for the product, and WP Courseware enrolls them to the appropriate course(s) based on the purchased product

= Check out Fly Plugins =
For more tools and resources for selling online courses check out:

* [WP Courseware](https://flyplugins.com/wp-courseware/) - The leading learning management system for WordPress. Create and sell online courses with a drag and drop interface. It’s that easy!
* [S3 Media Maestro](https://flyplugins.com/s3-media-maestro) - The most secure HTML 5 media player plugin for WordPress with full AWS (Amazon Web Services) S3 and CloudFront integration.

= Follow Fly Plugins =
* [Facebook](https://facebook.com/flyplugins)
* [YouTube](https://www.youtube.com/flyplugins)
* [Twitter](https://twitter.com/flyplugins)
* [Instagram](https://www.instagram.com/flyplugins/)
* [LinkedIn](https://www.linkedin.com/company/flyplugins)

= Disclaimer =
This plugin is only the integration, or “middle-man” between WP Courseware and MemberPress.

== Installation ==

1. Upload the `MemberPress for WP Courseware addon` folder into the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently asked questions ==

= Does this plugin require WP Courseware to already be installed =

Yes!

= Does this plugin require MemberPress to already be installed =

Yes!

= Where can I get WP Courseware? =

[WP Courseware](https://flyplugins.com/wp-courseware).

= Where can I get MemberPress? =

[MemberPress](https://flyplugins.com/memberpress).

== Screenshots ==

1. The Course Access Settings screen will display the courses associated with products

2. This is the screen where specific courses are selected to be associated with the product. The retroactive function will enroll students to courses that were recently associated to the product.

== Changelog ==

= 2.0 =
* New: Added ability to retoactively enroll a large quantity of students.
* New: Added new filter wpcw_mp_addon_can_load to allow enrollment method to be changed to "add" instead of "sync"

= 1.4.2 =
* Fix: Fixed issue where course list only displaying 20 courses

= 1.4.1 =
* Fix: Fixed issue with retroactive enrollment for users with subscriptions.

= 1.4.0 =
* New: Courses menu item added to the MemberPress account menu.
* New: Students will be de-enrolled from course if MemberPress membership expires.

= 1.3.0 =
* Fix: Fixed enrollment function to obtain the user object whether or not the user is logged in per MemberPress's recommendation.

= 1.2.0 =
* Fix: Fixed bug in which course ID's associated with products were not being passed into the enrollment function. Also, User ID data was incorrect.

= 1.1.0 =
* New: Added the ability to retroactively enroll students to a course when adding a new course to an existing product
* New: Now hooking into "mepr-subscr-store" to enroll students into courses

= 1.0.0 =
* Initial release


== Upgrade notice ==