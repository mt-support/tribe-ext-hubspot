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
				'label'     => 'Last Registered Ticket ID',
				'groupName' => $this->group_name,
				'type'      => 'number',
				'fieldType' => 'number',
			],
			'last_registered_ticket_type'          => [
				'label'     => 'Last Registered Ticket Name',
				'groupName' => $this->group_name,
				'type'      => 'string',
				'fieldType' => 'text',
			],
			'last_registered_ticket_commerce'      => [
				'label'     => 'Last Registered Ticket Commerce',
				'groupName' => $this->group_name,
				'type'      => 'string',
				'fieldType' => 'text',
			],
			'last_registered_ticket_attendee_id'   => [
				'label'     => 'Last Registered Ticket Attendee ID',
				'groupName' => $this->group_name,
				'type'      => 'number',
				'fieldType' => 'number',
			],
			'last_registered_ticket_attendee_name' => [
				'label'     => 'Last Registered Ticket Attendee Name',
				'groupName' => $this->group_name,
				'type'      => 'string',
				'fieldType' => 'text',
			],
			'last_registered_ticket_rsvp_is_going' => [
				'label'     => 'Last Registered Ticket RSVP\'d is Going',
				'groupName' => $this->group_name,
				'type'      => 'number',
				'fieldType' => 'number',
			],
		];
	}
}