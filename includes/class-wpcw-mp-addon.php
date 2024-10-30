<?php
/**
 * WP Courseware Memberpress Add-on Class
 *
 * @package WPCW_MP_Addon/Includes
 * @since 1.4.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPCW_MP_Addon' ) ) {
	/**
	 * Class WPCW_MP_Addon.
	 *
	 * @since 1.4.0
	 */
	final class WPCW_MP_Addon {

		/**
		 * @var bool Can Load Addon?
		 * @since 1.4.0
		 */
		public $can_load = false;

		/**
		 * @var WPCW_MP_Membership $membership The membership object.
		 * @since 1.3.0
		 */
		public $membership;

		/**
		 * @var WPCW_MP_Menu_Courses $menu_courses The menu courses object.
		 * @since 1.3.0
		 */
		public $menu_courses;

		/**
		 * Innitalize.
		 *
		 * @since 1.3.0
		 *
		 * @return WPCW_MP_Addon $mp_addon The addon object.
		 */
		public static function init() {
			$mp_addon = new self();

			$mp_addon->membership   = $mp_addon->load_membership();
			$mp_addon->menu_courses = $mp_addon->load_menu_courses();

			/**
			 * Action: Initalize Memberpress Addon.
			 *
			 * @since 1.4.0
			 *
			 * @param WPCW_MP_Addon $mp_addon The WPCW_MP_Addon object.
			 */
			do_action( 'wpcw_mp_addon_init', $mp_addon );

			return $mp_addon;
		}

		/**
		 * Load Compatability.
		 *
		 * @since 1.3.0
		 *
		 * @return null|WPCW_WC_Membership Null or WPCW_WC_Membership class object.
		 */
		public function load_membership() {
			// Load Class.
			$mp_membership = new WPCW_MP_Membership();

			// Check for WP Courseware.
			if ( ! $mp_membership->found_wpcourseware() ) {
				$mp_membership->attach_showWPCWNotDetectedMessage();
				return;
			}

			// Check for Memberpress.
			if ( ! $mp_membership->found_membershipTool() ) {
				$mp_membership->attach_showToolNotDetectedMessage();
				return;
			}

			/**
			 * Filter: WPCW Memberpress Addon Can Load Flag.
			 *
			 * @since 1.4.0
			 *
			 * @param bool $can_load If the addon can load.
			 */
			$this->can_load = apply_filters( 'wpcw_mp_addon_can_load', true );

			// Attach to tools.
			$mp_membership->attachToTools();

			/**
			 * Action: Load Membership.
			 *
			 * @since 1.4.0
			 *
			 * @param WPCW_MP_Membership $mp_membership The WPCW_MP_Membership class object.
			 * @param WPCW_MP_Addon      $this The WPCW_MP_Addon class object.
			 */
			do_action( 'wpcw_mp_addon_load_membership', $mp_membership, $this );

			return $mp_membership;
		}

		/**
		 * Load Menu Courses.
		 *
		 * @since 1.4.0
		 *
		 * @return null|WPCW_WC_Menu_Courses Null or the WPCW_WC_Menu_Courses class object.
		 */
		public function load_menu_courses() {
			if ( ! $this->can_load ) {
				return;
			}

			// Initialize Plugin.
			$mp_menu_courses = new WPCW_MP_Menu_Courses();
			$mp_menu_courses->hooks();

			/**
			 * Action: Load Menu Courses.
			 *
			 * @since 1.4.0
			 *
			 * @param WPCW_MP_Menu_Courses $mp_menu_courses The WPCW_MP_Menu_Courses class object.
			 * @param WPCW_MP_Addon        $this The WPCW_MP_Addon class object.
			 */
			do_action( 'wpcw_mp_addon_load_menu_courses', $mp_menu_courses, $this );

			return $mp_menu_courses;
		}
	}
}
