<?php

// Block direct access
if (!defined('ABSPATH')) { exit; }

function pluginsclub_sticky_bar_settings_page() {
    add_settings_field('sticky_bar_time_to_read_text', 'Time to Read Text:', 'sticky_bar_time_to_read_text_field', 'sticky_bar_settings', 'sticky_bar_general_section');
    add_settings_field('sticky_bar_author_text', 'Author Text:', 'sticky_bar_author_text_field', 'sticky_bar_settings', 'sticky_bar_general_section');
    add_settings_field('sticky_bar_words_per_minute', 'Words per Minute:', 'sticky_bar_words_per_minute_field', 'sticky_bar_settings', 'sticky_bar_general_section');
    add_settings_field('sticky_bar_dark_mode', 'Dark Mode:', 'sticky_bar_dark_mode_field', 'sticky_bar_settings', 'sticky_bar_general_section');

    register_setting('sticky_bar_settings_group', 'sticky_bar_time_to_read_text', 'sanitize_text_field');
    register_setting('sticky_bar_settings_group', 'sticky_bar_author_text', 'sanitize_text_field');
    register_setting('sticky_bar_settings_group', 'sticky_bar_words_per_minute', 'intval');
    register_setting('sticky_bar_settings_group', 'sticky_bar_position', 'sanitize_text_field');
    register_setting('sticky_bar_settings_group', 'sticky_bar_dark_mode', 'sanitize_text_field');

    // Check if user submitted the settings form
    if (isset($_POST['sticky_bar_settings_submit'])) {

// Save the settings in the database
update_option('sticky_bar_position', $_POST['sticky_bar_position']);
update_option('sticky_bar_time_to_read_text', $_POST['sticky_bar_time_to_read_text']);
update_option('sticky_bar_author_text', $_POST['sticky_bar_author_text']);
update_option('sticky_bar_words_per_minute', $_POST['sticky_bar_words_per_minute']);
update_option('sticky_bar_display_post_types', $_POST['sticky_bar_display_post_types']);
update_option('sticky_bar_category', isset($_POST['sticky_bar_category']));
update_option('sticky_bar_title', isset($_POST['sticky_bar_title']));
update_option('sticky_bar_author', isset($_POST['sticky_bar_author']));
update_option('sticky_bar_date', isset($_POST['sticky_bar_date']));
update_option('sticky_bar_time_to_read', isset($_POST['sticky_bar_time_to_read']));
update_option('sticky_bar_comments', isset($_POST['sticky_bar_comments']));
update_option('sticky_bar_sharing_buttons', isset($_POST['sticky_bar_sharing_buttons']));
update_option('sticky_bar_prev_next_links', isset($_POST['sticky_bar_prev_next_links']));
update_option('sticky_bar_category_bg_color', sanitize_hex_color($_POST['sticky_bar_category_bg_color']));
update_option('sticky_bar_dark_mode', isset($_POST['sticky_bar_dark_mode']));


// Redirect to the settings page to show the updated settings
wp_redirect(add_query_arg('settings-updated', 'true', admin_url('admin.php?page=sticky-bar-settings')));
}
    
    // Get the current settings from the database
    $category_enabled = get_option('sticky_bar_category', true);
    $title_enabled = get_option('sticky_bar_title', true);
    $author_enabled = get_option('sticky_bar_author', true);
    $date_enabled = get_option('sticky_bar_date', true);
    $time_to_read_enabled = get_option('sticky_bar_time_to_read', true);
    $comments_enabled = get_option('sticky_bar_comments', true);
    $sharing_buttons_enabled = get_option('sticky_bar_sharing_buttons', true);
    $prev_next_links_enabled = get_option('sticky_bar_prev_next_links', true);
    $selected_post_types = get_option('sticky_bar_display_post_types', array('post'));
    $category_bg_color = get_option('sticky_bar_category_bg_color', '#0074a1');
    $dark_mode_enabled = get_option('sticky_bar_dark_mode', false);

   

    // Check the active wp-admin page and load these files only on the plugin settings page
    $screen = get_current_screen();
       if ( $screen->id === 'settings_page_sticky-bar-settings'){
            wp_enqueue_style('settings_page_sticky-bar-settings-admin_page', plugins_url('css/settings-page.css', __FILE__));
    }

 ?>

    <div id="pluginsclub-cpanel">
					<div id="pluginsclub-cpanel-header">
			<div id="pluginsclub-cpanel-header-title">
				<div id="pluginsclub-cpanel-header-title-image">
<h1><a href="http://plugins.club/" target="_blank" class="logo"><img src="<?php echo plugins_url('img/pluginsclub_logo_black.png', __FILE__) ?>" style="height:27px"></a></h1></div>
				<div id="pluginsclub-cpanel-header-title-image-sep">
				</div>
<div id="pluginsclub-cpanel-header-title-nav">
	<?php
// Get our API endpoint and from it build the menu
$plugins_club_api_link = 'https://api.plugins.club/list_of_wp_org_plugins.php';
$remote_data = file_get_contents($plugins_club_api_link);
$menuItems = json_decode($remote_data, true);

foreach ($menuItems as $menuItem) :
    $isActive = isset($_GET['page']) && ($_GET['page'] === $menuItem['page']);
    $activeClass = $isActive ? 'active' : '';
    $isInstalled = function_exists($menuItem['check_function']) && function_exists($menuItem['check_callback']);
    $name = $menuItem['name'];
    if (!$isInstalled) {
        $name = ' <span class="dashicons dashicons-plus-alt"></span> '.$name;
    } else {
        $name .= ' <span class="dashicons dashicons-plugins-checked"></span>';
    }
?>
    <div class="pluginsclub-cpanel-header-nav-item <?php echo $activeClass; ?>">
        <?php if ($isInstalled) : ?>
            <a href="<?php echo $menuItem['url']; ?>" class="tab"><?php echo $name; ?></a>
        <?php else : ?>
            <a href="<?php echo $menuItem['fallback_url']; ?>" target="_blank" class="tab"><?php echo $name; ?></a>
        <?php endif; ?>
    </div>
<?php endforeach; ?>

<div id="pluginsclub-cpanel-header-title-image-sep">
				</div>
      
			</div>
		</div>
		
	</div>	
		  <div class="">

				<div id="pluginsclub-cpanel-admin-wrap" class="wrap">
			<h1 class="pluginsclub-cpanel-hide"><?php echo esc_html(get_admin_page_title()); ?></h1>

			<form id="pluginsclub-cpanel-form" method="post">
<h2>
    <?php echo esc_html(get_admin_page_title()); ?>
</h2>

		<p>Dispay a sticky bar at the bottom or top of posts, pages or products that shows category, post title, author, time needed to read article, share buttons and previous/next post links.</p>
			
			
		<div class="pluginsclub-cpanel-sep"></div>
		
<table class="form-table" role="presentation">

        <form method="post" action="">
                <tr>
                    <th scope="row">Enable/Disable Features</th>
                    <td>
                        <label>
                            <input type="checkbox" name="sticky_bar_category" <?php checked($category_enabled); ?>>
                            Category
                        </label>
                        <br>
                        <label>
                            <input type="checkbox" name="sticky_bar_title" <?php checked($title_enabled); ?>>
                            Title
                        </label>
                        <br>
                        <label>
                            <input type="checkbox" name="sticky_bar_author" <?php checked($author_enabled); ?>>
                            Author
                        </label>
                        <br>
                        <label>
                            <input type="checkbox" name="sticky_bar_date" <?php checked($date_enabled); ?>>
                            Date
                        </label>
                        <br>
                        <label>
                            <input type="checkbox" name="sticky_bar_time_to_read" <?php checked($time_to_read_enabled); ?>>
                            Time to Read
                        </label>
                        <br>
                        <label>
                            <input type="checkbox" name="sticky_bar_comments" <?php checked($comments_enabled); ?>>
                            Comments
                        </label>
                        <br>
                        <label>
                            <input type="checkbox" name="sticky_bar_sharing_buttons" <?php checked($sharing_buttons_enabled); ?>>
                            Sharing Buttons
                        </label>
                        <br>
                        <label>
                            <input type="checkbox" name="sticky_bar_prev_next_links" <?php checked($prev_next_links_enabled); ?>>
                            Prev/Next Links
                        </label>
                    </td>
                </tr>


<tr valign="top">
    <th scope="row">Sticky Bar Position:</th>
    <td>
        <label>
            <input type="radio" name="sticky_bar_position" value="top" <?php checked(get_option('sticky_bar_position', 'bottom'), 'top'); ?>>
            Top
        </label>
        <br>
        <label>
            <input type="radio" name="sticky_bar_position" value="bottom" <?php checked(get_option('sticky_bar_position', 'bottom'), 'bottom'); ?>>
            Bottom
        </label>
        <p class="description">Select the position of the sticky bar.</p>
    </td>
</tr>


<tr valign="top">
    <th scope="row">Display on Post Types:</th>
    <td>
        <?php
        $post_types = get_post_types(array('public' => true), 'objects');
        $selected_post_types = get_option('sticky_bar_display_post_types', array());
        
        foreach ($post_types as $post_type) {
    if ($post_type->name !== 'attachment') { // Exclude the 'attachment' post type
    $checked = in_array($post_type->name, $selected_post_types) || $post_type->name === 'post' ? 'checked' : '';
    echo '<label>';
    echo '<input type="checkbox" name="sticky_bar_display_post_types[]" value="' . $post_type->name . '" ' . $checked . '>';
    echo $post_type->label;
    echo '</label><br>';
}

}

        ?>
        <p class="description">Select the post types on which you want to display the sticky bar.</p>
    </td>
</tr>

<tr valign="top">
    <th scope="row">Background Color:</th>
    <td>
        <input type="color" style="padding:0px;" name="sticky_bar_category_bg_color" value="<?php echo esc_attr(get_option('sticky_bar_category_bg_color', '#0074a1')); ?>" />
        <p class="description">Select the background color for the category container and progress line.</p>
    </td>
</tr>


<tr valign="top">
    <th scope="row">Dark Mode:</th>
    <td>
        <label>
            <input type="checkbox" name="sticky_bar_dark_mode" <?php checked($dark_mode_enabled); ?>>
            Enable Dark Mode 😎
        </label>
    </td>
</tr>


            <tr valign="top">
        <th scope="row">Time to Read Text:</th>
        <td>
            <input type="text" name="sticky_bar_time_to_read_text" value="<?php echo esc_attr(get_option('sticky_bar_time_to_read_text', 'time to read')); ?>" placeholder="time to read" />
            <p class="description">Enter the text to display after the time to read value.</p>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row">Author Text:</th>
        <td>
            <input type="text" name="sticky_bar_author_text" value="<?php echo esc_attr(get_option('sticky_bar_author_text', 'by')); ?>" placeholder="by" />
            <p class="description">Enter the text to display before the author's name.</p>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row">Words per Minute:</th>
        <td>
            <input type="number" name="sticky_bar_words_per_minute" value="<?php echo esc_attr(get_option('sticky_bar_words_per_minute', 200)); ?>" min="1" step="1" />
            <p class="description">Enter the average words per minute to calculate the time to read.</p>
                    </td>
                </tr>
            </table>

            <p class="submit">
                <input type="submit" name="sticky_bar_settings_submit" class="button-primary" value="Save Settings">
            </p>
        </form>

    </div>

    <?php
}
