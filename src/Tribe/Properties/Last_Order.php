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
				'label'     => 'Last Order Date (UTC)',
				'groupName' => $this->group_name,
				'type'      => 'datetime',
				'fieldType' => 'date',
			],
			'last_order_total'                => [
				'label'       => 'Last Order Total',
				'groupName'   => $this->group_name,
				'description' => 'Total amount in cents.',
				'type'        => 'number',
				'fieldType'   => 'number',
			],
			'last_order_ticket_quantity'      => [
				'label'     => 'Last Order Ticket Quantity',
				'groupName' => $this->group_name,
				'type'      => 'number',
				'fieldType' => 'number',
			],
			'last_order_ticket_type_quantity' => [
				'label'     => 'Last Order Ticket Type Quantity', // Number of Different Ticket Types in Order
				'groupName' => $this->group_name,
				'type'      => 'number',
				'fieldType' => 'number',
			],
		];
	}
}