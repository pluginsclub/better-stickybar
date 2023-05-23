<?php
/**
 * Plugin Name:       Better StickyBar
 * Plugin URI:        https://plugins.club/free-wordpress-plugins/better-stickybar/
 * Description:       Dispay a sticky bar at the bottom or top of posts, pages or products that shows category, post title, author, time needed to read article, share buttons and previous/next post links.
 * Version:           1.0
 * Author:            plugins.club
 * Author URI:        https://plugins.club
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Requires at least: 5.0
 * Tested up to: 	  6.2.1
*/

// Don't call the file directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Include the settings page
require_once plugin_dir_path(__FILE__) . 'includes/sticky-bar-settings.php';

// Add Settings page to the menu
function sticky_bar_plugin_menu() {
    add_options_page('Better StickyBar', 'Better StickyBar', 'manage_options', 'sticky-bar-settings', 'pluginsclub_sticky_bar_settings_page');
}
add_action('admin_menu', 'sticky_bar_plugin_menu');



// Enqueue the plugin's CSS and JavaScript files
function pluginsclub_sticky_bar_enqueue_scripts() {
    //wp_enqueue_style( 'sticky-bar-style', plugin_dir_url( __FILE__ ) . 'includes/css/sticky-bar.css', array(), '1.3.3' );
    wp_enqueue_script( 'sticky-bar-script', plugin_dir_url( __FILE__ ) . 'includes/js/sticky-bar.js', array( 'jquery' ), '1.1', true );
    //wp_enqueue_script( 'sticky-bar-prev-script', plugin_dir_url( __FILE__ ) . 'includes/js/prev-next-links.js', array( 'jquery' ), '1.4', true );
    wp_enqueue_style('dashicons');

}

add_action( 'wp_enqueue_scripts', 'pluginsclub_sticky_bar_enqueue_scripts' );

// Based on the top or bottom setting load different css files
$sticky_bar_position = get_option('sticky_bar_position', 'bottom');

if ($sticky_bar_position === 'top') {
    wp_enqueue_style('sticky_bar_styles', plugin_dir_url(__FILE__) . 'includes/css/sticky-bar-top.css', array(), '1.4.6' );
} else {
    wp_enqueue_style('sticky_bar_styles', plugin_dir_url(__FILE__) . 'includes/css/sticky-bar.css', array(), '1.4.6' );
}




// Display the sticky bar HTML at the bottom of posts


function pluginsclub_sticky_bar_display() {
    $selected_post_types = get_option('sticky_bar_display_post_types', array('post'));
    if ( is_singular($selected_post_types) || is_page(2234) ) {
        // Get the settings from the database
        $category_enabled = get_option('sticky_bar_category', true);
        $title_enabled = get_option('sticky_bar_title', true);
        $author_enabled = get_option('sticky_bar_author', true);
        $date_enabled = get_option('sticky_bar_date', true);
        $time_to_read_enabled = get_option('sticky_bar_time_to_read', true);
        $comments_enabled = get_option('sticky_bar_comments', true);
        $sharing_buttons_enabled = get_option('sticky_bar_sharing_buttons', true);
        $prev_next_links_enabled = get_option('sticky_bar_prev_next_links', true);
        // Get the background color setting
        $category_bg_color = get_option('sticky_bar_category_bg_color', '#0074a1');
        $dark_mode_enabled = get_option('sticky_bar_dark_mode', false);

    $time_to_read_text = get_option('sticky_bar_time_to_read_text', 'min');
    $author_text = get_option('sticky_bar_author_text', 'by');
    $words_per_minute = intval(get_option('sticky_bar_words_per_minute', 200));

    // Save the settings for "time to read" text, author text, and words per minute
    register_setting('sticky_bar_settings_group', 'sticky_bar_time_to_read_text', 'sanitize_text_field');
    register_setting('sticky_bar_settings_group', 'sticky_bar_author_text', 'sanitize_text_field');
    register_setting('sticky_bar_settings_group', 'sticky_bar_words_per_minute', 'intval');

        
        $post_id = get_the_ID();
        $category = get_the_category_list( ', ', '', $post_id );
        if (is_page(2234) ) {
           $category = 'LIVE DEMO' ;
    }
        $title = get_the_title( $post_id );
        $date = get_the_date( '', $post_id );
        $author = get_the_author_meta( 'display_name', get_post_field( 'post_author', $post_id ) );
        $author_avatar = get_avatar($author, 16); // Size of the avatar
        $author_avatar = str_replace('<img', '<img style="border-radius: 15px; vertical-align: text-bottom;"', $author_avatar);
        $content = get_post_field( 'post_content', $post_id );
        $word_count = str_word_count( strip_tags( $content ) );
        $reading_time = ceil( $word_count / $words_per_minute ); // Calculate time to read
        $images_path = plugin_dir_url( __FILE__ ) . 'includes/img/';

        $prev_post = get_previous_post();
        $next_post = get_next_post();
        $prev_title = $prev_post ? get_the_title( $prev_post ) : '';
        $prev_date = $prev_post ? get_the_date( '', $prev_post ) : '';
        $next_title = $next_post ? get_the_title( $next_post ) : '';
        $next_date = $next_post ? get_the_date( '', $next_post ) : '';
        ?>
<script>
jQuery(function($) {
  var prevPopupContent =
    '<div class="sticky-bar-popup-content">' +
    '<p><?= $prev_title ?></p>' +
    '<span class="sticky-bar-popup-date"><?= $prev_date ?></span>' +
    '</div>';

  var nextPopupContent =
    '<div class="sticky-bar-popup-content">' +
    '<p><?= $next_title ?></p>' +
    '<span class="sticky-bar-popup-date"><?= $next_date ?></span>' +
    '</div>';

  $('.sticky-bar-navigation a').eq(0).hover(function() {
    if ('<?= get_the_post_thumbnail_url( $prev_post, 'full' ) ?>') {
      var prevImage = '<div class="sticky-bar-popup-image"><img src="<?= get_the_post_thumbnail_url( $prev_post, 'full' ) ?>" alt="Previous Post Image"></div>';
      $(this).append('<div class="sticky-bar-popup">' + prevImage + prevPopupContent + '</div>');
    } else {
      $(this).append('<div class="sticky-bar-popup">' + prevPopupContent + '</div>');
    }
  }, function() {
    $(this).find('.sticky-bar-popup').remove();
  });

  $('.sticky-bar-navigation a').eq(1).hover(function() {
    if ('<?= get_the_post_thumbnail_url( $next_post, 'full' ) ?>') {
      var nextImage = '<div class="sticky-bar-popup-image"><img src="<?= get_the_post_thumbnail_url( $next_post, 'full' ) ?>" alt="Next Post Image"></div>';
      $(this).append('<div class="sticky-bar-popup">' + nextImage + nextPopupContent + '</div>');
    } else {
      $(this).append('<div class="sticky-bar-popup">' + nextPopupContent + '</div>');
    }
  }, function() {
    $(this).find('.sticky-bar-popup').remove();
  });
});
</script>




<?php

if ($dark_mode_enabled) {
    // Load the dark mode CSS file
    wp_enqueue_style('sticky_bar_dark_mode', plugin_dir_url(__FILE__) . 'includes/css/dark-mode.css');
}

        // Start building the sticky bar HTML
        $sticky_bar_html = '<div id="progress-line" style="background-color: ' . esc_attr($category_bg_color) . ';"></div>';
        $sticky_bar_html .= '<div id="sticky-bar">';



        // Display the enabled features based on settings
        if ($category_enabled) {
          $sticky_bar_html .= '<div class="category-container" style="background-color: ' . esc_attr($category_bg_color) . ';">';
          $sticky_bar_html .= '<span class="sticky-bar-category">' . $category . '</span>';
          $sticky_bar_html .= '</div>';
}


        if ($title_enabled) {
            $sticky_bar_html .= '<span class="sticky-bar-title">' . $title . '</span>';
        }

        if ($author_enabled) {
            $sticky_bar_html .= '<span class="sticky-bar-author">' . ' &nbsp;'  . $author_text . ' ' . $author_avatar . ' ' . $author . '</span>';

        }

        if ($date_enabled) {
            $sticky_bar_html .= '<span class="sticky-bar-date">' . $date . '</span>';
        }

        if ($time_to_read_enabled) {
            $sticky_bar_html .= '<span class="sticky-bar-reading-time"> - ' . $time_to_read_text .' '. ceil($word_count / $words_per_minute) . ' min</span>';
        }

        if ($comments_enabled) {
            $sticky_bar_html .= '<div class="sticky-bar-comments">';
            $sticky_bar_html .= '<span class="dashicons dashicons-admin-comments"></span>';
            $sticky_bar_html .= '<span class="comment-count" style="background-color: ' . esc_attr($category_bg_color) . ';">' . get_comments_number() . '</span>';
            $sticky_bar_html .= '</div>';
        }

        if ($prev_next_links_enabled) {
    $sticky_bar_html .= '<div class="sticky-bar-navigation">';
    $sticky_bar_html .= get_previous_post_link('%link', '«');
    $sticky_bar_html .= get_next_post_link('%link', '»');
    $sticky_bar_html .= '</div>';
}


        if ($sharing_buttons_enabled) {
            $sticky_bar_html .= '<div class="sticky-bar-sharing-buttons">';
            $sticky_bar_html .= '<a class="facebook" href="https://www.facebook.com/sharer/sharer.php?u=' . get_permalink() . '" target="_blank"><img src="' . $images_path . 'facebook.png" alt="Share on Facebook"></a>';
            $sticky_bar_html .= '<a class="twitter" href="https://twitter.com/intent/tweet?url=' . get_permalink() . '&text=' . urlencode($title) . '" target="_blank"><img src="' . $images_path . 'twitter.png" alt="Share on Twitter"></a>';
            $sticky_bar_html .= '<a class="pinterest" href="https://pinterest.com/pin/create/button/?url=' . get_permalink() . '&media=' . get_the_post_thumbnail_url($post_id, 'full') . '&description=' . urlencode($title) . '" target="_blank"><img src="' . $images_path . 'pinterest.png" alt="Pin on Pinterest"></a>';
            $sticky_bar_html .= '<a class="linkedin" href="https://www.linkedin.com/shareArticle?url=' . get_permalink() . '&title=' . urlencode($title) . '&summary=' . urlencode($excerpt) . '&source=' . urlencode(get_bloginfo('name')) . '" target="_blank"><img src="' . $images_path . 'linkedin.png" alt="Share on LinkedIn"></a>';
            $sticky_bar_html .= '</div>';
        }

        // Close the sticky bar HTML
        $sticky_bar_html .= '</div>';

        // Output the sticky bar HTML
        echo $sticky_bar_html;
    }
}

add_action( 'wp_footer', 'pluginsclub_sticky_bar_display' );

