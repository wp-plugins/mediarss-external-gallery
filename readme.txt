=== MediaRSS external gallery ===
Contributors: Marco Constâncio
Tags: rss, feed, media, gallery
Requires at least: 3.1
Tested up to: 3.3.1
Stable Tag: trunk

Allows the user to insert a gallery on page/post from media rss feed url using shortcodes.

== Description ==

Allows the user to insert a gallery on page/post from media rss feed url using shortcodes.
It was mainly created to generate image galleries, but it can also be used to create galleries for other file types feed.

Some of the functions include generation of links to the feed/file, thumbnail creation and image caching.

== Installation ==

1. Install the plugin either by uploading the contents of
mediarss-external-gallery.zip to the '/wp-content/plugins/' directory
or by using the 'Add New' in 'Plugins' menu in WordPress
1. Activate the plugin through the 'Plugins' menu in WordPress

= Warnings =

* Make sure the directory *wp-content/plugins/mediarss-external-gallery/cache/* is writable.
* Make sure you have installed php-gd installed for the plugin to cache images. If don't have this installed, the plugin will display images without caching them.

= Parameters =
* **url** - url fo the feed. (mandatory)
* **columns** - number of columns of item for each line (default: 3)
* **width** or **height** - size in pixels to be used in image resizing. If only one option is set the other option will automatically determined. This will cache all images.
* **image_border** - border around the image
* **item_border** - border around the entire item
* **start_item** - start displaying items after a specific a from a specific index. (default: 0)
* **max_items** - limits the number of feeds to display
* **pagination** - display a pagination bar and show a number of feed items per page. (default: false)
* **max_pag_items** - limits the number of feeds to display per page when using the pagination option. (default: 6) 
* **items** - comma separated items to be included in each cell wich can be: *image, *image_thickbox*, *image_file_link*, *image_feed_link*, *title*, *title_file_link*, *title_feed_link*, *description*, *description_file_link*, *description_feed_link*

= Examples =

* [meg_gallery url=http://backend.deviantart.com/rss.xml?q=gallery%3Akerembeyit%2F463379&type=deviation items=title_feed_link,image columns=3  image_border=true width=150]

* [meg_gallery url=http://revision3.com/diggnation/feed/MP4-hd30 items=title_file_link,image,description_feed_link columns=4 item_border=true]

== Screenshots ==

1. Gallery example from a image feed.
2. Gallery example from a video feed in wich the thumbnails are displayed.

== Changelog ==

= 0.1 =
First version of the plugin.

= 0.2 =
Fixed security issue in the thumbnail generation script.

= 0.3 =
Updated the thumbnail generation script.

= 0.4 =
Several corrections and tweaks to the code.
Added the start_item, max_items options.
Added the pagination option.

= 0.4.1 =
Fixes for wordpress 3.5. 