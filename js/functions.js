jQuery(document).ready( function($){

	/**
	 *	Plugin Functionalities
	 *
	 */
	$('.logged-in .wpcf7-form #current_user').val(obj.user.ID);
	var form = $('.wpcf7-form').serialize() + '&user_id=' + obj.user.ID + '&user_login=' + obj.user.user_login;
	var url = obj.home_url + '/user/' + obj.user.user_login;
 
	$('.logged-in .wpcf7-form .wpcf7-submit').on( 'click', function( event ) {  
		
		$.ajax({
			url: obj.url,
			type: 'post',
			data: form,
			success: function( response ){
				console.log( response );
				$(location).attr('href', url);
				//window.location.replace(url);
			}
		});

	});

});