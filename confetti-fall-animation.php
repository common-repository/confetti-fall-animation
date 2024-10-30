<?php

/**
 * @package confetti-fall-animation
 * @version 1.3.0
 **/

/*
Plugin Name: Confetti Fall Animation
Plugin URI: https://wpdeveloperr.com/our-products/
Description:  Add a delightful falling confetti animation to your website. Welcome your visitors on special occasions such as new year, birthdays, festivals, promotions, or any other special events.
Author: WPDeveloperr
Author URI: https://wpdeveloperr.com/
Version: 1.3.0
License: GPLv2 or later
Text Domain: confetti-fall-animation
 */

defined('ABSPATH') or die('Hey, You can\'t access this directly.');

/* --- */
if (!function_exists("add_action")) {
    exit;
}
/* --- */

define("cfa_dir_url", plugin_dir_url(__FILE__));
define("cfa_dir_path", plugin_dir_path(__FILE__));
require_once cfa_dir_path . 'inc/popupBackgroundImage.php';
require_once cfa_dir_path . 'inc/confetti_settings.php';

/* --- */


function cfa_enqueue_scripts(){

    wp_enqueue_script("jquery");
    wp_enqueue_script("confetti-js",cfa_dir_url . "assets/js/confetti.min.js");
    wp_enqueue_script("confetti-fall-animation",cfa_dir_url . "assets/js/confetti-fall-animation.js",array("jquery", "confetti-js"));

    wp_enqueue_script('confetti-popup-script', cfa_dir_url . 'assets/js/popup-plugin.js', array('jquery'), '1.0', true);
    // Define JavaScript variable
    $delayInSeconds = get_option('confetti-popup-delay', 5); // Get delay time from WordPress settings
    wp_localize_script('confetti-popup-script', 'delayPopupSettings', array(
        'delayInSeconds' => $delayInSeconds
    ));
    wp_enqueue_style('custom-popup-style', cfa_dir_url . 'assets/css/popup-plugin.css', array(), '1.0');
}
add_action("wp_enqueue_scripts", "cfa_enqueue_scripts");

/* --- */
function confetti_backend_scripts(){
    wp_register_style( 'confetti-style', cfa_dir_url . 'assets/css/popup-plugin.css', array(), '1.0', 'all' );
    wp_enqueue_style('confetti-style');

}
add_action('admin_enqueue_scripts', 'confetti_backend_scripts');

/* --- */

// Activation and deactivation hooks
register_activation_hook(__FILE__, 'confetti_popup_activate');
register_deactivation_hook(__FILE__, 'confetti_popup_deactivate');

function confetti_popup_activate() {
    // Set an option to indicate that the welcome message has not been shown
    add_option('confetti_welcome_shown', false);
}

function confetti_popup_deactivate() {
    // Deactivation code here
}


// Add a menu item for the plugin settings
add_action('admin_menu', 'confetti_menu_func');

function confetti_menu_func() {
    $parent_url = 'confetti-animation';
    $icon_url = 'dashicons-buddicons-community';
    add_menu_page('CF Animation','CF Animation','manage_options', $parent_url,'confetti_animation_page', $icon_url, 50);
    add_submenu_page( $parent_url, 'Popup Settings', 'Popup Settings', 'manage_options', 'popup-settings', 'render_plugin_background_settings_page');

}

// Define Setting for Confetti Animation
function confetti_animation_page(){
    $confetti_settings = new Confetti_Settings();
    $confetti_settings->confetti_settings_page();

}


// Create settings fields and sections
function confetti_popup_settings() {
    // Add a section
    add_settings_section('confetti-popup-general','General Settings','confetti_popup_general_section_callback','confetti-animation');
    // Add a field for the popup content
    add_settings_field('confetti-popup-content','Add Popup Content','confetti_popup_content_callback','confetti-animation','confetti-popup-general');
    // Add a field for selecting pages
    add_settings_field('confetti-popup-pages','Select Page to Display Popup','confetti_popup_pages_callback','confetti-animation','confetti-popup-general');
    // Add a field for setting the popup display time
    add_settings_field('confetti-popup-delay','Popup Display Time (in seconds)','confetti_popup_delay_callback','confetti-animation','confetti-popup-general');
    // Register the setting for popup display time
    register_setting('confetti-animation', 'confetti-popup-delay');
    register_setting('confetti-animation', 'confetti-popup-content');
    register_setting('confetti-animation', 'confetti-popup-pages');
}
// Create settings fields and sections
add_action('admin_init', 'confetti_popup_settings');

// Callback functions for settings fields
function confetti_popup_general_section_callback() {
    echo 'Configure the confetti popup settings.';
}

function confetti_popup_content_callback() {
    $content = get_option('confetti-popup-content', '');
    $value  = !empty($content) ?  $content : '<h2>Welcome to Confetti Animation</h2>';
    echo '<textarea name="confetti-popup-content" rows="5" cols="50">' . esc_textarea($value) . '</textarea>';
    echo '<br><small>You can use html tags as well</small>';
}

function confetti_popup_pages_callback() {
    $pages = get_option('confetti-popup-pages', []);
    $all_pages = get_pages();

    echo '<select name="confetti-popup-pages[]">';
        echo '<option value="none"> None </option>';
    foreach ($all_pages as $page) {
        echo '<option value="' . esc_attr($page->ID) . '" ' . (in_array($page->ID, $pages) ? 'selected' : '') . '>' . esc_html($page->post_title) . '</option>';
    }
    echo '</select>';
}
function confetti_popup_display() {
    $content = get_option('confetti-popup-content', '');
    $pages = get_option('confetti-popup-pages', []);

    if (in_array(get_the_ID(), $pages)) {
        // Display the popup content here
        ?>
        <div id="confetti-popup" style="display: none;"> <?php echo wp_kses_post($content); ?> <a id="confetti-popup-close"> <i class="fa fa-close"></i>Close</a></div>
        <?php
    }
}

add_action('wp_footer', 'confetti_popup_display');

// Callback function for the delay field
function confetti_popup_delay_callback() {
    $delay = get_option('confetti-popup-delay', 5); // Default delay of 5 seconds
    echo '<input type="number" name="confetti-popup-delay" value="' . esc_attr($delay) . '" min="1" />';
}


// Function to display the welcome message
function confetti_welcome_message() {
    // Check if the welcome message has already been shown
    $welcome= get_option('confetti_welcome_shown', false);

    if ($welcome) {
        return true;
    }
        else{
        ?>
        <div class="notice notice-success is-dismissible">
            <p>Welcome to Confetti Fall Animation Plugin! Thank you for installing and activating our plugin.</p>
            <p>Now you can enjoy the confetti animation on your website with one click away.</p>
        </div>
        <?php

        // Set the flag to indicate that the welcome message has been shown
        update_option('confetti_welcome_shown', true);
    }
}

// Hook to display the welcome message
add_action('admin_notices', 'confetti_welcome_message');



// Add a meta box with a button to the page/post editor
function add_shortcode_button_meta_box() {
    add_meta_box(
        'shortcode_button_meta_box', // Meta box ID
        'Add CFA Shortcode', // Title of the meta box
        'shortcode_button_meta_box_content', // Callback function to display content
        'page', // Post type(s) where the meta box should appear (you can add more post types if needed)
        'side', // Context: 'normal', 'advanced', or 'side'
        'high' // Priority: 'high', 'core', 'default', or 'low'
    );
}
add_action('add_meta_boxes', 'add_shortcode_button_meta_box');
// Callback function to display content inside the meta box
function shortcode_button_meta_box_content($post) {
    // Output HTML for the button
    echo '<button id="add-shortcode-button" class="button">Add CF Animation Shortcode</button>';
}

// JavaScript to handle button click and insert shortcode
function add_shortcode_button_script() {
    ?>
    <script>
        jQuery(document).ready(function($) {
            $('#add-shortcode-button').on('click', function(e) {
                e.preventDefault();
                // Modify this line to insert your shortcode into the editor
                var shortcode = '[confetti-fall-animation delay="1" time="25"]';
                wp.media.editor.insert(shortcode);
            });
        });
    </script>
    <?php
}
add_action('admin_footer', 'add_shortcode_button_script');




// Add confetti to homepage based on the option
add_action('wp', 'add_confetti_to_homepage');
function add_confetti_to_homepage() {
    $confetti_active = get_option('confetti_active');

    if (is_front_page() && $confetti_active) {
        echo do_shortcode('[confetti-fall-animation delay="1" time="25"]');
    }
}

add_shortcode("confetti-fall-animation", "cfa_html_view_pages");
function cfa_html_view_pages($props)
{
    if ($props) {
        $props = shortcode_atts(
            array(
                "delay"    => "",
                "time" => "",
            ), $props
        );
        $delay    = $props["delay"];
        $time = $props["time"];
        return "<div class=\"confetti-fall-animation\" data-delay=\"" . $delay . "\" data-time=\"" . $time . "\"></div>";
    } 
}
