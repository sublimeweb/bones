<?php

class acf_field_website_screenshot extends acf_field
{

	var $settings;


	/*
	*  __construct
	*
	*  Set name / label needed for actions / filters
	*
	*  @since	1.0
	*  @date	29/11/13
	*/

	function __construct()
	{
		// vars
		$this->name = 'website_screenshot';
		$this->label = __("Website Screenshot",'acf');
		$this->category = __("Content",'acf');
		$this->defaults = array(
			'save_format'	=>	'object',
			'preview_size'	=>	'thumbnail',
		);

		// do not delete!
    	parent::__construct();


    	// settings
		$this->settings = array(
			'path' => apply_filters('acf/helpers/get_path', dirname ( __FILE__) ),
			'dir' => apply_filters('acf/helpers/get_dir', dirname ( __FILE__) ),
			'version' => '0.1'
		);

		add_action('wp_ajax_acf_generate_screenshot', array($this, 'acf_generate_screenshot'));

	}


	/*
	*  input_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
	*  Use this action to add css + javascript to assist your create_field() action.
	*
	*  $info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
	*  @type	action
	*  @since	1.0
	*  @date	29/11/13
	*/

	function input_admin_enqueue_scripts()
	{

		// register acf scripts
		wp_register_script( 'acf-input-website-screenshot', $this->settings['dir'] . 'assets/js/input.js', array('acf-input'), $this->settings['version']);
		wp_register_style( 'acf-input-website-screenshot', $this->settings['dir'] . 'assets/css/input.css', array('acf-input'), $this->settings['version'] );


		// scripts
		wp_enqueue_script(array(
			'acf-input-website-screenshot',
		));

		// styles
		wp_enqueue_style(array(
			'acf-input-website-screenshot',
		));

	}

	/*
	*  generate_screenshot()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$site_url - an string with the site's url
	*  @param	$post_id - the id of the post to attach the image to
	*
	*  @type	action
	*  @since	1.0
	*  @date	29/11/13
	*/

	function generate_screenshot($site_url, $post_id, $width = 300, $height = 300)
	{

		require_once(ABSPATH . "wp-admin" . '/includes/image.php');
        require_once(ABSPATH . "wp-admin" . '/includes/file.php');
        require_once(ABSPATH . "wp-admin" . '/includes/media.php');

		$image_url = 'http://s.wordpress.com/mshots/v1/' . urlencode($site_url) . '?w=' . $width .'&h='. $height;

		do {
		    $response = wp_remote_get($image_url);
		} while ($response['headers']['content-type'] != 'image/jpeg' );

		$tmp = download_url( $image_url );
		$file = basename( $image_url );
		$info = pathinfo($file);

		$site_url = str_replace('http://', '', $site_url);
		$site_url = str_replace('https://', '', $site_url);
		$site_url = str_replace('www.', '', $site_url);
		$site_url = str_replace('/', '', $site_url);
		$file_name = sanitize_file_name($site_url);
		$file_name = remove_accents($file_name);
		$file_name = substr($file_name, 0, 100);
		$file_name = $file_name .'.jpeg';

		$file_array = array(
			'name' => $file_name,
			'tmp_name' => $tmp
		);

		if ( is_wp_error( $tmp ) ) {
			@unlink( $file_array[ 'tmp_name' ] );
			return 0;
		}
		$id = media_handle_sideload( $file_array, $post_id );

		if ( is_wp_error( $id ) ) {
			@unlink( $file_array['tmp_name'] );
			return 0;
		}

		return $id;

    }

    function acf_generate_screenshot() {
		if ( !isset($_POST['post_id']) || !isset($_POST['site_url']) )
            return 0;
        $response['error'] = false;
        $response['message'] = '';

        $field_settings = get_post_meta($_POST['field_group'], $_POST['field_key'], true);

        $attach_id = $this->generate_screenshot( $_POST['site_url'], $_POST['post_id'], $field_settings['shot_width'], $field_settings['shot_height'] );

        $size = (!isset($field_settings['preview_size'])) ? 'thumbnail' : $field_settings['preview_size'];
        $image = wp_get_attachment_image_src($attach_id, $size);

		if ($image != 0) {
			$response['value'] = $attach_id;
			$response['image'] = $image[0];
		}
        echo json_encode($response);
        die;
    }

	/*
	*  create_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field - an array holding all the field's data
	*
	*  @type	action
	*  @since	1.0
	*  @date	29/11/13
	*/

	function create_field( $field )
	{
		// vars
		$o = array(
			'class'		=>	'',
			'url'		=>	'',
		);

		if( $field['value'] && is_numeric($field['value']) )
		{
			$url = wp_get_attachment_image_src($field['value'], $field['preview_size']);

			$o['class'] = 'active';
			$o['url'] = $url[0];
		}

		if ($field['link_field'] != '') { ?>
<div class="acf-website-screenshot clearfix">
	<div class="has-image">
		<img class="acf-screenshot-image" src="<?php echo $o['url']; ?>" alt=""/>
		<div class="hover">
			<ul class="bl">
				<li><a class="acf-button-delete ir" href="#"><?php _e("Remove",'acf'); ?></a></li>
			</ul>
		</div>
	</div>
	<input class="acf-image-value" type="hidden" name="<?php echo $field['name']; ?>" value="<?php echo $field['value']; ?>" />
	<input type="hidden" class="acf-wsg-source-link-field" value="<?php echo $field['link_field']; ?>" />
	<input type="hidden" class="acf-wsg-field-key" value="<?php echo $field['key']; ?>" />
	<input type="hidden" class="acf-wsg-field-group" value="<?php echo $field['field_group']; ?>" />
	<br>
	<input type="button" class="button generate-screenshot" value="<?php _e('Generate','acf'); ?>" />
	<div class="spinner"></div>
</div>
		<?php } else _e('Screenshot field not configured','acf'); { }
	}

	/*
	*  get_acf_fields()
	*
	*  Gets all the acf fields configure
	*
	*  @type	action
	*  @since	1.0
	*  @date	29/11/13
	*
	*  @param	$args	- an array holding arguments to limit the fields return
	*/

	private function get_acf_fields( $postid, $args = array() )
	{
		$fields = array();
		$meta = get_metadata( 'post', $postid );
		if ($meta) {
			foreach ($meta as $key => $value ) {
				if ( substr( $key, 0, 6 ) == 'field_' ) {
					$field_data = unserialize( $value[0] );

					if ( count($args) > 0 ) {
						foreach ($args as $arg_key => $arg_value) {
							if (isset($field_data[$arg_key]) && $field_data[$arg_key] == $arg_value ) {
								$fields[$field_data['name']] = $field_data['label'];
							}
						}
					} else {
						$fields[$field_data['name']] = $field_data['label'];
					}

				}
			}
		}
		return $fields;
	}


	/*
	*  create_options()
	*
	*  Create extra options for your field. This is rendered when editing a field.
	*  The value of $field['name'] can be used (like bellow) to save extra data to the $field
	*
	*  @type	action
	*  @since	1.0
	*  @date	29/11/13
	*
	*  @param	$field	- an array holding all the field's data
	*/

	function create_options( $field )
	{
		// vars
		$key = $field['name'];

		if (isset($_POST['post_id'])) {
			$post_id = $_POST['post_id'];
		} else {
			global $post;
			$post_id = $post->ID;
		}

		?>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label><?php _e("Source Website URL Field",'acf'); ?></label>
		<p><?php _e("Specify an ACF text field that will contain the URL to screenshot",'acf') ?></p>
	</td>
	<td>
		<?php

		$args = array( 'type' => 'text' );
		$fields = $this->get_acf_fields( $post_id, $args );
		$choices = array();
		if ($fields) {
			foreach( $fields as $name => $label )
			{
				$choices[$name] = $label;
			}
		}
		if (count($choices) == 0) $choices[] = 'You need to create an ACF text field that will contain the URL.';
		$link_value = (isset($field['link_field'])) ? $field['link_field'] : '';

		do_action('acf/create_field', array(
			'type' => 'select',
			'name' => 'fields[' . $key . '][link_field]',
			'value'	=> $link_value,
			'choices' => $choices,
			'multiple' => '0',
		));
		?>
	</td>
</tr>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label><?php _e("Screenshot Size",'acf'); ?></label>
		<p><?php _e("Specify the size (px) of the screenshot taken",'acf') ?></p>
	</td>
	<td>
		<?php

		$width = (isset($field['shot_width'])) ? $field['shot_width'] : '300';
		$height = (isset($field['shot_height'])) ? $field['shot_height'] : '300';

		_e('Width:', 'acf');
		do_action('acf/create_field', array(
			'type'	=>	'number',
			'name'		=>	'fields['.$key.'][shot_width]',
			'value'		=>	$width,
		));
		?>
		<br>
		<?php
		_e('Height:','acf');
		do_action('acf/create_field', array(
			'type'		=>	'number',
			'name'		=>	'fields['.$key.'][shot_height]',
			'value'		=>	$height
		));
		?>
	</td>
</tr>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label><?php _e("Return Value",'acf'); ?></label>
		<p><?php _e("Specify the returned value on front end",'acf') ?></p>
	</td>
	<td>
		<?php
		do_action('acf/create_field', array(
			'type'		=>	'radio',
			'name'		=>	'fields['.$key.'][save_format]',
			'value'		=>	$field['save_format'],
			'layout'	=>	'horizontal',
			'choices'	=> array(
				'object'	=>	__("Image Object",'acf'),
				'url'		=>	__("Image URL",'acf'),
				'id'		=>	__("Image ID",'acf')
			)
		));
		?>
	</td>
</tr>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label><?php _e("Preview Size",'acf'); ?></label>
		<p><?php _e("Shown when entering data",'acf') ?></p>
	</td>
	<td>
		<?php
		do_action('acf/create_field', array(
			'type'		=>	'radio',
			'name'		=>	'fields['.$key.'][preview_size]',
			'value'		=>	$field['preview_size'],
			'layout'	=>	'horizontal',
			'choices' 	=>	apply_filters('acf/get_image_sizes', array())
		));

		?>
	</td>
</tr>
<?php

	}

	/*
	*  format_value_for_api()
	*
	*  This filter is appied to the $value after it is loaded from the db and before it is passed back to the api functions such as the_field
	*
	*  @type	filter
	*  @since	1.0
	*  @date	29/11/13
	*
	*  @param	$value	- the value which was loaded from the database
	*  @param	$post_id - the $post_id from which the value was loaded
	*  @param	$field	- the field array holding all the field options
	*
	*  @return	$value	- the modified value
	*/

	function format_value_for_api( $value, $post_id, $field )
	{

		// validate
		if( !$value )
		{
			return false;
		}


		// format
		if( $field['save_format'] == 'url' )
		{
			$value = wp_get_attachment_url( $value );
		}
		elseif( $field['save_format'] == 'object' )
		{
			$attachment = get_post( $value );


			// validate
			if( !$attachment )
			{
				return false;
			}


			// create array to hold value data
			$src = wp_get_attachment_image_src( $attachment->ID, 'full' );

			$value = array(
				'id' => $attachment->ID,
				'alt' => get_post_meta($attachment->ID, '_wp_attachment_image_alt', true),
				'title' => $attachment->post_title,
				'caption' => $attachment->post_excerpt,
				'description' => $attachment->post_content,
				'mime_type'	=> $attachment->post_mime_type,
				'url' => $src[0],
				'width' => $src[1],
				'height' => $src[2],
				'sizes' => array(),
			);


			// find all image sizes
			$image_sizes = get_intermediate_image_sizes();

			if( $image_sizes )
			{
				foreach( $image_sizes as $image_size )
				{
					// find src
					$src = wp_get_attachment_image_src( $attachment->ID, $image_size );

					// add src
					$value[ 'sizes' ][ $image_size ] = $src[0];
					$value[ 'sizes' ][ $image_size . '-width' ] = $src[1];
					$value[ 'sizes' ][ $image_size . '-height' ] = $src[2];
				}
				// foreach( $image_sizes as $image_size )
			}
			// if( $image_sizes )

		}

		return $value;

	}


}

new acf_field_website_screenshot();

?>
