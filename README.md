# Wordpress Media Tag Gallery
A Wordpress media tag gallery that extends the Media Tags plugin.

*[Media Tags](https://wordpress.org/plugins/media-tags/ "Media Tags")* is a great plugin that allows you to add 
tags to your media - something that should come out of the box. However, the **Media Tagz Gallery** takes it to 
another level by adding the ability to pull in your photos by tag names to create a simple, yet flexible way to 
great a gallery. No need for bloated image gallery plugins that just become outdated over time. 

## Features 

- gallery display by using media tags
- display a tag-based album using media tags - opens to a gallery page
- generate random image (url)
- "load more" button to load more images via ajax
- modal popup: loads large image via ajax
- modal displays camera meta (iso, f-stop, exposure)
- add hyperlink to media (displays in modal)
- Bootrap 3 html syntax ready
- no css style - a blank canvas (with the exception of the modal style)
- shortcodes enabled

## Installation

1. Download and install the Media Tags plugin here: *[Media Tags](https://wordpress.org/plugins/media-tags/ "Media Tags")*
2. Upload the Media Tagz Gallery plugin files to the `/wp-content/plugins/plugin-name` directory, or install the 
plugin through the WordPress plugins screen directly.
3. Activate the plugin through the 'Plugins' screen in WordPress

### Template Setup

1. Inject the modal popup via ajax: After you have installed both Media Tags and Media Tagz Gallery plugins, add the following code below 
the body tag but within your template's header.php file:<br />
`<div id="load_popup_modal_show_id" class="modal fade" tabindex="-1"></div>`

2. Within the root of your template, add a file named `taxomonomy-media-tags.php`. 
3. Copy the code within the `category.php` file and paste the code in the `taxonomy-media-tags.php` file (this is required to display albums using the Media Tags taxonomy)
4. Within the body of the `taxonomy-media-tags.php` file, insert the code below: <br />
`<?php 
$current_tag_category = single_cat_title("", false);
echo tagz_get_media_by_tags($current_tag_category,18); // display 18 images max
?>`
5. Upload media
6. Add Media Tags to media: Within the Media Manager, click on an image. Click the "Edit more details" link. To the 
right you will see a "Media Tags" section where you can add tags. You can assign more than one media tag if you would like the image to show in multiple "Galleries".

### Functions

GET TAG ALBUM<br />
`get_tag_album($tagName)`<br />
Description: Displays a single tag's latest image (album) based on the tag called. The album's image links to a page with the tag's image set using the `taxonomy-media-tags.php` file.<br />
Usage: `<?php get_tag_album('wildlife'); ?>`<br />
Args:<br />
$tagName (string) - a single tag name    

GET MEDIATAG BY TAGS<br />
`get_mediatag_by_tags($tagNames, $limit = 18, $showmore = true, $showtitle = true)`<br />
Description: Displays the set of images based on the tag(s) requested.<br />
Usage: `<?php get_mediatag_by_tags('weddings,automobilia,wildlife,architecture,people,landscape', 6, 'show-no-more'); ?>`<br />
Args:<br />
$tagNames (array) - an array of tag names - required<br />
$limit (int) - a limit of the number of items that display - default is 18<br />
$showmore (string) - show the load more button - default is true - set to 'show no more' to remove the button<br />
$showtitle (boolean) - whether or not to show the image title - default is true - set to false to remove the title<br />

GENERATE MEDIATAG RANDOM IMAGE URL<br />
`generate_mediatag_random_image_url($tagNames, $size)`<br />
Description: Generate a random image from declared media tag(s).<br />
Usage: `<?php generate_mediatag_random_image_url('weddings,automobilia,wildlife', 'large'); ?>`<br />
Args:<br />
$tagNames (array) - an array of tag names - required<br />
$size (string) - Size of image [thumbnail, medium, large, full]<br />

## Frequently Asked Questions

Q&A coming soon. (As I get questions)

## Changelog

** 1.0 **
- * Hello world! * Hello world! - The first appearance of the Media Tag Gallery!

## Upgrade Notice

** 1.0 **
No upgrades yet - more to come!

