<?php

/*
  Plugin Name: Media Tagz Gallery
  Version: 1.1
  Plugin URI: http://danielscott.design/media-tag-gallery/
  Description: A media tag extension to add gallery functionality with modal popups
  Author: Daniel Murphy
  Author URI: http://danielscott.design
 */

defined('ABSPATH') or die('No script kiddies please!');

// load modal content via ajax
function enqueue_tagz_scripts() {
    wp_register_script('bootstrapmodal', plugins_url('js/bootstrap-modal.min.js', __FILE__), array('jquery'), '', true);
    wp_enqueue_script('bootstrapmodal');

    wp_register_script('loadtagzmodal', plugins_url('js/load-tagz-modal-ajax.js', __FILE__), array('jquery'), '', true);
    wp_enqueue_script('loadtagzmodal');
    wp_localize_script('loadtagzmodal', 'loadtagzmodalObj', array('ajax_url' => admin_url('admin-ajax.php')));

    wp_register_script('loadtagzmedia', plugins_url('js/load-tagz-media-ajax.js', __FILE__), array('jquery'), '', true);
    wp_enqueue_script('loadtagzmedia');
    wp_localize_script('loadtagzmedia', 'loadtagzmediaObj', array('ajax_url' => admin_url('admin-ajax.php')));
}

add_action('wp_enqueue_scripts', 'enqueue_tagz_scripts');

function enqueue_tagz_styles() {
    wp_register_style('bootstrapmodal', plugins_url('css/bootstrapmodal.css', __FILE__), array(), '1.0', 'all');
    wp_enqueue_style('bootstrapmodal');
}

add_action('wp_enqueue_scripts', 'enqueue_tagz_styles');

/**
 * load_tagz_modal
 *
 * Returns an ajax response that loads a modal - depenancies: bootstrap modal
 */
function load_tagz_modal() {
    $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
    $attachment_fields = tagz_get_attachment($id);
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
        echo '<div class="modal-link"><a href="' . $media_link . '">' . $media_link . '</a></div>';
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
add_action('wp_ajax_load_tagz_modal_ajax', 'load_tagz_modal');
add_action('wp_ajax_nopriv_load_tagz_modal_ajax', 'load_tagz_modal');

/**
 * load_tagz_media
 *
 * Returns an ajax response that loads more media (if the current set has more)
 */
function load_tagz_media() {
    $offset = filter_var($_POST["offset"], FILTER_SANITIZE_NUMBER_INT);
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
add_action('wp_ajax_load_tagz_media_ajax', 'load_tagz_media');
add_action('wp_ajax_nopriv_load_tagz_media_ajax', 'load_tagz_media');

/**
 * get_tag_album
 *
 * Returns an album cover that links to the media tags taxonomy page for that tag (a tag gallery)
 *
 * @param string $tagName The tag of which album to be shown
 */
function tagz_get_album($tagName) {
    if (function_exists('get_attachments_by_media_tags')) {
        $album = get_attachments_by_media_tags('media_tags=' . $tagName . '&size=thumbnail&numberposts=1&orderby=ID&order=DESC');
        if ($album !== null) {
            $image_title = $album[0]->post_title;
            $taggedThumbnail = wp_get_attachment_thumb_url($album[0]->ID, 'thumbnail');
            $output = '';
            $output .= '<div class="album-outside-wrapper">';
            $output .= '<div class="album-wrapper">';
            $output .= '<a href="' . get_home_url() . '/media-tags/' . $tagName . '">';
            $output .= '<div class="album-icon-background"></div>';
            $output .= '<div class="album-icon">';
            $output .= '<img src="' . $taggedThumbnail . '" alt="' . $image_title . '" />';
            $output .= '</div>';
            $output .= '<div class="album-title">' . $tagName . '</div>';
            $output .= '</a></div></div>';
        } else {
            $output = '<div style="padding:10px;border:1px solid red;"><p>Error: No album found. '
                    . 'Make sure you have tags set for the album requested.</p></div>';
        }
    } else {
        $output = '<div style="padding:10px;border:1px solid red;"><p><span '
                . 'style="font-weight:bold;color:red;font-style:italic;">Error:</span> '
                . 'Media Tags plugin not found. The Media Tags plugin is required for the '
                . 'Media Tagz Gallery to work. Get it here: <a href="https://wordpress.org/plugins/media-tags/">'
                . 'https://wordpress.org/plugins/media-tags/</a></p></div>';
    }

    return $output;
}

function tagz_get_album_shortcode($atts) {
    $atts = shortcode_atts(array(
        'tag' => '',
            ), $atts, 'tagz-album');
    if ($atts['tag'] == '') {
        $output = '<div style="padding:10px;border:1px solid red;"><p><span style="color:red;">You must add tags for this to work. Here\'s and example:</span><br />'
                . '[tagz-album tag=landscape]</p></div>';
    } else {
        $output = tagz_get_album($atts['tag']);
    }

    return $output;
}

add_shortcode('tagz-album', 'tagz_get_album_shortcode');

/**
 * tagz_get_media_by_tags
 *
 * get a list of media based on tag names: example: get_mediatag_by_tags('weddings,automobilia,wildlife,architecture,people,landscape','6',true);
 *
 * @param string $tagNames list of tag names to return in the set
 * @param int|string $limit max number of items to return - optional - default limit is 18
 * @param string $showmore whether or not to show more in request - optional - defualt is set to true
 * @param string $showtitle whether or not to show the image title - optional - defualt is set to true
 */
function tagz_get_media_by_tags($tagNames, $limit = 18, $showmore = true, $showtitle = true) {
    $media = get_attachments_by_media_tags('media_tags=' . $tagNames . '&size=thumbnail&numberposts=' . $limit . '&orderby=ID&order=DESC');
    $allmedia = get_attachments_by_media_tags('media_tags=' . $tagNames . '&size=thumbnail&orderby=ID&order=DESC');
    $output = '';
    if ($showmore === 'no-more') {
        $showmore = false;
    }
    if ($showtitle === 'no-title') {
        $showtitle = false;
    }
    if (function_exists('get_attachments_by_media_tags')) {
        if ($media !== null) {
            $output = '<div class="mediatag-group-wrapper" data-tag-group="' . $tagNames . '">';
            $output .= '<div class="mediatag-group-inner clearfix">';
            foreach ($media as $mediaItem) {
                $mediaTitle = $mediaItem->post_title;
                $taggedThumbnail = wp_get_attachment_thumb_url($mediaItem->ID, 'thumbnail');
                $output .= '<div class="col-xs-6 col-sm-4 col-lg-2">';
                $output .= '<div class="album-outside-wrapper">';
                $output .= '<div class="album-wrapper">';
                $output .= '<a href="" class="modal_popup" data-id="' . $mediaItem->ID . '">';
                if ($showtitle == true) {
                    $output .= '<div class="album-icon-background"></div>';
                    $output .= '<div class="album-icon">';
                    $output .= '<img src="' . $taggedThumbnail . '" alt="' . $mediaTitle . '" />';
                    $output .= '</div>';
                    $output .= '<div class="album-title">' . $mediaTitle . '</div>';
                    $output .= '</a>';
                } else {
                    $output .= '<div class="album-icon">';
                    $output .= '<img src="' . $taggedThumbnail . '" alt="' . $mediaTitle . '" />';
                    $output .= '</div>';
                    $output .= '</a>';
                }
                $output .= '</div></div></div>';
            }
            $output .= '</div>';
            if ($showmore == true && count($allmedia) > $limit) {
                $output .= '<div class="text-center"><button class="btn btn-primary clearfix load-more-media">load more</button></div>';
                $output .= '<div class="dynamic-total" data-total=""></div>';
            }
            $output .= '</div>';
        } else {
            $output = '<div style="padding:10px;border:1px solid red;"><p>Error: No images found. '
                    . 'Make sure you have tags set for the images requested.</p></div>';
        }
    } else {
        $output = '<div style="padding:10px;border:1px solid red;"><p><span '
                . 'style="font-weight:bold;color:red;font-style:italic;">Error:</span> '
                . 'Media Tags plugin not found. The Media Tags plugin is required for the '
                . 'Media Tagz Gallery to work. Get it here: <a href="https://wordpress.org/plugins/media-tags/">'
                . 'https://wordpress.org/plugins/media-tags/</a></p></div>';
    }

    return $output;
}

function tagz_get_media_by_tags_shortcode($atts) {
    $atts = shortcode_atts(array(
        'tags' => '',
        'limit' => '18',
        'show-more' => 'true',
        'show-title' => 'true',
            ), $atts, 'tagz-gallery');
    if ($atts['tags'] == '') {
        $output = '<div style="padding:10px;border:1px solid red;"><p><span style="color:red;">You must add tags for this to work. Here\'s and example:</span><br />'
                . '[tagz-gallery tags=landscape,wildlife limit=18]</p></div>';
    } else {
        $output = tagz_get_media_by_tags($atts['tags'], $atts['limit'], $atts['show-more'], $atts['show-title']);
    }

    return $output;
}

add_shortcode('tagz-gallery', 'tagz_get_media_by_tags_shortcode');

/**
 * tagz_get_random_image
 *
 * gets random images based on most recent in requested tag(s) using the requested size 
 *
 * @param string $tagNames image tags to be randomized
 * @param string $size : size of image to be randomized
 */
function tagz_get_random_image($tagNames, $size) {
    if (function_exists('get_attachments_by_media_tags')) {
        $output = '';
        $album = get_attachments_by_media_tags('media_tags=' . $tagNames . '&orderby=ID&order=DESC');
        $count = count($album);
        $randIndex = rand('0', $count - 1);
        $image_array = image_downsize($album[$randIndex]->ID, $size);
        $image_path = $image_array[0];
        if ($image_path !== '' && $image_path !== null) {
            $output = $image_path;
        } else {
            $output = '<div style="padding:10px;border:1px solid red;"><p>Error: No images found. '
                    . 'Make sure you have tags set for the images requested.</p></div>';
        }
    } else {
        $output = '<div style="padding:10px;border:1px solid red;"><p>'
                . '<span style="font-weight:bold;color:red;font-style:italic;">'
                . 'Error:</span> Media Tags plugin not found. The Media Tags plugin '
                . 'is required for the Media Tagz Gallery to work. Get it here: '
                . '<a href="https://wordpress.org/plugins/media-tags/">https://wordpress.org/plugins/media-tags/</a>'
                . '</p></div>';
    }
    return $output;
}

/**
 * tagz_get_random_image_shortcode
 *
 * add shortcode for the tagz random image
 *
 * @param array $atts - shortcode attributes
 * 
 */
function tagz_get_random_image_shortcode($atts) {
    $atts = shortcode_atts(array(
        'tags' => '',
        'size' => 'thumbnail'
            ), $atts, 'tagz-rand-img');
    if ($atts['tags'] == '') {
        $output = '<div style="padding:10px;border:1px solid red;"><p><span style="color:red;">You must add a media tag for this to work. Here\'s and example:</span><br />'
                . '[tagz-rand-img tags=landscape,wildlife size=thumbnail]</p></div>';
    } else {
        $output = tagz_get_random_image($atts['tags'], $atts['size']);
    }

    return $output;
}

add_shortcode('tagz-rand-img', 'tagz_get_random_image_shortcode');

/**
 * tagz_get_attachment
 *
 * returns an array with the attachment fields: alt, caption, description, href, src, title
 *
 * @param string|int $attachment_id the id of the attachment
 * @return array
 */
function tagz_get_attachment($attachment_id) {
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
 * @param string $fields The fields to be created
 * @param sting|int $post 
 * @return array
 */
function tagz_make_attachment_meta_url($fields, $post) {
    $meta = get_post_meta($post->ID, 'meta_link', true);
    $fields['meta_link'] = array(
        'label' => 'Media Link URL',
        'input' => 'text',
        'value' => $meta,
        'show_in_edit' => true,
    );
    return $fields;
}

add_filter('attachment_fields_to_edit', 'tagz_make_attachment_meta_url', 10, 2);

/**
 * tagz_update_attachment_meta_url
 * 
 * Update attachment meta url on save
 */
function tagz_update_attachment_meta_url($attachment) {
    global $post;
    update_post_meta($post->ID, 'meta_link', $attachment['attachments'][$post->ID]['meta_link']);
    return $attachment;
}

add_filter('attachment_fields_to_save', 'tagz_update_attachment_meta_url', 4);

/**
 * tagz_update_attachment_meta_url_ajax
 * 
 * Update attachment meta url via ajax
 * 
 */
function tagz_update_attachment_meta_url_ajax() {
    $post_id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
    $meta = filter_var($_POST['attachments'][$post_id]['meta_link'], FILTER_SANITIZE_URL);
    update_post_meta($post_id, 'meta_link', $meta);
    clean_post_cache($post_id);
}

add_action('wp_ajax_save-attachment-compat', 'tagz_update_attachment_meta_url_ajax', 0, 1);
