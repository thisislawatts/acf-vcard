<?php

class acf_field_vcard extends acf_field
{
	// vars
	var $settings, // will hold info such as dir / path
		$defaults,
		$DEBUG = false; // will hold default field options

	var $fields = array(
		array(
			'name' 		=> 'name',
			'itemprop'  => 'name',
			'class'		=> 'fn org',
			'label' 	=> 'Company Name'
		),
		array(
			'fieldset'	=> true,
			'name' 		=> 'address',
			'label'  	=> 'Address',
			'itemtype'	=> 'http://schema.org/PostalAddress',
			'itemprop'  => 'address',
			'class'		=> 'adr',
			'fields' 	=> array(
				array(
					'name'  => 'building-name',
					'label' => 'Building Name',
					'itemprop' => 'streetAddress',
					'class'	=> 'street-address'
				),
				array(
					'name'  => 'building-number',
					'label' => 'Building Number',
					'itemprop' => 'streetAddress',
					'class'	=> 'street-address'
				),
				array(
					'name'  => 'street-name',
					'label' => 'Street Name',
					'itemprop' => 'streetAddress',
					'class'	=> 'street-address'
				),
				array(
					'name'  	=> 'locality',
					'label' 	=> 'City',
					'itemprop' 	=> 'addressLocality',
					'class' 	=> 'locality'
				),
				array(
					'name'  => 'region',
					'label' => 'County',
					'itemprop' => 'addressRegion',
					'class' => 'region'
				),
				array(
					'name'  => 'postal-code',
					'label' => 'Post code',
					'itemprop' => 'postalCode',
					'class' => 'postal-code'
				),
				array(
					'name'  => 'country-name',
					'label' => 'Country',
					'itemprop' => 'addressCountry',
					'class' => 'country',
				)
			)
		),
		array(
			'name'  => 'tel',
			'itemprop' => 'telephone',
			'label' => 'Telephone',
			'tag' 	=> 'a'
		),
		array(
			'name'  => 'email',
			'itemprop' => 'email',
			'label' => 'Contact Email',
			'tag'	=> 'a'
		),
		array(
			'name'  => 'url',
			'label' => 'URL',
			'tag'   => 'a'
		),
		array(
			'name'  => 'twitter',
			'label' => 'Twitter',
			'tag'   => 'a'
		),
		array(
			'fieldset'	=> true,
			'name'		=> 'geoposition',
			'label'		=> 'GeoPositon',
			'itemtype'	=> 'GeoCoordinates',
			'itemprop'  => 'geo',
			'fields'	=> array(
				array(
					'name'  => 'latitude',
					'itemprop' => 'latitude',
					'label' => 'Latitude'
				),
				array(
					'name'	=>  'longitude',
					'itemprop' => 'longitude',
					'label' => 'Longitude'
				)
			),
			'defaults' => array(

			)
		)
	);



	/*
	*  __construct
	*
	*  Set name / label needed for actions / filters
	*
	*  @since	3.6
	*  @date	23/01/13
	*/

	function __construct()
	{
		// vars
		$this->name = 'vcard';
		$this->label = __('vcard', 'acf');
		$this->category = __("Content", 'acf'); // Basic, Content, Choice, etc
		$this->defaults = array(
			// add default here to merge into your field.
			// This makes life easy when creating the field options as you don't need to use any if( isset('') ) logic. eg:
			//'preview_size' => 'thumbnail'
		);


		// do not delete!
		parent::__construct();


		// settings
		$this->settings = array(
			'path' => apply_filters('acf/helpers/get_path', __FILE__),
			'dir' => apply_filters('acf/helpers/get_dir', __FILE__),
			'version' => '1.0.0'
		);

	}


	/*
	*  create_options()
	*
	*  Create extra options for your field. This is rendered when editing a field.
	*  The value of $field['name'] can be used (like bellow) to save extra data to the $field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field	- an array holding all the field's data
	*/

	function create_options($field)
	{
		// defaults?
		/*
		$field = array_merge($this->defaults, $field);
		*/

		// key is needed in the field names to correctly save the data
		$key = $field['name'];


		// Create Field Options HTML
		?><tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label><?php _e( 'Fields', 'acf'); ?></label>
		<p class="description"><?php _e("Pick and choose which fields to include in your vCard", 'acf'); ?></p>
	</td>
	<td><?php

	foreach ( $this->fields as $vcard_field ) {
		do_action('acf/create_field', array(
			'type'		=>	'true_false',
			'name'		=>	'fields['.$key.'][display][' . $vcard_field['name'] . ']',
			'value'		=>	isset( $field['display'][$vcard_field['name']] ) ? $field['display'][$vcard_field['name']] : '',
			'message'	=>	$vcard_field['label']
		));
	}

	?></td>
</tr>
		<?php

	}


	/*
	*  create_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field - an array holding all the field's data
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/

	function create_field( $field )
	{
		// defaults?
		$field = array_merge($this->defaults, $field);
		
		// initiate Fields
		?><div class="acf-vcard">
			<?php foreach ( $this->fields as $vcard_field ) {

				if ( $this->hide_this_field( $field, $vcard_field) ) {
					continue;
				}

				if ( $vcard_field['name'] === 'geoposition' ) {
					$this->create_google_map( $field, $vcard_field );

				} else if ( isset( $vcard_field['fieldset'] ) ) {
					echo '<fieldset class="acf-vcard-fieldset"><legend>' . $vcard_field['label'] . '</legend>';
					foreach ( $vcard_field['fields'] as $fieldset_field ) {
						$this->output_admin_field($field, $fieldset_field );
					}
					echo '</fieldset>';
				} else {
					$this->output_admin_field( $field, $vcard_field );

				}
			} ?>
		</div>
		<?php

		if ( $this->DEBUG ) {
			echo '<pre>';
			var_dump( $field );
			echo '</pre>';
		}
	}

	function create_google_map( $field, $vcard_field ) {

		$uid = preg_replace('/[\[\]]/i', '', $field['name'] ) . '-' . $vcard_field['name'];
		
		?><div class="acf-vcard--map-container">
			<div class="acf-vcard--map" id="location_map_<?php echo $uid; ?>"></div>
			<?php 
			/**
			 * dependencies
			 */
			foreach ( $vcard_field['fields'] as $fieldset_field ) {
				do_action( 'acf/create_field', array(
					'type'  => 'text',
					'name'  => $field['name'] . '[' . $fieldset_field['name'] . ']',
					'value' => !empty($field['value'][$fieldset_field['name']]) ? $field['value'][$fieldset_field['name']] : '', 
				) );
			} ?>
		</div><!-- .vcard--map-container -->
		<?php
	}

	function hide_this_field( $field_data, $field ) {
		return isset( $field_data['display'][$field['name']] ) && $field_data['display'][$field['name']] == 0;
	}

	function output_admin_field( $field, $vcard_field ) {
		printf(
			'<label for="%1$s">%3$s</label><input id="vcard_building_name" name="%1$s" type="text" value="%2$s" data-placeholder="%3$s"/>',
			$field['name'] . '[' . $vcard_field['name'] . ']',
			!empty($field['value'][$vcard_field['name']]) ? $field['value'][$vcard_field['name']] : '',
			$vcard_field['label']
		);
	}


	/*
	*  input_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
	*  Use this action to add css + javascript to assist your create_field() action.
	*
	*  $info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/

	function input_admin_enqueue_scripts()
	{
		// Note: This function can be removed if not used


		// register acf scripts

		wp_register_script( 'acf-vcard-google', 'https://maps.googleapis.com/maps/api/js?sensor=false' );
		wp_register_script('acf-input-vcard', $this->settings['dir'] . 'js/input.js', array('acf-input', 'acf-vcard-google'), $this->settings['version']);
		wp_register_style('acf-input-vcard', $this->settings['dir'] . 'css/input.css', array('acf-input'), $this->settings['version']);

		// scripts
		wp_enqueue_script(array(
			'acf-vcard-google',
			'acf-input-vcard'
		) );

		// styles
		wp_enqueue_style(array(
			'acf-input-vcard',
		) );

	}

	/*
	*  field_group_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is edited.
	*  Use this action to add css + javascript to assist your create_field_options() action.
	*
	*  $info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/

	function field_group_admin_enqueue_scripts()
	{
		// Note: This function can be removed if not used
	}


	/*
	*  field_group_admin_head()
	*
	*  This action is called in the admin_head action on the edit screen where your field is edited.
	*  Use this action to add css and javascript to assist your create_field_options() action.
	*
	*  @info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_head
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/

	function field_group_admin_head()
	{
		// Note: This function can be removed if not used
	}

	/*
	*  format_value_for_api()
	*
	*  This filter is appied to the $value after it is loaded from the db and before it is passed back to the api functions such as the_field
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value	- the value which was loaded from the database
	*  @param	$post_id - the $post_id from which the value was loaded
	*  @param	$field	- the field array holding all the field options
	*
	*  @return	$value	- the modified value
	*/

	function format_value_for_api( $value, $post_id, $field )
	{
		// defaults?
		/*
		$field = array_merge( $this->defaults, $field );
		*/

		if ( !$value ) return false;

		$output = '<div itemscope itemtype="http://schema.org/LocalBusiness" class="vcard">';

		foreach ( $this->fields as $field ) {

			if ( isset( $field['fieldset'] ) ) {

				$output .= sprintf( '<div itemscope itemprop="%s" itemtype="%s" class="%s">',
					$field['itemprop'],
					$field['itemtype'],
					isset( $field['class'] ) ? $field['class'] : $field['itemprop']
				);
				foreach ( $field['fields'] as $fieldset_sub_field ) {
					if (isset($value[$fieldset_sub_field['name']]))
						$output .= $this->formatted_output( $fieldset_sub_field , $value[ $fieldset_sub_field['name']] );
				}
				$output .= '</div>';

			} else {
				$output_value = isset($value[ $field['name'] ]) ? $value[ $field['name'] ] : '';
				$output .= $this->formatted_output( $field, $output_value );

				// Move in ACF Options
				if ( $field['name'] === "tel" ) {
					$output .= sprintf( '<a class="view-on-map" href="https://maps.google.com/maps?q=%s" target="_blank">View on Map</a>',
						implode(',', array(
							$value['building-number'] . ' ' . $value['street-name'],
							$value['locality'],
							$value['region'],
							$value['postal-code'],
							$value['country-name']
						) )
					);
				}
			}
		}
		$output .= '</div>';

		// Note: This function can be removed if not used
		return $output;
	}

	function formatted_output( $field, $value ) {
		
		if ( !isset( $field['name'] ) || empty( $value ) ) return;

		$is_link = !empty( $field['tag'] ) && $field['tag'] === 'a';

		return sprintf(
			'<%1$s %4$s class="%2$s" itemprop="%5$s">%3$s</%1$s> ',
			$is_link ? 'a' : 'div',
			isset($field['class']) ? $field['class'] : $field['name'],
			$value,
			$is_link ? $this->linkify( $value ) : '',
			isset($field['itemprop']) ? $field['itemprop'] : ''
		);
	}

	function linkify( $text ) {
		// Is it a phone number
		if ( strlen( preg_replace('/[0-9\+\(\)\s]/i', '', $text) ) === 0 ) {

			$patterns = array('/[\s\+\(0\)]/','/^07/', '/^44/');
			$replacements = array('', '00447', '0044');

			return 'href="tel:0044' . preg_replace( $patterns, $replacements, $text ) . '"';
		}

		$text = preg_replace("/(^|[\n ])([\w]*?)((ht|f)tp(s)?:\/\/[\w]+[^ \,\"\n\r\t<]*)/is", "href=\"$3\"", $text);
		$text = preg_replace("/(^|[\n ])([\w]*?)((www|ftp)\.[^ \,\"\t\n\r<]*)/is", "href=\"http://$3\"", $text);
		$text = preg_replace("/(^|[\n ])([a-z0-9&\-_\.]+?)@([\w\-]+\.([\w\-\.]+)+)/i", "href=\"mailto:$2@$3\"", $text);
		// Twitterify
    	$text= preg_replace("/^@(\w+)/", 'href="http://www.twitter.com/$1"', $text);

		return $text;
	}
}


// create field
new acf_field_vcard();