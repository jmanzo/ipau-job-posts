jQuery(document).ready( function($){

	/**
	 *	Plugin Functionalities
	 *
	 */
	$('.logged-in .wpcf7-form #current_user').val(obj.user.ID);

	var url_redirect = obj.home_url + '/user/' + obj.user.user_login;
 
	$('.logged-in .wpcf7-form .wpcf7-submit').on( 'click', function( event ) {  
		var user_id 	  = obj.user.ID;
		var	post_title	  = $('input[name="job-title"]').val();
		var	post_content  = $('textarea[name="job-description"]').val();
		var	post_category = $('select[name="job-term"] option:selected').val();

		var data = {
			action: 'apf_addpost',
			post_title: post_title,
			post_content: post_content,
			post_category: post_category,
			post_author: user_id
		};
		console.log(data);

		$.ajax({
			url: obj.url,
			type: 'post',
			data: data,
			success: function( response ){
				console.log( response );
				$(location).attr('href', url_redirect);
				//window.location.replace(url);
			},
			error: function(error){
				console.log(error);
			}
		});

	});

});