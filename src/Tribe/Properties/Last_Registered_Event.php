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
				'label'     => 'Last Registered Event ID',
				'groupName' => $this->group_name,
				'type'      => 'number',
				'fieldType' => 'number',
			],
			'last_registered_event_name'                 => [
				'label'     => 'Last Registered Event Name',
				'groupName' => $this->group_name,
				'type'      => 'string',
				'fieldType' => 'text',
			],
			'last_registered_event_venue'                => [
				'label'     => 'Last Registered Event Venue',
				'groupName' => $this->group_name,
				'type'      => 'string',
				'fieldType' => 'text',
			],
			'last_registered_event_venue_address'        => [
				'label'     => 'Last Registered Event Venue Address',
				'groupName' => $this->group_name,
				'type'      => 'string',
				'fieldType' => 'text',
			],
			'last_registered_event_venue_city'           => [
				'label'     => 'Last Registered Event Venue CIty',
				'groupName' => $this->group_name,
				'type'      => 'string',
				'fieldType' => 'text',
			],
			'last_registered_event_venue_state_province' => [
				'label'     => 'Last Registered Event Venue State|Province',
				'groupName' => $this->group_name,
				'type'      => 'string',
				'fieldType' => 'text',
			],
			'last_registered_event_venue_postal_code'    => [
				'label'     => 'Last Registered Event Venue Postal Code',
				'groupName' => $this->group_name,
				'type'      => 'string',
				'fieldType' => 'text',
			],
			'last_registered_event_organizer'            => [
				'label'     => 'Last Registered Event Venue Organizer',
				'groupName' => $this->group_name,
				'type'      => 'string',
				'fieldType' => 'text',
			],
			'last_registered_event_is_featured'          => [
				'label'     => 'Last Registered Event is Featured',
				'groupName' => $this->group_name,
				'type'      => 'string',
				'fieldType' => 'booleancheckbox', //stores true as string in HubSpot
			],
			'last_registered_event_cost'                 => [
				'label'     => 'Last Registered Event Cost',
				'groupName' => $this->group_name,
				'type'      => 'string',
				'fieldType' => 'text',
			],
			'last_registered_event_start_datetime_utc'   => [
				'label'     => 'Last Registered Event State Datetime (UtC)',
				'groupName' => $this->group_name,
				'type'      => 'date',
				'fieldType' => 'date',
			],
			'last_registered_event_start_time_utc'       => [
				'label'     => 'Last Registered Event Start Time (UTC)',
				'groupName' => $this->group_name,
				'type'      => 'datetime',
				'fieldType' => 'date',
			],
			'last_registered_event_timezone'             => [
				'label'     => 'Last Registered Event Timezone',
				'groupName' => $this->group_name,
				'type'      => 'string',
				'fieldType' => 'text',
			],
			'last_registered_event_duration'             => [
				'label'     => 'Last Registered Event Duration',
				'groupName' => $this->group_name,
				'type'      => 'number',
				'fieldType' => 'number',
			],
		];
	}
}