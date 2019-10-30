<?php

namespace Tribe\HubSpot\Properties;

/**
 * Class Last_Registered_Event
 *
 * @package Tribe\HubSpot\Properties
 */
class Last_Registered_Event extends Base {

	/**
	 * @var string
	 */
	protected $properties_grouping_name = 'last_registered_event';

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
			'last_registered_event_id'                   => [
				'label'     => __( 'Last Registered Event ID', 'tribe-ext-hubspot' ),
				'groupName' => $this->group_name,
				'type'      => 'number',
				'fieldType' => 'number',
			],
			'last_registered_event_name'                 => [
				'label'     => __( 'Last Registered Event Name', 'tribe-ext-hubspot' ),
				'groupName' => $this->group_name,
				'type'      => 'string',
				'fieldType' => 'text',
			],
			'last_registered_event_venue'                => [
				'label'     => __( 'Last Registered Event Venue', 'tribe-ext-hubspot' ),
				'groupName' => $this->group_name,
				'type'      => 'string',
				'fieldType' => 'text',
			],
			'last_registered_event_venue_address'        => [
				'label'     => __( 'Last Registered Event Venue Address', 'tribe-ext-hubspot' ),
				'groupName' => $this->group_name,
				'type'      => 'string',
				'fieldType' => 'text',
			],
			'last_registered_event_venue_city'           => [
				'label'     => __( 'Last Registered Event Venue CIty', 'tribe-ext-hubspot' ),
				'groupName' => $this->group_name,
				'type'      => 'string',
				'fieldType' => 'text',
			],
			'last_registered_event_venue_state_province' => [
				'label'     => __( 'Last Registered Event Venue State|Province', 'tribe-ext-hubspot' ),
				'groupName' => $this->group_name,
				'type'      => 'string',
				'fieldType' => 'text',
			],
			'last_registered_event_venue_postal_code'    => [
				'label'     => __( 'Last Registered Event Venue Postal Code', 'tribe-ext-hubspot' ),
				'groupName' => $this->group_name,
				'type'      => 'string',
				'fieldType' => 'text',
			],
			'last_registered_event_organizer'            => [
				'label'     => __( 'Last Registered Event Venue Organizer', 'tribe-ext-hubspot' ),
				'groupName' => $this->group_name,
				'type'      => 'string',
				'fieldType' => 'text',
			],
			'last_registered_event_is_featured'          => [
				'label'     => __( 'Last Registered Event is Featured', 'tribe-ext-hubspot' ),
				'groupName' => $this->group_name,
				'type'      => 'string',
				'fieldType' => 'booleancheckbox', //stores true as string in HubSpot
			],
			'last_registered_event_cost'                 => [
				'label'     => __( 'Last Registered Event Cost', 'tribe-ext-hubspot' ),
				'groupName' => $this->group_name,
				'type'      => 'string',
				'fieldType' => 'text',
			],
			'last_registered_event_start_datetime_utc'   => [
				'label'     => __( 'Last Registered Event State Datetime (UtC)', 'tribe-ext-hubspot' ),
				'groupName' => $this->group_name,
				'type'      => 'date',
				'fieldType' => 'date',
			],
			'last_registered_event_start_time_utc'       => [
				'label'     => __( 'Last Registered Event Start Time (UTC)', 'tribe-ext-hubspot' ),
				'groupName' => $this->group_name,
				'type'      => 'datetime',
				'fieldType' => 'date',
			],
			'last_registered_event_timezone'             => [
				'label'     => __( 'Last Registered Event Timezone', 'tribe-ext-hubspot' ),
				'groupName' => $this->group_name,
				'type'      => 'string',
				'fieldType' => 'text',
			],
			'last_registered_event_duration'             => [
				'label'     => __( 'Last Registered Event Duration', 'tribe-ext-hubspot' ),
				'groupName' => $this->group_name,
				'type'      => 'number',
				'fieldType' => 'number',
			],
		];
	}
}