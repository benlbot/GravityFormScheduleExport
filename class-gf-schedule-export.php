<?php

GFForms::include_addon_framework();

class GFScheduleExport extends GFAddOn {
	protected $_version = GF_SCHEDULE_EXPORT;
	protected $_min_gravityforms_version = '1.9';
	protected $_slug = 'gravityforms-schedule-export';
	protected $_path = 'GravityFormScheduleExport/gravityforms-schedule-export.php';
	protected $_full_path = __FILE__;
	protected $_title = 'Gravity Forms Schedule Export';
	protected $_short_title = 'Schedule Export';

	private static $_instance = null;
	/**
	 * Get an instance of this class.
	 *
	 * @return GFScheduleExport
	 */
	public static function get_instance() {
		if ( self::$_instance == null ) {
			self::$_instance = new GFScheduleExport();
		}
		return self::$_instance;
	}
	/**
	 * Handles hooks and loading of language files.
	 */
	public function init() {
		parent::init();
		add_filter( 'gform_submit_button', array( $this, 'form_submit_button' ), 10, 2 );
		add_action( 'gform_after_submission', array( $this, 'after_submission' ), 10, 2 );
	}
	// # SCRIPTS & STYLES -----------------------------------------------------------------------------------------------
	/**
	 * Return the scripts which should be enqueued.
	 *
	 * @return array
	 */
	public function scripts() {
		$scripts = array(
			array(
				'handle'  => 'gf_schedule_export_js',
				'src'     => $this->get_base_url() . '/js/gf-schedule-export.js',
				'version' => $this->_version,
				'deps'    => array( 'jquery' ),
				'enqueue' => array(
					array(
						'admin_page' => array( 'form_settings' ),
						'tab'        => 'gravityforms-schedule-export'
					)
				)
			),
		);
		return array_merge( parent::scripts(), $scripts );
	}
	/**
	 * Return the stylesheets which should be enqueued.
	 *
	 * @return array
	 */
	public function styles() {
		$styles = array(
			array(
				'handle'  => 'gf_schedule_export_css',
				'src'     => $this->get_base_url() . '/css/gf-schedule-export.css',
				'version' => $this->_version,
				'enqueue' => array(
					array( 'field_types' => array( 'poll' ) )
				)
			)
		);
		return array_merge( parent::styles(), $styles );
	}
	// # FRONTEND FUNCTIONS --------------------------------------------------------------------------------------------
	/**
	 * Add the text in the plugin settings to the bottom of the form if enabled for this form.
	 *
	 * @param string $button The string containing the input tag to be filtered.
	 * @param array $form The form currently being displayed.
	 *
	 * @return string
	 */
	function form_submit_button( $button, $form ) {
		$settings = $this->get_form_settings( $form );
		if ( $settings['activated'] ) {
			$text   = $this->get_plugin_setting( 'mytextbox' );
			$button = "<div>{$text}</div>" . $button;
		}
		return $button;
	}
	// # ADMIN FUNCTIONS -----------------------------------------------------------------------------------------------
	/**
	 * Creates a custom page for this add-on.
	 */
//	public function plugin_page() {
//		echo 'This page appears in the Forms menu';
//	}
	/**
	 * Configures the settings which should be rendered on the add-on settings tab.
	 *
	 * @return array
	 */
//	public function plugin_settings_fields() {
//		return array(
//			array(
//				'title'  => esc_html__( 'Simple Add-On Settings', 'gf_schedule_export' ),
//				'fields' => array(
//					array(
//						'name'              => 'mytextbox',
//						'tooltip'           => esc_html__( 'This is the tooltip', 'gf_schedule_export' ),
//						'label'             => esc_html__( 'This is the label', 'gf_schedule_export' ),
//						'type'              => 'text',
//						'class'             => 'small',
//						'feedback_callback' => array( $this, 'is_valid_setting' ),
//					)
//				)
//			)
//		);
//	}
	/**
	 * Configures the settings which should be rendered on the Form Settings > Simple Add-On tab.
	 *
	 * @return array
	 */
	public function form_settings_fields( $form ) {
		return array(
			array(
				'title'  => esc_html__( 'Schedule Export Settings', 'gf_schedule_export' ),
				'fields' => array(
					array(
						'label'   => esc_html__( 'Activate Schedule export', 'gf_schedule_export' ),
						'type'    => 'checkbox',
						'name'    => 'activate_export',
						'tooltip' => esc_html__( 'This will activate the schedule export for this form.', 'gf_schedule_export' ),
						'choices' => array(
							array(
								'label' => esc_html__( 'Active', 'gf_schedule_export' ),
								'name'  => 'activated',
							),
						),
					),
					array(
						'label'   => esc_html__( 'Export Period', 'gf_schedule_export' ),
						'type'    => 'select',
						'name'    => 'export_period',
						'tooltip' => esc_html__( 'Choose the period that will be included in the export.', 'gf_schedule_export' ),
						'choices' => array(
							array(
								'label' => esc_html__( 'Last Day', 'gf_schedule_export' ),
								'value'  => 'day',
							),
							array(
								'label' => esc_html__( 'Last Week', 'gf_schedule_export' ),
								'value'  => 'week',
							),
							array(
								'label' => esc_html__( 'Last Month', 'gf_schedule_export' ),
								'value'  => 'month',
							),
							array(
								'label' => esc_html__( 'Everything', 'gf_schedule_export' ),
								'value'  => 'all',
							),
							array(
								'label' => esc_html__( 'Choose period', 'gf_schedule_export' ),
								'value'  => 'choose_period',
							),
						),
					),
					array(
						'label' => esc_html__( 'Custom Period', 'gf_schedule_export' ),
						'type'  => 'custom_period',
						'name'  => 'custom_period',
						'hidden' => true,
						'args'  => array(
							'text'     => array(
								'label'         => esc_html__( 'Period type number', 'gf_schedule_export' ),
								'name'          => 'custom_period_number',
								'default_value' => '',
							),
							'select' => array(
								'label'   => esc_html__( 'Period type', 'gf_schedule_export' ),
								'name'    => 'custom_period',
								'tooltip' => esc_html__( 'Choose the kind of period.', 'gf_schedule_export' ),
								'choices' => array(
									array(
										'label' => esc_html__( 'Days', 'gf_schedule_export' ),
										'value'  => 'days',
									),
									array(
										'label' => esc_html__( 'Weeks', 'gf_schedule_export' ),
										'value'  => 'weeks',
									),
									array(
										'label' => esc_html__( 'Months', 'gf_schedule_export' ),
										'value'  => 'months',
									),
								),
							),
						),
					),
					array(
						'label'   => esc_html__( 'Export Frequency', 'gf_schedule_export' ),
						'type'    => 'select',
						'name'    => 'export_frequency',
						'tooltip' => esc_html__( 'Choose the frenquence the export will be done.', 'gf_schedule_export' ),
						'choices' => array(
							array(
								'label' => esc_html__( 'Hourly', 'gf_schedule_export' ),
								'value' => 'hourly',
							),
							array(
								'label' => esc_html__( 'Daily', 'gf_schedule_export' ),
								'value' => 'daily',
							),
							array(
								'label' => esc_html__( 'Weekly', 'gf_schedule_export' ),
								'value' => 'weekly',
							),
							array(
								'label' => esc_html__( 'Monthly', 'gf_schedule_export' ),
								'value' => 'monthly',
							),
							array(
								'label' => esc_html__( 'Choose frequence', 'gf_schedule_export' ),
								'value'  => 'choose_frequence',
							),
						),
					),
					array(
						'label' => esc_html__( 'Custom Frequency', 'gf_schedule_export' ),
						'type'  => 'custom_frequency',
						'name'  => 'custom_frequency',
						'hidden' => true,
						'args'  => array(
							'text'     => array(
								'label'         => esc_html__( 'Frequence number', 'gf_schedule_export' ),
								'name'          => 'number',
								'default_value' => '',
							),
							'select' => array(
								'label'   => esc_html__( 'Period type', 'gf_schedule_export' ),
								'name'    => 'type',
								'tooltip' => esc_html__( 'Choose the kind of frequence.', 'gf_schedule_export' ),
								'choices' => array(
									array(
										'label' => esc_html__( 'Hours', 'gf_schedule_export' ),
										'value' => 'hours',
									),
									array(
										'label' => esc_html__( 'Days', 'gf_schedule_export' ),
										'value' => 'days',
									),
									array(
										'label' => esc_html__( 'Weeks', 'gf_schedule_export' ),
										'value' => 'weeks',
									),
									array(
										'label' => esc_html__( 'Months', 'gf_schedule_export' ),
										'value' => 'months',
									),
								),
							),
						),
					),
					array(
						'label'   => esc_html__( 'Export types', 'gf_schedule_export' ),
						'type'    => 'checkbox',
						'horizontal' => true,
						'name'    => 'export_type',
						'tooltip' => esc_html__( 'Choose the type(s) of export for this form.', 'gf_schedule_export' ),
						'choices' => array(
							array(
								'label' => esc_html__( 'E-mail', 'gf_schedule_export' ),
								'name'  => 'export_email',
							),
							array(
								'label' => esc_html__( 'Folder', 'gf_schedule_export' ),
								'name'  => 'export_folder',
							),
						),
					),
					array(
						'label'             => esc_html__( 'Email Address', 'gf_schedule_export' ),
						'type'              => 'text',
						'name'              => 'export_email',
						'hidden'			=> true,
						'tooltip'           => esc_html__( 'The export will be sent to this email address.', 'gf_schedule_export' ),
						'class'             => 'medium',
					),
					array(
						'label'             => esc_html__( 'Email title', 'gf_schedule_export' ),
						'type'              => 'text',
						'name'              => 'mail_title',
						'hidden'			=> true,
						'tooltip'           => esc_html__( 'This is the tooltip', 'gf_schedule_export' ),
						'class'             => 'medium',
					),
					array(
						'label'   			=> esc_html__( 'Email content', 'gf_schedule_export' ),
						'type'    			=> 'textarea',
						'name'    			=> 'mail_content',
						'hidden'			=> true,
						'tooltip' 			=> esc_html__( 'This is the tooltip', 'gf_schedule_export' ),
						'class'   			=> 'medium merge-tag-support mt-position-right',
					),
					array(
						'label'             => esc_html__( 'Folder Location', 'gf_schedule_export' ),
						'type'              => 'text',
						'name'              => 'export_folder',
						'hidden'			=> true,
						'tooltip'           => esc_html__( 'The export file will be located in this folder.', 'gf_schedule_export' ),
						'class'             => 'medium',
					),
				),
			),
		);
	}
	/**
	 * Define the markup for the my_custom_field_type type field.
	 *
	 * @param array $field The field properties.
	 * @param bool|true $echo Should the setting markup be echoed.
	 */
	public function settings_custom_period( $field, $echo = true ) {
		echo '<div>' . esc_html__( 'Select the number and the custom period :', 'gf_schedule_export' ) . '</div>';

		echo '<div>' . esc_html__( 'Last', 'gf_schedule_export' ).' ';

		$text_field = $field['args']['text'];
		$this->settings_text( $text_field );

		$checkbox_field = $field['args']['select'];
		$this->settings_select( $checkbox_field );

		echo '</div>';
	}
	/**
	 * Define the markup for the my_custom_field_type type field.
	 *
	 * @param array $field The field properties.
	 * @param bool|true $echo Should the setting markup be echoed.
	 */
	public function settings_custom_frequency( $field, $echo = true ) {
		echo '<div>' . esc_html__( 'Select the number and the custom frequence :', 'gf_schedule_export' ) . '</div>';

		echo '<div>' . esc_html__( 'Every', 'gf_schedule_export' ).' ';

		$text_field = $field['args']['text'];
		$this->settings_text( $text_field );

		$checkbox_field = $field['args']['select'];
		$this->settings_select( $checkbox_field );

		echo '</div>';
	}
	/**
	 * Performing a custom action at the end of the form submission process.
	 *
	 * @param array $entry The entry currently being processed.
	 * @param array $form The form currently being processed.
	 */
	public function after_submission( $entry, $form ) {
		// Evaluate the rules configured for the custom_logic setting.
		$result = $this->is_custom_logic_met( $form, $entry );
		if ( $result ) {
			// Do something awesome because the rules were met.
		}
	}
	// # HELPERS -------------------------------------------------------------------------------------------------------
	/**
	 * The feedback callback for the 'mytextbox' setting on the plugin settings page and the 'mytext' setting on the form settings page.
	 *
	 * @param string $value The setting value.
	 *
	 * @return bool
	 */
	public function is_valid_setting( $value ) {
		return strlen( $value ) < 10;
	}
}