<?php

 defined('ABSPATH') or die('Hey, You can\'t access this directly.');

class Confetti_Settings {
   
    public function confetti_settings_page() {

    if (isset($_POST['activate_confetti'])) {
        update_option('confetti_active', '1'); // Set confetti activation option to 1
    } elseif (isset($_POST['deactivate_confetti'])) {
        update_option('confetti_active', '0'); // Set confetti activation option to 0
    }

    $confetti_active = get_option('confetti_active');
    ?>
    <div class="wrap">
        <?php settings_errors(); ?>
        <h2> Welcome to Confetti Fall Animation</h2>
        <div class="cfa-text">
            <p> "Confetti Fall Animation" is a WordPress plugin that makes your website look more fun.</p>
            <p> It adds falling confetti to your blog or web pages, which is great for celebrating birthdays, holidays, promotions, or any special events.You can easily install and use it to make your website more enjoyable for your visitors.</p>
            <p> Activate the confetti fall animation on HomePage by clicking the button below or Use the shortcode <b>[confetti-fall-animation delay="1" time="25"]</b> on any (individual) post or page to start a falling confetti animation.</p>

            <form method="post" action="">
                <?php if ($confetti_active !== '1') { ?>
                    <button type="submit" name="activate_confetti" class="button button-primary">Active</button><br>
                    <small style="font-size: 12px; color: red;">Confetti Fall Animation is not activeh. Click on active button to activate the animation</small>
                <?php } else { ?>
                    <button type="submit" name="deactivate_confetti" class="button button-secondary">Deactive</button><br>
                    <small style="font-size: 12px; color: green;">Congratulation - Confetti Fall Animation is successfully activated on Homepage</small>
                    <a href="<?php echo site_url(); ?>" target="_blank" style="font-size: 12px;">Go to HomePage</a>
                <?php } ?>
            </form>
        </div>
    </div>
    <?php
    }
}

