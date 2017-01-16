<?php
/*
Plugin Name: Publish post button
Description: Add publish post button to darft & pending post preview
Version: 1.0
Author: Evgeny Stefanenko
Author URI: www.millenniumfoto.com
*/

add_filter('the_content', 'show_publish_button', 10);
add_action('init', 'pop_publish_post');

function show_publish_button($content)
{
    global $post;
    //die(get_post_status($post->ID));
    if (current_user_can('manage_options') && ((get_post_status($post->ID) == 'pending') || (get_post_status($post->ID) == 'draft')) && $content != "" && $post->post_type === 'post') {
        $content = '<form action="" method="POST" name="front_end_publish"><input id="pid" type="hidden" name="pid" value="' . $post->ID . '" />' .
            '<input id="FE_PUBLISH" type="hidden" name="FE_PUBLISH" value="FE_PUBLISH" />' .
            '<input id="submit" type="submit" name="submit" value="Publish post" />' .
            wp_nonce_field('pop_action', 'pop_field') .
            '</form>' . $content;
    }
    return $content;
}

function pop_publish_post()
{
    if (isset($_POST['FE_PUBLISH']) && $_POST['FE_PUBLISH'] == 'FE_PUBLISH') {
        if (!wp_verify_nonce($_POST['pop_field'], 'pop_action')) {
            print 'Error';
            exit;
        }
        if (isset($_POST['pid']) && !empty($_POST['pid'])) {
            $post_id = (int)$_POST['pid'];
            wp_update_post(array('ID' => $post_id, 'post_status' => 'publish'));
        }
        wp_redirect(get_permalink($post_id));
        exit;
    }
}