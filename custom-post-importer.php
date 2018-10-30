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
$site_json = file_get_contents('http://localhost/labs/commandervee/results.json');
$site_pages = json_decode($site_json);

echo '<h1>Posts Imported!</h1>';
importPages($site_pages, array());

function importPages($pages, $categories) {

	foreach ($pages as $page) {
		/**
		 * Get the user to use as our post's author.
		 */
		// $author = getAuthor($page->author_slug);

		/**
		 * Import attachments and replace post content to use these local versions
		 */
        foreach ($page->attachments as $attachment_url) {
            $local_url = sideload_attachment($attachment_url);
            if (!is_wp_error($local_url)) {
                $page->content = str_replace($attachment_url->link, $local_url, $page->content);
            }
        }

		/**
		 * Create the post.
		 */
		$args = array (
			'post_type' => 'page',
			'post_status' => 'any',
			'meta_key' => 'old_url',
			'meta_value' => $page->old_url,
		);
		$existing_page_query = new WP_Query($args);

		if ($existing_page_query->have_posts()) {
			error_log(print_r('Skipping imort for duplicate content because a page already exists from '.$page->old_url, true));
			continue;

		} else {
			$import_data = array(
				'post_type'     => 'page',
				'post_title'	=> $page->title,
				'post_content'  => $page->content,
				'post_status'   => 'publish',
				'meta_input'    => array(
					'old_url'   => $page->old_url,
				),
				// OPTIONAL ARGS
				// 'post_name'		=> $page->page_slug,
				// 'post_date'		=> $page->publish_date,
				// 'post_author'	=> $author->get('ID'),
				// 'post_category'	=> $categories,
			);
			$new_post = wp_insert_post($import_data, true);

			if (is_wp_error($new_post)) {
				error_log(print_r($new_post, true));
			}
        }

        // UNCOMMENT TO STEP THROUGH PAGES ONE AT A TIME
        // break;
	}

}




/**
 * HELPER FUNCTIONS
 */

/**
 * Get the specified author
 */
function getAuthor($author_slug) {
	// If the author_slug field is populated, sweet deal.
	if ($author_slug) {

		$author = get_user_by( 'slug', $author_slug );

		// If no user exists with that slug, let's make one!
		if (!$author) {
			$userdata = array(
				'user_login' 	=>  $author_slug,
				'user_pass'  	=>  bin2hex(openssl_random_pseudo_bytes(4)),
				'display_name'	=>  $page->author,
			);
			wp_insert_user( $userdata );

			$author = get_user_by( 'slug', $author_slug );
		}
	}

	// Otherwise, assign the post to the 'paperleaf' user.
	if ( !$author || is_wp_error($author) ) {
		$author = get_user_by( 'slug', 'paperleaf' );
	}

	return $author;
}

/**
 * Sideload a URL as a WP attachment
 */
function sideload_attachment($file) {

    $file_link = 'https://www.afsc.ca/'.$file->link;
    $file_title = trim($file->title);

    $args = array (
        'post_type' => 'attachment',
        'post_status' => 'any',
        'meta_key' => 'old_attachment_url',
        'meta_value' => $file_link,
    );
    $attachment_query = new WP_Query($args);

    if ($attachment_query->have_posts()) {
        return wp_get_attachment_url($attachment_query->posts[0]->ID);

    } else {
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $file_array = array();

		// BASIC METHOD
		$file_array['name'] = basename( $file_link );
		$file_array['tmp_name'] = download_url( $file_link );
		if ( is_wp_error( $file_array['tmp_name'] ) ) {
			return $file_array['tmp_name'];
		}

        // COMPLICATED METHOD
        // if ( strpos(basename($file_link), 'image') !== false ) {
        //     $file_array['name'] = ($file_title ? $file_title : uniqid()) .'.jpg'; //basename( $file );
        // } elseif ( strpos(basename($file_link), 'doc') !== false ) {
        //     $file_array['name'] = ($file_title ? $file_title : uniqid()) .'.pdf';
        // } else {
        //     $file_array['name'] = ($file_title ? $file_title : uniqid()) . 'tmp';
        // }
        // $temp_filename = wp_tempnam();
        // $fput = file_put_contents($temp_filename, file_get_contents($file_link));
        // $file_array['tmp_name'] = $temp_filename;

        // Do the validation and storage stuff.
        $id = media_handle_sideload( $file_array, 0, $desc );
        if (!is_wp_error($id)) {
            update_post_meta($id, 'old_attachment_url', $file_link);
            return wp_get_attachment_url($id);
        }
        return $id;
    }
}
