<?php

namespace Tribe\HubSpot\Admin;

use Tribe\Events\Admin\Settings as TEC_Settings;
use Tribe\HubSpot\Process\Setup_Queue;
use Tribe__Settings_Manager;

/**
 * Do the Settings.
 */
class Settings {

	/**
	 * The prefix for our settings keys.
	 *
	 * Gets set automatically from the Text Domain or can be set manually.
	 * The prefix should not end with underscore `_`.
	 *
	 * @var string
	 */
	protected $opts_prefix = 'tribe_hubspot';

	/**
	 * Settings constructor.
	 *
	 * @since 1.0
	 *
	 */
	public function __construct() {

		$this->set_options_prefix( $this->opts_prefix );

		add_filter( 'tribe_addons_tab_fields', array( $this, 'add_settings' ) );
		add_action( 'current_screen', [ $this, 'maybe_clear_hubspot_credentials' ] );
	}

	/**
	 * Set the options prefix to be used for this extension's settings.
	 *
	 * Always has ends with a single underscore.
	 *
	 * @since 1.0
	 *
	 * @param string $opts_prefix
	 */
	private function set_options_prefix( $opts_prefix ) {

		$opts_prefix = $opts_prefix . '_';

		$this->opts_prefix = str_replace( '__', '_', $opts_prefix );
	}

	/**
	 * Given an option key, get this extension's option value.
	 *
	 * This automatically prepends this extension's option prefix so you can just do `$this->get_option( 'a_setting' )`.
	 *
	 * @since 1.0
	 *
	 * @param string $key
	 *
	 * @return mixed
	 * @see tribe_get_option()
	 *
	 */
	public function get_option( $key = '', $default = '' ) {
		$key = $this->sanitize_option_key( $key );

		return tribe_get_option( $key, $default );
	}

	/**
	 * Get an option key after ensuring it is appropriately prefixed.
	 *
	 * @since 1.0
	 *
	 * @param string $key
	 *
	 * @return string
	 */
	private function sanitize_option_key( $key = '' ) {
		$prefix = $this->get_options_prefix();

		if ( 0 === strpos( $key, $prefix ) ) {
			$prefix = '';
		}

		return $prefix . $key;
	}

	/**
	 * Get this extension's options prefix.
	 *
	 * @since 1.0
	 *
	 * @return string
	 */
	public function get_options_prefix() {
		if ( empty( $this->opts_prefix ) ) {
			$this->set_options_prefix( 'tribe_hubspot' );
		}

		return $this->opts_prefix;
	}

	/**
	 * Get an array of all of this extension's options without array keys having the redundant prefix.
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	public function get_all_options() {
		$raw_options = $this->get_all_raw_options();

		$result = [];

		$prefix = $this->get_options_prefix();

		foreach ( $raw_options as $key => $value ) {
			$abbr_key            = str_replace( $prefix, '', $key );
			$result[ $abbr_key ] = $value;
		}

		return $result;
	}

	/**
	 * Get an array of all of this extension's raw options (i.e. the ones starting with its prefix).
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	public function get_all_raw_options() {
		$tribe_options = Tribe__Settings_Manager::get_options();

		if ( ! is_array( $tribe_options ) ) {
			return [];
		}

		$result = [];

		foreach ( $tribe_options as $key => $value ) {
			if ( 0 === strpos( $key, $this->get_options_prefix() ) ) {
				$result[ $key ] = $value;
			}
		}

		return $result;
	}

	/**
	 * Given an option key, delete this extension's option value.
	 *
	 * This automatically prepends this extension's option prefix so you can just do `$this->delete_option( 'a_setting' )`.
	 *
	 * @since 1.0
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function delete_option( $key = '' ) {
		$key = $this->sanitize_option_key( $key );

		$options = Tribe__Settings_Manager::get_options();

		unset( $options[ $key ] );

		return Tribe__Settings_Manager::set_options( $options );
	}

	/**
	 * Update an Option given a Key and Value
	 *
	 * This automatically prepends this extension's option prefix so you can just do `$this->add_option( 'a_setting' )`.
	 *
	 * @since 1.0
	 *
	 * @param string $key   The option name without the prefix.
	 * @param mixed  $value The value to save for this option.
	 *
	 * @return mixed
	 */
	public function update_option( $key, $value ) {
		$key = $this->sanitize_option_key( $key );

		$options = Tribe__Settings_Manager::get_options();

		$options[ $key ] = $value;

		return Tribe__Settings_Manager::set_options( $options );
	}

	/**
	 * Adds HubSpot Settings
	 *
	 * @since 1.0
	 *
	 * @param array $fields An array of the settings already in the tab.
	 *
	 * @return array An array of settings.
	 */
	public function add_settings( array $fields ) {

		$hubspot_fields = [
			$this->opts_prefix . 'hubspot_header' => [
				'type' => 'html',
				'html' => $this->get_intro_text(),
			],
			$this->opts_prefix . 'hubspot_authorize' => [
				'type' => 'html',
				'html' => $this->get_authorize_fields(),
			],
			$this->opts_prefix . 'app_id' => [
				'type'            => 'text',
				'label'           => esc_html__( 'APP ID', 'tribe-ext-hubspot' ),
				'tooltip'         => sprintf( esc_html__( 'Enter the App ID from the application created in HubSpot', 'tribe-ext-hubspot' ) ),
				'validation_type' => 'html',
			],
			$this->opts_prefix . 'client_id'      => [
				'type'            => 'text',
				'label'           => esc_html__( 'Client ID', 'tribe-ext-hubspot' ),
				'tooltip'         => sprintf( esc_html__( 'Enter the Client ID from the application created in HubSpot', 'tribe-ext-hubspot' ) ),
				'validation_type' => 'html',
			],
			$this->opts_prefix . 'client_secret'  => [
				'type'            => 'text',
				'label'           => esc_html__( 'Client Secret', 'tribe-ext-hubspot' ),
				'tooltip'         => sprintf( esc_html__( 'Enter the Client Secret from the application created in HubSpot', 'tribe-ext-hubspot' ) ),
				'validation_type' => 'html',
			],
			$this->opts_prefix . 'user_id' => [
				'type'            => 'text',
				'label'           => esc_html__( 'User ID', 'tribe-ext-hubspot' ),
				'tooltip'         => sprintf( esc_html__( 'Enter the Developer Account User ID for HubSpot', 'tribe-ext-hubspot' ) ),
				'validation_type' => 'html',
			],
			$this->opts_prefix . 'hapi_key' => [
				'type'            => 'text',
				'label'           => esc_html__( 'API Key', 'tribe-ext-hubspot' ),
				'tooltip'         => sprintf( esc_html__( 'Enter the Developer Account API Key from the Developer Account in HubSpot', 'tribe-ext-hubspot' ) ),
				'validation_type' => 'html',
			],
		];

		return array_merge( (array) $fields, $hubspot_fields );
	}

	/**
	 * Get HubSpot Setting Intro Text
	 *
	 * @since 1.0
	 *
	 * @return string
	 */
	private function get_intro_text() {

		$guide = sprintf( '%s <a href="%s" target="_blank">%s</a> %s',
			esc_html_x( 'Review our', 'Intro text to the setup guide link.','tribe-ext-hubspot' ),
			esc_url( 'http://m.tri.be/hubspotkb' ),
			esc_html_x( 'setup guide', 'Link text to the setup guide.','tribe-ext-hubspot' ),
			esc_html_x( 'to help you get started.', 'Ending text to the setup guide link.','tribe-ext-hubspot' )
		);

		ob_start();
		?>
		<h3 id="tribe-hubspot-application-credientials">
			<?php echo esc_html_x( 'HubSpot', 'API connection header', 'tribe-ext-hubspot' ) ?>
		</h3>
		<div style="margin-left: 20px;">
			<p>
				<?php echo esc_html_x( 'You need to connect to your HubSpot account to be able to subscribe to actions.', 'Settings Description', 'tribe-ext-hubspot' ); ?>
			</p>
			<p>
				<?php echo $guide; ?>
			</p>
		</div>
		<?php

		echo $this->get_status_table();

		return ob_get_clean();
	}

	/**
	 * Authorization
	 *
	 * @since 1.0
	 *
	 * @return false|string|void
	 */
	private function get_authorize_fields() {

		if (  ! tribe( 'tickets.hubspot.api' )->has_required_fields() ) {
			return;
		}

		// HubSpot Requires a SSL, check and display an error message if SSL not detected
		if ( ! is_ssl() ) {
			ob_start();
			?>
			<fieldset id="tribe-field-hubspot_token" class="tribe-field tribe-field-text tribe-size-medium">
				<legend class="tribe-field-label"><?php esc_html_e( 'HubSpot Connection', 'tribe-ext-hubspot' ) ?></legend>
				<div class="tribe-field-wrap tribe-error">
					<?php esc_html_e( 'An SSL is required to connect to HubSpot, please enable it on your site.', 'tribe-ext-hubspot' ) ?>
				</div>
			</fieldset>
			<div class="clear"></div>
			<?php

			return ob_get_clean();
		}

		$missing_hubspot_credentials = ! tribe( 'tickets.hubspot.api' )->is_authorized();

		ob_start();

		?>
		<fieldset id="tribe-field-hubspot_token" class="tribe-field tribe-field-text tribe-size-medium">
			<legend class="tribe-field-label"><?php esc_html_e( 'HubSpot Connection', 'tribe-ext-hubspot' ) ?></legend>
			<div class="tribe-field-wrap">
				<?php
				$authorize_link        = tribe( 'tickets.hubspot.api' )->get_authorized_url();

				if ( $missing_hubspot_credentials ) {
					echo '<p>' . esc_html__( 'You need to connect to HubSpot.', 'tribe-ext-hubspot' ) . '</p>';
					$hubspot_button_label = __( 'Connect to HubSpot', 'tribe-ext-hubspot' );
				} else {
					$hubspot_button_label     = __( 'Refresh your connection to HubSpot', 'tribe-ext-hubspot' );
					$hubspot_disconnect_label = __( 'Disconnect', 'tribe-ext-hubspot' );
					$current_url              = tribe( TEC_Settings::class )->get_url( [ 'tab' => 'addons' ] );
					$hubspot_disconnect_url   = $this->build_disconnect_hubspot_url( $current_url );
				}
				?>
				<a target="_blank" class="tribe-button tribe-hubspot-button" href="<?php echo esc_url( $authorize_link ); ?>"><?php esc_html_e( $hubspot_button_label ); ?></a>
				<?php if ( ! $missing_hubspot_credentials ) : ?>
					<a href="<?php echo esc_url( $hubspot_disconnect_url ); ?>" class="tribe-hubspot-disconnect"><?php echo esc_html( $hubspot_disconnect_label ); ?></a>
				<?php endif; ?>
			</div>
		</fieldset>

		<!-- Uses style guide colors https://www.hubspot.com/style-guide -->
		<style>
			.tribe-hubspot-button {
				background: #00A4BD;
				border-radius: 3px;
				color: #fff;
				display: inline-block;
				padding: .5rem 1.5rem;
				text-decoration: none;
				-webkit-transition: all 0.5s ease;
				transition: all 0.5s ease;
			}

			.tribe-hubspot-button:active,
			.tribe-hubspot-button:hover,
			.tribe-hubspot-button:focus {
				background: #FF7A59;
				color: #253342;
			}
		</style>
		<div class="clear"></div>
		<?php

		return ob_get_clean();
	}

	/**
	 * Hooked to current_screen, this method identifies whether or not HubSpot credentials should be cleared
	 *
	 * @since 1.0
	 *
	 * @param WP_Screen $screen
	 */
	public function maybe_clear_hubspot_credentials( $screen ) {
		if ( 'tribe_events_page_tec-events-settings' !== $screen->base ) {
			return;
		}

		if ( ! isset( $_GET['tab'] ) || 'addons' !== $_GET['tab'] ) {
			return;
		}

		if (
			! (
				isset( $_GET['action'] )
				&& isset( $_GET['_wpnonce'] )
				&& 'disconnect-hubspot' === $_GET['action']
				&& wp_verify_nonce( $_GET['_wpnonce'], 'disconnect-hubspot' )
			)
		) {
			return;
		}

		$this->clear_hubspot_credentials();

		wp_redirect(
			tribe( TEC_Settings::class )->get_url( [ 'tab' => 'addons' ] )
		);
		die;
	}

	/**
	 * Given a URL, tack on the parts of the URL that gets used to disconnect from HubSpot
	 *
	 * @since 1.0
	 *
	 * @param string $url The base url to add the disconnect query variables.
	 *
	 * @return string The url with disconnect query variables.
	 */
	public function build_disconnect_hubspot_url( $url ) {
		return wp_nonce_url(
			add_query_arg(
				'action',
				'disconnect-hubspot',
				$url
			),
			'disconnect-hubspot'
		);
	}

	/**
	 * Disconnect from Hubspot by deleting all HubSpot options and clearing the Setup Queue.
	 *
	 * @since 1.0
	 *
	 */
	public function clear_hubspot_credentials() {

		$hb_options = $this->get_all_options();

		foreach ( $hb_options as $key => $option ) {
			$this->delete_option( $key );
		}

		$queue = new Setup_Queue();
		$queue->delete_all_queues( 'hubspot_setup_queue' );
	}

	/**
	 * Get the Status Table for HubSpot.
	 *
	 * @since 1.0
	 *
	 * @return string
	 */
	protected function get_status_table() {
		$options         = $this->get_all_options();
		$indicator_icons = [
			'good'    => 'marker',
			'warning' => 'warning',
			'bad'     => 'dismiss',
		];

		ob_start();
		?>
		<table class="hubspot-status event-aggregator-status">
			<thead>
			<tr class="table-heading">
				<th colspan="4"><?php esc_html_e( 'HubSpot Services', 'tribe-ext-hubspot' ); ?></th>
			</tr>
			</thead>
			<tbody>
			<?php

			// Connection Status ( checks for access token, refresh token, and expires site options )
			$indicator = 'warning';
			$notes     = '&nbsp;';
			$label     = _x( 'HubSpot Connection', 'Status Label HubSpot Main Connection.', 'tribe-ext-hubspot' );
			$text      = _x( 'Not connected.', 'Status for HubSpot Main Connection.', 'tribe-ext-hubspot' );

			if ( ! empty( $options['access_token'] ) && ! empty( $options['refresh_token'] ) && ! empty( $options['access_token'] ) ) {
				$indicator = 'good';
				$text      = _x( 'Connected!', 'Status for HubSpot Main Connection.', 'tribe-ext-hubspot' );
			}

			echo $this->get_status_row( $label, $indicator, $indicator_icons, $text, $notes );

			// Add Status of Group Name Setup.
			$status_group = $this->get_status_content( 'Group Name', 'group_name_setup', $options );
			echo $this->get_status_row( $status_group['label'], $status_group['indicator'], $indicator_icons, $status_group['text'], $status_group['notes'] );

			// Add Status of Custom Properties Setup.
			$status_properties = $this->get_status_content( 'Custom Properties', 'custom_properties_setup', $options );
			echo $this->get_status_row( $status_properties['label'], $status_properties['indicator'], $indicator_icons, $status_properties['text'], $status_properties['notes'] );

			// Add Status of Timeline Event Type Setup.
			$status_timeline = $this->get_status_content( 'Timeline Event Types', 'timeline_event_types_setup', $options );
			echo $this->get_status_row( $status_timeline['label'], $status_timeline['indicator'], $indicator_icons, $status_timeline['text'], $status_timeline['notes'] );
			?>
			</tbody>
		</table>
		<?php

		return ob_get_clean();
	}

	/**
	 * Get the Status Table for HubSpot.
	 *
	 * @since 1.0
	 *
	 * @param string $label   The name of the status.
	 * @param string $type    The setup status key.
	 * @param array  $options An array of saved HubSpot site options.
	 *
	 * @return array The array of information to use for the status.
	 */
	protected function get_status_content( $label, $type, $options ) {

		$status              = [];
		$status['label']     = _x( $label, 'Label of the HubSpot Setup.', 'tribe-ext-hubspot' );
		$status['indicator'] = 'warning';
		$status['text']      = _x( 'Setup on hold.', 'Message displayed when HubSpot Setup has not started.', 'tribe-ext-hubspot' );
		$status['notes']     = '&nbsp;';
		$status_value        = isset( $options[ $type ] ) ? $options[ $type ] : null;
		$setup_note          = _x( 'Setup can take up to 5 minutes. You may navigate away from this page, setup will continue in the background.', 'This note is displayed when HubSpot Setup is Pending or In Progress.', 'tribe-ext-hubspot' );

		if ( 'complete' === $status_value ) {
			$status['indicator'] = 'good';
			$status['text']      = _x( 'Setup Complete.', 'Message displayed when HubSpot Setup is complete.', 'tribe-ext-hubspot' );
		} elseif ( 'failed' === $status_value ) {
			$status['indicator'] = 'bad';
			$status['text']      = _x( 'Setup incomplete, please refresh your connection to try again.', 'Message displayed when HubSpot Setup Failed.', 'tribe-ext-hubspot' );
		} elseif ( 'pending' === $status_value ) {
			$status['text']  = _x( 'Setup is preparing to begin. ', 'Message displayed when HubSpot Setup has been initialized.', 'tribe-ext-hubspot' );
			$status['notes'] = $setup_note;
		} elseif ( is_numeric( $status_value ) ) {
			$status['text']  = _x( 'Setup in Progress.', 'Message displayed when HubSpot Setup is in Progress.', 'tribe-ext-hubspot' );
			$status['notes'] = $setup_note;
		}

		return $status;
	}

	/**
	 * Get a Status Row HTML.
	 *
	 * @since 1.0
	 *
	 * @param string $label           The name of the status.
	 * @param string $indicator       The indicator key.
	 * @param array  $indicator_icons An array of icons to use for the status symbol.
	 * @param string $text            The description of the status.
	 * @param string $notes           The additional notes for the status.
	 *
	 * @return string The html for the status row.
	 */
	protected function get_status_row( $label, $indicator, $indicator_icons, $text, $notes ) {

		ob_start();
		?>
		<tr>
			<td class="label"><?php echo esc_html( $label ); ?></td>
			<td class="indicator <?php echo esc_attr( $indicator ); ?>"><span class="dashicons dashicons-<?php echo esc_attr( $indicator_icons[ $indicator ] ); ?>"></span></td>
			<td><?php echo esc_html( $text ); ?></td>
			<td><?php echo esc_html( $notes ); ?></td>
		</tr>
		<?php

		return ob_get_clean();
	}
}
