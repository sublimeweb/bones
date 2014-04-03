/*
*  Table Editor
*
*  @description: 
*  @since: 1.0
*  @created: 08/12/13
*/

jQuery(document).ready(function($) {
	
	/*----------------------------------------------------------------------
	*
	*	Headers Type
	*
	*---------------------------------------------------------------------*/

    $(document).on('change', 'tr.column-headers input[type="radio"]', function( e ){
        e.preventDefault();
        toggle_headers(this);
    });

    $( 'tr.column-headers input[type="radio"]' ).each(function() {
        if ($(this).is(':checked'))  {
            toggle_headers(this);
        }
    });

    function toggle_headers(element) {
        var val = $(element).val(),
            $tr = $(element).parents('tr.column-headers').next('tr.column-header-names');
        if( val == "custom" ) {
            $tr.show();
        }
        else {
            $tr.hide();
        }
    }

});
