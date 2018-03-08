jQuery(document).ready( function($){
    window.jobform = {
        default:{
            s:'',
            job_role:'',
            job_location:'',
            firm_type:'',
            paged:0
        },
        data:{
            s:'',
            job_role:'',
            job_location:'',
            firm_type:'',
            paged:0
        },
        update:function(subdata){
            for(var info in subdata){
                if(jobform.data.hasOwnProperty(info)){
                    jobform.data[info] = subdata[info];
                }
            }
            jobform.inquire();
        },
        inquire:function(){
            $.ajax({
                url:job_form_ajaxdata.url,
                method:"POST",
                contentType:"application/x-www-form-urlencoded; charset=UTF-8",
                data:{
                    action:"jobsearch",
                    s:jobform.data.s,
                    job_role:jobform.data.job_role,
                    job_location:jobform.data.job_location,
                    firm_type:jobform.data.firm_type,
                    paged:jobform.data.paged,
                },
                beforeSend:function(){
                    $("#results").empty();
                    $("#results").append("<p>LOADING</p>");
                }
            }).done(function(rdata, textStatus, jqXHR){
                rdata = JSON.parse(rdata);
                if(rdata.status > 0){
                    $("#results").empty();
                    for(var row in rdata.data){
                        $("#results").append(rdata.data[row]);
                    }
                    $("#results").append(rdata.pagination);
                }else{
                   $("#results").empty();
                   $("#results").append("<p>"+rdata.data+"</p>");
                }
            });
        },
    };
    
    $("#job-search .filter").chosen({width: "100%"});
    
    $("#job-search .filter").on('change', function(e, params){
        var subdata = {};
        subdata[e.target.name] = params.selected;
        jobform.update(subdata);
    });
    
    var typingTimer;                //timer identifier
    var doneTypingInterval = 500;  //time in ms (5 seconds)

    $("#searchBox").keyup(function(){
        clearTimeout(typingTimer);
        var keyword = $(this).val();
        if ($(this).val()) {
            typingTimer = setTimeout(function(){ doneTyping(keyword); }, doneTypingInterval);
        }
    });
    
    function doneTyping(keyword){
        var subdata = {};
        subdata['s'] = keyword;
        jobform.update(subdata);
    }
    
    /**
	 *	Deprecated
	 *
	 *
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
    */
});