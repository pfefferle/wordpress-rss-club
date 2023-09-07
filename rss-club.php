<?php
/**
 * Plugin Name: RSS Club
 * Plugin URI: https://github.com/pfefferle/wordpress-rss-club
 * Description: 1st rule of RSS Club is “Don’t Talk About RSS Club”.
 * Author: Matthias Pfefferle
 * Author URI: https://notiz.blog/
 * Version: 1.0.0
 * License: GPL-2.0-or-later
 * License URI: https://opensource.org/license/gpl-2-0/
 * Text Domain: rssclub
 * Update URI: https://github.com/pfefferle/wordpress-rss-club
 */

namespace Rss_Club;

/**
 * Register post type
 */
function register_custom_post_type() {
	\register_post_type(
		'rssclub',
		array(
			'label'        => __( 'RSS Club', 'rssclub' ),
			'description'  => __( 'RSS Club', 'rssclub' ),
			'public'       => true,
			'supports'     => array( 'title', 'editor', 'author', 'comments', 'revisions', 'custom-fields', 'page-attributes' ),
			'has_archive'  => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-rss',
			'rewrite'      => array(
				'slug' => 'rss-club',
			),
			'labels'       => array(
				'name'               => __( 'RSS Club', 'rssclub' ),
				'singular_name'      => __( 'RSS Club', 'rssclub' ),
				'all_items'          => __( 'All RSS Club posts', 'rssclub' ),
				'new_item'           => __( 'New RSS Club post', 'rssclub' ),
				'add_new'            => __( 'Add New', 'rssclub' ),
				'add_new_item'       => __( 'Add New RSS Club post', 'rssclub' ),
				'edit_item'          => __( 'Edit RSS Club post', 'rssclub' ),
				'view_item'          => __( 'View RSS Club post', 'rssclub' ),
				'search_items'       => __( 'Search RSS Club post', 'rssclub' ),
				'not_found'          => __( 'No RSS Club post found', 'rssclub' ),
				'not_found_in_trash' => __( 'No RSS Club post found in trash', 'rssclub' ),
				'parent_item_colon'  => __( 'Parent RSS Club post', 'rssclub' ),
				'menu_name'          => __( 'RSS Club', 'rssclub' ),
			),
		)
	);
}
add_action( 'init', __NAMESPACE__ . '\register_custom_post_type' );

/**
 * Add warning to content
 *
 * @param string $content The post content.
 *
 * @return string The modified post content.
 */
function add_warning( $content ) {
	if ( 'rssclub' === get_post_type() ) {
		$content = '<p>' . wp_kses( __( 'It\'s a secret to everyone! <a href="https://daverupert.com/rss-club/">Read more about RSS Club</a>.', 'rssclub' ), array( 'a' => array( 'href' => array() ) ) ) . '</p>' . $content;
	}

	return $content;
}
add_filter( 'the_content', __NAMESPACE__ . '\add_warning' );

/**
 * Add custom post type to main query
 *
 * @param array $query_vars The query vars.
 *
 * @return array The modified query vars.
 */
function request( $query_vars ) {
	if ( isset( $query_vars['feed'] ) && ! isset( $query_vars['post_type'] ) ) {
		$query_vars['post_type'] = array( 'post', 'rssclub' );
	}

	return $query_vars;
}
add_filter( 'request', __NAMESPACE__ . '\request' );

/**
 * “We are much better at writing code than haiku.”
 * — Matt Mullenweg, founder of Automattic
 *
 * @return void
 */
function add_feed_head() {
	echo "\n\n<!--\n
	Feeds whisper secrets,
	In the RSS Club's embrace,
	Knowledge blooms in grace.\n
-->\n\n";
}
add_action( 'rss_tag_pre', __NAMESPACE__ . '\add_feed_head' );
