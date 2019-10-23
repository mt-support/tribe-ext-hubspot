<?php

namespace Tribe\HubSpot\Properties;

/**
 * Class Last_Registered_Ticket
 *
 * @package Tribe\HubSpot\Properties
 */
class Last_Registered_Ticket extends Base {

	/**
	 * @var string
	 */
	protected $properties_grouping_name = 'last_registered_ticket';

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
			'last_registered_ticket_type_id'       => [
				'label'     => __( 'Last Registered Ticket ID', 'tribe-ext-hubspot' ),
				'groupName' => $this->group_name,
				'type'      => 'number',
				'fieldType' => 'number',
			],
			'last_registered_ticket_type'          => [
				'label'     => __( 'Last Registered Ticket Name', 'tribe-ext-hubspot' ),
				'groupName' => $this->group_name,
				'type'      => 'string',
				'fieldType' => 'text',
			],
			'last_registered_ticket_commerce'      => [
				'label'     => __( 'Last Registered Ticket Commerce', 'tribe-ext-hubspot' ),
				'groupName' => $this->group_name,
				'type'      => 'string',
				'fieldType' => 'text',
			],
			'last_registered_ticket_attendee_id'   => [
				'label'     => __( 'Last Registered Ticket Attendee ID', 'tribe-ext-hubspot' ),
				'groupName' => $this->group_name,
				'type'      => 'number',
				'fieldType' => 'number',
			],
			'last_registered_ticket_attendee_name' => [
				'label'     => __( 'Last Registered Ticket Attendee Name', 'tribe-ext-hubspot' ),
				'groupName' => $this->group_name,
				'type'      => 'string',
				'fieldType' => 'text',
			],
			'last_registered_ticket_rsvp_is_going' => [
				'label'     => __( 'Last Registered Ticket RSVP\'d is Going', 'tribe-ext-hubspot' ),
				'groupName' => $this->group_name,
				'type'      => 'number',
				'fieldType' => 'number',
			],
		];
	}
}