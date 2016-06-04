# Media Tagz Gallery for Wordpress
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

### Shortcodes

TAG ALBUM<br />
`[tagz-album tag=landscape]`
Attributes:<br />
- tag: (string) tag of album to be displayed - shows the most recent image in the tag set as cover image

TAG GALLERY<br />
`[tagz-gallery tags=landscape,wildlife limit=18 show-more=true show-title=true]`
Attributes:<br />
- tags: (array|string) media tags
- limit: (int) limit of images to be displayed in the gallery
- show-more: (string) set to "true" if you want to display the show more button - set to "no-more" if you wish to remove this button
- show-title: (string) set to "true" if you want to display the image title - set to "no-title" if you wish to not show the image title

GENERATE MEDIATAG RANDOM IMAGE URL<br />
`[tagz-rand-img tags=landscape,wildlife size=thumbnail]`
Attributes:<br />
- tags: (array|string) media tags

## Frequently Asked Questions

Q&A coming soon. (As I get questions)

## Changelog

** 1.1 **
* Added shortcode functionality
* Security fixes - filtered post variables

** 1.0 **
* Hello world! - The first appearance of the Media Tag Gallery!

