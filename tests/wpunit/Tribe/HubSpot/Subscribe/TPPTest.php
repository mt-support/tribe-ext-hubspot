<?php

namespace Tribe\HubSpot\Subscribe;

use Tribe\Events\Test\Factories\Event;
use Tribe\Tickets\Test\Commerce\PayPal\Ticket_Maker as PayPal_Ticket_Maker;
use Tribe\Tickets\Test\Commerce\PayPal\Order_Maker as PayPal_Order_Maker;
use Tribe__Tickets__Commerce__PayPal__Main as Main_TPP;
use Tribe__Tickets__Commerce__PayPal__Tickets_View as Tickets_View;
use Tribe__Tickets__Data_API as Data_API;
use Tribe\HubSpot\Subscribe\TPP as TPP_Subscribe;
use Tribe\HubSpot\Properties\Event_Data as Data;

class TPPTest extends \Codeception\TestCase\WPTestCase {

	use PayPal_Ticket_Maker;
	use PayPal_Order_Maker;

	public function setUp() {
		// before
		parent::setUp();

		// Enable post as ticket type.
		add_filter( 'tribe_tickets_post_types', function () {
			return [ 'post' ];
		} );

		// your set up methods here
		$this->tickets_view = new Tickets_View();

		$this->tpp_subscribe = new TPP_Subscribe();

		// let's avoid die()s
		add_filter( 'tribe_exit', function () {
			return [ $this, 'dont_die' ];
		} );

		// Enable Tribe Commerce.
		add_filter( 'tribe_tickets_commerce_paypal_is_active', '__return_true' );
		add_filter( 'tribe_tickets_get_modules', function ( $modules ) {
			$modules['Tribe__Tickets__Commerce__PayPal__Main'] = tribe( 'tickets.commerce.paypal' )->plugin_name;

			return $modules;
		} );

		// Reset Data_API object so it sees Tribe Commerce.
		tribe_singleton( 'tickets.data_api', new Data_API );
	}

	public function tearDown() {
		// your tear down methods here
		// then
		parent::tearDown();
	}

	private function make_instance() {
		/** @var Main_TPP $instance */
		$instance = ( new \ReflectionClass( Main_TPP::class ) )->newInstanceWithoutConstructor();
		$instance->set_tickets_view( $this->tickets_view );

		return $instance;
	}

	/**
	 * @test
	 */
	public function it_should_return_initial_properties_array_from_attendee_data() {
		$base_data     = $this->make_base_data();
		$test_attendee = $base_data['test_attendee'];

		// Getting Initial Properties Array that is sent to HubSpot
		$related_data  = $this->tpp_subscribe->get_tpp_related_data_by_attendee_id( $test_attendee['attendee_id'] );
		$attendee_data = $this->tpp_subscribe->get_tpp_contact_data_from_order( $related_data['order'] );
		$properties    = $this->tpp_subscribe->get_initial_properties_array( $attendee_data );

		self::assertEquals( 'firstname', $properties[0]['property'] );
		self::assertEquals( $attendee_data['first_name'], $properties[0]['value'] );
		self::assertEquals( 'lastname', $properties[1]['property'] );
		self::assertEquals( $attendee_data['last_name'], $properties[1]['value'] );
	}

	/**
	 * @test
	 */
	public function it_should_return_order_data_array_from_attendee_data() {
		$data          = new Data();

		$base_data     = $this->make_base_data();
		$test_attendee = $base_data['test_attendee'];

		// Return Array of Order Data that is used to send to HubSpot
		$related_data  = $this->tpp_subscribe->get_tpp_related_data_by_attendee_id( $test_attendee['attendee_id'] );
		$qty           = $data->get_tpp_order_quantities( $related_data['order'] );
		$attendee_data = $this->tpp_subscribe->get_tpp_contact_data_from_order( $related_data['order'] );
		$order_data    = $this->tpp_subscribe->get_order_data_array( $attendee_data, $qty, 'register' );

		self::assertEquals( $attendee_data['date'], $order_data['order_date'] );
		self::assertEquals( $attendee_data['total'], $order_data['order_total'] );
		self::assertEquals( $qty['total'], $order_data['order_ticket_quantity'] );
		self::assertEquals( count( $qty['tickets'] ), $order_data['order_ticket_type_quantity'] );
		self::assertEquals( $qty['events_per_order'], $order_data['events_per_order'] );
		self::assertEquals( 'register', $order_data['aggregate_type'] );
	}

	/**
	 * @test
	 */
	public function it_should_return_extra_data() {
		$base_data     = $this->make_base_data();
		$post_id       = $base_data['post_id'];
		$ticket_id     = $base_data['ticket_id'];
		$test_attendee = $base_data['test_attendee'];

		// Get the array of Extra Data for HubSpot Timeline Events
		$related_data  = $this->tpp_subscribe->get_tpp_related_data_by_attendee_id( $test_attendee['attendee_id'] );
		$attendee_data = $this->tpp_subscribe->get_tpp_contact_data_from_order( $related_data['order'] );
		$attendee_id   = $this->tpp_subscribe->get_first_attendee_id_from_order( $related_data['order_id'], 'tpp' );
		$extra_data    = $this->tpp_subscribe->get_extra_data( $post_id, $ticket_id, $attendee_id, 'tpp', $attendee_data['name'] );

		self::assertEquals( $post_id, $extra_data['event']['event_id'] );
		self::assertEquals( get_the_title( $post_id ), $extra_data['event']['event_name'] );
		self::assertEquals( $ticket_id, $extra_data['ticket']['ticket_type_id'] );
		self::assertEquals( get_the_title( $ticket_id ), $extra_data['ticket']['ticket_type'] );
		self::assertEquals( $attendee_id, $extra_data['ticket']['ticket_attendee_id'] );
	}

	/**
	 * @test
	 */
	public function it_should_return_first_attendee_id_from_order() {
		$Main_TPP      = new Main_TPP();

		$base_data     = $this->make_base_data();
		$post_id       = $base_data['post_id'];
		$test_attendee = $base_data['test_attendee'];

		// Get the First Attendee
		$related_data   = $this->tpp_subscribe->get_tpp_related_data_by_attendee_id( $test_attendee['attendee_id'] );
		$test_attendees = $Main_TPP->get_attendees_array( $post_id );
		$test_attendee  = current( $test_attendees );
		$attendee_id    = $this->tpp_subscribe->get_first_attendee_id_from_order( $related_data['order_id'], 'tpp' );


		self::assertEquals( $test_attendee['attendee_id'], $attendee_id );
	}

	/**
	 * @test
	 */
	public function it_should_spilt_full_name() {

		// split_name( $string )
		$full_name_1 = 'HubSpot McHubspotty';
		$full_name_2 = 'HubSpot Tribe McHubspotty';

		$names_1 = $this->tpp_subscribe->split_name( $full_name_1 );
		$names_2 = $this->tpp_subscribe->split_name( $full_name_2 );

		self::assertEquals( 'HubSpot', $names_1['first_name'] );
		self::assertEquals( 'McHubspotty', $names_1['last_name'] );
		self::assertEquals( '', $names_1['middle_name'] );
		self::assertEquals( 'HubSpot', $names_2['first_name'] );
		self::assertEquals( 'McHubspotty', $names_2['last_name'] );
		self::assertEquals( 'Tribe', $names_2['middle_name'] );
	}

	/**
	 * @test
	 */
	public function it_should_return_related_order_data_with_attendee_id() {
		$base_data     = $this->make_base_data();
		$post_id       = $base_data['post_id'];
		$ticket_id     = $base_data['ticket_id'];
		$order_id      = $base_data['order_id'];
		$order         = $base_data['order'];
		$test_attendee = $base_data['test_attendee'];

		// Get Related Data by Attendee ID
		$related_data = $this->tpp_subscribe->get_tpp_related_data_by_attendee_id( $test_attendee['attendee_id'] );

		self::assertEquals( $order_id, $related_data['order_id'] );
		self::assertEquals( $order, $related_data['order'] );
		self::assertEquals( $post_id, $related_data['post_id'] );
		self::assertEquals( $ticket_id, $related_data['ticket_id'] );
	}

	protected function make_base_data( $sales = 0, $stock = 10 ) {
		$post_id = $this->factory()->post->create();

		$ticket_id = $this->create_paypal_ticket( $post_id, 1, [
			'meta_input' => [
				'total_sales' => $sales,
				'_stock'      => $stock,
				'_capacity'   => $stock + $sales,
			],
		] );

		$generated      = $this->create_paypal_orders( $post_id, $ticket_id, 2, 1 );
		$order          = \Tribe__Tickets__Commerce__PayPal__Order::from_order_id( $generated[0]['Order ID'] );
		$test_attendees = $order->get_attendees();
		$test_attendee  = current( $test_attendees );

		return [
			'post_id'       => $post_id,
			'ticket_id'     => $ticket_id,
			'order_id'      => $generated[0]['Order ID'],
			'order'         => $order,
			'test_attendee' => $test_attendee,
		];
	}
}
