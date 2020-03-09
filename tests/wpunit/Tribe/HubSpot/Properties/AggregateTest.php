<?php

namespace Tribe\HubSpot\Properties;

use Codeception\TestCase\WPTestCase;
use stdClass;
use Tribe\Events\Test\Factories\Event;
use Tribe\Tickets_Plus\Test\Commerce\WooCommerce\Order_Maker;
use Tribe\Tickets_Plus\Test\Commerce\WooCommerce\Ticket_Maker;
use Tribe__Tickets_Plus__Commerce__WooCommerce__Main as Main_Woo;
use Tribe\HubSpot\Subscribe\Woo as Woo_Subscribe;
use Tribe\HubSpot\Properties\Event_Data as Data;
use Tribe\HubSpot\Properties\Aggregate_Data as Aggregate_Data;

class AggregateTest extends WPTestCase {

	use Ticket_Maker;
	use Order_Maker;

	public function setUp() {
		// before
		parent::setUp();

		$this->factory()->event = new Event();

		$this->aggregate_data = new Aggregate_Data();
	}

	public function tearDown() {
		// your tear down methods here
		// then
		parent::tearDown();
	}

	/**
	 * @test
	 */
	public function it_should_return_calculated_aggregate_data_for_register() {
		$current_properties = $this->make_base_data();
		$tickets_qty        = 1;
		$events_per_order   = 2;

		$agg_data = $this->aggregate_data->get_values( 'register', $current_properties, $tickets_qty, $events_per_order );

		// is event wrong should that not take into account the amount of events in an order?

		self::assertEquals( 5, $agg_data['total_registered_events'] );
		self::assertEquals( 6, count( $agg_data['values'] ) );
		self::assertEquals( 2, count( $agg_data ) );

		//total_registered_events
		self::assertEquals( 'total_registered_events', $agg_data['values'][0]['property'] );
		self::assertEquals( 5, $agg_data['values'][0]['value'] );

		//total_number_of_orders
		self::assertEquals( 'total_number_of_orders', $agg_data['values'][1]['property'] );
		self::assertEquals( 6, $agg_data['values'][1]['value'] );

		//average_tickets_per_order_list
		self::assertEquals( 'average_tickets_per_order_list', $agg_data['values'][2]['property'] );
		self::assertEquals( '1,1,1,1', $agg_data['values'][2]['value'] );

		//average_tickets_per_order
		self::assertEquals( 'average_tickets_per_order', $agg_data['values'][3]['property'] );
		self::assertEquals( 1, $agg_data['values'][3]['value'] );

		//average_events_per_order_list
		self::assertEquals( 'average_events_per_order_list', $agg_data['values'][4]['property'] );
		self::assertEquals( '2,2,2,2', $agg_data['values'][4]['value'] );

		//average_events_per_order
		self::assertEquals( 'average_events_per_order', $agg_data['values'][5]['property'] );
		self::assertEquals( 2, $agg_data['values'][5]['value'] );
	}

	protected function make_base_data( $values = [] ) {
		$current_properties = new stdClass();

		$properties = [
			'total_registered_events',
			'total_number_of_orders',
			'average_tickets_per_order',
			'average_tickets_per_order_list',
			'average_events_per_order',
			'average_events_per_order_list',
			'total_attended_events',
		];

		if ( empty( $values ) ) {
			$values = [
				4,
				5,
				1,
				'1,1,1',
				1,
				'2,2,2',
				2,
			];
		}

		foreach ( $properties as $key => $property ) {
			$current_properties->{$property}        = new stdClass();
			$current_properties->{$property}->value = $values[ $key ];
		}

		return $current_properties;
	}

	/**
	 * @test
	 */
	public function it_should_return_calculated_aggregate_data_for_register_2() {
		$values = [
			8,
			10,
			1,
			'1,1,1,4,3,3',
			1,
			'2,1,2,3,2,1',
			7,
		];

		$current_properties = $this->make_base_data( $values );
		$tickets_qty        = 2;
		$events_per_order   = 1;

		$agg_data = $this->aggregate_data->get_values( 'register', $current_properties, $tickets_qty, $events_per_order );

		self::assertEquals( 9, $agg_data['total_registered_events'] );
		self::assertEquals( 6, count( $agg_data['values'] ) );
		self::assertEquals( 2, count( $agg_data ) );

		//total_registered_events
		self::assertEquals( 'total_registered_events', $agg_data['values'][0]['property'] );
		self::assertEquals( 9, $agg_data['values'][0]['value'] );

		//total_number_of_orders
		self::assertEquals( 'total_number_of_orders', $agg_data['values'][1]['property'] );
		self::assertEquals( 11, $agg_data['values'][1]['value'] );

		//average_tickets_per_order_list
		self::assertEquals( 'average_tickets_per_order_list', $agg_data['values'][2]['property'] );
		self::assertEquals( '1,1,1,4,3,3,2', $agg_data['values'][2]['value'] );

		//average_tickets_per_order
		self::assertEquals( 'average_tickets_per_order', $agg_data['values'][3]['property'] );
		self::assertEquals( 2.1428571428571, $agg_data['values'][3]['value'] );

		//average_events_per_order_list
		self::assertEquals( 'average_events_per_order_list', $agg_data['values'][4]['property'] );
		self::assertEquals( '2,1,2,3,2,1,1', $agg_data['values'][4]['value'] );

		//average_events_per_order
		self::assertEquals( 'average_events_per_order', $agg_data['values'][5]['property'] );
		self::assertEquals( 1.7142857142857, $agg_data['values'][5]['value'] );
	}

	/**
	 * @test
	 */
	public function it_should_return_calculated_aggregate_data_for_register_3() {
		$values = [
			1543,
			1445,
			1,
			'1,1,1,4,3,3,3,2,1,4,3,2,1,5,3,2,1,3,2,1',
			1,
			'2,1,2,3,2,1,1,2,3,1,2,3,4,2,1,3,4,2,1,3',
			1553,
		];

		$current_properties = $this->make_base_data( $values );
		$tickets_qty        = 6;
		$events_per_order   = 3;

		$agg_data = $this->aggregate_data->get_values( 'register', $current_properties, $tickets_qty, $events_per_order );

		self::assertEquals( 1544, $agg_data['total_registered_events'] );
		self::assertEquals( 6, count( $agg_data['values'] ) );
		self::assertEquals( 2, count( $agg_data ) );

		//total_registered_events
		self::assertEquals( 'total_registered_events', $agg_data['values'][0]['property'] );
		self::assertEquals( 1544, $agg_data['values'][0]['value'] );

		//total_number_of_orders
		self::assertEquals( 'total_number_of_orders', $agg_data['values'][1]['property'] );
		self::assertEquals( 1446, $agg_data['values'][1]['value'] );

		//average_tickets_per_order_list
		self::assertEquals( 'average_tickets_per_order_list', $agg_data['values'][2]['property'] );
		self::assertEquals( '1,1,1,4,3,3,3,2,1,4,3,2,1,5,3,2,1,3,2,1,6', $agg_data['values'][2]['value'] );

		//average_tickets_per_order
		self::assertEquals( 'average_tickets_per_order', $agg_data['values'][3]['property'] );
		self::assertEquals( 2.4761904761905, $agg_data['values'][3]['value'] );

		//average_events_per_order_list
		self::assertEquals( 'average_events_per_order_list', $agg_data['values'][4]['property'] );
		self::assertEquals( '2,1,2,3,2,1,1,2,3,1,2,3,4,2,1,3,4,2,1,3,3', $agg_data['values'][4]['value'] );

		//average_events_per_order
		self::assertEquals( 'average_events_per_order', $agg_data['values'][5]['property'] );
		self::assertEquals( 2.1904761904762, $agg_data['values'][5]['value'] );
	}

	/**
	 * @test
	 */
	public function it_should_return_calculated_aggregate_data_for_update() {
		$current_properties = $this->make_base_data();

		// update has no aggregate fields
		$agg_data = $this->aggregate_data->get_values( '', $current_properties, 0, 0 );

		self::assertEquals( 0, count( $agg_data ) );
	}

	/**
	 * @test
	 */
	public function it_should_return_calculated_aggregate_data_for_checkin() {
		$current_properties = $this->make_base_data();
		$agg_data           = $this->aggregate_data->get_values( 'checkin', $current_properties, 0, 0 );

		self::assertEquals( 1, count( $agg_data['values'] ) );
		self::assertEquals( 1, count( $agg_data ) );

		//total_attended_events
		self::assertEquals( 'total_attended_events', $agg_data['values'][0]['property'] );
		self::assertEquals( 3, $agg_data['values'][0]['value'] );
	}

	/**
	 * @test
	 */
	public function it_should_return_calculated_aggregate_data_for_checkin_2() {
		$values = [
			4,
			5,
			1,
			'1,1,1',
			1,
			'2,2,2',
			21,
		];

		$current_properties = $this->make_base_data( $values );
		$agg_data           = $this->aggregate_data->get_values( 'checkin', $current_properties, 0, 0 );

		self::assertEquals( 1, count( $agg_data['values'] ) );
		self::assertEquals( 1, count( $agg_data ) );

		//total_attended_events
		self::assertEquals( 'total_attended_events', $agg_data['values'][0]['property'] );
		self::assertEquals( 22, $agg_data['values'][0]['value'] );
	}

	/**
	 * @test
	 */
	public function it_should_return_calculated_aggregate_data_for_checkin_3() {
		$values = [
			4,
			5,
			1,
			'1,1,1',
			1,
			'2,2,2',
			9689,
		];

		$current_properties = $this->make_base_data( $values );
		$agg_data           = $this->aggregate_data->get_values( 'checkin', $current_properties, 0, 0 );

		self::assertEquals( 1, count( $agg_data['values'] ) );
		self::assertEquals( 1, count( $agg_data ) );

		//total_attended_events
		self::assertEquals( 'total_attended_events', $agg_data['values'][0]['property'] );
		self::assertEquals( 9690, $agg_data['values'][0]['value'] );
	}

	/**
	 * @test
	 */
	public function it_should_return_calculated_average_from_list_for_tickets() {
		$values = [
			1543,
			1445,
			1,
			'1,1,1,4,3,3,3,2,1,4,3,2,1,5,3,2,1,3,2,1',
			1,
			'2,1,2,3,2,1,1,2,3,1,2,3,4,2,1,3,4,2,1,3',
			1553,
		];

		$current_properties = $this->make_base_data( $values );
		$tickets_qty        = 6;
		$values             = $this->aggregate_data->get_average_from_list( $current_properties, 'average_tickets_per_order_list', $tickets_qty );

		self::assertEquals( '1,1,1,4,3,3,3,2,1,4,3,2,1,5,3,2,1,3,2,1,6', $values['list'] );
		self::assertEquals( 2.4761904761905, $values['current_value'] );
	}

	/**
	 * @test
	 */
	public function it_should_return_calculated_average_from_list_for_tickets_2() {
		$values = [
			1543,
			1445,
			1,
			'1,1,1,4,3,3,3,2,1,4,3,2,1,5,3,2,1,3,2,1,3,4,2,6,4,3,2,1,43,3,2,4,4,2,2,2,1,1,1,1',
			1,
			'2,1,2,3,2,1,1,2,3,1,2,3,4,2,1,3,4,2,1,3',
			1553,
		];

		$current_properties = $this->make_base_data( $values );
		$tickets_qty        = 12;
		$values             = $this->aggregate_data->get_average_from_list( $current_properties, 'average_tickets_per_order_list', $tickets_qty );

		self::assertEquals( '1,1,1,4,3,3,3,2,1,4,3,2,1,5,3,2,1,3,2,1,3,4,2,6,4,3,2,1,43,3,2,4,4,2,2,2,1,1,1,1,12', $values['list'] );
		self::assertEquals( 3.6341463414634, $values['current_value'] );
	}

	/**
	 * @test
	 */
	public function it_should_return_calculated_average_from_list_for_tickets_3() {
		$values = [
			1543,
			1445,
			1,
			'1,1,1,4,1,1,1',
			1,
			'2,1,2,3,2,1,1,2,3,1,2,3,4,2,1,3,4,2,1,3',
			1553,
		];

		$current_properties = $this->make_base_data( $values );
		$tickets_qty        = 1;
		$values             = $this->aggregate_data->get_average_from_list( $current_properties, 'average_tickets_per_order_list', $tickets_qty );

		self::assertEquals( '1,1,1,4,1,1,1,1', $values['list'] );
		self::assertEquals( 1.375, $values['current_value'] );
	}

	/**
	 * @test
	 */
	public function it_should_return_calculated_average_from_list_for_events() {
		$values = [
			1543,
			1445,
			1,
			'1,1,1,4,3,3,3,2,1,4,3,2,1,5,3,2,1,3,2,1',
			1,
			'2,1,2,3,2,1,1,2,3,1,2,3,4,2,1,3,4,2,1,3',
			1553,
		];

		$current_properties = $this->make_base_data( $values );
		$events_per_order   = 3;
		$values             = $this->aggregate_data->get_average_from_list( $current_properties, 'average_events_per_order_list', $events_per_order );

		self::assertEquals( '2,1,2,3,2,1,1,2,3,1,2,3,4,2,1,3,4,2,1,3,3', $values['list'] );
		self::assertEquals( 2.1904761904762, $values['current_value'] );
	}

	/**
	 * @test
	 */
	public function it_should_return_calculated_average_from_list_for_events_2() {
		$values = [
			1543,
			1445,
			1,
			'1,1,1,4,3,3,3,2,1,4,3,2,1,5,3,2,1,3,2,1',
			1,
			'2,1,2,3,2,1,1,2,3,1,2,3,4,2,1,3,4,2,1,3,4,3,5,6,3,2,4,1,3,4,3,3',
			1553,
		];

		$current_properties = $this->make_base_data( $values );
		$events_per_order   = 2;
		$values             = $this->aggregate_data->get_average_from_list( $current_properties, 'average_events_per_order_list', $events_per_order );

		self::assertEquals( '2,1,2,3,2,1,1,2,3,1,2,3,4,2,1,3,4,2,1,3,4,3,5,6,3,2,4,1,3,4,3,3,2', $values['list'] );
		self::assertEquals( 2.6060606060606, $values['current_value'] );
	}

	/**
	 * @test
	 */
	public function it_should_return_calculated_average_from_list_for_events_3() {
		$values = [
			1543,
			1445,
			1,
			'1,1,1,4,3,3,3,2,1,4,3,2,1,5,3,2,1,3,2,1',
			1,
			'2,1,2,3,2,1,1,2,3',
			1553,
		];

		$current_properties = $this->make_base_data( $values );
		$events_per_order   = 1;
		$values             = $this->aggregate_data->get_average_from_list( $current_properties, 'average_events_per_order_list', $events_per_order );

		self::assertEquals( '2,1,2,3,2,1,1,2,3,1', $values['list'] );
		self::assertEquals( 1.8, $values['current_value'] );
	}
}
