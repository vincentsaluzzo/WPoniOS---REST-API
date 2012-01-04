=== WPoniOS ===
Contributors: vincentsaluzzo
Donate link: http://example.com/
Tags: rest, iphone, android, webservice, post, page
Requires at least: 3.0
Tested up to: 3.3
Stable tag: 4.3

<b>WPoniOS - Rest API</b> is a plugin which provide a rest interface in WordPress CMS.

== Description ==

WPoniOS - Rest API is a plugin which provide a rest interface in WordPress CMS.

The REST interface return and accept JSON-Format data. Example:

	[
		{ 
			"post_type":"post",
			"post":"this is the post body",
			"date":"04/01/2012"
		},
		...
	]

The REST API provide methods to get information about post, page, comment, tag and category:

*	GET /posts
*	GET /post/:post_id
*	GET /post/:post_id/comments
*	GET /post/:post_id/comment/:comment_id
*	PUT /post/:post_id/comment
*	GET /pages
*	GET /page/:page_id
*	GET /page/:page_id/comments
*	GET /page/:page_id/comment/:comment_id
*	PUT /page/:page_id/comment
*	GET /categories
*	GET /tags

== Installation ==


1. Upload the entire wponios-restapi folder to the /wp-content/plugins/ directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Configure the plugin through the WPoniOS menu in Wordpress.

== Frequently Asked Questions ==


== Screenshots ==


== Changelog ==

= 1.0 =
* First version