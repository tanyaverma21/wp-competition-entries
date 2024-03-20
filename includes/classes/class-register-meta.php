<?php
/**
 * The class registers custom meta fields for post types.
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
class Register_Meta {
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
        add_action( 'add_meta_boxes', array( $this, 'entries_form_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'entries_form_save_meta' ), 10, 2 );
	}

    /**
	 * Creates instance of the class.
	 *
	 * @return WP_Object $instance.
	 * @since 1.0.0
	 */
	public static function get_instance() {
        $instance = new Register_Meta();
        return $instance;
    }

    /**
	 * Adds the meta box container.
	 *
	 * @return void
	 */
	public function entries_form_meta_boxes(): void {
		add_meta_box(
			'form_entries',
			__( 'Entry Specifics', 'wp-competition-entries' ),
			array( $this, 'render_entries_metabox_html' ),
			WCE_ENTRIES,
			'normal',
			'high'
		);
	}

	/**
	 * Render form entries metabox.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function render_entries_metabox_html( $post ): void {
        $post_id = $post->ID;
		// Reterive current meta vales.
		$first_name     = get_post_meta( $post_id, 'wce-first_name', true );
		$last_name      = get_post_meta( $post_id, 'wce-last_name', true );
		$email          = get_post_meta( $post_id, 'wce-email', true );
		$phone          = get_post_meta( $post_id, 'wce-phone', true );
		$description    = get_post_meta( $post_id, 'wce-description', true );
		$competition_id = get_post_meta( $post_id, 'wce-competition_id', true );
		?>

		<div class="entry-details">
			<div class="fieldset">
                <label for="wce-first_name">
                    <?php esc_html_e( 'First Name ', 'wp-competition-entries' ); ?>
                </label>   
                <input type="text" value="<?php echo esc_attr( $first_name ); ?>" id="wce-first_name" name="wce-first_name" />   			
			</div>
		    <div class="fieldset">
                <label for="wce-last_name">
                    <?php esc_html_e( 'Last Name ', 'wp-competition-entries' ); ?>
                </label>
                <input type="text" value="<?php echo esc_attr( $last_name ); ?>" id="wce-last_name" name="wce-last_name" /> 			
			</div>
		    <div class="fieldset">
                <label for="wce-email">
                    <?php esc_html_e( 'Email ', 'wp-competition-entries' ); ?>
                </label>  
                <input type="email" value="<?php echo esc_attr( $email ); ?>" id="wce-email" name="wce-email" /> 			
			</div>
		    <div class="fieldset">
                <label for="wce-phone">
                    <?php esc_html_e( 'Phone ', 'wp-competition-entries' ); ?>
                </label>  
                <input type="text" value="<?php echo esc_attr( $phone ); ?>" id="wce-phone" name="wce-phone" />  			
			</div>
		    <div class="fieldset">
                <label for="wce-description">
                    <?php esc_html_e( 'Description ', 'wp-competition-entries' ); ?>
                </label>   
                <textarea id="wce-description" name="wce-description" ><?php echo esc_html( $description ); ?></textarea>   			
			</div>
		    <div class="fieldset">
                <label for="wce-competition_id">
                    <?php esc_html_e( 'Submitted via Competition ID ', 'wp-competition-entries' ); ?>
                </label>
                <input type="text" value="<?php echo esc_attr( $competition_id ); ?>" id="wce-competition_id" name="wce-competition_id" /> 
                <a href="<?php echo esc_url(get_edit_post_link($competition_id)) ?>" title="<?php echo esc_attr(get_the_title($competition_id)) ?>" target="__blank"><?php esc_html_e('Go to Competition >>', 'wp-competition-entries') ?></a>			
			</div>
		</div>
		<?php
	}

	/**
	 * Save meta field values when the post is saved.
	 *
	 * @param int     $post_id The ID of the post being saved.
	 * @param WP_Post $post The post object.
	 */
	public function entries_form_save_meta( $post_id, $post ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		$post_type = get_post_type_object( $post->post_type );
		if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) || WCE_ENTRIES !== get_post_type($post_id) ) {
			return $post_id;
		}

		if ( isset( $_POST['wce-first_name'] ) ) {
			$first_name = sanitize_text_field( wp_unslash( $_POST['wce-first_name'] ) );
			update_post_meta( $post_id, 'wce-first_name', $first_name );
		}
		if ( isset( $_POST['wce-last_name'] ) ) {
			$last_name = sanitize_text_field( wp_unslash( $_POST['wce-last_name'] ) );
			update_post_meta( $post_id, 'wce-last_name', $last_name );
		}
		if ( isset( $_POST['wce-email'] ) ) {
			$email = sanitize_text_field( wp_unslash( $_POST['wce-email'] ) );
			update_post_meta( $post_id, 'wce-email', $email );
		}
		if ( isset( $_POST['wce-phone'] ) ) {
			$phone = sanitize_text_field( wp_unslash( $_POST['wce-phone'] ) );
			update_post_meta( $post_id, 'wce-phone', $phone );
		}
		if ( isset( $_POST['wce-description'] ) ) {
			$description = sanitize_textarea_field( wp_unslash( $_POST['wce-description'] ) );
			update_post_meta( $post_id, 'wce-description', $description );
		}
		if ( isset( $_POST['wce-competition_id'] ) ) {
			$competition_id = sanitize_text_field( wp_unslash( $_POST['wce-competition_id'] ) );
			update_post_meta( $post_id, 'wce-competition_id', $competition_id );
		}
	}
}
