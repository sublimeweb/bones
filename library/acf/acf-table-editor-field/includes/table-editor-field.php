<?php

class acf_field_table_editor extends acf_field
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
		$this->name = 'table_editor';
		$this->label = __("Table Editor",'acf');
		$this->category = __("Content",'acf');
        $this->defaults = array(
            'number_columns'	=>	'3',
            'min_rows'          =>  '0',
            'custom_headers' 	=>	'simple',
            'header_names'		=>	'',
            'row_headers' 	    =>	'',
            'edit_columns' 	    =>	'',
            'drag_down'         => 'on',
            'table_width'       => 'on',
            'table_class'       => '',
        );

		// do not delete!
    	parent::__construct();


    	// settings
		$this->settings = array(
			'path' => apply_filters('acf/helpers/get_path', dirname ( __FILE__) ),
			'dir' => apply_filters('acf/helpers/get_dir', dirname ( __FILE__) ),
			'version' => '0.1'
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
	*  @since	1.0
	*  @date	29/11/13
	*/

	function input_admin_enqueue_scripts()
	{

		// register acf scripts
		wp_register_script( 'acf-input-table-editor', $this->settings['dir'] . 'assets/js/input.js', array('acf-input'), $this->settings['version']);
		wp_register_style( 'acf-input-table-editor', $this->settings['dir'] . 'assets/css/input.css', array('acf-input'), $this->settings['version'] );

        wp_register_script( 'acf-handsontable', $this->settings['dir'] . 'assets/js/jquery.handsontable.full.js', array('acf-input-table-editor'), $this->settings['version']);
        wp_register_style( 'acf-handsontable', $this->settings['dir'] . 'assets/css/jquery.handsontable.full.css', array('acf-input-table-editor'), $this->settings['version'] );

        // scripts
		wp_enqueue_script(array(
			'acf-input-table-editor',
            'acf-handsontable',
		));

		// styles
		wp_enqueue_style(array(
			'acf-input-table-editor',
            'acf-handsontable',
		));

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
        wp_register_script( 'acf-field-group-table-editor', $this->settings['dir'] . 'assets/js/field-group.js', array('acf-field-group'), $this->settings['version']);
        wp_register_style( 'acf-field-group-table-editor', $this->settings['dir'] . 'assets/css/field-group.css', array('acf-field-group'), $this->settings['version'] );

        // scripts
        wp_enqueue_script(array(
            'acf-field-group-table-editor',
        ));

        // styles
        wp_enqueue_style(array(
            'acf-field-group-table-editor',
        ));
    }

    function clean_headers($headers, $columns) {
        $data = array($headers);
        $headers = $this->clean_columns($data, $columns, true);
        return $headers[0];
    }

    function clean_columns($data, $columns, $header = false) {
        if (!is_array($data)) {
            //$data = htmlspecialchars(json_encode( $data ), ENT_QUOTES, 'UTF-8');
            $data = json_decode($data);
        }
        if ($data) {
            foreach ($data as $row_key => $row) {
                $count = count($row);
                if ($count == $columns) continue;
                if ($count < $columns) {
                    $alpha = 65 + $count;
                    for ($r = 0; $r <= ($columns - $count - 1); $r++) {
                        $alpha = $alpha + $r;
                        $td = ($header) ? chr($alpha) : "";
                        $data[$row_key][] = $td;
                    }
                }
                if ($count > $columns) {
                    for ($r = ($count - 1); $r >= ($columns); $r--) {
                        unset($data[$row_key][$r]);
                    }
                }
            }
        }
        return $data;
    }

    function clean_table_data($data, $columns) {
        $data = $this->clean_columns($data, $columns);
        $string = json_encode($data);
        $string = str_replace('&quot;', '\u0022', $string );
        $string = str_replace('\&quot;', '&quot;', htmlspecialchars($string));
        return str_replace("\'", '\u0027', $string);
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
        $settings = array(  'number_columns' => $field['number_columns'],
                            'row_headers' => $field['row_headers'],
                            'edit_columns' => $field['edit_columns'],
                            'drag_down' => $field['drag_down'],
                            'min_rows' => ((is_numeric($field['min_rows'])) ? $field['min_rows'] : '0'),
                            'table_width' => $field['table_width'],
                    );
        $settings = htmlspecialchars(json_encode($settings));

        $table_headers = '';
        if( $field['custom_headers'] && $field['custom_headers'] == 'custom'
                && $field['header_names'] && $field['header_names'] != '' ) {
            $headers = preg_split('/\r\n|\r|\n/', $field['header_names']);
            $table_headers = htmlspecialchars(json_encode($headers));
            $table_headers = str_replace("\r", "", $table_headers);
		} else if($field['custom_headers'] && $field['custom_headers'] == 'none') {
            $table_headers = 'none';
        }
        $table_data = (isset($field['value'])) ? $field['value'] : '';
        $table_data = $this->clean_table_data($table_data, $field['number_columns']);
        ?>
        <div class="handsontable_editor_wrapper" data-table="<?php echo $field['key']; ?>">
            <div class="handsontable_editor"></div>
            <input type="hidden" class="table-settings" value="<?php echo $settings; ?>">
            <input type="hidden" class="table-values" name="<?php echo $field['name']; ?>"  value="<?php echo $table_data; ?>">
            <input type="hidden" class="table-headers" value="<?php echo $table_headers; ?>">
            <input type="hidden" class="table-generated" value="false">
        </div>
	    <?php
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
		?>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label><?php _e("Number of columns",'acf'); ?></label>
		<p><?php _e("Specify the number of columns for the table", 'acf') ?></p>
	</td>
	<td>
		<?php
		$number_columns = (isset($field['number_columns'])) ? $field['number_columns'] : '';
		do_action('acf/create_field', array(
			'type' => 'number',
			'name' => 'fields[' . $key . '][number_columns]',
			'value'	=> $number_columns,
		));
		?>
	</td>
</tr>
<tr class="field_option field_option_<?php echo $this->name; ?> column-headers">
	<td class="label">
		<label><?php _e("Column Headers",'acf'); ?></label>
		<p><?php _e("Configure the column headers for the table", 'acf') ?></p>
	</td>
	<td>
		<?php
        $custom_headers = (isset($field['custom_headers'])) ? $field['custom_headers'] : 'simple';
        do_action('acf/create_field', array(
			'type'	    =>	'radio',
			'name'		=>	'fields['.$key.'][custom_headers]',
			'value'		=>	$custom_headers,
            'layout'	=>	'horizontal',
            'choices'	=>	array(
                'simple'	=>	__("Simple",'acf'),
                'custom'	=>	__("Custom",'acf'),
                'none'	    =>	__("None",'acf'),
            )
		));
		?>
	</td>
</tr>
<tr class="field_option field_option_<?php echo $this->name; ?> column-header-names">
	<td class="label">
		<label><?php _e("Custom Header Names",'acf'); ?></label>
		<p><?php _e("Enter a column header on a new line",'acf') ?></p>
	</td>
	<td>
		<?php
		do_action('acf/create_field', array(
			'type'		=>	'textarea',
			'name'		=>	'fields['.$key.'][header_names]',
			'value'		=>	$field['header_names'],
		));
		?>
	</td>
</tr>
<tr class="field_option field_option_<?php echo $this->name; ?>">
    <td class="label">
        <label><?php _e("Minimum Rows",'acf'); ?></label>
        <p><?php _e("Specify the number of minimum spare rows for the table", 'acf') ?></p>
    </td>
    <td>
        <?php
        $min_rows = (isset($field['min_rows'])) ? $field['min_rows'] : '0';
        do_action('acf/create_field', array(
            'type' => 'number',
            'name' => 'fields[' . $key . '][min_rows]',
            'value'	=> $min_rows,
        ));
        ?>
    </td>
</tr>
<tr class="field_option field_option_<?php echo $this->name; ?>">
    <td class="label">
        <label><?php _e("Row Numbers",'acf'); ?></label>
    </td>
    <td>
        <?php
        do_action('acf/create_field', array(
            'type'	    =>	'checkbox',
            'name'		=>	'fields['.$key.'][row_headers]',
            'value'		=>	$field['row_headers'],
            'layout'	=>	'horizontal',
            'choices'	=>	array(
                'on'	=>	__("Show row numbers on the table",'acf'),
            )
        ));
        ?>
    </td>
</tr>
<?php /*
<tr class="field_option field_option_<?php echo $this->name; ?>">
    <td class="label">
        <label><?php _e("Editable Columns",'acf'); ?></label>
    </td>
    <td>
        <?php
        do_action('acf/create_field', array(
            'type'	    =>	'checkbox',
            'name'		=>	'fields['.$key.'][edit_columns]',
            'value'		=>	$field['edit_columns'],
            'layout'	=>	'horizontal',
            'choices'	=>	array(
                'on'	=>	__("Allow users to add or remove columns",'acf'),
            )
        ));
        ?>
    </td>
</tr>
 */ ?>
<tr class="field_option field_option_<?php echo $this->name; ?>">
    <td class="label">
        <label><?php _e("Full Width Down",'acf'); ?></label>
    </td>
    <td>
        <?php
        do_action('acf/create_field', array(
            'type'	    =>	'checkbox',
            'name'		=>	'fields['.$key.'][table_width]',
            'value'		=>	$field['table_width'],
            'layout'	=>	'horizontal',
            'choices'	=>	array(
                'on'	=>	__("Makes the table width 100%",'acf'),
            )
        ));
        ?>
    </td>
</tr>
<tr class="field_option field_option_<?php echo $this->name; ?>">
    <td class="label">
        <label><?php _e("Drag Down",'acf'); ?></label>
    </td>
    <td>
        <?php
        do_action('acf/create_field', array(
            'type'	    =>	'checkbox',
            'name'		=>	'fields['.$key.'][drag_down]',
            'value'		=>	$field['drag_down'],
            'layout'	=>	'horizontal',
            'choices'	=>	array(
                'on'	=>	__("Allow users to drag down cells to repeat values",'acf'),
            )
        ));
        ?>
    </td>
</tr>
<tr class="field_option field_option_<?php echo $this->name; ?>">
    <td class="label">
        <label><?php _e("Table Class",'acf'); ?></label>
        <p><?php _e("CSS class for the table HTML output", 'acf') ?></p>
    </td>
    <td>
        <?php
        do_action('acf/create_field', array(
            'type'	    =>	'text',
            'name'		=>	'fields['.$key.'][table_class]',
            'value'		=>	$field['table_class']
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

	    $html = '<table class="acf-table-editor '. $field['table_class'] .'">';

        $headers = false;
        if( $field['custom_headers'] == 'simple' ) {
            for($alpha = 65; $alpha < (65 + $field['number_columns']); $alpha++) {
                $headers[] = chr($alpha);
            }
        } else if( $field['custom_headers'] == 'custom' && $field['header_names'] != '' ) {
            $headers = preg_split('/\r\n|\r|\n/', $field['header_names']);
        }

        if ($headers && is_array($headers)) {
            $headers = $this->clean_headers($headers,  $field['number_columns']);
            $html .= '<thead><tr>';
            foreach ($headers as $th) {
                $html .= '<th>'. $th .'</th>';
            }
            $html .= '</tr></thead>';
        }
        $html .= '<tbody>';
        $table_data = $value;
        $table_data = str_replace('\"', '"', $table_data);
        $table_data = str_replace("\'", "'", $table_data);
        $table_data = json_decode($table_data);
        if ($table_data) {
            $table_data = $this->clean_columns($table_data, $field['number_columns']);
            $row_count = 0;
            foreach($table_data as $tr) {
                $row_count++;
                if ($field['min_rows'] > 0 && $row_count > (count($table_data) - $field['min_rows'])) continue;
                $html .= '<tr>';
                foreach($tr as $td_key => $td) {
                    $value = str_replace('&quot;', '"', $td);
                    $value = do_shortcode( stripslashes( $value ) );
                    $html .= '<td>'. $value . '</td>';
                }
                $html .= '</tr>';
            }
        }
        $html .= '</tbody>';
        $html .= '</table>';
		return $html;
	}


}

new acf_field_table_editor();

?>
