/*
*  Website Screenshot
*
*  @description:
*  @since: 1.0
*  @created: 17/01/13
*/

(function($){

	/*
	*  Vars
	*
	*  @description:
	*  @since: 1.0
	*  @created: 29/11/13
	*/

	acf.fields.website_screenshot = {}


	var _webscr = acf.fields.website_screenshot;


	/*
	*  Generate website screenshot
	*
	*  @description:
	*  @since 1.0
	*  @created: 29/11/13
	*/

	$('.acf-website-screenshot .generate-screenshot').live('click', function(){

		// vars

		var that = this;

		var link_field = $(that).prevAll('.acf-wsg-source-link-field').val();

		var url = $('#acf-field-' + link_field).val();
		var post_id = $('#post_ID').val();

		var field_group = $(that).prevAll('.acf-wsg-field-group').val();
		var field_key = $(that).prevAll('.acf-wsg-field-key').val();

		var spinner = $(that).next('.spinner');
		var attach_id = $(that).prevAll('.acf-image-value');
		var image = $(that).prevAll('.has-image').children('.acf-screenshot-image');

		$(spinner).show();
		$(that).attr("disabled", "disabled");

		$.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: { action:'acf_generate_screenshot',
            		post_id: post_id,
					site_url: url,
					field_group: field_group,
					field_key: field_key
            	 },
            success: function(response){
                if(response.error){
                    alert(response.message);
                    $(spinner).hide();
					$(that).removeAttr("disabled");
                } else {
                    $(attach_id).val(response.value);
                    $(image).attr('src', response.image);
                    $(spinner).hide();
					$(that).removeAttr("disabled");
                }
            },
            error: function(response, status, error){
            	alert('Error: ' + error.replace(/(<([^>]+)>)/ig,""));
            	$(spinner).hide();
            	$(that).removeAttr("disabled");
            }
        });

        return false;
	});

	$('.acf-website-screenshot .acf-button-delete').live('click', function(){
		$(this).parents('.has-image').nextAll('.acf-image-value').val('');
		$(this).parents('.hover').prev('.acf-screenshot-image').hide();
	});


})(jQuery);
