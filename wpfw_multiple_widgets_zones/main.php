<?php
/*
Plugin Name: WPFW - Multiple Widgets Zones
Plugin URI: http://www.WPFW.net
Description: Add as many sidebars you like and personalize them. Attach a sidebar to any taxonomy, post, page or custom post. For setting up the plugin please go to <a href="admin.php?page=settings_sidebars">Settings Page</a>.
Version: 1.2
Author: Catalin Nita
Author URI: http://www.WPFW.net
License: GNU General Public License v2 or later
*/

include('settings.php');
include('functions.php');
include('data.php');
include('optionspage.php');

// Create sidebar edit box for all selected custom post and taxonomies
function wpfw_infinite_sidebars_boxes() {
	global $wpdb;
	$wpfw_sidebars_posts = unserialize(get_option("wpfw_sidebars_posts"));
	$args = array(
   	'public'   => true
   );
	$post_types = get_post_types($args, 'objects');
	foreach ($post_types as $post_type) {
		if(in_array($post_type->name, $wpfw_sidebars_posts)) {
			add_meta_box( 'myplugin_sectionid', __( 'Show sidebar', 'myplugin_textdomain' ), 
		                'Show_sidebar', $post_type->name, 'side', 'high' );                
		}
	}
	
	$taxonomies = unserialize(get_option("wpfw_sidebars_taxonomies"));
	foreach ( $taxonomies as $taxonomy ) {
		add_action ($taxonomy.'_edit_form', 'es_category_sidebar_form_fields');
		add_action ($taxonomy.'_add_form_fields', 'es_category_sidebar_form_fields_add');
		add_action ('edited_'.$taxonomy, 'es_category_sidebar_form_save');
		add_action ('create_'.$taxonomy, 'es_category_sidebar_form_save_add');	
	}	
	
}
add_action('admin_menu', 'wpfw_infinite_sidebars_boxes');


// Action for saving the sidebar selected
add_action('save_post', 'Sidebar_config_save');


// Show sidebar config form function
function Show_sidebar() {
	global $wpdb, $_GET;
	
	echo '<table border=0 cellspacing=0 cellpadding=0>
	<tr>
	<td style="font-size: 11px !important;">Show sidebar</td>
	<td style="padding: 0px 0px 0px 5px;">
		<select name="SideBar">
			<option value="0">No sidebar</option>';
	
	$sidebars = $wpdb->get_results("SELECT * FROM es_sidebars");
	$sidebarID = $wpdb->get_var($wpdb->prepare("SELECT SidebarID FROM es_posts_sidebar WHERE PostID = ".$_GET['post'], ""));		
	
	//print_r($sidebars);
	
	foreach($sidebars as $sidebar) {
		
		echo '<option value="'.$sidebar->ID.'"'; if ($sidebar->ID == $sidebarID) { echo ' selected=selected'; } echo '>'.$sidebar->Name.'</option>';
		
	}
			
	echo '
		</select>
	</td>
	</tr>
	</table>
	<p class="howto">Select which sidebar you want to show on this post. Select <b>No sidebar</b> if you want to create a full page layout. <b>You must click update button after a change was made.</b></p>
	';	
	
}

// Save sidebar option function
function Sidebar_config_save() {
	global $_POST, $wpdb;
	
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) 
        return $postID;
  
  
	if(isset($_POST['post_ID'])) {
	$sidebarexists = $wpdb->get_var($wpdb->prepare("SELECT ID FROM es_posts_sidebar WHERE PostID = ".$_POST['post_ID'], ""));		
	}
	
	if ($sidebarexists) {
		if ($_POST['SideBar'] != 0) {
		$wpdb->query("UPDATE es_posts_sidebar SET 
			SidebarID = '".$_POST['SideBar']."'
				WHERE PostID = ".$_POST['post_ID'] );		
		}
		else {
		$wpdb->query("DELETE FROM es_posts_sidebar 
				WHERE PostID = ".$_POST['post_ID'] );					
		}
	}
	else {
		if ($_POST['SideBar'] != 0) {
		$wpdb->query("INSERT INTO es_posts_sidebar 
			(PostID, SidebarID)
				VALUES (".$_POST['post_ID'].",".$_POST['SideBar'].")");
		}
	}
	
}


function es_category_sidebar_form_fields() {
	global $_GET, $wpdb;
	
	// category sidebar
	$content = '<tr class="form-field">';
	$content .= '<th scope="row" valign="top"><label for="category_theme">Show sidebar</label></th>';
	$content .= '<td>';
  $content .= '<select name="SideBar" id="category_sidebar">';
 	$content .= '<option value="0">No sidebar</option>';
   
	$sidebars = $wpdb->get_results("SELECT * FROM es_sidebars");
	if ($_GET['tag_ID']) {
		$sidebarID = $wpdb->get_var($wpdb->prepare("SELECT SidebarID FROM es_terms_sidebar WHERE TermID = ".$_GET['tag_ID'], ""));		
	}
	
	
	foreach($sidebars as $sidebar) {
		
		$content .= '<option value="'.$sidebar->ID.'"'; if ($sidebar->ID == $sidebarID) {$content .= ' selected=selected'; } $content .= '>'.$sidebar->Name.'</option>';
		
	}
	
	$content .= '</select>';
	$content .= '<p class="description">Select which sidebar you want to show on this category.</p>';
	$content .= '</td>';
	$content .= '</tr>';
	
	echo $content;	
}

function es_category_sidebar_form_fields_add() {
	global $_GET, $wpdb;
	
	
	// category sidebar
	$content = '<div class="form-field">';
	$content .= '<label for="category_theme">Show sidebar</label>';
  $content .= '<select name="SideBar" id="category_sidebar">';
 	$content .= '<option value="0">No sidebar</option>';
   
	$sidebars = $wpdb->get_results("SELECT * FROM es_sidebars");
	if ($_GET['tag_ID']) {
	$sidebarID = $wpdb->get_var($wpdb->prepare("SELECT SidebarID FROM es_terms_sidebar WHERE TermID = ".$_GET['tag_ID'], ""));		
	}
	
	
	foreach($sidebars as $sidebar) {
		
		$content .= '<option value="'.$sidebar->ID.'"'; if ($sidebar->ID == $sidebarID) {$content .= ' selected=selected'; } $content .= '>'.$sidebar->Name.'</option>';
		
	}
	
	$content .= '</select>';
	$content .= '<p>Select which sidebar you want to show on this category. Select <b>No sidebar</b> if you want to create a full page layout.</p>';
	$content .= '</div>';
	
	echo $content;	
}

function es_category_sidebar_form_save() {
	global $_POST, $_GET, $wpdb;
	
	// sidebar
	if ($_POST['tag_ID']) {
	$sidebarexists = $wpdb->get_var($wpdb->prepare("SELECT ID FROM es_terms_sidebar WHERE TermID = ".$_POST['tag_ID'], ""));		
	}
	
	if ($sidebarexists) {
		if ($_POST['SideBar'] != 0) {
		$wpdb->query("UPDATE es_terms_sidebar SET 
			SidebarID = '".$_POST['SideBar']."'
				WHERE TermID = ".$_POST['tag_ID'] );		
		}
		else {
		$wpdb->query("DELETE FROM es_terms_sidebar 
				WHERE TermID = ".$_POST['tag_ID'] );					
		}
	}
	else {
		if ($_POST['SideBar'] != 0) {
		$wpdb->query("INSERT INTO es_terms_sidebar 
			(TermID, SidebarID)
				VALUES (".$_POST['tag_ID'].",".$_POST['SideBar'].")");
		}
	}		
	
}

function es_category_sidebar_form_save_add($category_ID) {
	global $_POST, $_GET, $wpdb;
	
	// sidebar

	$wpdb->query("INSERT INTO es_terms_sidebar 
		(TermID, SidebarID)
			VALUES (".$category_ID.",".$_POST['SideBar'].")");
}

function get_sidebar_name($echo=0) {
	global $wpdb;
	
	if (is_single() || is_page()) {
	$temp_query = $wp_query;
	wp_reset_query();		
	
	$postssidebars = $wpdb->get_results("SELECT * FROM es_posts_sidebar WHERE PostID = ".get_the_ID());
	$sidebar = $wpdb->get_results("SELECT * FROM es_sidebars WHERE ID = ".$postssidebars[0]->SidebarID);
	$SidebarName = $sidebar[0]->Name;
		
	$wp_query = $temp_query;
	}
	
	if (is_category()) {
	$temp_query = $wp_query;
	wp_reset_query();		
	
	$catname = single_cat_title('', false);
	$catid = get_cat_ID($catname);		
	
	
	$termssidebars = $wpdb->get_results("SELECT * FROM es_terms_sidebar WHERE TermID = ".$catid);

	$sidebar = $wpdb->get_results("SELECT * FROM es_sidebars WHERE ID = ".$termssidebars[0]->SidebarID);
	$SidebarName = $sidebar[0]->Name;
	
	$wp_query = $temp_query;
	}	
	if (!$SidebarName) { $SidebarName = 'defaultsidebar'; }	
	if ($SidebarName) {

		if ($echo == 1) { echo "Sidebar ".$SidebarName; }
		else { return "Sidebar ".$SidebarName; }		
	}
	
}

if (function_exists("register_sidebar")) {
	 $sidebars = $wpdb->get_results("SELECT * FROM es_sidebars");	
   foreach($sidebars as $sidebar) {
   	register_sidebar(
   		Array(		
   			"name" => sprintf( 'Sidebar '.$sidebar->Name, $sidebar->ID),
				"id" => 'sidebar-'.$sidebar->ID,
				'before_widget' => stripslashes(get_option("wpfw_sidebars_before_widget")),
				'after_widget' => stripslashes(get_option("wpfw_sidebars_after_widget")),
				'before_title' => stripslashes(get_option("wpfw_sidebars_before_title")),
				'after_title' => stripslashes(get_option("wpfw_sidebars_after_title"))
			));
   }
  
}

?>