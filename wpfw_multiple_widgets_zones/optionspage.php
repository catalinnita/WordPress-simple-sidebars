<?php
// add config page in wordpress menu
add_action('admin_menu', 'sidebar_config_menu');  

function sidebar_config_menu() {
	add_menu_page('Sidebars', 'Sidebars', 'administrator', 'setup_sidebars', 'setup_sidebars', '');	
	add_submenu_page('setup_sidebars', 'Sidebars Settings', __('Settings', 'wpfw'), 'administrator', 'settings_sidebars', 'settings_sidebars');	
}

// build config page

function setup_sidebars() {
	global $_POST, $_GET, $wpdb;
	
	if ($_POST['addsidebar'] == 1 ) {
		add_sidebars();
	}	
	if ($_POST['editsidebar'] == 1 ) {
		edit_sidebars();
	}		

	if ($_GET['deleteid']) {
		delete_sidebars();
	}		
	
	if(!isset($_GET['editid'])) {
		$before_widget = stripslashes(get_option("wpfw_sidebars_before_widget"));
		$after_widget = stripslashes(get_option("wpfw_sidebars_after_widget"));
		$before_title = stripslashes(get_option("wpfw_sidebars_before_title"));
		$after_title = stripslashes(get_option("wpfw_sidebars_after_title"));
	}
	
	
	
	$content = '<div class="wrap wpfw_options_page">';
	$content .= '<div id="icon-themes" class="icon32"><br /></div>';
	if(isset($_GET['editid'])) {
	$esidebar = $wpdb->get_results("SELECT * FROM es_sidebars WHERE ID = ".$_GET['editid']);
	$before_widget = $esidebar[0]->BeforeWidget;
	$after_widget = $esidebar[0]->AfterWidget;
	$before_title = $esidebar[0]->BeforeTitle;
	$after_title = $esidebar[0]->AfterTitle;
	$content .= '<h2><a href="admin.php?page=setup_sidebars">Manage sidebars</a> > Edit '.$esidebar[0]->Name.'</h2><br/>';
	}
	else {
	$content .= '<h2>Manage sidebars</h2><br/>';
	}
	
	$content .= '<div id="col-container">';
	$content .= '<div id="col-right" class="visible">';	
	$content .= '<table class="widefat post fixed">';
	$content .= '<thead>';		
	$content .= '<tr>';		
	$content .= '<th class="manage-column column-title">Sidebar name</th>';		
	$content .= '<th class="manage-column column-title">Sidebar ID</th>';		
	$content .= '<th style="text-align: center; width: 300px;" class="manage-column column-title">Actions</th>';		
	$content .= '</tr>';	
	$content .= '</thead>';		

	$content .= '<tbody>';		

	$sidebars = $wpdb->get_results("SELECT * FROM es_sidebars");
	
	$nr = 1;
	foreach($sidebars as $sidebar) {
	
	if ($nr%2 == 1) {
	$content .= '<tr class="author-self status-publish iedit">';		
	}
	else {
	$content .= '<tr class="alternate author-self status-publish iedit">';	
	}
	if($sidebar->ID == $esidebar[0]->ID) {
		$content .= '<tr class="author-self status-publish iedit selected">';	
	}
	
	
	$content .= '<td>'.$sidebar->Name.'</td>';	
	$content .= '<td>'.$sidebar->SidebarID.'</td>';	
	
	if($sidebar->Name == 'defaultsidebar') {
	$content .= '<td align="center">This sidebar can\'t be deleted nor edited.</td>';		
	}
	else {
	$content .= '<td align="center">';
	$content .= '<a class="button button-small" href="admin.php?page=setup_sidebars&editid='.$sidebar->ID.'">EDIT</a>&nbsp;';
	$content .= '<a class="button button-small" href="admin.php?page=setup_sidebars&deleteid='.$sidebar->ID.'">DELETE</a>';		
	$content .= '</td>';
	}
	$content .= '</tr>';	
		
	$nr++;
	}
	$content .= '</tbody>';		
	
	$content .= '<tfoot>';		
	$content .= '<tr>';		
	$content .= '<th class="manage-column column-title">Sidebar name</th>';		
	$content .= '<th class="manage-column column-title">Sidebar ID</th>';		
	$content .= '<th style="text-align: center;" class="manage-column column-title">Actions</th>';		
	$content .= '</tr>';	
	$content .= '</tfoot>';		
	
	$content .= '</table>';
	$content .= '</div>';
	
	echo $content;
	
	$content  = '<div id="col-left">';
	$content .= '<div class="form-wrap">';
	
	
	if(isset($_GET['editid'])) {
	$content .= '<h3>Edit Sidebar</h3>';			
	$content .= '<form name="editsidebars" id="EditSidebar" method="POST" action="admin.php?page=setup_sidebars&editid='.$_GET['editid'].'">';
	$content .= '<input type=hidden name=editsidebar value=1>';	
	$content .= '<input type=hidden name=editid value="'.$_GET['editid'].'">';	
	}
	else {
	$content .= '<h3>Add Sidebar</h3>';			
	$content .= '<form name="addsidebars" id="AddSidebar" method="POST" action="admin.php?page=setup_sidebars">';
	$content .= '<input type=hidden name=addsidebar value=1>';	
	}
	
	// sidebar name
	$content .= '<div class="form-field">';
	$content .= '<label>Sidebar name</label>';
	$content .= '<input type="text" name="sidebarname" value="'; if(isset($esidebar[0]->Name)) { $content .= $esidebar[0]->Name; } $content .= '" >';
	$content .= '<p>Please add the sidebar name as it will appear in widgets page.</p>';
	$content .= '</div>';
	
	// before widget
	$content .= '<div class="form-field">';
	$content .= '<label>Before widget HTML code</label>';
	$content .= '<textarea name="sidebar_before_widget">'.stripslashes($before_widget).'</textarea>';
	$content .= '<p>HTML code that will be output BEFORE the each widget of this sidebar. You can use two variables for creating dynamic id or class names:<br/>
							<strong>%1$s</strong> - for widget id (id_base + widget number)<br/>
							<strong>%2$s</strong> - for widget name</p>';
	$content .= '</div>';	
	
	// before title
	$content .= '<div class="form-field">';
	$content .= '<label>Before title HTML code</label>';
	$content .= '<textarea name="sidebar_before_title">'.stripslashes($before_title).'</textarea>';
	$content .= '<p>HTML code that will be output BEFORE the title of each widgets.</p>';
	$content .= '</div>';	
	
	// after title
	$content .= '<div class="form-field">';
	$content .= '<label>After title HTML code</label>';
	$content .= '<textarea name="sidebar_after_title">'.stripslashes($after_title).'</textarea>';
	$content .= '<p>HTML code that will be output AFTER the title of each widget</p>';
	$content .= '</div>';	
	
	// after widget
	$content .= '<div class="form-field">';
	$content .= '<label>After widget HTML code</label>';
	$content .= '<textarea name="sidebar_after_widget">'.stripslashes($after_widget).'</textarea>';
	$content .= '<p>HTML code that will be output AFTER the each widget of this sidebar.</p>';
	$content .= '</div>';				
	
	if(isset($_GET['editid'])) {
	$content .= '<input class="button-primary action" type=submit value="Save Sidebar">&nbsp;';
	$content .= '<input onclick="document.getElementById(\'EditSidebar\').action=\'admin.php?page=setup_sidebars\';
															 document.getElementById(\'EditSidebar\').submit();" 
											class="button-secondary action" type="button" value="Save Sidebar And Go Back">';
	}
	else {
	$content .= '<input class="button-primary action" type=submit value="Add New Sidebar">';
	}

	$content .= '</form>';
	$content .= '</div>';
	$content .= '</div>';
	$content .= '</div>';

	
	echo $content;
	
}

function delete_sidebars() {
	global $_GET, $wpdb;
	
	$wpdb->query("DELETE FROM es_sidebars
			WHERE ID = ".$_GET['deleteid']);	
	
}

function add_sidebars() {
	global $_POST, $wpdb;
	
	$sidebarexists = $wpdb->get_results("SELECT ID FROM es_sidebars WHERE Name = '".$_POST['sidebarname']."'");
	
	if (!$sidebarexists[0]->ID) {
		$sidebar_name = $_POST['sidebarname'];
	}
	else {
		$sidebar_name = $_POST['sidebarname']."_copy";
	}
	
	$wpdb->query("INSERT INTO es_sidebars
			(Name, BeforeWidget, AfterWidget, BeforeTitle, AfterTitle)
				VALUES ('".$sidebar_name."', '".addslashes($_POST['sidebar_before_widget'])."', '".addslashes($_POST['sidebar_after_widget'])."', '".addslashes($_POST['sidebar_before_title'])."', '".addslashes($_POST['sidebar_after_title'])."')");
				
	$wpdb->query("UPDATE es_sidebars SET 
									SidebarID = 'sidebar-".$wpdb->insert_id."'
										WHERE ID = ".$wpdb->insert_id);				
	
}

function edit_sidebars() {
	global $wpdb;
	
	$sidebarexists = $wpdb->get_results("SELECT ID FROM es_sidebars WHERE Name = '".$_POST['sidebarname']."' AND ID <> ".$_POST['editid']);
	
	if (!$sidebarexists[0]->ID) {
		$sidebar_name = $_POST['sidebarname'];
	}
	else {
		$sidebar_name = $_POST['sidebarname']."_copy";
	}
	
	$wpdb->query("UPDATE es_sidebars SET 
								  Name = '".$sidebar_name."',
								  BeforeWidget = '".addslashes($_POST['sidebar_before_widget'])."',
								  AfterWidget = '".addslashes($_POST['sidebar_after_widget'])."',
								  BeforeTitle = '".addslashes($_POST['sidebar_before_title'])."',
								  AfterTitle = '".addslashes($_POST['sidebar_after_title'])."'
								  	WHERE ID = ".$_POST['editid']);
	
}

function settings_sidebars() {
	global $wpdb;
	
	if($_POST['save_settings'] == 1) {
		
		$args = array(
   		'public'   => true
   	);
		$post_types = get_post_types($args, 'objects');
		$sidebars_posts = array();
		foreach ( $post_types as $post_type ) {		
			if(isset($_POST[$post_type->name])) $sidebars_posts[] = $post_type->name;
		}
	
		$wpfw_sidebars_posts = serialize($sidebars_posts);
		
		$args = array(
   		'public'   => true
   	);
		$taxonomies = get_taxonomies($args, 'objects');
		$sidebars_taxonomies = array();
		foreach ( $taxonomies as $taxonomy ) {	
			if(isset($_POST[$taxonomy->name])) $sidebars_taxonomies[] = $taxonomy->name;
		}
		$wpfw_sidebars_taxonomies = serialize($sidebars_taxonomies);
		
		update_option("wpfw_sidebars_posts", $wpfw_sidebars_posts);
		update_option("wpfw_sidebars_taxonomies", $wpfw_sidebars_taxonomies);
		update_option("wpfw_sidebars_before_widget", $_POST['before_widget']);
		update_option("wpfw_sidebars_after_widget", $_POST['after_widget']);
		update_option("wpfw_sidebars_before_title", $_POST['before_title']);
		update_option("wpfw_sidebars_after_title", $_POST['after_title']);
	}	
	
	$wpfw_sidebars_posts = unserialize(get_option("wpfw_sidebars_posts"));
	$wpfw_sidebars_taxonomies = unserialize(get_option("wpfw_sidebars_taxonomies"));
	
	?>
	<div class="wrap">
	<h2>Sidebars Settings</h2>

	<form method="post" action="admin.php?page=settings_sidebars">
	<input type="hidden" name="save_settings" value="1" />		
	<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row">Use sidebar on post types</th>
				<td>
					<fieldset>
						<?php 
						$args = array(
   						'public'   => true
   					);
						$post_types = get_post_types($args, 'objects');
						foreach ( $post_types as $post_type ) {
						?>
						<legend class="screen-reader-text"><span><?php echo $post_type->name; ?></span></legend>
						<label for="<?php echo $post_type->name; ?>">
							<input name="<?php echo $post_type->name; ?>" type="checkbox" id="<?php echo $post_type->name; ?>" value="1" <?php if(in_array($post_type->name, $wpfw_sidebars_posts)) { ?> checked="checked" <?php } ?>>
							<?php echo $post_type->label; ?>
						</label><br>
						<?php
						}	
						?>
					</fieldset>
					<p class="description">Please select on what post types you want to use WPFW Infinite Sidebars plugin</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Use sidebar on taxonomies</th>
				<td>
					<fieldset>
						<?php 
						$args = array(
   						'public'   => true
   					);
						$taxonomies = get_taxonomies($args, 'objects');
						foreach ( $taxonomies as $taxonomy ) {
						?>
						<legend class="screen-reader-text"><span><?php echo $taxonomy->name; ?></span></legend>
						<label for="<?php echo $taxonomy->name; ?>">
							<input name="<?php echo $taxonomy->name; ?>" type="checkbox" id="<?php echo $taxonomy->name; ?>" value="1" <?php if(in_array($taxonomy->name, $wpfw_sidebars_taxonomies)) { ?> checked="checked" <?php } ?>>
							<?php echo $taxonomy->label; ?>
						</label><br>
						<?php
						}	
						?>
					</fieldset>
					<p class="description">Please select on what taxonomies you want to use WPFW Infinite Sidebars plugin</p>
				</td>
			</tr>			
		</table>
		<h2>Sidebars Default Layout Settings</h2>
		<table class="form-table">
			<tr valign="top">
				<th scope="row">Before Widget Code</th>
				<td>
					<fieldset>
						<legend class="screen-reader-text"><span>Before Widget</span></legend>
						<label for="before_widget">
							<textarea name="before_widget" rows="5" cols="50" id="before_widget" class="large-text code"><?php echo stripslashes(get_option("wpfw_sidebars_before_widget")); ?></textarea>
						</label><br>
					</fieldset>
					<p class="description">Please add the HTML code you want to be placed before the each widget. You can use two variables for creating dynamic id or class names:<br/>
						<strong>%1$s</strong> - for widget id (id_base + widget number)<br/>
						<strong>%2$s</strong> - for widget name</p>
				</td>
			</tr>	
			<tr valign="top">
				<th scope="row">Before Title Code</th>
				<td>
					<fieldset>
						<legend class="screen-reader-text"><span>Before Title</span></legend>
						<label for="before_title">
							<textarea name="before_title" rows="3" cols="50" id="before_title" class="large-text code"><?php echo stripslashes(get_option("wpfw_sidebars_before_title")); ?></textarea>
						</label><br>
					</fieldset>
					<p class="description">Please add the HTML code that will be output before each widget title</p>
				</td>
			</tr>	
			<tr valign="top">
				<th scope="row">After Title Code</th>
				<td>
					<fieldset>
						<legend class="screen-reader-text"><span>After Title</span></legend>
						<label for="after_title">
							<textarea name="after_title" rows="3" cols="50" id="after_title" class="large-text code"><?php echo stripslashes(get_option("wpfw_sidebars_after_title")); ?></textarea>
						</label><br>
					</fieldset>
					<p class="description">Please add the HTML code that will be output after each widget title</p>
				</td>
			</tr>	
			<tr valign="top">
				<th scope="row">After Widget Code</th>
				<td>
					<fieldset>
						<legend class="screen-reader-text"><span>After Widget</span></legend>
						<label for="after_widget">
							<textarea name="after_widget" rows="5" cols="50" id="after_widget" class="large-text code"><?php echo stripslashes(get_option("wpfw_sidebars_after_widget")); ?></textarea>
						</label><br>
					</fieldset>
					<p class="description">Please add the HTML code that will be output after each widget</p>
				</td>
			</tr>										
		</tbody>
	</table>
	<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p></form>
</div>
	
<?php	
}

?>