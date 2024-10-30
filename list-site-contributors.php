<?php
/*
Plugin Name: List Site Contributors
Plugin URI: http://www.mallsop.com/plugins
Description: List site contributors and authors - Shortcode: [listsitecontributors].
Version: 1.1.8
Author: mallsop 
Author URI: http://www.mallsop.com
License: GPL2
*/

/*  Copyright 2011  mallsop.com  (support at mallsop dot com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
 
if ( function_exists('add_action') ) {
	// Pre-2.6 compatibility
	if ( !defined( 'WP_CONTENT_URL' ) )
		define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
	if ( !defined( 'WP_CONTENT_DIR' ) )
		define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
	if ( !defined( 'WP_PLUGIN_URL' ) )
		define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
	if ( !defined( 'WP_PLUGIN_DIR' ) )
		define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );

	// plugin definitions - needed?
	define( 'FB_ADMINIMIZE_BASENAME', plugin_basename( __FILE__ ) );
	define( 'FB_ADMINIMIZE_BASEFOLDER', plugin_basename( dirname( __FILE__ ) ) );
	define( 'FB_ADMINIMIZE_TEXTDOMAIN', 'adminimize' );
}

// Hook for adding admin menus
add_action('admin_menu', 'list_site_contributors');

// Hook into the head
add_action('wp_head', 'list_site_contributors_head');

// Add the shortcode "listsitecontributors"
add_shortcode( 'listsitecontributors', 'list_site_contributors_display' );

// load defaults for each option
$opt = array();
$opt['lsc_show_user_url'] = 0;
add_option("lsctrib", $opt);
$opt['lsc_show_user_desc_max_chars'] = 150; // 01-10-2013
add_option("lsctribmax", $opt); // 01-10-2013
$opt['lsc_show_user_all_roles'] = 0; // 01-24-2013
add_option("lsctriball", $opt); // 01-24-2013
$opt['lsctribexc'] = ""; // 02-15-2014
add_option("lsctribexc", $lsctribexc); // 02-15-2014
$opt['lsc_add_line_breaks'] = 0; // 03-21-2015
add_option("lsctriblb", $opt); // 03-21-2015
$opt['lsc_use_normal_image_size'] = 0; // 12-03-2015 for userphoto
add_option("lsctribimg", $opt); // 12-03-2015

// Add admin menus
function list_site_contributors() {
    add_menu_page('List Site Contributors', 'LSC', 'administrator', 'list-site-contributors', 'list_site_contributors_main_page');
    add_submenu_page('list-site-contributors', 'Options', 'Options', 'administrator', 'list-site-contributors-sub', 'list_site_contributors_sub_page');
    //add_submenu_page('list-site-contributors', 'Options',   'Options',   'administrator', 'list-site-contributors-sub',  'list_site_contributors_sub_page');   
}

function list_site_contributors_main_page() { 
	global $wpdb;
	echo '<div class="wrap">';
	echo '<h2>List Site Contributors</h2>';
	echo '<p>Shortcode: &#91;listsitecontributors&#93; - Place this in a page.</p>'; //  - Optional: User Photo Plugin.
	echo '<p>Displays a listing of active authors and contributors with images. It has an A-Z directory and last name search.</p>';
	echo '<p>See the Options below. Be sure to set the Biographical Info and Display Name for each user.</p>';
	echo '<p>Remember to logout to see the normal user view and paged data. See the readme.txt.</p>'; // 10-14-2013
	echo '<p>More <a href="http://wordpress.org/plugins/list-site-contributors/">plugin</a> information at: <a href="http://www.mallsop.com/plugins">mallsop.com</a></p>';
	echo '<p>Visit <a href="http://codex.wordpress.org/Roles_and_Capabilities">this</a> link for role information.</p>';
	echo '</div>';
}

// options - yeah i did it my way.
function list_site_contributors_sub_page() { 
	global $wpdb;
  // ? 01-10-2013 $lsctrib_table   = $wpdb->prefix . "lsctrib";  
  if(!isset($wpdb->lsctrib)){ $wpdb->lsctrib = $wpdb->prefix . 'lsctrib'; }
  if(!isset($wpdb->lsctribmax)){ $wpdb->lsctribmax = $wpdb->prefix . 'lsctribmax'; } // 01-10-2013
  if(!isset($wpdb->lsctriball)){ $wpdb->lsctriball = $wpdb->prefix . 'lsctriball'; } // 01-24-2013  
	//echo 'No other options.';
	
	$message = " ";
	if (!current_user_can('manage_options'))
		wp_die(__('Sorry, but you have no permissions to change settings.'));
		
	// save data
	if ( isset($_POST['lsc_save']) ) {
		$message = "Saved.";
			
		$lsc_show_user_url = ( isset($_POST['lsc_show_user_url']) ) ? 1 : 0;			
		//$message .= "debug show user url=[".$lsc_show_user_url."] ";	
		update_option('lsctrib', $lsc_show_user_url);
		
		// 01-10-2013 lsc_show_user_desc_max_chars added
		$lsc_show_user_desc_max_chars = (isset($_POST['lsc_show_user_desc_max_chars']) ) ? $_POST['lsc_show_user_desc_max_chars'] : 150;
		if (!is_numeric($lsc_show_user_desc_max_chars)) {
			$lsc_show_user_desc_max_chars = 150;
			}
		//$message .= "debug max desc chars=[".$lsc_show_user_desc_max_chars."] ";
		update_option('lsctribmax', $lsc_show_user_desc_max_chars);		
		
		$lsc_show_user_all_roles = ( isset($_POST['lsc_show_user_all_roles']) ) ? 1 : 0; // 01-24-2013
		//$message .= "debug show all roles=[".$lsc_show_user_all_roles."] ";
		update_option('lsctriball', $lsc_show_user_all_roles); // 01-24-2013
		
		$lsc_suppress_users_zero_posts = ( isset($_POST['lsc_suppress_users_zero_posts']) ) ? 1 : 0; // 01-31-2013
		//$message .= "debug suppress author or contributor with zero posts=[".$lsc_suppress_users_zero_posts."] ";
		update_option('lsctribsupp', $lsc_suppress_users_zero_posts); // 01-31-2013
	
		//$message .= "debug exclude users=[".$lsc_exclude_users."] ";	
		$lsc_exclude_users = (isset($_POST['lsc_exclude_users']) ) ? $_POST['lsc_exclude_users'] : "Skip";
		if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$lsc_exclude_users)) { $lsc_exclude_users = "Skip"; } // 02-15-2014	
		update_option('lsctribexc', $lsc_exclude_users);	// 02-15-2014
		
		$lsc_add_line_breaks = ( isset($_POST['lsc_add_line_breaks']) ) ? 1 : 0; // 03-21-2015
		//$message .= "debug add user desc line breaks =[".$lsc_add_line_breaks."] ";
		update_option('lsctriblb', $lsc_add_line_breaks); // 03-21-2015

		$lsc_use_normal_image_size = ( isset($_POST['lsc_use_normal_image_size']) ) ? 1 : 0; // 12-03-2015 for userphoto
		//$message .= "debug use normal image size=[".$lsc_use_normal_image_size."] ";
		update_option('lsctribimg', $lsc_use_normal_image_size); // 12-03-2015

		}
	else {	
		$opt  = get_option('lsctrib');
		if (isset($opt)) {	// 10-14-2013
			$lsc_show_user_url = $opt;	
			//$message .= "Get option url= ".$lsc_show_user_url.". ";	
			}		
		else { // 10-14-2013
			//$message .= "Get option url NOT SET. ";	
			$lsc_show_user_url = 1; // default yes
			}		
				
		$opt = get_option('lsctribmax'); // 1-10-2013								
		if (isset($opt)) { // 10-14-2013
			$lsc_show_user_desc_max_chars  = $opt; // 1-10-2013					
			//$message .= " Get option max chars= ".$lsc_show_user_desc_max_chars.". "; // 01-10-2013		
			if (!is_numeric($lsc_show_user_desc_max_chars)) {
				$lsc_show_user_desc_max_chars = 150;
				}
			}
		else { // 10-14-2013
			//$message .= "Get option max chars NOT SET. ";	
			$lsc_show_user_desc_max_chars = 150; // default show 150 chars 
			}
		
		$opt = get_option('lsctriball'); // 01-24-2013
		if (isset($opt)) {	// 10-14-2013
			$lsc_show_user_all_roles = $opt;	// 01-24-2013
			//$message .= "Get option show all active roles except subscriber = ".$lsc_show_user_url.". ";	
			}
		else { // 10-14-2013
			//$message .= "Get option show all active roles except subscriber NOT SET. ";	
			$lsc_show_user_all_roles = 1; // default yes
			}		
		
		$opt = get_option('lsctribsupp'); // 01-31-2013
		if (isset($opt)) {	// 10-14-2013
			$lsc_suppress_users_zero_posts = $opt;	// 01-31-2013
			//$message .= "Get option suppress author or contributor with zero posts = ".$lsc_suppress_users_zero_posts.". ";	
			}
		else { // 10-14-2013
			//$message .= "Get option suppress author or contributor with zero posts NOT SET. ";	
			$lsc_suppress_users_zero_posts = 0; // default no
			}
		}	
						
		$opt = get_option('lsctribexc'); // 02-15-2014				
		if (isset($opt)) { // 02-15-2014			
			$lsc_exclude_users  = $opt; // 02-15-2014						
			}	
		
		$opt = get_option('lsctriblb'); // 03-21-2015
		if (isset($opt)) {	// 10-14-2013
			$lsc_add_line_breaks = $opt;	// 03-21-2015
			//$message .= "Get option add line breaks to user desc  = ".$lsc_add_line_breaks.". ";	
			}
		else { // 10-14-2013
			//$message .= "Get option add line breaks to user desc NOT SET. ";	
			$lsc_add_line_breaks = 0; // default no
			}		
			
		$opt = get_option('lsctribimg'); // 12-03-2015 for userphoto
		if (isset($opt)) {	// 10-14-2013
			$lsc_use_normal_image_size = $opt;	// 12-03-2015
			//$message .= "Get option use normal image size = ".$lsc_show_user_url.". ";	
			}
		else { // 10-14-2013
			//$message .= "Get option use normal image size NOT SET. ";	
			$lsc_use_normal_image_size = 0; // default no
			}			
			
	// show page
	echo "<div class=\"wrap\">";
	echo "<h2>Options</h2>\n";	
	echo "<div id='message' class='updated'>".$message."</div>\n"; // 10-14-2013
	echo "<form method=\"post\" action=\"";
	echo $_SERVER['REQUEST_URI'];
	echo "\" id='lsctrib'>\n";
  echo "<table class=\"form-table\">\n";
	echo "<tr><th>Show author/contributor url?</th>\n";
 	echo "<td><input name=\"lsc_show_user_url\" id=\"lsc_show_user_url\" type=\"checkbox\"";
 	if ($lsc_show_user_url==1) { echo 'checked="checked"'; }
 	echo " /></td>\n";
	echo "</tr>";
	echo "<tr><th>Number of characters to show in the search results description?</th>\n"; 
 	echo "<td><input name=\"lsc_show_user_desc_max_chars\" id=\"lsc_show_user_desc_max_chars\" type=\"text\" value=\"";
 	echo $lsc_show_user_desc_max_chars."\" size=\"3\" maxlength=\"3\"> (Default is 150)";
 	echo "</td>\n";
	echo "</tr>";
	echo "</tr>";
	echo "<tr><th>Nice line breaks in the user description?</th>\n"; // 03-21-2015
 	echo "<td><input name=\"lsc_add_line_breaks\" id=\"lsc_add_line_breaks\" type=\"checkbox\"";
 	if ($lsc_add_line_breaks==1) { echo 'checked="checked"'; } 	
 	echo " /></td>\n";
	echo "</tr>";	
	echo "<tr><th>Include editors and administrators?</th>\n"; 
 	echo "<td><input name=\"lsc_show_user_all_roles\" id=\"lsc_show_user_all_roles\" type=\"checkbox\"";
 	if ($lsc_show_user_all_roles==1) { echo 'checked="checked"'; } 	
 	echo " /></td>\n";
	echo "</tr>";
	echo "<tr><th>Suppress author/contributor with zero posts?</th>\n"; 
 	echo "<td><input name=\"lsc_suppress_users_zero_posts\" id=\"lsc_suppress_users_zero_posts\" type=\"checkbox\"";
 	if ($lsc_suppress_users_zero_posts==1) { echo 'checked="checked"'; } 	
 	echo " /></td>\n";
	echo "</tr>";
	echo "<tr><th>Exclude the user with this email.</th>\n"; 
 	echo "<td><input name=\"lsc_exclude_users\" id=\"lsc_exclude_users\" type=\"text\" value=\"";
 	echo $lsc_exclude_users."\" size=\"50\" maxlength=\"255\"> Optional";
 	echo "</td>\n";
	echo "</tr>";
	echo "<tr><th>Show normal size userphoto? (if userphoto plugin is used)</th>\n";
 	echo "<td><input name=\"lsc_use_normal_image_size\" id=\"lsc_use_normal_image_size\" type=\"checkbox\"";
 	if ($lsc_use_normal_image_size==1) { echo 'checked="checked"'; }
 	echo " /> (No thumbnails)</td>\n";
	echo "</tr>";
	echo "</table>\n";
	echo "<p class=\"submit\">\n";
	echo "<input name=\"lsc_save\" class=\"button-primary\" value=\"Save Changes\" type=\"submit\" />";
	echo "</p>";
	echo "<input type=\"hidden\" name=\"lsctrib\" value=\"SET\" />";
	echo "</form>\n";
	echo "</div>\n";
	// end options
	}

// Insert into head tags 
function list_site_contributors_head() {
	echo "\n\n"; 
	echo '<!-- List Site Contributors Scripts - Start -->';
	echo "\n";
	echo '<link rel="stylesheet" href="'. WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) .'/css/list_site_contributors.css" type="text/css" media="screen"/>';
	echo "\n\n";
	echo '<!-- List Site Contributors End -->';	
	echo "\n\n";	
}

// Display List Site Contributors and Authors now.
function list_site_contributors_display() {
		global $wpdb;
		$content = "";				
		$page_limit = 26; // mysql - maximum number of records returned per page + 1 (for paging results)
		$page_show = 25; // mysql - maximum number of records shown per page(for paging results)
		$page_start = 0;  // page query start

		if (current_user_can('manage_options') ) { // 09-04-2011 ma for relevanssi
			$page_limit = 9999; 
			$page_show = 9998; // mysql - maximum number of records shown per page(for paging results)
			}
		if (isset($_GET['s'])) { // 09-13-2011 ma for search results page
			$page_limit = 9999; 
			$page_show = 9998; // mysql - maximum number of records shown per page(for paging results)
			}

		// Optional: User Photo Plugin - Else gravatar is used. fixed 12-03-2015
		$plugin_found = 0;
		//$check_for_user_photo_plugin = "".WP_PLUGIN_URL."/user-photo/user-photo.php";
		//if (file_exists($check_for_user_photo_plugin)) {
			//	$plugin_found = 1; // found user photo plugin
			//}
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		// check for plugin using plugin name
		if ( is_plugin_active( 'user-photo/user-photo.php' ) ) {
  		$plugin_found = 1; // found user photo plugin
			} 	

		if (isset($_GET["aid"])) { // one record details by id
			$lsc_add_line_breaks = get_option('lsctriblb'); // 03-21-2015
			$content .= "<span id=\"authorlist\">\n";
			$author_id = $_GET["aid"];
			if (is_numeric($author_id)) { // safety
					$content .= "<div id=\"authorinfo\">";
					$content .= "<div id=\"authorpiclarge\">\n";
   				// check if user photo plugin found 
   				if ($plugin_found > 0) {
	   				if (userphoto_exists($author_id)) {
							$content .= "".userphoto($author_id); // wp user photo else gets gravatar if no user photo found.
							}
						}
					else {
						$content .= "".get_avatar( $author_id, $size = '96', $default = '' ); 
						}
					$content .= "</div><b>";
					$content .= "".get_the_author_meta('display_name', $author_id);
					$content .= "</b><br />\n";
					$descx = "".get_the_author_meta('description', $author_id);	 // 03-21-2015			
					if ($lsc_add_line_breaks==1) {							 
						$content .= "&nbsp;<br />";
						$descx = preg_replace('/\./','.<br />&nbsp;<br />', $descx); 	// added line break option 03-21-2015									
						}
					$content .= $descx; // 03-21-2015		
					
					$opt  = get_option('lsctrib');
					$lsc_show_user_url = $opt;	// 10-14-2013 ['lsc_show_user_url']
					if ($lsc_show_user_url) {
						$user_url = get_the_author_meta('user_url', $author_id);
						if (trim($user_url) != "") { // 01-25-2013
							$content .= "<br /><b>Website:</b> <a href=\"";
							$content .= $user_url;					
							$content .= "/\" target='_blank'>";
							$content .= $user_url;
							$content .= "</a>";
							}
						else { // 01-24-2013
							$content .= "<br /><b>Website:</b> Not listed.";
							}
						}
					$num_posts = count_user_posts( $author_id );	// 01-25-2013
					if ($num_posts > 0) {
						$content .= "<br /><b>Read:</b><a href=\"".get_bloginfo('url')."/author/";
						$content .= "".get_the_author_meta('user_nicename', $author_id);
						//$content .= $author->user_nicename;
						$content .= "/\">&nbsp;";
						$content .= "".get_the_author_meta('display_name', $author_id);
						$content .= "`s articles</a>";
						}					
					else { // 01-25-2013
						$content .= "<br />";
						$content .= "".get_the_author_meta('display_name', $author_id);
						$content .= " has not posted any articles.";
						}
					$content .= "</div>\n";
					// etc.
					}
			else {
					$content .= "<p>Information is not available.</p>\n";
					}
			$content .= "</span><br />\n";
			}
		else {			
				// all contributors and authors listing, and partial desc
				//$mypage = $_SERVER["REQUEST_URI"]; // current page, example: "/meet-our-authors/"
				$mypage = $_SERVER["DOCUMENT_URI"];
				//$mypage = "http://www.yoursite.com/meet-our-authors/";				
				$content .= "<span class=\"alphabet_nav\">";
				$content .= "<a href=\"$mypage?alpha=All\">All</a>&nbsp;";
				$content .= "<a href=\"$mypage?alpha=A\">A</a>&nbsp;";
	      $content .= "<a href=\"$mypage?alpha=B\">B</a>&nbsp;";
	      $content .= "<a href=\"$mypage?alpha=C\">C</a>&nbsp;";
	      $content .= "<a href=\"$mypage?alpha=D\">D</a>&nbsp;";
	      $content .= "<a href=\"$mypage?alpha=E\">E</a>&nbsp;";
	      $content .= "<a href=\"$mypage?alpha=F\">F</a>&nbsp;";
	      $content .= "<a href=\"$mypage?alpha=G\">G</a>&nbsp;";
	      $content .= "<a href=\"$mypage?alpha=H\">H</a>&nbsp;";
	      $content .= "<a href=\"$mypage?alpha=I\">I</a>&nbsp;";
	      $content .= "<a href=\"$mypage?alpha=J\">J</a>&nbsp;";
	      $content .= "<a href=\"$mypage?alpha=K\">K</a>&nbsp;";
	      $content .= "<a href=\"$mypage?alpha=L\">L</a>&nbsp;";
	      $content .= "<a href=\"$mypage?alpha=M\">M</a>&nbsp;";
	      $content .= "<a href=\"$mypage?alpha=N\">N</a>&nbsp;";
	      $content .= "<a href=\"$mypage?alpha=O\">O</a>&nbsp;";
	      $content .= "<a href=\"$mypage?alpha=P\">P</a>&nbsp;";
	      $content .= "<a href=\"$mypage?alpha=Q\">Q</a>&nbsp;";
	      $content .= "<a href=\"$mypage?alpha=R\">R</a>&nbsp;";
	      $content .= "<a href=\"$mypage?alpha=S\">S</a>&nbsp;";
	      $content .= "<a href=\"$mypage?alpha=T\">T</a>&nbsp;";
	      $content .= "<a href=\"$mypage?alpha=U\">U</a>&nbsp;";
	      $content .= "<a href=\"$mypage?alpha=V\">V</a>&nbsp;";
	      $content .= "<a href=\"$mypage?alpha=W\">W</a>&nbsp;";
	      $content .= "<a href=\"$mypage?alpha=X\">X</a>&nbsp;";
	      $content .= "<a href=\"$mypage?alpha=Y\">Y</a>&nbsp;";
	      $content .= "<a href=\"$mypage?alpha=Z\">Z</a>&nbsp;";
	      $content .= "</span><br/>";
	      $content .= "<form method=\"post\" name=\"saform1\" id=\"saform1\" action=\"$php_self\"> \n"; // post to self;
	      $content .= "<p>Or Search by Last Name:<input type=\"TEXT\" name=\"alpha\" value=\"";
	      $content .= "\" size=\"10\" maxlength=\"20\">&nbsp;";
	      $content .= "<input type=\"SUBMIT\" name=\"SearchAuth\" value=\"Search\">\n";
	      $content .= "&nbsp;</p>\n";
				$content .= "</form>\n";			
				$content .= "<span id=\"authorlist\">\n";
				$count_authors = 0;
				// build query to list contributors	and authors
				// note that single quotes are auto-backslashed by wp
				$alpha = "All"; // default to All
				if (isset($_GET["alpha"])) { // single by id
					$alpha = $_GET["alpha"]; // A-Z char
					}					
				if (isset($_POST["alpha"])) { // lname entered
					$alpha = $_POST["alpha"];
					}
				if (isset($_GET['page_start'])) {
						$page_start = $_GET['page_start'];
						}
				if (isset($_GET['page_limit'])) {
						$page_limit = $_GET['page_limit'];
						}
				$searchletter = $alpha;
				if ($alpha == "All") {
					$searchletter = "";
					}
				$pattern = '/\\\\/'; 
				$replacement = ''; 
				$showa = preg_replace( $pattern, $replacement, $alpha );  // hide display of backslash to be nice.
				$content .= "<div id=\"authorinfo\">";
				if (current_user_can('manage_options') ) { // 09-04-2011 ma - for relevanssi
	     	 	$content .= "<b>Logout to see the normal user view.</b><br />\n";
	    		}
				$content .= "Search results for: ".$showa."";
				$content .= "</div><br />\n";							
				$prefix = $wpdb->get_blog_prefix();
				// http://codex.wordpress.org/Roles_and_Capabilities
				$query = "SELECT DISTINCT ID, user_nicename, display_name, user_email, user_status ";
				$query .= "from $wpdb->users ";
				$query .= "INNER JOIN $wpdb->usermeta as wpma ON $wpdb->users.ID = wpma.user_id ";
				$query .= "INNER JOIN $wpdb->usermeta as wpmb ON $wpdb->users.ID = wpmb.user_id ";
				// avoids previous_contributor - if you have that role
				$query .= "WHERE wpma.meta_key = '".$prefix."capabilities' ";
								
				$lsc_show_user_all_roles = get_option('lsctriball'); // 01-24-2013				
				if ($lsc_show_user_all_roles==1) { // 01-24-2013					
					$query .= "AND (wpma.meta_value not like '%subscriber%') "; // 01-24-2013
					}
				else { // do not show editors and admins					
					$query .= "AND (wpma.meta_value like '%author%' OR wpma.meta_value like '%contributor%') ";
					}
				
				$lsctribexc = get_option('lsctribexc'); // 02-15-2014
				if (trim($lsctribexc) != "") { 
					$query .= "AND (user_email <> '".$lsctribexc."') "; // 02-15-2014
					}
					
				$query .= "AND (wpma.meta_value not like '%previous_contributor%') "; // wpma.meta_value not like '%previous_author%' AND 
				$query .= "AND wpmb.meta_key = 'last_name' AND wpmb.meta_value like '$searchletter%' ";	
				$query .= "AND user_status = 0 "; // buddypress might use this - but old field
				$query .= "ORDER BY wpmb.meta_value limit $page_start, $page_limit ";											

				$lsc_show_user_desc_max_chars = get_option('lsctribmax'); // 01-13-2013				
				if ($lsc_show_user_desc_max_chars < 1) { // 01-13-2013
					$lsc_show_user_desc_max_chars = 150; // 01-13-2013
					} // 01-13-2013
								
				//$save_display = "";
				//$content .= "<!-- \n debug-lsc-query: ".$query. " [".$lsc_show_user_all_roles."] -->\n";
				// do query
				$authors = $wpdb->get_results($query);
				if ($authors) {
					foreach ($authors as $author) {	
					   if ($count_authors < $page_show) {
					   	$author_id = $author->ID;					
					   	$num_posts = count_user_posts( $author_id );	// 01-31-2013
					    $lsc_suppress_users_zero_posts = get_option('lsctribsupp'); // 01-13-2013		
					    $okshow = 1;	// 01-31-2013 default yes
						  if ($lsc_suppress_users_zero_posts > 0) { // 01-31-2013
						  	if ($num_posts < 1) { $okshow = 0; }
						  	}					   	
					   	if ($okshow) { // 01-31-2013
								$content .= "<div id=\"authorinfo\">";
								$content .= "<div id=\"authorpicthumb\">\n";								   	
								if ($plugin_found > 0) { // 12-03-2015user photo plugin found									
									$lsc_use_normal_image_size = get_option('lsctribimg'); // 12-03-2015			
									if ($lsc_use_normal_image_size==1) { // 12-03-2015										
										$content .= "".userphoto($author_id); // user photo normal size 
										}
									else {
										$content .= "".userphoto_thumbnail($author_id); // user photo thumbnail 
										}
									}
								else {
									$content .= "".get_avatar( $author_id, $size = '40', $default = '' ); 
								}
								$content .= "</div>";	
								$content .= "<a href=\"".$mypage."?aid=";
								$content .= $author->ID;					
								$content .= "\"><b>";
								$content .= $author->display_name;								
								$content .= "</b></a>&nbsp;"; 
								// need more time...sigh.
								$query = "SELECT meta_key, meta_value ";
								$query .= " from $wpdb->usermeta ";
								$query .= " WHERE user_id = ".$author->ID." ";
								$query .= " AND meta_key = 'description' ";
								$query .= " limit 0, 1 "; // only 1 desc 
								$author_desc = $wpdb->get_results($query);
								// debug
								// $content .= $query;	
								// $desc = the_author_meta('description', $author->ID);
								foreach ($author_desc as $desc) {
										//$desc = the_author_meta('description', $author->ID);
										if (trim(strlen($desc->meta_value) > 2)) {
											if (!current_user_can('manage_options') && !isset($_GET['s'])) { // 09-04-2011 mallsop, 09-13-2011 s for search
												$descx = strip_tags(substr($desc->meta_value, 0, $lsc_show_user_desc_max_chars)); // max 01-13-2012 was 150
												}
											else { // 9-4-2011 mallsop
												// authority user - desc not limited for relevanssi shortcode expand option (logout if admin)
												$descx = strip_tags($desc->meta_value);
												}
											$descx = preg_replace('/[^\x0A\x20-\x7E]/','', $descx); 											
											$content .= $descx;
											}
										else { // default msg
											$content .= "Author";
											}
										}
								$content .= "...&nbsp;<a href=\"".$mypage."?aid=";
								$content .= $author->ID;					
								$content .= "\">";
								$content .= "More&nbsp;&gt;";
								$content .= "</a> "; // $count_authors 
								$content .= "</div><br />&nbsp;<br />\n";	
								}	// end okshow
							}
						if ($okshow) {	// 01-31-2013
							$count_authors++;									
							}
						} // end loop
					} // end authors found
					$content .= "<br />\n";
					$content .= "<div id=\"authorinfo\">";
					if ($alpha != "All") {
						$content .= "Found  ".$count_authors." author(s). <br />";					
						}
					// next and prev links (with alpha)
					if ($page_start > 0) { // then prev button needed
							$prev_start = $page_start - $page_show; // i.e. 0
							$prev_limit = $page_limit - $page_show;	// i.e. 26				
							$content .= "<a href='$mypage?alpha=$alpha&amp;page_start=$prev_start&amp;page_limit=$prev_limit'>Prev</a>&nbsp;&nbsp;&nbsp;";
							}
					if ($count_authors > $page_show) { // then next button needed
							$next_start = $page_start + $page_show; // i.e. 25
							$next_limit = $page_limit + $page_show;	// i.e. 51
							$content .= "<a href='$mypage?alpha=$alpha&amp;page_start=$next_start&amp;page_limit=$next_limit'>Next</a>";
							}
					$content .= "</div><br />\n";
			} // end else all
			return $content;
	} // end function
?>