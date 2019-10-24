<?php

namespace Tribe\HubSpot\Properties;

/**
 * Class Last_Attended_Event
 *
 * @package Tribe\HubSpot\Properties
 */
class Last_Attended_Event extends Base {

	/**
	 * @var string
	 */
	protected $properties_grouping_name = 'last_attended_event';

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
			'last_attended_event_id'                   => [
				'label'     => __( 'Last Attended Event ID', 'tribe-ext-hubspot' ),
				'groupName' => $this->group_name,
				'type'      => 'number',
				'fieldType' => 'number',
			],
			'last_attended_event_name'                 => [
				'label'     => __( 'Last Attended Event Name', 'tribe-ext-hubspot' ),
				'groupName' => $this->group_name,
				'type'      => 'string',
				'fieldType' => 'text',
			],
			'last_attended_event_venue'                => [
				'label'     => __( 'Last Attended Event Venue', 'tribe-ext-hubspot' ),
				'groupName' => $this->group_name,
				'type'      => 'string',
				'fieldType' => 'text',
			],
			'last_attended_event_venue_address'        => [
				'label'     => __( 'Last Attended Event Venue Address', 'tribe-ext-hubspot' ),
				'groupName' => $this->group_name,
				'type'      => 'string',
				'fieldType' => 'text',
			],
			'last_attended_event_venue_city'           => [
				'label'     => __( 'Last Attended Event Venue City', 'tribe-ext-hubspot' ),
				'groupName' => $this->group_name,
				'type'      => 'string',
				'fieldType' => 'text',
			],
			'last_attended_event_venue_state_province' => [
				'label'     => __( 'Last Attended Event Venue State|Province', 'tribe-ext-hubspot' ),
				'groupName' => $this->group_name,
				'type'      => 'string',
				'fieldType' => 'text',
			],
			'last_attended_event_venue_postal_code'    => [
				'label'     => __( 'Last Attended Event Venue Postal Code', 'tribe-ext-hubspot' ),
				'groupName' => $this->group_name,
				'type'      => 'string',
				'fieldType' => 'text',
			],
			'last_attended_event_organizer'            => [
				'label'     => __( 'Last Attended Event Venue Organizer', 'tribe-ext-hubspot' ),
				'groupName' => $this->group_name,
				'type'      => 'string',
				'fieldType' => 'text',
			],
			'last_attended_event_is_featured'          => [
				'label'     => __( 'Last Attended Event is Featured', 'tribe-ext-hubspot' ),
				'groupName' => $this->group_name,
				'type'      => 'string',
				'fieldType' => 'booleancheckbox', //stores true as string in HubSpot
			],
			'last_attended_event_cost'                 => [
				'label'     => __( 'Last Attended Event Cost', 'tribe-ext-hubspot' ),
				'groupName' => $this->group_name,
				'type'      => 'string',
				'fieldType' => 'text',
			],
			'last_attended_event_start_datetime_utc'   => [
				'label'     => __( 'Last Attended Event State Datetime (UtC)', 'tribe-ext-hubspot' ),
				'groupName' => $this->group_name,
				'type'      => 'date',
				'fieldType' => 'date',
			],
			'last_attended_event_start_time_utc'       => [
				'label'     => __( 'Last Attended Event Start Time (UTC)', 'tribe-ext-hubspot' ),
				'groupName' => $this->group_name,
				'type'      => 'datetime',
				'fieldType' => 'date',
			],
			'last_attended_event_timezone'             => [
				'label'     => __( 'Last Attended Event Timezone', 'tribe-ext-hubspot' ),
				'groupName' => $this->group_name,
				'type'      => 'string',
				'fieldType' => 'text',
			],
			'last_attended_event_duration'             => [
				'label'     => __( 'Last Attended Event Duration', 'tribe-ext-hubspot' ),
				'groupName' => $this->group_name,
				'type'      => 'number',
				'fieldType' => 'number',
			],
		];
	}
}