<?php

/*
  Plugin Name: Media Tag Gallery
  Version: 1.0
  Plugin URI: http://danielscott.design/media-tag-gallery/
  Description: A media tag extension to add gallery functionality with modal popups
  Author: Daniel Murphy
  Author URI: http://danielscott.design
 */

//---------------------//
// load modal content via ajax
//---------------------//

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

function enqueue_mediatag_gallery_scripts() {
    wp_register_script('bootstrapmodal', plugins_url('js/bootstrap-modal.min.js', __FILE__), array('jquery'), '', true);
    wp_enqueue_script('bootstrapmodal');

    wp_register_script('loadmodal', plugins_url('js/load-modal-ajax.js', __FILE__), array('jquery'), '', true);
    wp_enqueue_script('loadmodal');
    wp_localize_script('loadmodal', 'loadmodalObj', array('ajax_url' => admin_url('admin-ajax.php')));

    wp_register_script('loadmoremedia', plugins_url('js/load-more-media-ajax.js', __FILE__), array('jquery'), '', true);
    wp_enqueue_script('loadmoremedia');
    wp_localize_script('loadmoremedia', 'loadmoremediaObj', array('ajax_url' => admin_url('admin-ajax.php')));
}

add_action('wp_enqueue_scripts', 'enqueue_mediatag_gallery_scripts');

function enqueue_media_gallery_styles() {
    wp_register_style('bootstrapmodal', plugins_url('css/bootstrapmodal.css', __FILE__), array(), '1.0', 'all');
    wp_enqueue_style('bootstrapmodal');
}

add_action('wp_enqueue_scripts', 'enqueue_media_gallery_styles');

/**
 * load_modal
 *
 * Returns an ajax response that loads a modal - depenancies: bootstrap modal
 */
function load_modal() {
    $id = sanitize_text_field($_POST["id"]);
    $attachment_fields = wp_get_attachment($id);
    $caption = $attachment_fields['caption'];
    $media_array = image_downsize($id, 'large');
    $media_path = $media_array[0];
    $media_meta = wp_get_attachment_metadata($id);
    $mediaiso = $media_meta['image_meta']['iso'];
    $shutter = $media_meta['image_meta']['shutter_speed'];
    $aperture = $media_meta['image_meta']['aperture'];
    $imageTitle = $attachment_fields['title'];
    $media_link = get_post_meta($id)['meta_link'][0];
    echo '<div class="modal-dialog" role="document">';
    echo '<div class="modal-content">';
    echo '<div class="modal-header">';
    echo '<h4 class="modal-title" id="myModalLabel">' . $imageTitle . '</h4>';
    echo '<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>';
    echo '</div>';
    echo '<div class="modal-body">';
    echo '<img src="' . $media_path . '" style="max-width:100%;" />';
    echo '</div>';
    echo '<div class="modal-footer">';
    if ($caption !== '') {
        echo '<div class="modal-caption">' . $caption . '</div>';
    }
    if ($media_link !== '' && $media_link !== null) {
        echo '<div class="modal-link"><a href="' . $media_link . '">'. $media_link .'</a></div>';
    }
    if ($mediaiso !== '0' || $shutter !== '0' || $aperture !== '0') {
        echo '<div class="modal-camera-meta"><h4>Camera Meta Info:</h4>';
        echo '<ul>';
        echo '<li>ISO: ' . $mediaiso . '</li>';
        echo '<li>Shutter speed: ' . $shutter . 's</li>';
        echo '<li>Aperture: f/' . $aperture . '</li>';
        echo '</ul>';
        echo '</div>';
    }
    
    echo '</div></div></div></div>';
    die();
}

// bind the modal with wordpress ajax
add_action('wp_ajax_load_modal_ajax', 'load_modal');
add_action('wp_ajax_nopriv_load_modal_ajax', 'load_modal');

/**
 * load_more_media
 *
 * Returns an ajax response that loads more media (if the current set has more)
 */
function load_more_media() {
    $offset = sanitize_text_field($_POST["offset"]);
    $tagNames = sanitize_text_field($_POST["tagNames"]);
    $media = get_attachments_by_media_tags('media_tags=' . $tagNames . '&size=thumbnail&numberposts=6&orderby=ID&order=DESC&offset=' . $offset . '');
    if ($media !== null) {
        foreach ($media as $mediaItem) {
            $mediaTitle = $mediaItem->post_title;
            $taggedThumbnail = wp_get_attachment_thumb_url($mediaItem->ID, 'thumbnail');
            $media_array = image_downsize($mediaItem->ID, 'large');
            $media_path = $media_array[0];
            $media_meta = wp_get_attachment_metadata($mediaItem->ID);
            $mediaiso = $media_meta['image_meta']['iso'];
            $shutter = $media_meta['image_meta']['shutter_speed'];
            $aperture = $media_meta['image_meta']['aperture'];
            echo '<div class="col-xs-6 col-sm-4 col-lg-2">';
            echo '<div class="album-outside-wrapper">';
            echo '<div class="album-wrapper">';
            echo '<a href="' . $media_path . '" class="modal_popup" data-title="' . $mediaTitle . '" data-iso="' . $mediaiso . '" data-shutter="' . $shutter . '" data-aperture="' . $aperture . '">';
            echo '<div class="album-icon">';
            echo '<img src="' . $taggedThumbnail . '" alt="' . $mediaTitle . '" />';
            echo '</div></a></div></div></div>';
        }
        die();
    } else {
        echo '<div class="no-media">End of media.</div>';
        die();
    }
}

// bind the modal with wordpress ajax
add_action('wp_ajax_load_more_media_ajax', 'load_more_media');
add_action('wp_ajax_nopriv_load_more_media_ajax', 'load_more_media');

/**
 * get_tag_album
 *
 * Returns an album cover that links to the media tags taxonomy page for that tag (a tag gallery)
 *
 * @param string $tagName The tag of which album to be shown
 */
function get_tag_album($tagName) {
    $album = get_attachments_by_media_tags('media_tags=' . $tagName . '&size=thumbnail&numberposts=1&orderby=ID&order=DESC');
    if ($album !== null) {
        $image_title = $album[0]->post_title;
        $taggedThumbnail = wp_get_attachment_thumb_url($album[0]->ID, 'thumbnail');
        echo '<div class="album-outside-wrapper">';
        echo '<div class="album-wrapper">';
        echo '<a href="' . get_home_url() . '/media-tags/' . $tagName . '">';
        echo '<div class="album-icon-background"></div>';
        echo '<div class="album-icon">';
        echo '<img src="' . $taggedThumbnail . '" alt="' . $image_title . '" />';
        echo '</div>';
        echo '<div class="album-title">' . $tagName . '</div>';
        echo '</a></div></div>';
    } else {
        echo '<p>Unable to retrieve the album based on the request. Make sure you are calling the correct tag and you have added media with that tag.</p>';
    }
}

/**
 * get_mediatag_by_tags
 *
 * get a list of media based on tag names: example: get_mediatag_by_tags('weddings,automobilia,wildlife,architecture,people,landscape','6',true);
 *
 * @param string $tagNames list of tag names to return in the set
 * @param int|string $limit max number of items to return - optional - default limit is 18
 * @param string $showmore whether or not to show more in request - optional - defualt is set to true
 * @param string $showtitle whether or not to show the image title - optional - defualt is set to true
 */
function get_mediatag_by_tags($tagNames, $limit = 18, $showmore = true, $showtitle = true) {
    $media = get_attachments_by_media_tags('media_tags=' . $tagNames . '&size=thumbnail&numberposts=' . $limit . '&orderby=ID&order=DESC');
    $allmedia = get_attachments_by_media_tags('media_tags=' . $tagNames . '&size=thumbnail&orderby=ID&order=DESC');
    echo '<div class="mediatag-group-wrapper" data-tag-group="' . $tagNames . '">';
    echo '<div class="mediatag-group-inner clearfix">';
    if ($showmore === 'show-no-more'){
        $showmore = false;
    }
    if ($showtitle === 'no-title'){
        $showtitle = false;
    }
    if ($media !== null) {
        foreach ($media as $mediaItem) {
            $mediaTitle = $mediaItem->post_title;
            $taggedThumbnail = wp_get_attachment_thumb_url($mediaItem->ID, 'thumbnail');
            echo '<div class="col-xs-6 col-sm-4 col-lg-2">';
            echo '<div class="album-outside-wrapper">';
            echo '<div class="album-wrapper">';
            echo '<a href="" class="modal_popup" data-id="' . $mediaItem->ID . '">';
            if ($showtitle == true) {
                echo '<div class="album-icon-background"></div>';
                echo '<div class="album-icon">';
                echo '<img src="' . $taggedThumbnail . '" alt="' . $mediaTitle . '" />';
                echo '</div>';
                echo '<div class="album-title">' . $mediaTitle . '</div>';
                echo '</a>';
            }
            else{
                echo '<div class="album-icon">';
                echo '<img src="' . $taggedThumbnail . '" alt="' . $mediaTitle . '" />';
                echo '</div>';
                echo '</a>';
            }
            echo '</div></div></div>';
        }
        echo '</div>';
        if ($showmore == true && count($allmedia) > $limit) {
            echo '<div class="text-center"><button class="btn btn-primary clearfix load-more-media">load more</button></div>';
            echo '<div class="dynamic-total" data-total=""></div>';
        }
        echo '</div>';
    } else {
        echo '<p>Woops! No media was found mathing the request :(</p>';
    }
}

/**
 * generate_mediatag_random_image_url
 *
 * generates random large images based on most recent image for each specified tag name
 *
 * @param string $tagNames image tags to be randomized
 * @param string $size : size of image to be randomized
 */
function generate_mediatag_random_image_url($tagNames, $size) {
    $album = get_attachments_by_media_tags('media_tags=' . $tagNames . '&orderby=ID&order=DESC');
    $count = count($album);
    $randIndex = rand('0', $count - 1);
    $image_array = image_downsize($album[$randIndex]->ID, $size);
    $image_path = $image_array[0];
    echo $image_path;
}

/**
 * wp_get_attachment
 *
 * returns an array with the attachment fields: alt, caption, description, href, src, title
 *
 * @param string|int $attachment_id the id of the attachment
 * @return array
 */
function wp_get_attachment($attachment_id) {
    $attachment = get_post($attachment_id);
    return array(
        'alt' => get_post_meta($attachment->ID, '_wp_attachment_image_alt', true),
        'caption' => $attachment->post_excerpt,
        'description' => $attachment->post_content,
        'href' => get_permalink($attachment->ID),
        'src' => $attachment->guid,
        'title' => $attachment->post_title
    );
}

/**
 * mediatag_gallery_attachment_fields
 * 
 * Adds attachement fields to the media attachment
 * 
 * @author kosinix https://gist.github.com/kosinix/5493051
 * 
 * @param string $fields The fields to be created
 * @param sting|int $post 
 * @return array
 */
function mediatag_gallery_attachment_fields( $fields, $post ) {
    $meta = get_post_meta($post->ID, 'meta_link', true);
    $fields['meta_link'] = array(
        'label' => 'Media Link URL',
        'input' => 'text',
        'value' => $meta,
        'show_in_edit' => true,
    );
    return $fields;
}
add_filter( 'attachment_fields_to_edit', 'mediatag_gallery_attachment_fields', 10, 2 );
/**
 * Update custom field on save
*/
function mediatag_gallery_update_attachment_meta($attachment){
    global $post;
    update_post_meta($post->ID, 'meta_link', $attachment['attachments'][$post->ID]['meta_link']);
    return $attachment;
}
add_filter( 'attachment_fields_to_save', 'mediatag_gallery_update_attachment_meta', 4);
/**
 * Update custom field via ajax
*/
function mediatag_gallery_xtra_fields() {
    $post_id = $_POST['id'];
    $meta = $_POST['attachments'][$post_id ]['meta_link'];
    update_post_meta($post_id , 'meta_link', $meta);
    clean_post_cache($post_id);
}
add_action('wp_ajax_save-attachment-compat', 'mediatag_gallery_xtra_fields', 0, 1);
