=== Remove Deleted Pages from Search Index ===
Contributors: Oliver Hancke
Tags: delete, pages, search index, 410 status code, klutch, oliver hancke
Requires at least: 5.0
Tested up to: 6.2.2
Stable tag: 3.0
License: GPLv3

A lightweight plugin that implements the 410 HTTP status code for deleted pages to inform Google that the pages should be removed from its search index.

== Description ==

When a page is deleted on your website, it's important to inform search engines so they can remove it from their index. This plugin implements the 410 HTTP status code for deleted pages, which tells search engines that the page is no longer available and should be removed from the index.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. That's it! The plugin will automatically implement the 410 HTTP status code for deleted pages on your website.

== Frequently Asked Questions ==

= How do I know if the 410 HTTP status code is being implemented for deleted pages on my website? =

You can use a tool such as Google Search Console to check the HTTP status code for your pages. If a deleted page returns a 410 status code, it means the plugin is working correctly.

= What happens if I deactivate the plugin? =

If you deactivate the plugin, the 410 HTTP status code will no longer be implemented for deleted pages on your website.

== Screenshots ==

N/A

== Changelog ==

= 3.0 =
* Added functionality to handle trash status for posts and pages meaning that pages placed in 'Trash' is now added to the list of URLs with status code 410. This includes registering to 'trashed_post', 'untrashed_post', and 'before_delete_post' WordPress actions.
* Created new admin page for manual management of deleted URLs. Administrators can manually add and remove URLs to and from the deleted_urls table. 
* Improved security by using wp_nonce_field to protect against CSRF attacks.

= 2.0 =
* Registers the rdp_deleted_page_410 function to the template_redirect action, which checks for a 404 status and whether the requested URL exists in the deleted_urls table. If it does, it sends a 410 status header.
* Creates a new table called deleted_urls upon plugin activation by using the register_activation_hook and the rdp_create_deleted_urls_table function. This table stores deleted URLs with a unique constraint on the URL field.
* Hooks the rdp_store_deleted_url function to the before_delete_post action, which stores the URL of a post or page before it's permanently deleted.

= 1.0 =
* Initial release
