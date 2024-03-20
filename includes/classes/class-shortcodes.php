<?php
/**
 * The class registers shortcodes for the plugin.
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
 * Class for registering the shortcodes.
 */
class Shortcodes {
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
        add_shortcode('competition_list', array( $this, 'display_competition_list' ));
		add_shortcode('competition_entry_form', array( $this, 'display_entry_form' ));
		add_action( 'wp_ajax_process_entry_form', [ $this, 'process_entry_form' ] );
        add_action( 'wp_ajax_nopriv_process_entry_form', [ $this, 'process_entry_form' ] );
	}

    /**
	 * Creates instance of the class.
	 *
	 * @return WP_Object $instance.
	 * @since 1.0.0
	 */
	public static function get_instance() {
        $instance = new Shortcodes();
        return $instance;
    }

    /**
	 * Displays entry form on the competition page.
	 * @param array $atts, Attributes.
	 * @return void
	 * @since 1.0.0
	 */
	public function display_entry_form($atts) {
		global $post;
		$post_id = $post->ID;
		ob_start();
		$html = ''; ?>
		<div class="entry-form">
			<div class="success"></div>
			<form method="post" id="entry-form">
				<input type="hidden" value="<?php echo esc_attr($post_id) ?>" id="competition-id" />
				<table>
					<tbody>
						<tr class="form-field">
							<td class="label-td">
								<label for="first-name">
									<?php esc_html_e('First Name', 'wp-competition-entries') ?>
									<span class="req">*</span>
								</label>
							</td>
							<td class="field-td">
								<input type="text" id="first-name" required class="input-field" />
								<span class="err-msg"></span>
							</td>
						</tr>
						<tr class="form-field">
							<td class="label-td">
								<label for="last-name">
									<?php esc_html_e('Last Name', 'wp-competition-entries') ?>
									<span class="req">*</span>
								</label>
							</td>
							<td class="field-td">
								<input type="text" id="last-name" required class="input-field" />
								<span class="err-msg"></span>
							</td>
						</tr>
						<tr class="form-field">
							<td class="label-td">
								<label for="email">
									<?php esc_html_e('Email', 'wp-competition-entries') ?>
									<span class="req">*</span>
								</label>
							</td>
							<td class="field-td">
								<input type="email" id="email" required class="input-field" />
								<span class="err-msg"></span>
							</td>
						</tr>
						<tr class="form-field">
							<td class="label-td">
								<label for="phone">
									<?php esc_html_e('Phone', 'wp-competition-entries') ?>
									<span class="req">*</span>
								</label>
							</td>
							<td class="field-td">
								<input type="text" id="phone" required class="input-field" />
								<span class="err-msg"></span>
							</td>
						</tr>
						<tr class="form-field">
							<td class="label-td">
								<label for="description">
									<?php esc_html_e('Description', 'wp-competition-entries') ?>
								</label>
							</td>
							<td class="field-td">
								<textarea id="description" class="input-area"></textarea>
							</td>
						</tr>
					<tbody>
				</table>
				<button type="submit" class="submit-btn" id="entry-submit"><?php esc_html_e('Submit Entry', 'wp-competition-entries') ?></button>
			</form>
		</div><?php
		$html = ob_get_clean();
		return $html;
	}

    /**
     * Processes form entries and fill them in Entries post type.
     *
     * @return void
     */
    public function process_entry_form() {
        // Check nonce.
        if ( ! isset( $_POST['_ajaxnonce'] ) && ! wp_verify_nonce( sanitize_key( $_POST['_ajaxnonce'] ), 'ajax-load-event' ) ) {
            return false;
        }

        $postData = filter_input_array(INPUT_POST);
        if (empty($postData)) {
            return false;
        }

        $html = '';
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
		$competition_id = filter_input(INPUT_POST, 'competitionId', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $first_name = filter_input(INPUT_POST, 'firstName', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		$phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		$last_name = filter_input(INPUT_POST, 'lastName', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $message = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if (!empty($email) && !empty($first_name) && !empty($last_name) && !empty($phone)) {
			$args = [
				'post_title' => $first_name.' '.$last_name,
				'post_status' => 'publish',
				'post_type' => 'entries',
				'post_author' => get_option('admin_email')
			];
			$post_id = wp_insert_post( $args, true );
			if (!is_wp_error($post_id)) {
				update_post_meta($post_id, 'wce-first_name', sanitize_text_field($first_name));
				update_post_meta($post_id, 'wce-last_name', sanitize_text_field($last_name));
				update_post_meta($post_id, 'wce-email', sanitize_email($email));
				update_post_meta($post_id, 'wce-phone', sanitize_text_field($phone));
				update_post_meta($post_id, 'wce-competition_id', sanitize_text_field($competition_id));
				update_post_meta($post_id, 'wce-description', sanitize_text_field($message));
                $html .= '<span class="msg">'.__( 'Congratulations! Your entries are submitted successfully. Someone from our team will look into your query and will contact you soon. ', 'wp-competition-entries' ).'</span><span class="btn"><a href="'.esc_url(get_the_permalink($competition_id)).'" class="back-button">'.__('<< Back to Competition').'</a><span>';
			} else {
				$html .=  '<span class="msg error">'.__( 'Oops! Unexpected error occurred. Please try again after sometime', 'wp-competition-entries' ).'</span><span class="btn"><a href="'.esc_url(get_the_permalink($competition_id)).'" class="back-button">'.__('<< Back to Competition').'</a><span>';
			}
        } else {
            $html .= '<span class="msg error">'.__('Please fill all the required fields with valid inputs.', 'wp-competition-entries').'</span><span class="btn"><a href="'.esc_url(get_the_permalink($competition_id)).'" class="back-button">'.__('<< Back to Competition').'</a><span>';
        }

        wp_send_json_success( $html );
    }

    /**
	 * Render callback method for 'competition_list' shortcode.
	 *
	 * @return string
	 */
	public function display_competition_list() {
		$args = array(
			'post_type'      => WCE_COMPETITIONS,
			'post_status'    => 'publish',
            'posts_per_page' => -1
		);

		$query = new \WP_Query( $args );
        ob_start();
		?>
		<div class="competitions">
            <?php if ($query->have_posts()) : ?>
                <table>
                    <tbody>
                        <?php while($query->have_posts()):
                            $query->the_post();
                            $post_id = \get_the_ID(); ?>
                            <tr>
                                <td class="comp-name">
                                    <a href="<?php echo esc_url(get_the_permalink($post_id)) ?>" target="__blank">
                                    <?php echo get_the_post_thumbnail($post_id, 'thumbnail') ?>
                                    <br>
                                    <?php echo esc_html(\get_the_title($post_id)) ?></a>
                                </td>
                                <td class="comp-desc"><?php echo get_the_excerpt($post_id) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-records">
                    <?php \esc_html_e('No Records Found!', 'wp-competition-entries') ?>
                </div>
            <?php endif; ?>
		</div>
		<?php
        wp_reset_postdata();
        return ob_get_clean();
	}
}
