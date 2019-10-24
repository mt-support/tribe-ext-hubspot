<?php

namespace Tribe\HubSpot\Properties;

/**
 * Class First_Order
 *
 * @package Tribe\HubSpot\Properties
 */
class First_Order extends Base {

	/**
	 * @var string
	 */
	protected $properties_grouping_name = 'first_order';

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
			'first_order_date_utc'             => [
				'label'     => 'First Order Date (UTC)',
				'groupName' => $this->group_name,
				'type'      => 'datetime',
				'fieldType' => 'date',
			],
			'first_order_total'                => [
				'label'       => 'First Order Total',
				'groupName'   => $this->group_name,
				'description' => 'Total amount in cents.',
				'type'        => 'number',
				'fieldType'   => 'number',
			],
			'first_order_ticket_quantity'      => [
				'label'     => 'First Order Ticket Quantity',
				'groupName' => $this->group_name,
				'type'      => 'number',
				'fieldType' => 'number',
			],
			'first_order_ticket_type_quantity' => [
				'label'     => 'First Order Ticket Type Quantity', // Number of Different Ticket Types
				'groupName' => $this->group_name,
				'type'      => 'number',
				'fieldType' => 'number',
			],
		];
	}
}