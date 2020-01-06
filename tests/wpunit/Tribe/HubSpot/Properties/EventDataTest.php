<?php

namespace Tribe\HubSpot\Properties;

use Codeception\TestCase\WPTestCase;
use Tribe\Events\Test\Factories\Event;
use Tribe\Events\Test\Factories\Venue;
use Tribe\Events\Test\Factories\Organizer;
use Tribe\HubSpot\Subscribe\Woo as Woo_Subscribe;
use Tribe\Tickets_Plus\Test\Commerce\WooCommerce\Order_Maker;
use Tribe\Tickets_Plus\Test\Commerce\WooCommerce\Ticket_Maker;
use Tribe\HubSpot\Properties\Event_Data;
use WC_Order;

class EventDataTest extends WPTestCase {

	use Ticket_Maker;
	use Order_Maker;

	public function setUp() {
		// before
		parent::setUp();

		$this->factory()->event     = new Event();
		$this->factory()->venue     = new Venue();
		$this->factory()->organizer = new Organizer();

		$this->event_data    = new Event_Data();
		$this->woo_subscribe = new Woo_Subscribe();
	}

	public function tearDown() {
		// your tear down methods here
		// then
		parent::tearDown();
	}

	/**
	 * @test
	 */
	public function it_should_return_event_data() {
		$venue_1      = $this->factory()->venue->create();
		$organizer_1  = $this->factory()->organizer->create();
		$event_id     = $this->factory()->event->create( [ 'venue' => $venue_1, 'organizer' => $organizer_1 ] );
		$event_values = $this->event_data->get_event_values( 'last_registered_', $event_id );

		// Has Event ID
		$event_id_prop = wp_list_filter( $event_values, [
			'property' => 'last_registered_event_id',
		] );
		self::assertEquals( $event_id, $event_id_prop[0]['value'] );
		self::assertCount( 1, $event_id_prop );

		// Has Event Start Date
		$event_start_date = wp_list_filter( $event_values, [
			'property' => 'last_registered_event_start_date_utc',
		] );
		self::assertCount( 1, $event_start_date );
		$event_start_date = current( $event_start_date );
		self::assertNotEmpty( $event_start_date['value'] );


		// Has Venue Name
		$venue_id_prop = wp_list_filter( $event_values, [
			'property' => 'last_registered_event_venue',
		] );
		self::assertCount( 1, $venue_id_prop );
		$venue_id_prop = current( $venue_id_prop );
		self::assertNotEmpty( $venue_id_prop['value'] );

		// Has Organizer Name
		$organizer_id_prop = wp_list_filter( $event_values, [
			'property' => 'last_registered_event_organizer',
		] );
		self::assertCount( 1, $organizer_id_prop );
		$organizer_id_prop = current( $organizer_id_prop );
		self::assertNotEmpty( $venue_id_prop['value'] );
	}

	/**
	 * @test
	 */
	public function it_should_return_post_data() {
		$post_id     = $this->factory->post->create();
		$post_values = $this->event_data->get_event_values( 'last_registered_', $post_id );

		// Has Post ID
		$post_id_prop = wp_list_filter( $post_values, [
			'property' => 'last_registered_event_id',
		] );
		self::assertEquals( $post_id, $post_id_prop[0]['value'] );
		self::assertCount( 1, $post_id_prop );

		// Does Not Have Event Start Date
		$event_start_date = wp_list_filter( $post_values, [
			'property' => 'last_registered_event_start_date_utc',
		] );
		self::assertCount( 1, $event_start_date );
		$event_start_date = current( $event_start_date );
		self::assertEmpty( $event_start_date['value'] );


		// Does Not Have Venue Name
		$venue_id_prop = wp_list_filter( $post_values, [
			'property' => 'last_registered_event_venue',
		] );
		self::assertCount( 1, $venue_id_prop );
		$venue_id_prop = current( $venue_id_prop );
		self::assertEmpty( $venue_id_prop['value'] );

		// Does Not Have Organizer Name
		$organizer_id_prop = wp_list_filter( $post_values, [
			'property' => 'last_registered_event_organizer',
		] );
		self::assertCount( 1, $organizer_id_prop );
		$organizer_id_prop = current( $organizer_id_prop );
		self::assertEmpty( $venue_id_prop['value'] );
	}

	/**
	 * @test
	 */
	public function it_should_return_order_data() {
		$event_id      = $this->factory()->event->create();
		$ticket_id     = $this->create_woocommerce_ticket( $event_id, 5 );
		$order_id      = $this->create_woocommerce_order( $ticket_id, 2, [ 'status' => 'completed' ] );
		$order         = new WC_Order( $order_id );
		$qty           = $this->event_data->get_woo_order_quantities( $order );
		$attendee_data = $this->woo_subscribe->get_woo_contact_data_from_order( $order );

		$order_values = $this->event_data->get_order_values( 'last_order_', $attendee_data['date'], $attendee_data['total'], $qty['total'], count( $qty['tickets'] ) );

		// Has Date
		$date_prop = wp_list_filter( $order_values, [
			'property' => 'last_order_date_utc',
		] );
		self::assertCount( 1, $date_prop );
		$date_prop = current( $date_prop );
		self::assertEquals( ms_timestamp( $attendee_data['date'] ), $date_prop['value'] );

		// Has Total
		$total_prop = wp_list_filter( $order_values, [
			'property' => 'last_order_total',
		] );
		self::assertCount( 1, $total_prop );
		$total_prop = current( $total_prop );
		self::assertEquals( $attendee_data['total'], $total_prop['value'] );

		// Has Ticket QTY
		$ticket_quantity_prop = wp_list_filter( $order_values, [
			'property' => 'last_order_ticket_quantity',
		] );
		self::assertCount( 1, $ticket_quantity_prop );
		$ticket_quantity_prop = current( $ticket_quantity_prop );
		self::assertEquals( $qty['total'], $ticket_quantity_prop['value'] );

		// Has Ticket Type QTY
		$ticket_type_quantity_prop = wp_list_filter( $order_values, [
			'property' => 'last_order_ticket_type_quantity',
		] );
		self::assertCount( 1, $ticket_type_quantity_prop );
		$ticket_type_quantity_prop = current( $ticket_type_quantity_prop );
		self::assertEquals( count( $qty['tickets'] ), $ticket_type_quantity_prop['value'] );
	}

	/**
	 * @test
	 */
	public function it_should_return_ticket_data() {
		$event_id      = $this->factory()->event->create();
		$ticket_id     = $this->create_woocommerce_ticket( $event_id, 5 );
		$order_id      = $this->create_woocommerce_order( $ticket_id, 2, [ 'status' => 'completed' ] );
		$order         = new WC_Order( $order_id );
		$attendee_id   = $this->woo_subscribe->get_first_attendee_id_from_order( $order_id, 'woo' );
		$attendee_data = $this->woo_subscribe->get_woo_contact_data_from_order( $order );

		$order_values = $this->event_data->get_ticket_values( $ticket_id, $attendee_id, 'woo', $attendee_data['name'], false );

		// Has Ticket ID
		$date_prop = wp_list_filter( $order_values, [
			'property' => 'last_registered_ticket_type_id',
		] );
		self::assertCount( 1, $date_prop );
		$date_prop = current( $date_prop );
		self::assertEquals( $ticket_id, $date_prop['value'] );

		// Has Ticket Name
		$ticket_name_prop = wp_list_filter( $order_values, [
			'property' => 'last_registered_ticket_type',
		] );
		self::assertCount( 1, $ticket_name_prop );
		$ticket_name_prop = current( $ticket_name_prop );
		self::assertEquals( get_the_title( $ticket_id ), $ticket_name_prop['value'] );

		// Has Ticket Provider
		$ticket_provider_prop = wp_list_filter( $order_values, [
			'property' => 'last_registered_ticket_commerce',
		] );
		self::assertCount( 1, $ticket_provider_prop );
		$ticket_provider_prop = current( $ticket_provider_prop );
		self::assertEquals( 'woo', $ticket_provider_prop['value'] );

		// Has Attendee ID
		$attendee_id_prop = wp_list_filter( $order_values, [
			'property' => 'last_registered_ticket_attendee_id',
		] );
		self::assertCount( 1, $attendee_id_prop );
		$attendee_id_prop = current( $attendee_id_prop );
		self::assertEquals( $attendee_id, $attendee_id_prop['value'] );

		// Has Attendee Name
		$attendee_name_prop = wp_list_filter( $order_values, [
			'property' => 'last_registered_ticket_attendee_name',
		] );
		self::assertCount( 1, $attendee_name_prop );
		$attendee_name_prop = current( $attendee_name_prop );
		self::assertEquals( $attendee_data['name'], $attendee_name_prop['value'] );

		// Does not RSVP is going
		$rsvp_going_prop = wp_list_filter( $order_values, [
			'property' => 'last_registered_ticket_rsvp_is_going',
		] );
		self::assertCount( 1, $rsvp_going_prop );
		$rsvp_going_prop = current( $rsvp_going_prop );
		self::assertEquals( false, $rsvp_going_prop['value'] );
	}
}
