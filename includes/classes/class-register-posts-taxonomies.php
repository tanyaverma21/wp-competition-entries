<?php
/**
 * The class registers custom post types, and taxonomies for the plugin.
 *
 * @since      1.0.0
 * @package    WP_Competition_Entries
 * @subpackage WP_Competition_Entries/Includes
 */

namespace WP_Competition_Entries\Includes;

if ( ! defined( 'WPINC' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class for registering the custom post type.
 */
class Register_Posts_Taxonomies {
    /**
	 * Constructor.
	 */
	public function __construct() {
        $this->setup_hooks();
	}

	/**
	 * Sets up hooks initially.
	 */
	public function setup_hooks() {
		add_action( 'init', array( $this, 'init' ) );
		add_filter( 'the_content', array( $this, 'render_competition_detail_content' ) );
		add_action( 'query_vars', array( $this, 'add_custom_query_var' ) );
	}

    /**
	 * Callbacks for 'init' action.
	 */
	public function init() {
        $this->register_post_types();
        $this->add_custom_rewrite_rule_tag();
    }

    /**
	 * Creates instance of the class.
	 *
	 * @return WP_Object $instance.
	 * @since 1.0.0
	 */
	public static function get_instance() {
        $instance = new Register_Posts_Taxonomies();
        return $instance;
    }

	/**
	 * Registers the post types Entries and Competitions.
	 *
	 * @return void
	 */
	public function register_post_types(): void {
		register_post_type(
			WCE_COMPETITIONS,
			array(
				'labels'             => array(
					'name'          => __( 'Competitions', 'wp-competition-entries' ),
					'singular_name' => __( 'Competition', 'wp-competition-entries' ),
					'search_items'  => __( 'Search Competitions', 'wp-competition-entries' ),
					'menu_name'     => __( 'Competitions', 'wp-competition-entries' ),
				),
				'public'             => true,
				'has_archive'        => false,
				'show_in_menu'       => true,
				'show_ui'            => true,
				'menu_icon'          => 'dashicons-star-filled',
				'supports'           => array( 'title', 'thumbnail', 'editor', 'featured-image'),
				'show_in_rest'       => true,
				'publicly_queryable' => true,
				'rewrite'            => true,

			)
		);

		register_post_type(
			WCE_ENTRIES,
			array(
				'labels'             => array(
					'name'          => __( 'Entries', 'wp-competition-entries' ),
					'singular_name' => __( 'Entry', 'wp-competition-entries' ),
					'search_items'  => __( 'Search Entries', 'wp-competition-entries' ),
					'menu_name'     => __( 'Entries', 'wp-competition-entries' ),
				),
				'public'             => false,
				'has_archive'        => false,
				'show_in_menu'       => true,
				'show_ui'            => true,
				'menu_icon'          => 'dashicons-schedule',
				'supports'           => array( 'title' ),
				'show_in_rest'       => true,
				'publicly_queryable' => true,
				'rewrite'            => true,

			)
		);
	}

	/**
	 * Render different content on single competitions page.
	 *
	 * @param string $content Post content.
	 * @return string
	 */
	public function render_competition_detail_content( $content ): string {
		if ( is_singular( WCE_COMPETITIONS ) ) {
			if ( get_query_var( 'submit-entry' ) === 'true' ) {
				$content = do_shortcode( '[competition_entry_form]' );
			} else {
				$content .= '<a href=' . get_the_permalink() . 'submit-entry class="submit-btn">Submit Entry</a>';
			}
		}
        
		return $content;
	}

	/**
	 * Adds custom rewrite-rule and rewrite-tag for Submit Entry page.
	 *
	 * @return void
	 */
	public function add_custom_rewrite_rule_tag(): void {
		add_rewrite_rule( '^competitions/([^/]+)/submit-entry/?$', 'index.php?post_type=competitions&name=$matches[1]&submit-entry=true', 'top' );
		add_rewrite_tag( '%title', '([^&]+)' );
	}

	/**
	 * Adds custom query_vars 'submit-entry'.
	 *
	 * @param array $query_vars Query vars.
	 * @return array
	 */
	public function add_custom_query_var( $query_vars ): array {
		$query_vars[] = 'submit-entry';
		return $query_vars;
	}
}
