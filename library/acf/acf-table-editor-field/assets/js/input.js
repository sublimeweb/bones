/*
*  Table Editor
*
*  @description:
*  @since: 1.0
*  @created: 17/01/13
*/

jQuery(document).ready(function($) {


    var table_key;
    var table_editor;
    var table_settings;
    var table_values;
    var table_headers;
    var table_generated;

    function get_table(table) {

        table_key = $( table ).data("table");
        table_editor = $( table ).children( '.handsontable_editor' );
        table_settings = $( table ).children( '.table-settings' );
        table_values = $( table ).children( '.table-values' );
        table_headers = $( table ).children( '.table-headers' );
        table_generated = $( table ).children( '.table-generated' );
    }

    generate_tables();

     /*
     *  Generate table editors
     *
     *  @description:
     *  @since 1.0
     *  @created: 29/11/13
     */
    function generate_tables() {

        $( ".handsontable_editor_wrapper" ).each(function() {

            get_table(this);

            // Don't generate table for ACF Repeater clone
            if ( $(this).parents('.row-clone').length ) return;

            // Don't generate table for ACF Flexible Content clone
            if ( $(this).parents('.clones').length ) return;

            // Don't generate table if already generated
            if ( $( table_generated ).val() != 'false' ) return;

            var settings = $( table_settings ).val();
            settings = JSON.parse(settings);
            var cols = settings.number_columns;

            var row_data = [];
            for ( var i = 0; i < cols; i++ ) {
                row_data.push("");
            }

            var table_data = [];
            table_data.push(row_data);

            var saved_data =  $( table_values ).val();
            if ( saved_data != '' && saved_data != 'null' ) {
                table_data = JSON.parse(saved_data);
            }

            var parent_width = $(this).parents('.inside').width();

            var config = {
                data: table_data,
                colHeaders: true,
                contextMenu: ['row_above', 'row_below', 'remove_row', 'undo', 'redo']
            };

            if (settings.row_headers == 'on') {
                config['rowHeaders'] = true;
                parent_width = parent_width - 50;
            }

            if (settings.min_rows != '0') {
                config['minSpareRows'] = settings.min_rows;
            }

            if (settings.edit_columns == 'on') {
                config['contextMenu'] = true;
            }

            if (settings.drag_down == '') {
                config['fillHandle'] = false;
            }

            if (settings.table_width == 'on') {
                var col_width = (parent_width / cols) - 5;
                config['colWidths'] = Math.floor(col_width);
            }

            var headers =  $( table_headers ).val();
            if (headers != '' && headers != 'none') {
                config['colHeaders'] = JSON.parse( headers );
            } else if (headers == 'none') {
                config['colHeaders'] = false;
            }

            $( table_editor ).handsontable(config);

            $( table_generated ).val( 'true');
        });
    }

    $('#publish').on('click', function () {
        save_tables();
    });

    /*
     *  Save table editor data
     *
     *  @description:
     *  @since 1.0
     *  @created: 29/11/13
     */
    function save_tables() {
        $( ".handsontable_editor_wrapper" ).each(function() {
            get_table(this);
            var $container = $( table_editor );
            var table_data = $container.handsontable('getData');
            table_data = escape_data( table_data );
            table_data = JSON.stringify(table_data);
            $( table_values ).val( table_data );
        });
    }

    /*
     *  Clean cell data to fix double quote issue
     *
     *  @description:
     *  @since 1.1
     *  @created: 10/02/2014
     */
    function escape_data( data ) {
        if ( data && data.length ) {
            for(i = 0; i < data.length;i++){
                for( j = 0; j < data[i].length; j++){
                    var innerValue = data[i][j];
                    if (innerValue != null) {
                        data[i][j] = innerValue.replace(new RegExp('\"', "g"), '&quot;');
                        data[i][j] = innerValue.replace(new RegExp('"', "g"), '&quot;');
                    }
                }
            }
        }
        return data;
    }

    $(document).on('click', '.repeater .repeater-footer .add-row-end', function( e ){
        setTimeout(function(){
            generate_tables();
        }, 200);
    });

    $(document).on('click', '.repeater td.remove .add-row-before', function( e ){
        setTimeout(function(){
            generate_tables();
        }, 200);
    });

    $(document).on('click', '.acf-flexible-content .acf-fc-popup li a', function( e ){
        setTimeout(function(){
            generate_tables();
        }, 200);
    });

});