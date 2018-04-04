<?php

/**
 * INSTRUCTIONS:
 *   NOTE: These instructions assume that you have files containing the JSON
 *   produced by ./crawler.js loaded on your localhost at some address.
 *
 *   1. Place this file in the base folder of your WP theme.
 *   2. Modify the 'Load up the JSON' section (line 11) to point at your JSON
 *   	files. Note that each post category requires a separate JSON file.
 *   3. Modify the calls to importPages() on line 82 to match the code you
 *   	tweaked on step 2. Additionally, provide the category IDs you wish to
 *   	associate with the posts from each file.
 *   4. Ensure your Wordpress install contains categories that match the IDs
 *   	you provided in step 3.
 *   5. Run this file using Chrome.
 *   6. Verify that the file ran without PHP errors, and then check out your
 *   	newly imported posts!
 */


// Load up dat Wordpress
$parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );
require_once( $parse_uri[0] . 'wp-load.php' );
require_once( $parse_uri[0] . 'wp-admin/includes/media.php');
require_once( $parse_uri[0] . 'wp-admin/includes/file.php');
require_once( $parse_uri[0] . 'wp-admin/includes/image.php');


// Load up the JSON!
$blog_json = file_get_contents('http://localhost/devon/labs/crawler/blog_json.txt');
$blog_pages = json_decode($blog_json);

$indepth_json = file_get_contents('http://localhost/devon/labs/crawler/indepth_json.txt');
$indepth_pages = json_decode($indepth_json);

$tanker_json = file_get_contents('http://localhost/devon/labs/crawler/tanker_json.txt');
$tanker_pages = json_decode($tanker_json);



function importPages($pages, $categories) {

	foreach ($pages as $page) {
		/**
		 * Get the user to use as our post's author.
		 */

		$author_slug = $page->author_slug;

		// If the author_slug field is populated, sweet deal.
		if ($author_slug) {

			$author = get_user_by( 'slug', $author_slug );

			// If no user exists with that slug, let's make one!
			if (!$author) {
				$userdata = array(
				    'user_login' 	=>  $author_slug,
				    'user_pass'  	=>  'p4perWo0d',
				    'display_name'	=>  $page->author,
				);
				wp_insert_user( $userdata );

				$author = get_user_by( 'slug', $author_slug );
			}

		// Otherwise, assign the post to the 'dogwood' user.
		} else {
			$author = get_user_by( 'slug', 'dogwood' );
		}


		/**
		 * Create the post.
		 */

		$import_data = array(
		    'post_title'	=> $page->title,
		    'post_name'		=> $page->page_slug,
		    'post_date'		=> $page->publish_date,
		    'post_author'	=> $author->get('ID'),
		    'post_content'  => $page->content,
		    'post_category'	=> $categories,
		);
		$new_post = wp_insert_post($import_data, true);


		/**
		 * Load up the featured image, and add it to our new post!
		 */

		// Eff that.

	}
}


echo '<h1>Posts Imported!</h1>';

importPages($blog_pages, array());
importPages($indepth_pages, array(111));
importPages($tanker_pages, array(112));



// function sideload_attachment($file) {

//     $args = array (
//         'post_type' => 'attachment',
//         'post_status' => 'any',
//         'meta_key' => 'old_attachment_url',
//         'meta_value' => $file,
//     );
//     $attachment_query = new WP_Query($args);

//     if ($attachment_query->have_posts()) {
//         return wp_get_attachment_url($attachment_query->posts[0]->ID);
//     } else {

//         require_once(ABSPATH . 'wp-admin/includes/media.php');
//         require_once(ABSPATH . 'wp-admin/includes/file.php');
//         require_once(ABSPATH . 'wp-admin/includes/image.php');

//         $file_array = array();
//         $file_array['name'] = basename( $file );

//         // Download file to temp location.
//         $file_array['tmp_name'] = download_url( $file );
//         if ( is_wp_error( $file_array['tmp_name'] ) ) {
//             return $file_array['tmp_name'];
//         }

//         // Do the validation and storage stuff.
//         $id = media_handle_sideload( $file_array, 0, $desc );

//         if (!is_wp_error($id)) {
//             update_post_meta($id, 'old_attachment_url', $file);
//             return wp_get_attachment_url($id);
//         }
//         return $id;
//     }
// }


// $public_json = file_get_contents('http://localhost/devon/htai-scrape/sitemap-json.txt');
// $public_json = json_decode($public_json);

// foreach ($public_json as $page) {
//     if ($page->page_num > 137) {
//         error_log($page->page_num);

//         foreach ($page->attachments as $attachment_url) {
//             $file = 'https://www.htai.org/'.$attachment_url;

//             $local_url = sideload_attachment($file);
//             if (!is_wp_error($local_url)) {
//                 $page->content = str_replace($attachment_url, $local_url, $page->content);
//             }
//         }

//         $import_data = array(
//             'post_type'     => 'page',
//             'post_title'	=> $page->title,
//             'post_content'  => $page->content,
//             'post_status'   => 'publish',
//             'meta_input'    => array(
//                 'old_url'   => $page->old_url,
//             ),
//         );
//         $new_post = wp_insert_post($import_data, true);

//         if (is_wp_error($new_post)) {
//             error_log(print_r($new_post, true));
//         }

//     }
// }