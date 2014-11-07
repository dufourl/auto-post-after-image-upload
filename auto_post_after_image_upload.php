<?php
/**
 * @package Auto Post With Image Upload
 * @version 1.0
 */
/*
Plugin Name: Auto Post With Image Upload
Plugin URI: http://wordpress.org/extend/plugins/auto-post-after-image-upload/
Description: This plugin will provide you the facility to create automated post when you will upload an image to your wordpress media gallery. Each time after uploading one media file upload one post will be created with attached this uploaded image automatically
Author: G. M. Shaharia Azam
Version: 1.0
Author URI: http://www.shahariaazam.com/
*/

add_action('add_attachment', 'auto_post_after_image_upload'); // Wordpress Hook

function auto_post_after_image_upload($attachId)
{

    $attachment = get_post($attachId);
    $image = wp_get_attachment_image_src( $attachId, 'large');
    $image_tag = '<p><img src="'.$image[0].'" /></p>';

    $postData = array(
        'post_title' => $attachment->post_title,
        'post_type' => 'post',
        'post_content' => $image_tag . $attachment->post_title,
        'post_category' => array('0'),
        'post_status' => 'publish'
    );

    $post_id = wp_insert_post($postData);

    // attach media to post
    wp_update_post(array(
        'ID' => $attachId,
        'post_parent' => $post_id,
    ));

    set_post_thumbnail($post_id, $attachId);

    return $attachId;
}
