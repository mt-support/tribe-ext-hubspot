<?php

namespace Tribe\HubSpot\Process;

/**
 * Class Setup
 *
 * @package Tribe\HubSpot\Process
 */
class Setup {

	/**
	 * Name of the CPT that holds HubSpot Subscription.
	 *
	 * @since 1.0
	 *
	 * @var string
	 */
	protected $post_type = 'tribe_hubspot';

	/**
	 * The paid status key.
	 *
	 * @since 1.0
	 */
	protected $status_complete = 'tribe-hubspot-complete';

	/**
	 * The pending status key.
	 *
	 * @since 1.0
	 */
	protected $status_pending = 'tribe-hubspot-pending';

	/**
	 * The failed status key.
	 *
	 * @since 1.0
	 */
	protected $status_failed = 'tribe-hubspot-failed';

	/**
	 * Allowed statuses for actions.
	 *
	 * @since 1.0
	 *
	 * @var array
	 */
	protected $supported_statuses = [];

	public function hook() {

		add_action( 'init', [ $this, 'on_init' ] );

	}

	/**
	 * Holds all functions we want to fire on `init`.
	 *
	 * @since 1.0
	 *
	 */
	public function on_init() {

		$this->register_post_type();
		$this->register_post_statuses();
	}

	/**
	 * Register the custom post type.
	 *
	 * @since 1.0
	 */
	public function register_post_type() {
		$hubspot_post_args = [
			'label'              => 'HubSpot Subscription',
			'labels'             => [
				'name'          => __( 'HubSpot Subscriptions', 'tribe-ext-hubspot' ),
				'singular_name' => __( 'HubSpot Subscription', 'tribe-ext-hubspot' ),
			],
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'public'             => false,
			'publicly_queryable' => false,
			'query_var'          => false,
			'rewrite'            => false,
			'show_in_menu'       => false,
			'show_ui'            => false,

		];

		/**
		 * Filter the arguments that craft the HubSpot Subscription post type.
		 *
		 * @since 1.0
		 *
		 * @param array $hubspot_post_args Post type arguments, passed to register_post_type()
		 *
		 * @see   register_post_type
		 *
		 */
		$hubspot_post_args = apply_filters( 'tribe_hubspot_register_hubspot_subscription_post_type_args', $hubspot_post_args );

		register_post_type( $this->post_type, $hubspot_post_args );
	}

	/**
	 * Build our custom post statuses.
	 *
	 * @since 1.0
	 */
	protected function build_post_statuses() {
		$statuses = [
			$this->status_complete => [
				'label'                     => __( 'Complete', 'tribe-ext-hubspot' ),
				'public'                    => false,
				'exclude_from_search'       => true,
				'show_in_admin_all_list'    => false,
				'show_in_admin_status_list' => false,
			],
			$this->status_pending  => [
				'label'                     => __( 'Pending', 'tribe-ext-hubspot' ),
				'public'                    => false,
				'exclude_from_search'       => true,
				'show_in_admin_all_list'    => false,
				'show_in_admin_status_list' => false,
			],
			$this->status_failed   => [
				'label'                     => __( 'Failed', 'tribe-ext-hubspot' ),
				'public'                    => false,
				'exclude_from_search'       => true,
				'show_in_admin_all_list'    => false,
				'show_in_admin_status_list' => false,
			],
		];

		/**
		 * Filter the arguments that craft the HubSpot Subscription post statuses.
		 *
		 * @since 1.0
		 *
		 * @param array $statuses List of post status arguments that will be looped and passed to register_post_status()
		 *
		 * @see   register_post_type
		 *
		 */
		$this->supported_statuses = apply_filters( 'tribe_hubspot_register_subscription_post_statuses', $statuses );
	}

	/**
	 * Register our custom post $statuses.
	 *
	 * @since 1.0
	 */
	public function register_post_statuses() {
		$this->build_post_statuses();

		// Don't register post status until we're doing / have done action init.
		if ( ! doing_action( 'init' ) && ! did_action( 'init' ) ) {
			return;
		}

		foreach ( $this->supported_statuses as $status => $args ) {
			register_post_status( $status, $args );
		}
	}

	/**
	 * Get status label from HubSpot Subscription status.
	 *
	 * @since 1.0
	 *
	 * @param string HubSpot Subscription status.
	 *
	 * @return string|null Status label for HubSpot Subscription status, or null if not found.
	 */
	public function get_status_label( $status ) {
		// Maybe register post statuses.
		if ( empty( $this->supported_statuses ) ) {
			$this->build_post_statuses();
		}

		if ( isset( $this->supported_statuses[ $status ] ) ) {
			return $this->supported_statuses[ $status ]['label'];
		}

		return null;
	}

	/**
	 * Get list of supported statuses.
	 *
	 * @since 1.0
	 *
	 * @return array List of supported statuses.
	 */
	public function get_supported_statuses() {
		// Maybe register post statuses.
		if ( empty( $this->supported_statuses ) ) {
			$this->build_post_statuses();
		}

		return $this->supported_statuses;
	}
}

