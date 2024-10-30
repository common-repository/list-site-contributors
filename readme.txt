=== List Site Contributors ===
Contributors: mallsop
Donate link: http://www.mallsop.com/downloads.html
Tags: contributors, authors, plugins
Requires at least: 3.1
Tested up to: 4.3
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

List site contributors and authors. 

== Description ==

List site contributors allows you list your site's contributors and authors. 
Just add the shortcode [listsitecontributors] to a page on your blog. 
Optionally you can use the User Photo Plugin (http://wordpress.org/extend/plugins/user-photo/) for images,  
otherwise Avatars will be used.

== Installation ==

1. Extract and upload this directory to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.

== Frequently Asked Questions ==

= What does this do? =

This shows a listing of active contributors and authors with images with an A-Z directory and last name search.

= What if I do not have the User Photo Plugin? =

This will use the User Photo Plugin image, if installed. Otherwise, the Wordpress Avatar image will be used. 

= Where are the options? =

Option: Yes/No for showing the web link url for the listed authors/contributors.
Option: Number of characters to limit the authors and contributors descriptions on the search results. Default is 150 chars.

= Any other notes? =

If the userphoto plugin does not place the photo correctly on the page, trying changing the `echo` to `return` in the function userphoto and function userphoto_thumbnail.
If you are logged in as admin, you will see the full descriptions of authors and contributors.
This allows search plugins like Relevanssi to get all that information. Logout to see the normal user view. 
Also note that some security plugins can break this plugin due to author id removal.
And remember to backup any style sheet changes you make, before upgrading this plugin.

= I have a problem with your plugin or would like to suggest a change / new feature. How? =

Try the wordpress plugin forum first. Otherwise email me using my mallsop dot com contact form.

== Upgrade Notice ==

None.

== Screenshots ==

1. screenshot-1.jpg
2. screenshot-2.jpg

== Changelog ==

= 1.0.0 =
* First version: Tested ok. Fix for use with Relevanssi plugin.
= 1.1.0 =
* Added option: Added option to specify the number of characters in the search results description. 
= 1.1.1 =
* Minor fix: Max characters get error fix. 
= 1.1.2 =
* Added option: Include editors and admins (all active user roles except subscribers).
= 1.1.3 =
* Modification: Do not show a link to user posts if no posts found. Show `Not listed` if user has no website link.
= 1.1.4 =
* Modification: Option to suppress authors and contributors with zero posts.
= 1.1.5 =
* Modification: Cleanup options php illegal type warnings.
= 1.1.6 =
* Modification: You can now exclude one user by entering the email of that user in the exclude option.
= 1.1.7 =
* Modification: Added line break option to make the user description look nice.
= 1.1.8 =
* Modification: Option to use normal images instead of thumbnails (if the userphoto plugin is used).

