=== Plugin Name ===
Contributors: Marco Constâncio
Tags: rss, feed, media, gallery 
Requires at least: 3.1
Tested up to: 3.1.1

Allows the user to insert a gallery on page/post from media rss feed url using shortcodes. Can also be used create galleries for other file types feed. 

== Description ==

Allows the user to insert a gallery on page/post from media rss feed url using shortcodes. 
It was mainly created to generate image galleries, but it can also be used to create galleries for other file types feed. 

Some of the functions are:
* Customization of some gallery aspects
* Generation of links to the feed/file
* Thumbnail creation 
* Image caching 

== Installation ==

1. Install the plugin either by uploading the contents of 
mediarss-external-gallery.zip to the '/wp-content/plugins/' directory 
or by using the 'Add New' in 'Plugins' menu in WordPress
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Create the directory '/wp-content/cache' if does not exist

== Screenshots ==

1. Gallery example from a image feed.
2. Gallery example from a video feed in wich the thumbnails are displayed.

== Changelog ==

= 0.1 =
First version of the plugin.

== Instructions ==

= Parameters =
* **url** - url fo the feed. (mandatory)
* **columns** - number of columns of item for each line (default: 3)
* **width** or **height** - size in pixels to be used in image resizing. If only one option is set the other option will automatically determined. This uses image caching wich requires the */wp-content/cache* folder to exist.   
* **image_border** - border around the image
* **item_border** - border around the intire item
* **items** - comma separated items to be included in each cell wich can be: image, image_thickbox, image_file_link, image_feed_link, title, title_file_link, title_feed_link, description, description_file_link, description_feed_link

= Examples =

[meg_gallery url=http://backend.deviantart.com/rss.xml?q=gallery%3Akerembeyit%2F463379&type=deviation items=title_feed_link,image columns=3  image_border=true width=150]

[meg_gallery url=http://revision3.com/diggnation/feed/MP4-hd30 items=title_file_link,image,description_feed_link columns=4 item_border=true]
