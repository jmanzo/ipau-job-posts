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
        getPage:function(){
            jobform.data.paged = $('#filterForm input[name="page"]').val();
            console.log(jobform.data.paged);
        },
        update:function(subdata){
            console.log(subdata);
            jobform.getPage();
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
                rdata = JSON.parse(rdata); console.log(rdata);
                if(rdata.status > 0){
                    $("#results").empty();
                    for(var row in rdata.data){
                        $("#results").append(rdata.data[row]);
                    }
                    $("#results").append(rdata.pagination[0]);
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

    $(document).on('click', '#pagination a', function(e){
        e.preventDefault();
        var link = $(this).attr('href');
        var form = $('#filterForm');
        form.attr('action', link);
        form.submit();
    });
    
    $(document).on('click', '#applybtn', function(e){
        $('#apply').addClass('active'); 
    });
    
    $(document).on('click', '#apply .close-hotspot', function(e){
        $('#apply').removeClass('active'); 
    });
    
    $('.marquee').marquee({
        pauseOnHover: true,
        duration: 5000,
        gap: 50,
        delayBeforeStart: 0,
        direction: 'left',
    });

/*
    $(document).on( 'click', '.ajax_pagination .page-numbers', function( event ) {
        event.preventDefault();
        paged = $(this).text()=='Next »' ? parseInt($('.ajax_pagination .page-numbers.current').text())+1 : $(this).text();
        
        $.ajax({
            url: job_form_ajaxdata.url,
            type: 'post',
            data: {
                action:"jobsearch",
                s:jobform.data.s,
                job_role:jobform.data.job_role,
                job_location:jobform.data.job_location,
                firm_type:jobform.data.firm_type,
                paged:paged,
            },
            beforeSend:function(){
                $("#results").empty();
                $("#results").append("<p>LOADING</p>");
            },
            success: function(rdata){
                rdata = JSON.parse(rdata);
                if(rdata.status > 0){
                    $("#results").empty();
                    for(var row in rdata.data){
                        $("#results").append(rdata.data[row]);
                    }
                    $("#results").append(rdata.pagination[0]);
                }else{
                   $("#results").empty();
                   $("#results").append("<p>"+rdata.data+"</p>");
                }
            }
        })
    })
 */
 
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