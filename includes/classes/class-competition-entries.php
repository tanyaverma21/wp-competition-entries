<?php
/**
 * The core plugin class.
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
 * Main class File.
 */
class Competition_Entries {
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
        Register_Posts_Taxonomies::get_instance();
		Register_Meta::get_instance();
		Shortcodes::get_instance();
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'plugins_loaded', array( $this, 'set_locale' ) );
	}

	/**
	 * Creates instance of the class.
	 *
	 * @return WP_Object $instance.
	 * @since 1.0.0
	 */
	public static function get_instance() {
        $instance = new Competition_Entries();
        return $instance;
    }

    /**
	 * Define the locale for this plugin for internationalization.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	public function set_locale() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'wp-competition-entries' );
		load_textdomain( 'wp-competition-entries', WCE_DIR . '/languages/' . $locale . '.mo' );
		load_plugin_textdomain(
			'wp-competition-entries',
			false,
			dirname( dirname( WCE_BASEPATH ) ) . '/languages/'
		);
	}

	/**
	 * Enqueue scripts on frontend.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_style(
			'wce-form-css',
			WCE_URL . 'assets/src/css/form.css',
			array(),
			filemtime( WCE_PATH . '/assets/src/css/form.css' ),
			'all'
		);
		wp_register_script( 'wce-form-js', WCE_URL.'assets/src/js/form.js', array(), WCE_VERSION, true );
		wp_localize_script(
            'wce-form-js',
            'ajaxload_params',
            [
                'ajaxurl'          => site_url() . '/wp-admin/admin-ajax.php',
                'nonce'            => wp_create_nonce( 'ajax-load' ),
            ]
        );
		wp_enqueue_script( 'wce-form-js', WCE_URL.'assets/src/js/form.js', array(), WCE_VERSION, true );
	}

	/**
	 * Enqueue scripts in back-end.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function admin_enqueue_scripts() {
		wp_enqueue_style(
			'wce-admin-css',
			WCE_URL . 'assets/src/css/admin.css',
			array(),
			filemtime( WCE_PATH . '/assets/src/css/admin.css' ),
			'all'
		);
	}
}
