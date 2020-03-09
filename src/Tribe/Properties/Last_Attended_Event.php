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
				'label'     => 'Last Attended Event ID',
				'groupName' => $this->group_name,
				'type'      => 'number',
				'fieldType' => 'number',
			],
			'last_attended_event_name'                 => [
				'label'     => 'Last Attended Event Name',
				'groupName' => $this->group_name,
				'type'      => 'string',
				'fieldType' => 'text',
			],
			'last_attended_event_venue'                => [
				'label'     => 'Last Attended Event Venue',
				'groupName' => $this->group_name,
				'type'      => 'string',
				'fieldType' => 'text',
			],
			'last_attended_event_venue_address'        => [
				'label'     => 'Last Attended Event Venue Address',
				'groupName' => $this->group_name,
				'type'      => 'string',
				'fieldType' => 'text',
			],
			'last_attended_event_venue_city'           => [
				'label'     => 'Last Attended Event Venue City',
				'groupName' => $this->group_name,
				'type'      => 'string',
				'fieldType' => 'text',
			],
			'last_attended_event_venue_state_province' => [
				'label'     => 'Last Attended Event Venue State|Province',
				'groupName' => $this->group_name,
				'type'      => 'string',
				'fieldType' => 'text',
			],
			'last_attended_event_venue_postal_code'    => [
				'label'     => 'Last Attended Event Venue Postal Code',
				'groupName' => $this->group_name,
				'type'      => 'string',
				'fieldType' => 'text',
			],
			'last_attended_event_organizer'            => [
				'label'     => 'Last Attended Event Venue Organizer',
				'groupName' => $this->group_name,
				'type'      => 'string',
				'fieldType' => 'text',
			],
			'last_attended_event_is_featured'          => [
				'label'     => 'Last Attended Event is Featured',
				'groupName' => $this->group_name,
				'type'      => 'string',
				'fieldType' => 'booleancheckbox', //stores true as string in HubSpot
			],
			'last_attended_event_cost'                 => [
				'label'     => 'Last Attended Event Cost',
				'groupName' => $this->group_name,
				'type'      => 'string',
				'fieldType' => 'text',
			],
			'last_attended_event_start_date_utc'   => [
				'label'     => 'Last Attended Event Start Date (UTC)',
				'groupName' => $this->group_name,
				'type'      => 'date',
				'fieldType' => 'date',
			],
			'last_attended_event_start_datetime_utc' => [
				'label'     => 'Last Attended Event Start DateTime (UTC)',
				'groupName' => $this->group_name,
				'fieldType' => 'datetime',
				'type'      => 'datetime',
			],
			'last_attended_event_timezone'             => [
				'label'     => 'Last Attended Event Timezone',
				'groupName' => $this->group_name,
				'type'      => 'string',
				'fieldType' => 'text',
			],
			'last_attended_event_duration'             => [
				'label'     => 'Last Attended Event Duration',
				'groupName' => $this->group_name,
				'type'      => 'number',
				'fieldType' => 'number',
			],
		];
	}
}