<?php

 defined('ABSPATH') or die('Hey, You can\'t access this directly.');

// Callback function to render the submenu page content
function render_plugin_background_settings_page() { ?>
     <div class="wrap">
        <?php settings_errors();?>
        <h2>Confetti Popup Settings</h2>
        <form method="post" action="options.php">
            <?php
            settings_fields('confetti-animation');
            do_settings_sections('confetti-animation');
            submit_button();
            ?>
        </form>
    </div>
    <?php

    if (isset($_POST['submit_image'])) {
        // Handle media upload for the background image
        $attachment_id = (int) $_POST['background_image_id'];
        $background_image_url = $attachment_id ? wp_get_attachment_url($attachment_id) : '';

        // Update the background image URL in the plugin's options
        update_option('popup_background_image', $background_image_url);
        echo '<div class="updated"><p>Background image updated successfully!</p></div>';
    }

    if (isset($_POST['remove'])) {
        // Remove the background image URL from the plugin's options
        update_option('popup_background_image', ''); 
        echo '<div class="updated"><p>Background image removed!</p></div>';
    }

    // Get the background image URL from the saved option
    $background_image_url = get_option('popup_background_image'); 

    // Display the form to set the background image
    ?>
    <div class="wrap">
        <h2>Popup Background Image Setting</h2>
        <form method="post" action="" enctype="multipart/form-data">
            <!-- <label for="background_image_upload">Upload Background Image:</label><br>
            <input type="file" id="background_image_upload" name="background_image_upload"><br><br> -->
            <div id="background_image_preview"></div><br>
            <?php
            // Display the selected image if already set
            if ($background_image_url) {
                echo '<img src="' . esc_url($background_image_url) . '" style="max-width: 300px; border-radius:"50px"><br><br>';
                echo '<input type="submit" name="remove" value="Remove Image" class="button">';
            }
            // Add Media Library Upload Button
            echo '<button class="button" id="upload_background_image">Choose from Media Library</button>';
            ?>
            <input type="hidden" id="background_image_id" name="background_image_id" value="<?php echo esc_attr($attachment_id); ?>">
            <input type="submit" name="submit_image" value="Save Image" class="button button-primary">
        </form>
    </div>

    <script>
        jQuery(document).ready(function($) {
            // Media Library Upload Functionality
            $('#upload_background_image').on('click', function(e) {
                e.preventDefault();
                var image = wp.media({
                    title: 'Upload Background Image',
                    multiple: false
                }).open().on('select', function(e) {
                    var uploadedImage = image.state().get('selection').first();
                    var image_url = uploadedImage.toJSON().url;
                    var image_id = uploadedImage.toJSON().id;

                    $('#background_image_preview').html('<img src="' + image_url + '" style="max-width: 300px;">');
                    $('#background_image_id').val(image_id);
                });
            });
        });
    </script>
    <?php
}

// Add inline styles to set background image
function custom_background_image_styles() {
    $background_image_url = get_option('popup_background_image');

    if ($background_image_url) {
        echo '<style type="text/css">
            #confetti-popup {
                background-image: url("' . esc_url($background_image_url) . '");
                background-position: center;
                background-repeat: no-repeat;
                background-size: cover;
            }
        </style>';
    }
}
add_action('wp_head', 'custom_background_image_styles');

// Enqueue necessary scripts for media uploader
function enqueue_media_uploader() {
    if (!did_action('wp_enqueue_media')) {
        wp_enqueue_media();
    }
}
add_action('admin_enqueue_scripts', 'enqueue_media_uploader');
