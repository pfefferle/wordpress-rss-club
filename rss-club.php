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
			'description'  => __( 'RSS Club is a collection of blogs (personal and otherwise) committed to providing RSS-only content. It’s like a newsletter delivered to your feed reader in order to celebrate the medium of RSS and breakaway from social media.', 'rssclub' ),
			'public'       => true,
			'supports'     => array(
				'title',
				'editor',
				'author',
				'comments',
				'revisions',
				'trackbacks',
				'post-formats',
				'custom-fields',
				'page-attributes',
			),
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

	\register_taxonomy_for_object_type( 'post_tag', 'rssclub' );
}
\add_action( 'init', __NAMESPACE__ . '\register_custom_post_type' );

/**
 * Add help tabs to the RSS Club post list
 *
 * @return void
 */
function add_help_tabs() {
	$screen = \get_current_screen();

	if ( ! $screen || 'rssclub' !== $screen->post_type ) {
		return;
	}

	$screen->add_help_tab(
		array(
			'id'      => 'rssclub-overview',
			'title'   => __( 'Overview', 'rssclub' ),
			'content' => '<p>' . esc_html( \get_post_type_object( 'rssclub' )->description ) . '</p>',
		)
	);

	$screen->add_help_tab(
		array(
			'id'      => 'rssclub-how-it-works',
			'title'   => __( 'How it works', 'rssclub' ),
			'content' => '<ul>' .
				'<li>' . esc_html__( 'RSS Club posts are added to your main feed and to every feed that contains regular posts.', 'rssclub' ) . '</li>' .
				'<li>' . esc_html__( 'They do not show up on your homepage or in your regular post archives.', 'rssclub' ) . '</li>' .
				'<li>' . esc_html__( 'Every RSS Club post gets a footnote that links to the RSS Club rules.', 'rssclub' ) . '</li>' .
				'</ul>',
		)
	);

	$screen->set_help_sidebar(
		'<p><strong>' . esc_html__( 'For more information:', 'rssclub' ) . '</strong></p>' .
		'<p><a href="https://daverupert.com/rss-club/">' . esc_html__( 'About RSS Club', 'rssclub' ) . '</a></p>' .
		'<p><a href="https://github.com/pfefferle/wordpress-rss-club">' . esc_html__( 'Plugin on GitHub', 'rssclub' ) . '</a></p>'
	);
}
\add_action( 'load-edit.php', __NAMESPACE__ . '\add_help_tabs' );

/**
 * Add info to content
 *
 * @param string $content The post content.
 *
 * @return string The modified post content.
 */
function add_info( $content ) {
	if ( 'rssclub' === get_post_type() ) {
		$info = '<li style="list-style-type: \'*\2008  \'">' . wp_kses( __( '👊 It\'s a secret to everyone! <a href="https://daverupert.com/rss-club/">Read more about RSS Club</a>.', 'rssclub' ), array( 'a' => array( 'href' => array() ) ) ) . '</li>';

		// Add the info as last entry of the core footnotes list, or start one.
		$footnotes = strpos( $content, 'wp-block-footnotes' );
		$end       = false === $footnotes ? false : strpos( $content, '</ol>', $footnotes );

		if ( false === $end ) {
			$content .= '<ol class="wp-block-footnotes">' . $info . '</ol>';
		} else {
			$content = substr_replace( $content, $info, $end, 0 );
		}
	}

	return $content;
}
\add_filter( 'the_content', __NAMESPACE__ . '\add_info' );

/**
 * Add custom post type to main query
 *
 * @param array $query_vars The query vars.
 *
 * @return array The modified query vars.
 */
function request( $query_vars ) {
	if ( ! isset( $query_vars['feed'] ) ) {
		// Do nothing.
		return $query_vars;
	}

	if ( ! isset( $query_vars['post_type'] ) ) {
		// This is the *unaltered* main feed.
		$query_vars['post_type'] = array( 'post', 'rssclub' );
	} elseif ( is_array( $query_vars['post_type'] ) && in_array( 'post', $query_vars['post_type'], true ) ) {
		// Wherever `post` is included, add `rssclub` as well.
		$query_vars['post_type'][] = 'rssclub';
	}

	return $query_vars;
}
\add_filter( 'request', __NAMESPACE__ . '\request' );

/**
 * “We are much better at writing code than haiku.”
 * — Matt Mullenweg, founder of Automattic
 *
 * @param string $context Type of feed. Possible values include 'rss2', 'rss2-comments',
 *                        'rdf', 'atom', and 'atom-comments'.
 *
 * @return void
 */
function add_feed_head( $context ) {
	if ( ! in_array( $context, array( 'rss2', 'atom' ), true ) ) {
		return;
	}

	echo "\n\n<!--\n
	Feeds whisper secrets,
	In the RSS Club's embrace,
	Knowledge blooms in grace.\n
-->\n\n";
}
\add_action( 'rss_tag_pre', __NAMESPACE__ . '\add_feed_head' );
