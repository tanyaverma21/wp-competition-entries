<?php
/**
 * Plugin Name:     WP Competition Entries
 * Plugin URI:      https://github.com/tanyaverma21/wp-competition-entries
 * Description:     The plugin registers competitions and stores its entries fetched from frontend form.
 * Author:          Tanya Verma
 * Author URI:      https://profiles.wordpress.org/tanyaverma/
 * Text Domain:     wp-competition-entries
 * Domain Path:     /languages
 * Version:         1.0.0
 *
 * @package        WP_Competition_Entries
 */

namespace WP_Competition_Entries;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

define( 'WCE_VERSION', '1.0.0' );
define( 'WCE_URL', plugin_dir_url( __FILE__ ) );
define( 'WCE_DIR', plugin_dir_path( __FILE__ ) );
define( 'WCE_BASEPATH', plugin_basename( __FILE__ ) );
define( 'WCE_PLACEHOLDER_IMG', WCE_URL . 'assets/src/images/placeholder.png' );
define( 'WCE_PLACEHOLDER_THUMB_IMG', WCE_URL . 'assets/src/images/placeholder-150x150.png' );

if ( ! defined( 'WCE_PATH' ) ) {
    define( 'WCE_PATH', __DIR__ );
}

if ( ! defined( 'WCE_FILE' ) ) {
    define( 'WCE_FILE', __FILE__ );
}

/**
 * Custom post types constants.
 */
define( 'WCE_ENTRIES', 'entries' );
define( 'WCE_COMPETITIONS', 'competitions' );

// Load the autoloader.
require_once WCE_DIR . '/includes/autoloader.php';

/**
 * Begins execution of the plugin.
*
* @since    1.0.0
*/
function initialize() {
    new \WP_Competition_Entries\Includes\Competition_Entries();
    flush_rewrite_rules();
}
initialize();
