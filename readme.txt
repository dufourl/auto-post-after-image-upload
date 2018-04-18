=== Auto Post After Image Upload ===
Contributors: shaharia.azam / laurent.dufour
Tags: auto post, post, image upload
Requires at least: 3.0.1
Tested up to: 4.9.4
Stable tag: 2.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin will provide you the facility to create post after uploading each media from wordpress media gallery automatically.

== Description ==

This `Auto Post After Image Upload` plugin will let you create single/bulk post after uploading any media from wordpress media gallery. This is very much essential plugin for photo blog or where there are a lots number of image posting in a wordpress driven site.

When you will upload an image from wordpress media gallery then a post will be created automatically with that image as featured image.

Website of Author: [http://www.shahariaazam.com](http://www.shahariaazam.com)
Submit Issues/Suggestions/Recommendations: [https://github.com/shahariaazam/auto-post-after-image-upload/issues/new](https://github.com/shahariaazam/auto-post-after-image-upload/issues/new)
Project GitHub URL: [https://github.com/shahariaazam/auto-post-after-image-upload](https://github.com/shahariaazam/auto-post-after-image-upload)

For more details you can send mail with your suggestions, recommendation to shaharia.azam@gmail.com or laurent.dufour@havas.com


== Installation ==

To **Install** this `Auto Post After Image Upload` plugin there is no any complexity. It's very simple like other plugin. Just follow the procedure described below.

1. Download the plugin from Wordpress Plugin repository. After downloading the zip file extract it.
2. Upload `auto_post_after_image_upload` plugin directory to the `/wp-content/plugins/` directory.
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Upload an image from 'Add Media' menu in Wordpress. Then go to 'All Post' menu and see after uploading that image there will be a new post with that image.

== Frequently Asked Questions ==

= This is useful for creating bulk post? =

Yes, of course. The main intention to create this plugin to use where user need bulk image posting and creating post.

== Screenshots ==

There is no Screenshot yet!

== Changelog ==

= 1.0 =
* Initial release

= 1.01 =

New features:

* New plugin settings page
* You can now customize for all the photo that you will uploaded : default text content, default title, default simple or multiple tags, default format, default status
* An HREF to your photo is now possible, you can customize the class for HREF (Eg : "lightbox" if you are using a plugin based on lightbox to display your photo)
* If you want you can use your photo filename as a default title
* If you want you can add the article's title to the ALT of the HREF
* If possible, exif from you photo will be used to complete width and height, as well as a bottom caption text with a sum up of useful exif informations
* If your photo is detected as a square, you can add somme specific tags (Eg : 6x6 )

= 1.02 =

Bug corrected:

*properly handled the camera brand and model as tags

New features:

* If you want you can use the year date of the post or the date of a photo attached as a tag
* If you want you can autodetect from the date of a photo attached the season as a tag (winter, spring, summer, autumn)
* If you want you can autodetect from the time of a photo attached the period of the day as a tag (morning, afternoon, night)
* If your photo is detected as a landscape format, you can add some specific tags (Eg : Landscape )
* If your photo is detected as a portrait format, you can add some specific tags (Eg : Portrait )
* If your photo is detected as a landscape format, you can add some specific tags (Eg : Landscape )

= 1.03 =

Bug corrected:

*Now properly handle the season from the date of a photo

= 1.04 =

Bug corrected:

*Now properly handle the Shutter Speed from exif
*Now properly handle the Focal Length from exif
*Now properly handle the F Stop Number from exif

New features:

* If you want you can use the focal length of a photo attached as a tag
* If you want you can use the type of focal length (Ultra Wide Angle, Wide Angle, Standard, Telephoto, Super Telephoto) of a photo attached as a tag
* If you want you can autodetect long exposure from the shutter speed of a photo attached and set appropriate tags (Eg : Long Exposure)

= 1.05 =

Bug corrected:

*Now properly handle the autodetection of long exposure from exif

New features:

* If you want you can autodetect color or black and white photography from the density of grey in the photo set appropriate tags (Eg : Colors)
* Now handle GPS Coordinates (Tested with Nikon, need to test with other brand like Canon/Pentax/etc...)
* Display GPS Location on Google Maps (Need to download an API Key from Google)
* Tested in Wordpress 4.7.3

= 1.06 =

Bug corrected:

*Now properly handle the season from the date of a photo
 
New features:

* Better detection of BW
* Now autodetect analog or digital photography based on the presence of exif and set appropriate tags (Eg : Analog)
* Now you can set additional categories to the default one
* Automatic addition of your own categories ID based on detection of colors, black & white, analog or digital, long exposure

* Tested in Wordpress 4.8.1

= 1.07 =
 
New features:

* Now autodetect format 24x36, 6x5, 6x6 6x7, 6x17 24x65 (aka XPAN) , and set appropriate categories ID or tags
* Now detect Color analog or Color digital photography based on the presence of exif and set appropriate tags  (Eg : Color Film) or categories ID
* Now detect BW analog or BW digital photography based on the presence of exif and set appropriate tags (Eg : Black & White Film) or categories ID

* Tested in Wordpress 4.9.4

= 1.1 =
 
New features:

* Now you can use category name instead of category IDs

* Tested in Wordpress 4.9.5