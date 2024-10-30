<?php
/**
 * WP Courseware Memberpress Membership Class.
 *
 * This includes all functionality of the old addon
 * that eventually will be moved into something new.
 *
 * @package WPCW_MP_Addon/Includes
 * @since 1.4.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPCW_MP_Membership' ) ) {
	/**
	 * Class WPCW_MP_Membership.
	 *
	 * Class that handles the specifics of the Membrepress plugin and
	 * handling the data for products for that plugin.
	 *
	 * @since 1.4.0
	 */
	class WPCW_MP_Membership extends WPCW_MP_Members {

		/**
		 * @var string Add Version.
		 * @since 1.4.0
		 */
		protected $addon_version = '1.0.0';

		/**
		 * @var string Addon Id.
		 * @since 1.4.0
		 */
		protected $addon_id = 'WPCW_memberpress';

		/**
		 * @var string Addon Name.
		 * @since 1.4.0
		 */
		protected $addon_name = 'MemberPress';

		/**
		 * WPCW_MP_Membership constructor.
		 *
		 * @since 1.4.0
		 */
		function __construct() {
			parent::__construct( $this->addon_name, $this->addon_id, $this->addon_version );
		}

		/**
		 * Get Membership Levels.
		 *
		 * @since 1.4.0
		 *
		 * @return array|bool Membership levels or false on failure.
		 */
		protected function getMembershipLevels() {
			$membership_levels = array();

			$mp_products = get_posts( array(
				'post_type'   => 'memberpressproduct',
				'post_status' => 'publish',
				'numberposts' => 10000,
			) );

			if ( $mp_products && count( $mp_products ) > 0 ) {
				foreach ( $mp_products as $mp_product ) {
					$mp_product_level         = array();
					$mp_product_level['name'] = $mp_product->post_title;
					$mp_product_level['id']   = $mp_product->ID;
					$mp_product_level['raw']  = $mp_product;

					$membership_levels[ $mp_product_level['id'] ] = $mp_product_level;
				}
			}

			return ! empty( $membership_levels ) ? $membership_levels : false;
		}

		/**
		 * Attach Update User Course Access.
		 *
		 * Function called to attach hooks for handling when a user is updated or created.
		 *
		 * @sicne 1.4.0
		 */
		protected function attach_updateUserCourseAccess() {
			// Events called whenever the user products are changed, which updates the user access.
			add_action( 'mepr-txn-store', array( $this, 'handle_updateUserCourseAccess' ) );
			add_action( 'mepr-subscr-store', array( $this, 'handle_updateUserCourseAccess' ) );
			add_action( 'mepr-transaction-expired', array( $this, 'handle_updateUserCourseAccess' ) );

			// Transaction status options 'complete', 'pending', 'failed', 'refunded'
			//add_action( 'mepr-txn-transition-status', 			array( $this, 'status_check' ), 10, 3 );
			// Subscription status optinos 'active', 'pending', 'suspended', 'cancelled'
			//add_action( 'mepr_subscription_transition_status',  array( $this, 'status_check' ), 10, 3 );
		}

		/**
		 * Assign selected courses to members of a paticular product.
		 *
		 * @since 1.4.0
		 *
		 * @param string $level_id The Level Id in which members will get courses enrollment adjusted.
		 */
		protected function retroactive_assignment( $level_id ) {
			global $wpdb;

			$mepr_db = new MeprDb();

			$page = new PageBuilder( false );

			$batch = 50;
			$step  = isset( $_GET['step'] ) ? absint( $_GET['step'] ) : 1;
			$count = isset( $_GET['count'] ) ? absint( $_GET['count'] ) : 0;
			$steps = isset( $_GET['steps'] ) ? $_GET['steps'] : 'continue';

			$coursesToAdd = get_transient( 'wpcw_add_courses_' . $level_id );
			$coursesToRemove = get_transient( 'wpcw_remove_courses_' . $level_id );

			$summary_url = add_query_arg( array( 'page' => $this->extensionID ), admin_url( 'admin.php' ) );
			$course_url  = add_query_arg( array( 'page' => $this->extensionID, 'level_id' => $level_id ), admin_url( 'admin.php' ) );

			if ( 'finished' === $steps ) {
				$page->showMessage(
					esc_html__( 'Course access settings successfully updated.', 'wpcw-wc-addon' )
					. '<br />' .
					esc_html__( 'All existing customers were retroactively enrolled into the selected courses successfully.', 'wpcw-wc-addon' )
					. '<br /><br />' .
					/* translators: %s - Summary Url. */
					sprintf( __( 'Want to return to the <a href="%s">Course Access Settings</a>?', 'wpcw-wc-addon' ), $summary_url )
				);

				printf( '<br /><a href="%s" class="button-primary">%s</a>', $course_url, __( '&laquo; Return to Course', 'wpcw-wc-addon' ) );

				if ( $coursesToRemove ){
					delete_transient( 'wpcw_remove_courses_' . $level_id );
				}

				if ( $coursesToAdd ){
					delete_transient( 'wpcw_add_courses_' . $level_id );
				}

				return;
			}

			if ( isset( $_POST['retroactive_assignment'] ) ) {
				$step  = 1;
				$count = 0;
				$steps = 'continue';
			}

			// Get user's assigned products and enroll them into courses accordingly.
			$customers = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT user_id, id
			         FROM {$mepr_db->transactions}
				     WHERE product_id = %d
				     AND (status = 'confirmed' OR status = 'complete')
				     LIMIT %d
				     OFFSET %d",
					$level_id,
					$batch,
					$count
				), ARRAY_A );

			if ( ! $customers && ! isset( $_GET['action'] ) ) {
				$page->showMessage( esc_html__( 'No existing customers found for the specified product.', 'wpcw-wc-addon' ) );

				return;
			}

			if ( $customers && 'continue' === $steps ) {
				if ( count( $customers ) < $batch ) {
					$steps = 'finished';
				}
				
				foreach ( $customers as $customer ) {
					wpcw_log($customer);
					$customer_id = $customer['user_id'];
					$user    = new MeprUser( $customer_id );

				if ( $coursesToRemove ){
						$products = array();
						$courseIDList = array();

						$product_subscriptions = $user->active_product_subscriptions( 'ids', true );

						foreach ( $product_subscriptions as $subscription ) { 
								// Got courses for this product.
								$courses = $this->getCourseAccessListForLevel( $subscription ); 

								if ( $courses ) {
									foreach ( $courses as $courseIDToKeep => $levelID ) { 
										$courseIDList[] = $courseIDToKeep; 
									}
								}
							}

						$removeCourses = array_diff( $coursesToRemove, $courseIDList );

						// De-enroll students from specified courses
						$this->handle_course_de_enrollment( $customer_id, $removeCourses );

				}

				if ( $coursesToAdd ){
						// Enroll students into specified courses
						WPCW_courses_syncUserAccess( $customer_id, $coursesToAdd, 'add' );
					}
					// Increment Count.
					$count += 1;
				}

				$step += 1;
			}  else {
				$steps = 'finished';
			}

			$page->showMessage( esc_html__( 'Please wait. Retroactively updating existing customers...', 'wpcw-wc-addon' ) );

			$location_url = add_query_arg( array(
				'page'     => $this->extensionID,
				'level_id' => $level_id,
				'step'     => $step,
				'count'    => $count,
				'steps'    => $steps,
				'action'   => 'retroactiveassignment'
			), admin_url( 'admin.php' ) );

			?>
			<script type="text/javascript">
				setTimeout( function () {
					document.location.href = "<?php echo $location_url; ?>";
				}, 1000 );
			</script>
			<?php
		}

		/**
		 * Update User Course Access.
		 *
		 * Function just for handling course enrollment.
		 *
		 * @since 1.4.0
		 *
		 * @param int $object The transaction object.
		 */
		public function handle_updateUserCourseAccess( $object ) {

			// Get User Object.
			$user = new MeprUser( $object->user_id );

			// Get product list for user. Returns an array of Product ID's the user has purchased and is paid up on.
			$product_subscriptions = $user->active_product_subscriptions( 'ids', true );

			// Get user ID.
			$user_id = $user->ID;

			// Over to the parent class to handle the sync of data.
			$this->handle_courseSync( $user_id, $product_subscriptions );
		}

		/**
		 * Handle Course De-Enrollment.
		 *
		 * @since 1.5.0
		 *
		 * @param int   $student_id The student id.
		 * @param array $course_ids The course ids to enroll.
		 */
		public function handle_course_de_enrollment( $student_id, $course_ids = array() ) {
			global $wpdb, $wpcwdb;

			if ( empty( $course_ids ) || ! is_array( $course_ids ) ) {
				return;
			}

			$csv_course_ids = implode( ',', $course_ids );

			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpcwdb->user_courses} WHERE user_id = %d AND course_id IN ({$csv_course_ids})", $student_id ) ); // phpcs:ignore WordPress.DB

			WPCW_queue_dripfeed::updateQueueItems_removeUser_fromCourseList( $student_id, $course_ids );
		}

		/**
		 * Detect presence of MemberPress plugin.
		 *
		 * @since 1.4.0
		 */
		public function found_membershipTool() {
			return function_exists( 'mepr_plugin_info' );
		}
	}
}

if ( ! class_exists( 'WPCW_Members_MemberPress' ) ) {
	/**
	 * Class WPCW_Members_MemberPress.
	 *
	 * Included for compatability with old addon.
	 *
	 * @since 1.0.0
	 */
	class WPCW_Members_MemberPress extends WPCW_MP_Membership {

	}
}
