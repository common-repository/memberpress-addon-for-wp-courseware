<?php
/**
 * WP Courseware Memberpress Menu Courses Class
 *
 * @package WPCW_MP_Addon/Includes
 * @since 1.4.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPCW_MP_Menu_Courses' ) ) {
	/**
	 * Class WPCW_MP_Menu_Courses.
	 *
	 * @since 1.4.0
	 */
	class WPCW_MP_Menu_Courses {

		/**
		 * @var string Menu Slug.
		 * @since 1.4.0
		 */
		protected $menu_slug = 'courses';

		/**
		 * Menu Courses Hooks.
		 *
		 * @since 1.4.0
		 */
		public function hooks() {
			add_action( 'mepr_account_nav', array( $this, 'account_menu_courses_item' ) );
			add_action( 'mepr_account_nav_content', array( $this, 'account_menu_courses_content' ) );
		}

		/**
		 * Account Menu Courses Item.
		 *
		 * @since 1.4.0
		 */
		public function account_menu_courses_item( $items ) {
			$mepr_options = MeprOptions::fetch();
			$account_url  = $mepr_options->account_page_url();
			$delim        = MeprAppCtrl::get_param_delimiter_char( $account_url );
			?>
			<span class="mepr-nav-item <?php MeprAccountHelper::active_nav( 'courses' ); ?>">
                <a href="<?php echo MeprHooks::apply_filters( 'mepr-account-nav-courses-link', $account_url . $delim . 'action=courses' ); ?>" id="mepr-account-courses">
	                <?php echo MeprHooks::apply_filters( 'mepr-account-nav-courses-label', _x( 'Courses', 'ui', 'wpcw-mp-addon' ) ); ?>
                </a>
			</span>
			<?php
		}

		/**
		 * Account Menu Courses Content.
		 *
		 * @since 1.4.0
		 *
		 * @param int $action The current action.
		 */
		public function account_menu_courses_content( $action ) {
			if ( 'courses' !== $action ) {
				return;
			}

			if ( function_exists( 'wpcw_student_account_courses' ) ) {
				$current_page = 1;
				wpcw_student_account_courses( $current_page );
			} else {
				printf( '<h2>%s</h3>', apply_filters( 'wpcw_mp_account_menu_courses_content_title', esc_html__( 'My Courses', 'wpcw-mp-addon' ) ) );

				echo do_shortcode( '[wpcw_course_progress user_progress="true" user_grade="true"]' );
			}
		}
	}
}
