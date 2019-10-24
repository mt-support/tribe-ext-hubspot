<?php

namespace Tribe\HubSpot\Properties;

/**
 * Class Last_Order
 *
 * @package Tribe\HubSpot\Properties
 */
class Last_Order extends Base {

	/**
	 * @var string
	 */
	protected $properties_grouping_name = 'last_order';

	/**
	 * @var array
	 */
	protected $properties = [];

	public function __construct() {

		$this->set_properties();
	}

	/**
	 * Set the Individual Properties for this Grouping
	 *
	 * @since 1.0
	 *
	 */
	public function set_properties() {

		$this->properties = [
			'last_order_date_utc'             => [
				'label'     => __( 'Last Order Date (UTC)', 'tribe-ext-hubspot' ),
				'groupName' => $this->group_name,
				'type'      => 'datetime',
				'fieldType' => 'date',
			],
			'last_order_total'                => [
				'label'       => __( 'Last Order Total', 'tribe-ext-hubspot' ),
				'groupName'   => $this->group_name,
				'description' => __( 'Total amount in cents.', 'tribe-ext-hubspot' ),
				'type'        => 'number',
				'fieldType'   => 'number',
			],
			'last_order_ticket_quantity'      => [
				'label'     => __( 'Last Order Ticket Quantity', 'tribe-ext-hubspot' ),
				'groupName' => $this->group_name,
				'type'      => 'number',
				'fieldType' => 'number',
			],
			'last_order_ticket_type_quantity' => [
				'label'     => __( 'Last Order Ticket Type Quantity', 'tribe-ext-hubspot' ),
 				'groupName' => $this->group_name,
				'type'      => 'number',
				'fieldType' => 'number',
			],
		];
	}
}