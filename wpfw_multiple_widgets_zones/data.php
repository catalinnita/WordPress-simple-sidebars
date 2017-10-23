<?php
$sql = "CREATE TABLE IF NOT EXISTS `es_sidebars` (
				  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
				  `Name` varchar(255) NOT NULL,
				  `SidebarID` varchar(255) NOT NULL,
				  `BeforeWidget` text NOT NULL,
				  `AfterWidget` text NOT NULL,
				  `BeforeTitle` text NOT NULL,
				  `AfterTitle` text NOT NULL,
				   PRIMARY KEY (`ID`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";

$sql = "CREATE TABLE IF NOT EXISTS `es_posts_sidebar` (
	  				`ID` bigint(20) NOT NULL AUTO_INCREMENT,
	  				`PostID` bigint(20) NOT NULL,
	  				`SidebarID` bigint(20) NOT NULL,
						PRIMARY KEY (`ID`) 
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
		
					
$sql = "CREATE TABLE IF NOT EXISTS `es_terms_sidebar` ( 
	  				`ID` bigint(20) NOT NULL AUTO_INCREMENT,
	  				`TermID` bigint(20) NOT NULL,
	  				`SidebarID` bigint(20) NOT NULL,
						PRIMARY KEY (`ID`) 
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";


$tables_info = array();
$tables_info['es_sidebars'] = array('Name' => 'varchar(255)',
																		'SidebarID' => 'varchar(255)',
																		'BeforeWidget' => 'text',
																		'AfterWidget' => 'text',
																		'BeforeTitle' => 'text',
																		'AfterTitle' => 'text');
																		
$tables_info['es_posts_sidebar'] = array('PostID' => 'bigint(20)',
																				 'SidebarID' => 'bigint(20)');
																				 
$tables_info['es_terms_sidebar'] = array('TermID' => 'bigint(20)',
																				 'SidebarID' => 'bigint(20)');																				 																		

wpfw_create_tables($tables_info);


// add default sidebar
$sidebarexists = $wpdb->get_var($wpdb->prepare("SELECT ID FROM es_sidebars WHERE Name = 'defaultsidebar'", "" ));					
if (!$sidebarexists) {
$wpdb->query("INSERT INTO es_sidebars
			(Name)
				VALUES ('defaultsidebar')");	
}

// add default options
if(!get_option("wpfw_sidebars_posts")) {
	
	$wpfw_sidebars_posts = array(
		"0" => "posts"
	);
	
	update_option("wpfw_sidebars_posts", serialize($wpfw_sidebars_posts));
}
if(!get_option("wpfw_sidebars_taxonomies")) {
	
	$wpfw_sidebars_taxonomies = array(
		"0" => "categories"
	);
	
	update_option("wpfw_sidebars_taxonomies", serialize($wpfw_sidebars_taxonomies));
}

if(!get_option("wpfw_sidebars_before_widget")) {
	global $wp_registered_sidebars;
	
	if (count($wp_registered_sidebars) > 0) {
		$found = 0;
		foreach($wp_refisterd_sidebars as $sidebar) {
			if(is_active_sidebar($sidebar->ID) && $found == 0) {
				
				$wpfw_sidebars_before_widget = wpfw_get_sidebar_info($sidebar->ID, 'before_widget');
				$wpfw_sidebars_after_widget = wpfw_get_sidebar_info($sidebar->ID, 'after_widget');
				$wpfw_sidebars_before_title = wpfw_get_sidebar_info($sidebar->ID, 'before_title');
				$wpfw_sidebars_after_title = wpfw_get_sidebar_info($sidebar->ID, 'after_title');	
				
				$found = 1;
			}
		}
	}
	
	// if no sidebar exists i am adding the WordPress default settings
	if($found == 0) {
		$wpfw_sidebars_before_widget = '<li id="%1$s" class="widget %2$s">';
		$wpfw_sidebars_after_widget = '</li>';
		$wpfw_sidebars_before_title = '<h2 class="widgettitle">';
		$wpfw_sidebars_after_title = '</h2>';	
	}
		
	update_option("wpfw_sidebars_before_widget", $wpfw_sidebars_before_widget);
	update_option("wpfw_sidebars_after_widget", $wpfw_sidebars_after_widget);
	update_option("wpfw_sidebars_before_title", $wpfw_sidebars_before_title);
	update_option("wpfw_sidebars_after_title", $wpfw_sidebars_after_title);
		
}
?>