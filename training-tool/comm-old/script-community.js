/* 
 author: istockphp.com
 */

$ = jQuery;
var modadded = 0;
var formjson = {'fields' : [
                  {
                    "label": "Share Youe Experience?",
                    "field_type": "text",
                    "required": false,
                    "field_options": {},
                    "cid": "c1"
                  },                  
                  {
                    "label": "How was the seminar?",
                    "field_type": "radio",
                    "required": true,
                    "field_options": {
                        "options": [{
                            "label": "Fine",
                            "checked": false
                        }, {
                            "label": "Good",
                            "checked": false
                        }, {
                            "label": "Not Good",
                            "checked": false
                        }, {
                            "label": "Bad",
                            "checked": false
                        }],
                        "include_other_option": true
                    },
                    "cid": "c10"
                  }

                ]};
            

var dataofform = JSON.stringify(formjson);

jQuery(function ($) {
    
    datatables(); 
    forms(); 
    modalhide(); 
    genfuntions($);
    funs($);    
    chosen_initilize();    
   
    if($("#formid").length > 0 && $("#formid").val() > 0){
        var idmentor = $("#creatementorform").val();        
        if(idmentor > 0){            
            if($.trim($(".savedjsondata").text()) != ''){
                var savedjson = $.trim($(".savedjsondata").text());
                dataofform = savedjson;
            }
            loadform(idmentor,1);
        }
    }
    $( ".accordion" ).accordion({
        collapsible: true
    });
    $(".tabcustom a").click(function(){
            $(this).tab('show');
        }); 
        
    if($(".singlepagecourse").length > 0){
        
        $(".perint").text($('#percent_bar').val());
        $(".perdiv").css("width",$('#percent_bar').val()+"%");           
        setTimeout(function(){        
            $(".subheader").hide();
            $(".module").removeClass("active");
            $(".sidebar-left ul:first-child li.module:first-child").addClass("active").addClass("parentli");
            $(".modulelesson1").slideDown('slow');   
        },300);
        
    }    

    if($("#formid").val() > 0){
        var form_id = $("#formid").val();
        if($.trim($(".savedjsondata").text()) != ''){
            var savedjson = $.trim($(".savedjsondata").text());
            dataofform = savedjson;
            savedjson = $.parseJSON(savedjson);               
            formbuilder(form_id,savedjson.fields);
        }
        else{
            formbuilder(form_id,formjson.fields);
        }
        
    }
    var isclicked = 0;
    $('.sidebar-left').find('a').click(function () {

        isclicked = 1;
        if ($(this).parent().hasClass("active")) {
            return false;
        }                
            
        $(".active").removeClass("active");
        $(".parentli").removeClass("parentli");
        if ($(this).parent().hasClass("module")) {
                                    
            $(this).parent().addClass("parentli");               
            
            if(!$(this).parent().parent().next(".subheader").is(':visible')){            
                $(".subheader").slideUp('slow');
                $(this).parent().parent().next(".subheader").slideDown('slow');
            }
            
        }                      
        
        $(this).parent().addClass("active");        
        var sel = this;
        var hash = $(sel).attr('href');
       
        var newTop = parseInt(($(hash).offset().top) - $('.content_header').height());
        var old_top = newTop;
        if($("div.hor-menu-full").length > 0){
             var headht = $("div.hor-menu-full .navbar-nav-full").height();
             newTop = newTop - headht;
        }
        
        $('html,body').stop().animate({'scrollTop': newTop}, 300, function () {
            //window.location.hash = hash;
        });
        
        setTimeout(function(){
            isclicked = 0;
        },1500);
        
        return false;

    });

    var i = 0;
    var lastScrollTop = 0;
    $(window).scroll(function () {        
        if($(".templatemain").length > 0 && $(".singlepagecourse").length > 0){
            if($("div.hor-menu-full").length > 0){

                var mched = $("div.hor-menu-full").get(0).getBoundingClientRect();
                var headht = $("div.hor-menu-full").height();
                var mcctop = Math.abs(mched.top);           
                if (mcctop < headht) {
                        $("div.hor-menu-full .navbar-nav-full").removeAttr("style");
                    }
                    else {
                       $("div.hor-menu-full .navbar-nav-full").css({'top': '0', 'z-index': '999999','position': 'fixed', 'background': '#444D58'});
                    }

            }

            if($("#contentheader").length > 0){

                    if ($("#contentheader").visible(true)) {
                        $(".fixed_header").removeAttr("style").css({'visibility': 'hidden','display':'none'});
                    }
                    else {

                        var toptrainign = 0;
                        if($("div.hor-menu-full").length > 0){
                            var headht = $("div.hor-menu-full .navbar-nav-full").height();
                            toptrainign = headht;
                        }

                        $(".fixed_header").css({'top': toptrainign, 'visibility': 'visible','display':'block', 'position': 'fixed', 'z-index': '99999'}).show();
                    }
                }

            if(isclicked == 0){            
                var paridmod = $(".parentli").attr("id");
                var parattrmod = $(".parentli").attr("data-attr");

                var ob = $(this);
                var st = ob.scrollTop();
                $('.innerdata .blockcontent').each(function () {
                    var id = $(this).attr("id");
                    var el = document.getElementById(id).getBoundingClientRect();
                    var top = el.top;
                    if (top <= 200 && top >= 60) {
                        var obj = $(".sidebar-left a[href='#" + id + "']");
                        if (obj.parent().hasClass('module')) {

                            if (!obj.parent().hasClass('parentli')) {
                                if ($("#" + paridmod).hasClass("parentli")) {
                                    $("#" + paridmod).parent().next(".subheader").slideUp('slow');
                                }

                                $(".parentli").removeClass("parentli");
                                obj.parent().addClass('parentli')

                                obj.parent().parent().next(".subheader").slideDown('slow');
                            }
                        }
                        else {
                            //scroll up
                            if (st < lastScrollTop) {
                                if (obj.parent().hasClass('leson')) {
                                    var atr = obj.parent().attr('data-attr');

                                    if (parattrmod != atr) {
                                        if ($("#" + paridmod).hasClass("parentli")) {
                                            $(".parentli").removeClass("parentli");
                                            $("#" + paridmod).parent().next(".subheader").slideUp('slow');
                                            $(".module[data-attr=" + atr + "]").addClass("parentli");
                                            $(".module[data-attr=" + atr + "]").parent().next(".subheader").slideDown('slow');
                                        }
                                    }
                                }
                            }
                        }

                        $(".active").removeClass("active");
                        obj.parent().addClass("active");
                    }
                });

                lastScrollTop = st;
            }
        
        }
        
    });


    if($(".toplevel_page_triningtool,.toplevel_page_manage_mentor_calls").length > 0){        
        liset();       
    }


    if($(".mentorcallpage").length > 0){
        if($("#student_user").val() != '' && $("#student_user").val() > 0){
            $("#datecall").focus();
        }
    }


if($(".smallinfo").length > 0){    
    $(".smallinfo").each(function(){
        $(this).html($(this).html().replace('Â',' '));
    });
    
}

});

function liset(){
    $(".toplevel_page_triningtool li a,.toplevel_page_manage_mentor_calls li a").each(function(){
           var text = $.trim($(this).text());
           if(text == ''){
               $(this).css("padding","0");
           }
        });
}

$(window).bind("load",function(){ 
    if($(".singlepagecourse").length > 0){
        setTimeout(function(){
                $('body').scrollTop(0);            

            },100);
    }
})

window.onscroll = function (ev) {
    if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight) {
        if($(".templatemain").length > 0 && $(".singlepagecourse").length > 0){
            setTimeout(function(){                
                jQuery(".active").removeClass("active");
                if(jQuery(".lastproj").length > 0)
                    jQuery(".module:last").addClass("active");
                else
                    jQuery(".subheader li:last").addClass("active");
            },50);            
        }
        
    }
};

function datatables(){
    jQuery('#data_courses').dataTable
        ({
                // "bJQueryUI": false,
                 "bAutoWidth": true,
                "sPaginationType": "full_numbers",
                 "sDom": '<"datatable-header"fl>t<"datatable-footer"ip>',
                 "oLanguage": 
                 {
                         "sLengthMenu": "<span>Show entries:</span> _MENU_"
                 },
                 "aaSorting": [[ 0, "asc" ]],
                 "aoColumnDefs": [{ "bSortable": false, "aTargets": [5] }]
        });
    
    jQuery('#data_assign').dataTable
        ({
                // "bJQueryUI": false,
                 "bAutoWidth": true,
                "sPaginationType": "full_numbers",
                 "sDom": '<"datatable-header"fl>t<"datatable-footer"ip>',
                 "oLanguage": 
                 {
                         "sLengthMenu": "<span>Show entries:</span> _MENU_"
                 },
                 "aaSorting": [[ 1, "asc" ]],
                 "aoColumnDefs": [{ "bSortable": false, "aTargets": [0,4] }]
        });
    
    jQuery('#coursesimages').dataTable
        ({
                // "bJQueryUI": false,
                 "bAutoWidth": true,
                "sPaginationType": "full_numbers",
                 "sDom": '<"datatable-header"fl>t<"datatable-footer"ip>',
                 "oLanguage": 
                 {
                         "sLengthMenu": "<span>Show entries:</span> _MENU_"
                 },
                 "aaSorting": [[ 0, "asc" ]],
                 "aoColumnDefs": [{ "bSortable": false, "aTargets": [3] }]
        });
    
    jQuery('#data_modules').dataTable
        ({
                // "bJQueryUI": false,
                 "bAutoWidth": true,
                "sPaginationType": "full_numbers",
                 "sDom": '<"datatable-header"fl>t<"datatable-footer"ip>',
                 "oLanguage": 
                 {
                         "sLengthMenu": "<span>Show entries:</span> _MENU_"
                 },
                 "aaSorting": [[ 0, "asc" ]],
                 "aoColumnDefs": [{ "bSortable": false, "aTargets": [5] }]
        });
        
    jQuery('#data_lessons').dataTable
        ({
                // "bJQueryUI": false,
                 "bAutoWidth": true,
                "sPaginationType": "full_numbers",
                 "sDom": '<"datatable-header"fl>t<"datatable-footer"ip>',
                 "oLanguage": 
                 {
                         "sLengthMenu": "<span>Show entries:</span> _MENU_"
                 },
                 "aaSorting": [[ 0, "asc" ]],
                 "aoColumnDefs": [{ "bSortable": false, "aTargets": [5] }]
        });
        
        jQuery('#data_resources').dataTable
        ({
                // "bJQueryUI": false,
                 "bAutoWidth": true,
                "sPaginationType": "full_numbers",
                 "sDom": '<"datatable-header"fl>t<"datatable-footer"ip>',
                 "oLanguage": 
                 {
                         "sLengthMenu": "<span>Show entries:</span> _MENU_"
                 },
                 "aaSorting": [[ 0, "asc" ]],
                 "aoColumnDefs": [{ "bSortable": false, "aTargets": [5] }]
        });
        
        jQuery('#data_calls,#data_forms').dataTable
        ({
                // "bJQueryUI": false,
                 "bAutoWidth": true,
                "sPaginationType": "full_numbers",
                 "sDom": '<"datatable-header"fl>t<"datatable-footer"ip>',
                 "oLanguage": 
                 {
                         "sLengthMenu": "<span>Show entries:</span> _MENU_"
                 },
                  //"aaSorting": [[ 2, "desc" ]],
                 //"aoColumnDefs": [{ "bSortable": false, "aTargets": [5] }]
        });
        
        jQuery('#data_notes').dataTable
        ({
                // "bJQueryUI": false,
                 "bAutoWidth": true,
                "sPaginationType": "full_numbers",
                 "sDom": '<"datatable-header"fl>t<"datatable-footer"ip>',
                 "oLanguage": 
                 {
                         "sLengthMenu": "<span>Show entries:</span> _MENU_"
                 },
                 "aaSorting": [[ 2, "desc" ]],
                 "aoColumnDefs": [{ "bSortable": false, "aTargets": [3] }]
        });
        
        jQuery('#data_links, #data_docs').dataTable
        ({
                // "bJQueryUI": false,
                 "bAutoWidth": true,
                "sPaginationType": "full_numbers",
                 "sDom": '<"datatable-header"fl>t<"datatable-footer"ip>',
                 "oLanguage": 
                 {
                         "sLengthMenu": "<span>Show entries:</span> _MENU_"
                 },
                 "aaSorting": [[ 0, "asc" ]],
                 "aoColumnDefs": [{ "bSortable": false, "aTargets": [2] }]
        });   
        
        
        jQuery('#data_sresult').dataTable
        ({
                // "bJQueryUI": false,
                 "bAutoWidth": true,
                "sPaginationType": "full_numbers",
                 "sDom": '<"datatable-header"fl>t<"datatable-footer"ip>',
                 "oLanguage": 
                 {
                         "sLengthMenu": "<span>Show entries:</span> _MENU_"
                 },
                 "aaSorting": [[ 4, "desc" ]],
                 "aoColumnDefs": [{ "bSortable": false, "aTargets": [5] }]
        });   
        
        jQuery('.commontbl').dataTable
        ({
                // "bJQueryUI": false,
                 "bAutoWidth": true,
                "sPaginationType": "full_numbers",
                 "sDom": '<"datatable-header"fl>t<"datatable-footer"ip>',
                 "oLanguage": 
                 {
                         "sLengthMenu": "<span>Show entries:</span> _MENU_"
                 }                
        });
        tabldynmic();     
        
        jQuery(".datetimepicker").datetimepicker();
        
}


function tabldynmic(){
    $('.tblenrolled').dataTable
        ({               
                 "bAutoWidth": true,
                "sPaginationType": "full_numbers",
                 "sDom": '<"datatable-header"fl>t<"datatable-footer"ip>',
                 "oLanguage": 
                 {
                         "sLengthMenu": "<span>Show entries:</span> _MENU_"
                 },
                 "aaSorting": [[ 0, "asc" ]],
                 //"aoColumnDefs": [{ "bSortable": false, "aTargets": [3] }]
        });
}

function forms(){
    jQuery("#add_course").validate({
        ignore: ".hidfield",
        submitHandler: function(){
                        
            var description = '';
            if (jQuery("#wp-description-wrap").hasClass("tmce-active")){
                
                    description  = encodeURIComponent(tinyMCE.get('description').getContent());
            }
            else{
                    description  = encodeURIComponent(jQuery('#description').val());
            }
            
            var isupdate = 0;
            var param = "&param=add_course&action=training_lib";
            if(jQuery("#course_id").length > 0 && jQuery("#course_id").val() > 0){
                isupdate = 1;
            }
            
            var podata = jQuery("#add_course").serialize()+"&description="+description + param;
            $("body").addClass("processing");
            jQuery.post(ajaxurl, podata, function(dat){
                    $("body").removeClass("processing");
                    var data = jQuery.parseJSON(dat);                   
                    if(data.status == 0){
                        show_msg(data.sts,data.msg);
                    }
                    else{
                        if(isupdate == 1){
                            show_msg(data.sts,data.msg);
                        }
                        else{
                            
                            var insertid = data.arr.lastid;
                            location.href = "admin.php?page=course_detail&course_id="+insertid;
                        }
                    }                   
            });

           
        }
    });
    
    jQuery("#addmodules").validate({
        submitHandler: function(){
            
            var description = '';
            if (jQuery("#wp-description-wrap").hasClass("tmce-active")){
                
                    description  = encodeURIComponent(tinyMCE.get('description').getContent());
            }
            else{
                    description  = encodeURIComponent(jQuery('#description').val());
            }
            var podata = jQuery("#addmodules").serialize()+"&description="+description;
            $("body").addClass("processing");
            jQuery.post(ajaxurl, podata + "&param=add_module&action=training_lib", function(dat){
                    $("body").removeClass("processing");
                    var data = jQuery.parseJSON(dat);
                    show_msg(data.sts,data.msg);                          
                    if(jQuery("#id").val() == 0) {
                        jQuery("#addmodules").get(0).reset();
                        window.location.href = jQuery(".bkbtn").attr('href');
                    }
                    else{
                        window.location.reload();
                    }
                                       
             });

           
        }
    });
    jQuery("#addlesson").validate({
        submitHandler: function(){
            
            var description = '';
            if (jQuery("#wp-description-wrap").hasClass("tmce-active")){
                
                    description  = encodeURIComponent(tinyMCE.get('description').getContent());
            }
            else{
                    description  = encodeURIComponent(jQuery('#description').val());
            }
            var podata = jQuery("#addlesson").serialize()+"&description="+description;
            $("body").addClass("processing");
            jQuery.post(ajaxurl, podata + "&param=add_lesson&action=training_lib", function(dat){
                    $("body").removeClass("processing");
                    var data = jQuery.parseJSON(dat);
                    show_msg(data.sts,data.msg);      
                    
                    if(jQuery("#lessid").val() == 0){
                        jQuery("#addlesson").get(0).reset();
                        window.location.href = jQuery(".bkbtn").attr('href');
                    }
                    else{
                        window.location.reload();
                    }
                    
             });

           
        }
    });
    
    jQuery("#addresource").validate({
        submitHandler: function(){
            
            var description = '';
            if (jQuery("#wp-description-wrap").hasClass("tmce-active")){
                
                    description  = encodeURIComponent(tinyMCE.get('description').getContent());
            }
            else{
                    description  = encodeURIComponent(jQuery('#description').val());
            }
            var podata = jQuery("#addresource").serialize()+"&description="+description;
            $("body").addClass("processing");
            jQuery.post(ajaxurl, podata + "&param=add_resource&action=training_lib", function(dat){
                    $("body").removeClass("processing");
                    var data = jQuery.parseJSON(dat);
                    show_msg(data.sts,data.msg);      
                    if($("#typerescreated").val() == "page"){
                        $("#addresource").get(0).reset();
                    }
                    if($("#resid").val() == 0)
                        $("#addresource").get(0).reset();
                    
                    modadded++;
             });

           
        }
    });
    
    jQuery("#addprojectexce").validate({
        submitHandler: function(){
            
            var description = '';
            if (jQuery("#wp-description1-wrap").hasClass("tmce-active")){
                
                    description  = encodeURIComponent(tinyMCE.get('description1').getContent());
            }
            else{
                    description  = encodeURIComponent(jQuery('#description1').val());
            }
            
            var podata = jQuery("#addprojectexce").serialize()+"&description="+description;
            $("body").addClass("processing");
            jQuery.post(ajaxurl, podata + "&param=add_projectexcersie&action=training_lib", function(dat){
                    $("body").removeClass("processing");
                    var data = jQuery.parseJSON(dat);
                    show_msg(data.sts,data.msg);                    
             });

           
        }
    });
        
    
    jQuery("#addcall").validate({
        submitHandler: function(){
                        
            var param = "&param=add_call&action=training_lib";                        
            var podata = jQuery("#addcall").serialize() + param;
            jQuery("body").addClass("processing");
            jQuery.post(ajaxurl, podata, function(dat){
                    jQuery("body").removeClass("processing");
                    var data = jQuery.parseJSON(dat);                   
                    show_msg(data.sts,data.msg);
                    if(data.sts == 1){
                        window.location.href = "admin.php?page=manage_mentor_calls";
                    }
            });

           
        }
    });
    
    jQuery("#addvideo").validate({
        submitHandler: function(){
            var typematerial = $("#typematerial").val(); 
			if(typematerial=="community_call"){
				var course_id = $('#course_id').val(); 
			    var param = "&param=add_video&action=training_lib&course_id="+course_id;         
			}else{
			    var param = "&param=add_video&action=training_lib";  
			}
           
                var podata = jQuery("#addvideo").serialize() + "&typematerial="+typematerial + param;
				jQuery("body").addClass("processing");
				jQuery.post(ajaxurl, podata, function(dat){

						$("body").removeClass("processing");
						var data = jQuery.parseJSON(dat);                   
						show_msg(data.sts,data.msg); 
						if(data.sts == 1){
							window.location.reload();
						}else{ console.log("Failed"); }
				});
        }
    });
    
    
    jQuery("#addnote").validate({
        submitHandler: function(){
                        
            var typematerial = $("#typematerial").val();  
			if(typematerial=="community_call"){
				var course_id = $('#course_id').val(); 
			    var param = "&param=add_note&action=training_lib&course_id="+course_id;         
			}else{
				var param = "&param=add_note&action=training_lib";                        
				
			}
			    var notetxt = '';
				if (jQuery("#wp-descriptionnote-wrap").hasClass("tmce-active")){

						notetxt  = encodeURIComponent(tinyMCE.get('descriptionnote').getContent());
				}
				else{
						notetxt  = encodeURIComponent(jQuery('#descriptionnote').val());
				}
				if(notetxt == ''){
					alert("Please enter notes"); return false;
				}
				var podata = jQuery("#addnote").serialize() + "&notetxt="+ notetxt + "&typematerial="+typematerial + param;
					jQuery("body").addClass("processing");
					jQuery.post(ajaxurl, podata, function(dat){
							jQuery("body").removeClass("processing");
							var data = jQuery.parseJSON(dat);                   
							show_msg(data.sts,data.msg); 
							if(data.sts == 1){
								if($("#noteid").val() == 0)
									$("#addnote").get(0).reset()
							}
							modadded++;
					});
		}
    });
    
    jQuery("#addhlink").validate({
        submitHandler: function(){
               
                                   
            var typematerial = $("#typematerial").val();
			
			if(typematerial=="community_call"){
				var course_id = $('#course_id').val(); 
			    var param = "&param=add_hlink&action=training_lib&course_id="+course_id;         
			}else{
			    var param = "&param=add_hlink&action=training_lib"; 
			}
			
            var podata = jQuery("#addhlink").serialize() + "&typematerial="+typematerial + param;
            jQuery("body").addClass("processing");
            jQuery.post(ajaxurl, podata, function(dat){
                    jQuery("body").removeClass("processing");
                    var data = jQuery.parseJSON(dat);                   
                    show_msg(data.sts,data.msg); 
                    if(data.sts == 1){
                        if($("#helpnkid").val() == 0)
                            $("#addhlink").get(0).reset()
                    }
                    modadded++;
                    
            });

           
        }
    });
    
    
    $("#addimg").validate({
        submitHandler: function(){
            
            if($("#responseimg").val() == ''){
                if($.trim($(".uploadedimg").html()) == ''){
                    alert("Please choose a file"); return false;
                }
            }
            var data = '';
            if($("#responseimg").val() != ''){
                data = new FormData();
                $.each($('#responseimg')[0].files, function (i, file) {
                    data.append('file-' + i, file);
                });
            }
            
            var course_id = $("#course_id").val();
            var urlimg = $("#urlimg").val();
            
            var podata = ajaxurl+ "?course_id=" + course_id + "&urlimg=" + urlimg + "&param=save_courseimg&action=training_lib";
            $("body").addClass("processing");        

            $.ajax({
                type: "POST",
                url: podata,
                data: data,
                processData: false,
                contentType: false,
                success: function (msg)
                {
                    $("body").removeClass("processing");
                    var data = $.parseJSON(msg);
                    show_msg(data.sts,data.msg); 
                    if(data.sts == 1){                        
                        var imgtag = data.arr.tag;
                        if(typeof imgtag !== 'undefined')
                            $(".rowmod[data-id="+course_id+"] .imgtd").html(imgtag);
                        
                        if(typeof data.arr.fullpath !== 'undefined')
                            $(".rowmod[data-id="+course_id+"] .imgtd").attr("data-img",data.arr.fullpath);
                        
                        if(typeof data.arr.link !== 'undefined')
                            $(".rowmod[data-id="+course_id+"] .imgtd").attr("data-link",data.arr.link);
                        if(typeof imgtag === 'undefined'){
                            if(typeof data.arr.link !== 'undefined')
                                $(".rowmod[data-id="+course_id+"] .imgtd a").attr("href",data.arr.link);
                        }
                        
                        $("#image_dialog").modal("hide");
                        $("#addimg").get(0).reset();
                        $(".uploadedimg").empty();
                    }

                },
                error: function (msg)
                {
                    $("#fileList").empty();
                    $("#responsedoc").val("");
                    $("body").removeClass("processing");                
                }
            });
                      
        }
    });
    
    
    /* for user */
    $("#userform").validate({
        submitHandler: function(){
            var is_enrol = 0;
            $(".msgsml").html('Press Enter to check user available');
            var podata = jQuery("#userform").serialize()+"&is_enrol="+is_enrol+"&course_id="+$("#reportcourse").val();
            $("body").addClass("processing");
            $.post(ajaxurl, podata + "&param=enroll_user&action=training_lib", function(dat){
                    $("body").removeClass("processing");
                    var data = jQuery.parseJSON(dat);                    
                    show_msg(data.sts,data.msg);
                    $(".msgsml").html(data.msg);                                
             });

           
        }
    });
    
       
    $(document).on("keyup","#uemail",function(e){
        if($.trim($(this).val()) == ''){
            $(".msgsml").html('Press Enter to check user available');
        }
    });
            
    $(document).on("click",".btnenrolclk",function(e){
        if($("#userform").valid()){
            var is_enrol = 1;
            $(".msgsml").html('Press Enter to check user available');
            var podata = jQuery("#userform").serialize()+"&is_enrol="+is_enrol+"&course_id="+$("#reportcourse").val();
            $("body").addClass("processing");
            $.post(ajaxurl, podata + "&param=enroll_user&action=training_lib", function(dat){
                    $("body").removeClass("processing");
                    var data = jQuery.parseJSON(dat);
                    show_msg(data.sts,data.msg);     
                    $(".msgsml").html(data.msg);
                    if(data.sts == 1){
                      window.location.reload();
                    }               
             });
        }                
        
    });
    
    
    /* for mentor */
    $(document).on("keyup","#memail",function(e){
        if($.trim($(this).val()) == ''){
            $(".msgsml").html('Press Enter to check mentor available');
        }
    });
     
    $("#mentorform").validate({
        submitHandler: function(){
            var is_enrol = 0;
            $(".msgsml").html('Press Enter to mentor available');
            var podata = jQuery("#mentorform").serialize()+"&is_enrol="+is_enrol+"&course_id="+$("#reportcourse").val();
            $("body").addClass("processing");
            $.post(ajaxurl, podata + "&param=add_mentor&action=training_lib", function(dat){
                    $("body").removeClass("processing");
                    var data = jQuery.parseJSON(dat);                    
                    show_msg(data.sts,data.msg);
                    $(".msgsml").html(data.msg);                                
             });

           
        }
    });
    
    $(document).on("click",".btnmentoradd",function(e){
        if($("#mentorform").valid()){
            var is_enrol = 1;
            $(".msgsml").html('Press Enter to mentor available');
            var podata = jQuery("#mentorform").serialize()+"&is_enrol="+is_enrol+"&course_id="+$("#reportcourse").val();
            $("body").addClass("processing");
            $.post(ajaxurl, podata + "&param=add_mentor&action=training_lib", function(dat){
                    $("body").removeClass("processing");
                    var data = jQuery.parseJSON(dat);
                    show_msg(data.sts,data.msg);     
                    $(".msgsml").html(data.msg);
                    if(data.sts == 1){
                      window.location.reload();
                    }               
             });
        }                
        
    });
    
    
    $(document).on("click",".revoke_course_access",function(e){
        
        var conf = confirm("Are you sure?");
        if(conf){
            var enrol_id = $(this).attr("data-id");
            $(".msgsml").html('Press Enter to check user available');
            var podata = "enrol_id="+enrol_id;
            $("body").addClass("processing");
            $.post(ajaxurl, podata + "&param=revoke_user&action=training_lib", function(dat){
                    $("body").removeClass("processing");
                    var data = jQuery.parseJSON(dat);
                    show_msg(data.sts,data.msg);     
                    $(".msgsml").html(data.msg);
                    if(data.sts == 1){
                       window.location.reload();
                    }               
             });   
        }
        
    });
    
    
     $(document).on("click",".remove_mentor",function(e){
        
        var conf = confirm("Are you sure?");
        if(conf){
            var u_id = $(this).attr("data-id");
            var course_id = $("#reportcourse").val();
            $(".msgsml").html('Press Enter to check mentor available');
            var podata = "course_id="+course_id+"&u_id="+u_id;
            $("body").addClass("processing");
            $.post(ajaxurl, podata + "&param=remove_mentor&action=training_lib", function(dat){
                    $("body").removeClass("processing");
                    var data = jQuery.parseJSON(dat);
                    show_msg(data.sts,data.msg);     
                    $(".msgsml").html(data.msg);
                    if(data.sts == 1){
                       window.location.reload();
                    }               
             });   
        }
        
    });
    
}

var timout;

function openvideodialog(){
    
    var txt = '';
    if(jQuery(".videotxt").length > 0){
        $(".btnupdt").text("Update");
        txt = $(".videotxt").text();
    }
    jQuery("#embedcode").val(txt);
    open_modal('video_dialog');
     
}
function show_msg(sts,msg){    
    clearTimeout(timout);
    if(jQuery(".messdv").length == 0){
        jQuery(".msg").html('<div class="messdv"></div>').show();
    }
    
    if(sts == 0){
        jQuery(".messdv").addClass("alert alert-danger").html(' <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> \n\
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>\n\
            ' + msg).slideDown('slow');
        
    }
    else if(sts == 1){
        jQuery(".messdv").addClass("alert alert-success").html(' <span class="glyphicon glyphicon-ok" aria-hidden="true"></span> \n\
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>\n\
            ' + msg).slideDown('slow');
    }
    
    timout = setTimeout(function(){
        jQuery(".msg").slideUp('slow').html('').show();
    },8000);
}

function open_modal(id){
    jQuery("#"+id).modal();
}
function close_modal(){
    jQuery(".modal").modal('hide');
}

function submitmodle(){
    jQuery("#addmodules").submit();
}

function reset_form(){
    
    jQuery("form").each(function(){
        jQuery(this).get(0).reset();
    });
    
    if($(".btnupdt").length > 0){
        $(".btnupdt").text("Submit");
    }
    if($("#fileList").length > 0){
        $("#fileList").empty();
    }
    
    jQuery("#helpnkid").val('');
    jQuery("#noteid").val('');
    jQuery("#addmodules #id").val('');
    jQuery("#addlesson #lessid").val('');
    jQuery("#addlesson #lessid").val('');
    jQuery("#addresource #resid").val('');
    
}

function modalhide(){    
    $('#lesson_dialog,#confirm_dialog,.modealrealodonclose').on('hidden.bs.modal', function () {       
        if(modadded > 0)
            location.reload();
    });
}

function genfuntions($){
    $(document).on("click",".editmod",function(){
       var id = $(this).attr("data-id");
       var dat = $(".rowmod[data-id="+id+"] td.text div.textdiv").html();
       var titl = $(".rowmod[data-id="+id+"] td.title").attr("data-txt");
       var lnk = $(".rowmod[data-id="+id+"] td.title").attr("data-lnk");
       $("#id").val(id);
       if (jQuery("#wp-description-wrap").hasClass("tmce-active")){
                                
                tinyMCE.get('description').setContent(dat, {format : 'html'} );
        }
        else{
                $('#description').val(dat)
        }
       
       $("#title").val(titl);
       $("#link").val(lnk);
       open_modal('confirm_dialog');
       
    });
    
    
    $(document).on("click",".deletemod",function(){
        var conf = confirm("This will also delete all lessons associated with this module. Are you sure to delete?");
        if(conf){
            var id = $(this).attr("data-id");
            var podata = jQuery("#addmodules").serialize()+"&id="+id;
            $("body").addClass("processing");
            $.post(ajaxurl, podata + "&param=delete_module&action=training_lib", function(dat){
                    $("body").removeClass("processing");
                    var data = $.parseJSON(dat);
                    show_msg(data.sts,data.msg);      
                    $(".rowmod[data-id="+id+"]").remove();
             });
        }
    });
    
    
    
    $(document).on("click",".deletedoc",function(){
        var conf = confirm("Are you sure to delete?");
        if(conf){
            var id = $(this).attr("data-id");
            var podata = "id="+id;
            $("body").addClass("processing");
            $.post(ajaxurl, podata + "&param=delete_doc&action=training_lib", function(dat){
                    $("body").removeClass("processing");
                    var data = $.parseJSON(dat);
                    show_msg(data.sts,data.msg);      
                    $(".rowmoddoc[data-id="+id+"]").remove();
             });
        }
    });
    
    
    $(document).on("click",".deletecou",function(){
        var conf = confirm("This will also delete all module and lessons associated with this course. Are you sure to delete?");
        if(conf){
            var id = $(this).attr("data-id");
            $("body").addClass("processing");
            $.post(ajaxurl, "id="+ id + "&param=delete_course&action=training_lib", function(dat){
                    $("body").removeClass("processing");
                    var data = $.parseJSON(dat);
                    show_msg(data.sts,data.msg);      
                    $(".rowmod[data-id="+id+"]").remove();
             });
        }
    });
    
    
    $(document).on("click",".editless",function(){
       var id = $(this).attr("data-id");
       var dat = $(".rowmod[data-id="+id+"] td.text div.textdiv").html();
       var titl = $(".rowmod[data-id="+id+"] td.title").attr("data-txt");
       var lnk = $(".rowmod[data-id="+id+"] td.title").attr("data-lnk");       
       $("#lessid").val(id);
       
        if (jQuery("#wp-description-wrap").hasClass("tmce-active")){
                
                tinyMCE.get('description').setContent(dat, {format : 'html'} );
        }
        else{
                $('#description').val(dat)
        }
       
       $("#title").val(titl);
       $("#link").val(lnk);       
       open_modal('lesson_dialog');
       
    });
    
    
    $(document).on("click",".deleteless",function(){
        var conf = confirm("This will also delete associated resources. Are you sure to delete?");
        if(conf){
            var id = $(this).attr("data-id");
            var podata = jQuery("#addlesson").serialize()+"&id="+id;
            $("body").addClass("processing");
            $.post(ajaxurl, podata + "&param=delete_lesson&action=training_lib", function(dat){
                    $("body").removeClass("processing");
                    var data = $.parseJSON(dat);
                    show_msg(data.sts,data.msg);      
                    $(".rowmod[data-id="+id+"]").remove();
             });
        }
    });
    
    $(document).on("click",".deleteres",function(){
        var conf = confirm("Are you sure to delete?");
        if(conf){
            var id = $(this).attr("data-id");
            var podata = jQuery("#addresource").serialize()+"&id="+id;
            $("body").addClass("processing");
            $.post(ajaxurl, podata + "&param=delete_resource&action=training_lib", function(dat){
                    $("body").removeClass("processing");
                    var data = $.parseJSON(dat);
                    show_msg(data.sts,data.msg);      
                    $(".rowmod[data-id="+id+"]").remove();
             });
        }
    });
    
    $(document).on("click",".deletelink",function(){
        var conf = confirm("Are you sure to delete?");
        if(conf){
            var id = $(this).attr("data-id");
            var podata = "id="+id;
            $("body").addClass("processing");
            $.post(ajaxurl, podata + "&param=delete_hlink&action=training_lib", function(dat){
                    $("body").removeClass("processing");
                    var data = $.parseJSON(dat);
                    show_msg(data.sts,data.msg);      
                    $(".rowmodlink[data-id="+id+"]").remove();
             });
        }
    });
    
    
    $(document).on("click",".editres",function(){
       $(".btnupdt").text("Update");
       var id = $(this).attr("data-id");
       var dat = $(".rowmod[data-id="+id+"] td.text div.textdiv").html();
       var titl = $(".rowmod[data-id="+id+"] td.title").attr("data-txt");
       var lnk = $(".rowmod[data-id="+id+"] td.title").attr("data-lnk");
       var btntype = $(".rowmod[data-id="+id+"] td.title").attr("data-btn");
       var hrs = $.trim($(".rowmod[data-id="+id+"] td.hrs").text());
       
       $("#resid").val(id);
       
        if (jQuery("#wp-description-wrap").hasClass("tmce-active")){
                
                tinyMCE.get('description').setContent(dat, {format : 'html'} );
        }
        else{
                $('#description').val(dat)
        }
       
       $("#button_type").val(btntype);
       $("#title").val(titl);
       $("#link").val(lnk);
       $("#hours").val(hrs);
       open_modal('lesson_dialog');
       
    });
        
    
    $(document).on("click",".editnote",function(){
       var id = $(this).attr("data-id");      
       $(".btnupdt").text("Update");
       var note = $(".rowmodnote[data-id="+id+"] td.title .notetext").html();       
       $("#noteid").val(id);    
       tinyMCE.get('descriptionnote').setContent(note, {format : 'html'} )
       //$("#notetxt").val(note);      
       open_modal('note_dialog');
       
    });
       
    $(document).on("click",".editlink",function(){
       var id = $(this).attr("data-id");      
       $(".btnupdt").text("Update");
       var lnk = $(".rowmodlink[data-id="+id+"] td.title").attr('data-link');       
       var lnktitle = $(".rowmodlink[data-id="+id+"] td.title").attr('data-title');       
       $("#helpnkid").val(id);              
       $("#linktitle").val(lnktitle);      
       $("#linkurl").val(lnk);      
       open_modal('help_dialog');
       
    });   
    
       
    $(document).on("click",".deletenote",function(){                         
       var conf = confirm("Are you sure?");
       if(conf){
           $("body").addClass("processing");
            var id = $(this).attr('data-id');
            var podata = "id="+id + "&param=delete_note&action=training_lib";
            $.post(ajaxurl,podata,function(dat){
                $("body").removeClass("processing");
                var data = $.parseJSON(dat);
                show_msg(data.sts,data.msg);
                if(data.sts == 1){
                    $(".rowmodnote[data-id="+id+"]").remove();
                }
            });
        }
       
       
    });
    
    $(document).on("click",".pemissioncourse",function(){                         
       
        if($(this).prop("checked") == true){            
            $(".usersdivperm").slideDown("slow");
            $("#userperm").removeClass("hidfield");
        }
        else{            
            $(".usersdivperm").slideUp("slow");
            $("#userperm").addClass("hidfield");
        }
       
    });
    
        
    $(document).on("click",".moreinfo", function(){
        $(this).closest('.smallinfo').hide();
        $(this).closest('.smallinfo').next().show();
    });
    
    $(document).on("click",".lessinfo", function(){
        $(this).closest('.largeinofinfo').hide();
        $(this).closest('.largeinofinfo').prev().show();
    });
    
    $(document).on("click",".settingbtn", function(){        
       var podata = $("#settingform").serialize()+ "&param=save_settings&action=training_lib";
       $("body").addClass("processing");
       $.post(ajaxurl,podata,function(dat){
           $("body").removeClass("processing");
           var data = $.parseJSON(dat);
           show_msg(data.sts,data.msg);
           if(data.sts == 1){
               window.location.reload();
           }
       });
       
    });
    
   $(document).on("click",".enroll", function(){
       var course = $(this).attr("data-attr");       
       var podata = "course="+course+ "&param=enroll_course&action=training_lib";
       $("body").addClass("processing");
       
       $.post(ajaxurl,podata,function(dat){
           $("body").removeClass("processing");
           var data = $.parseJSON(dat);
           show_msg(data.sts,data.msg);
           var url = $("#url_redirect").val()+course;
           if(data.sts == 1){               
               setTimeout(function(){
                   window.location.href = url;
               },3000);
           }
           else{
               window.location.href = url;
           }
       });
       
    });
    
    $(document).on("click",".markresource", function(){
        
       var typ = $(this).attr("data-buttontype");
       if(typ == 'mark'){         
                var resource_id = $(this).attr("data-attr");
                var status = $(this).attr("data-status");
                var uidadmincase = 0;
                if($("#uidused").length > 0){
                    uidadmincase = $("#uidused").val();
                }               
                var podata = "resource_id="+resource_id + "&status="+status + "&uidadmincase="+uidadmincase + "&param=mark_resource&action=training_lib";
                $("body").addClass("processing");

                $.post(ajaxurl,podata,function(dat){
                    $("body").removeClass("processing");
                    var data = $.parseJSON(dat);                     
                    if(data.sts == 1){                           
                        if(status == 'unmarked'){
                            $("#resource_"+resource_id).removeClass("unmarkeddiv").addClass("markeddiv");
                            $("#resource_"+resource_id+" .markresource").attr("data-status","marked").text("Completed");
                            calcpercent($,'inc');
                        }
                        else{
                            $("#resource_"+resource_id).removeClass("markeddiv").addClass("unmarkeddiv");
                            $("#resource_"+resource_id+" .markresource").attr("data-status","unmarked").text("Mark Complete");
                            calcpercent($,'dec');
                        }
                    }
                    else{
                        show_msg(data.sts,data.msg);
                    }

                });
        }
        else{
            
            var obj = $(this);
            $("input[name=project_links]").val('');
            $(".remove_project_ctn").hide();
            var uidadmincase = 0;
            if($("#uidused").length > 0){
                uidadmincase = $("#uidused").val();
            }
            var resou = $(this).attr("data-attr");
            $("body").addClass("processing");
            var podata = "typ=resource&resource_id="+resou+"&uidadmincase="+uidadmincase+"&param=get_links&action=training_lib";
            $.post(ajaxurl,podata,function(dat){
                var data = $.parseJSON(dat);
                $("body").removeClass("processing");
                if(data.sts == 1){
                    $("input[name=project_links]").val(data.arr.links);
                    $(".remove_project_ctn").attr("data-typ",'resource').attr("data-id",resou).show(); 
                }

                var pos = obj.offset(); var top = pos.top - 105;
                $(".arrow_box.submit_project").attr("data-id",resou).css({'display':'block','top':top});
                $(".project-submit-btn").attr("data-typ",'resource').attr("data-id",resou);       

            });
            
            
        }
       
    });
    
    
    $(document).on("click",".enrollbylist", function(){
       $("#ernrolledlist").html($(".gifhidden").html()); 
       $("#enrolled_dialog .modal-title").text("Course - '"+$(this).attr("data-title")+"' Enrolled Users");
       open_modal('enrolled_dialog');
       var podata = "course_id="+ $(this).attr("data-attr") + "&param=listenrolled&action=training_lib";       
       $.post(ajaxurl,podata,function(dat){
           
           var data = $.parseJSON(dat);
           var users = data.arr;
           var ul = "<ul class='list-group'>"; var li = ""; var i = 0;
           for(a in users){
               li += "<li class='list-group-item'> "+ users[a].display_name +" - "+ users[a].user_email +" </li>";
               i++;
           }
           
           ul = li + "</ul>";           
           if(i > 0)
               $("#ernrolledlist").html(ul);
           else
               $("#ernrolledlist").html("Course is not enrolled by any user yet.");
       });
       
    });
    
    
    $(document).on("click",".view_mentor a", function(){
       $("#mentorsid").html($(".gifhidden").html());
       open_modal('mentors_dialog');
       var podata = "ids="+ $(this).attr("data-ids") + "&param=listmentorscourses&action=training_lib";       
       $.post(ajaxurl,podata,function(dat){
           
           var data = $.parseJSON(dat);
           var users = data.arr;
           var ul = "<ul class='list-group'>"; var li = ""; var i = 0;
           for(a in users){
               li += "<li class='list-group-item'> "+ users[a].display_name +" - "+ users[a].user_email +" </li>";
               i++;
           }
           
           ul = li + "</ul>";
           if(i > 0)
               $("#mentorsid").html(ul);
           else
               $("#mentorsid").html("No Mentor Associated With This Course.");
       });
       
    });
    
    $(document).on("click",".cancelcall", function(){
        
       var conf = confirm("Are you sure?");
       if(conf){
           $("body").addClass("processing");
            var id = $(this).attr('data-id');
            var podata = "id="+id+ "&param=cancel_call&action=training_lib";       
            $.post(ajaxurl,podata,function(dat){
                $("body").removeClass("processing");
                var data = $.parseJSON(dat);
                show_msg(data.sts,data.msg);
                if(data.sts == 1){
                    $(".rowmentor[data-id="+id+"] td.status").html("<div class='alert alert-danger'>cancelled</div>");
                    $(".rowmentor[data-id="+id+"] td.actiontd ").html('<a href="javascript:;" data-id="'+id+'" class="deletecall btn btn-danger" title="Delete Call">Delete</a>');
                    
                    if($(".detailcallpage").length > 0){
                        window.location.reload();
                    }
                
                }
            });
        }
       
    });        
    
    
    $(document).on("click",".deletecall", function(){
       var conf = confirm("Are you sure?");
       if(conf){
           $("body").addClass("processing");
            var id = $(this).attr('data-id');
            var podata = "id="+id + "&param=delete_call&action=training_lib";
            $.post(ajaxurl,podata,function(dat){
                $("body").removeClass("processing");
                var data = $.parseJSON(dat);
                show_msg(data.sts,data.msg);
                if(data.sts == 1){
                    $(".rowmentor[data-id="+id+"]").remove();
                    if($(".detailcallpage").length > 0){
                        window.location.href = $(".backbread").attr("href");
                    }
                }
            });
        }
       
    });
    
    $(document).on("click",".btnclospop", function(){
        $(".arrow_box.submit_project").hide();
    });
    
     $(document).on("click",".submitproj", function(){
        var obj = $(this);
        $("input[name=project_links]").val('');
        $(".remove_project_ctn").hide();
        var proj = $(this).attr("data-id");
        $("body").addClass("processing");
        var podata = "proj="+proj+"&param=get_links&action=training_lib";
        $.post(ajaxurl,podata,function(dat){
            var data = $.parseJSON(dat);
            $("body").removeClass("processing");
            if(data.sts == 1){                
                $("input[name=project_links]").val(data.arr.links);
                $(".remove_project_ctn").attr("data-typ",'exercise').attr("data-id",proj).show(); 
                
            }
            
            var pos = obj.offset(); var top = pos.top - 105;
            $(".arrow_box.submit_project").attr("data-id",proj).css({'display':'block','top':top});
            $(".project-submit-btn").attr("data-typ",'exercise').attr("data-id",proj);       
            
        });                 
        
    });
    
    $(document).on("click",".remove_project_ctn", function(){
        var proj = $(this).attr("data-id");
        var datatyp = $(this).attr("data-typ");
        $("body").addClass("processing");
        var uidadmincase = 0;
        if($("#uidused").length > 0){
            uidadmincase = $("#uidused").val();
        }  
        
        var podata = "proj="+proj+"&datatyp="+datatyp+"&uidadmincase="+uidadmincase+"&param=remove_links&action=training_lib";
        $.post(ajaxurl,podata,function(dat){
            var data = $.parseJSON(dat);
            $("body").removeClass("processing");
            if(data.sts == 1){                                
                
                if(datatyp == 'exercise'){
                    
                    $("a.submitproj[data-id="+proj+"]").removeClass("linksumitted").text("Submit Project");
                    $("#proj"+proj).removeClass("submittedproj");                
                
                
                    var ttx = 'Submit project for this module';
                    if($("#proj"+proj+" .projlnk").hasClass("lastfinal"))
                        ttx = 'Complete final project';                    

                    $("#proj"+proj+" .projlnk").html('<a target="_blank" href="javascript:;">'+ttx+'</a>');
                    
                    $("#proj"+proj+" .sublinksstudents").hide();
                    $("#proj"+proj+" .projlinksdiv").empty();
                    
                }
                else{
                    
                    $(".markresource[data-attr="+proj+"]").attr("data-status","unmarked").text("Submit Project");
                    $("#resource_"+proj).removeClass("markeddiv");      
                    
                    $("#resource_"+proj+" .sublinksstudents").hide();
                    $("#resource_"+proj+" .projlinksdiv").empty();
                    
                }
                
                calcpercent($,'dec');
                $(".arrow_box.submit_project").hide();
                $("input[name=project_links]").val('');
                $(".remove_project_ctn").removeAttr("data-id").hide(); 
                
            }
               
            
        }); 
    });
    
    $(document).on("click",".project-submit-btn", function(){
        
		/*custom*/
		
        var dattyp = $(this).attr("data-typ");
        var proj = $(this).attr("data-id");        
        var links = $("input[name=project_links]").val();
        if(links == ''){
            alert("Please enter values"); return false;
        }
        
		/*Checking file is uploaded or not*/
		if($("#responsedoc").val() == ''){
            alert("Please choose file(s)");
            return false;
        }
		/*../ends*/
		
        links = links.split(","); var x = 0;
        for(i = 0; i < links.length; i++){
            if(ValidURL(links[i]) == false){
                x++;                
            }
        }
		
		var podata = "proj="+proj+"&links="+links+"&uidadmincase="+uidadmincase+"&dattyp="+dattyp+"&param=submit_links&action=training_lib&do=noupdate";
		
		/*collecting multiple files*/
		
		   /* var dataFiles = new FormData();
		    var img_ar = [];
			$.each($('#responsedoc')[0].files, function (i, file) {
				//dataFiles.append(i, file);
				img_ar.push(file);
			});
		    console.log(img_ar);*/
	
		/* ../ends */
		
        if(x > 0){
            alert("You have entered "+x+" invalid URL(s). Please correct before submit."); return false;
        }
        
        var uidadmincase = 0;
        if($("#uidused").length > 0){
            uidadmincase = $("#uidused").val();
        }  
       // $("body").addClass("processing");
        /*Form Multiple Files*/
		    var fd = new FormData();
			var file_data = $('input[type="file"]')[0].files; // for multiple files
			for(var i = 0;i<file_data.length;i++){
				fd.append("file_"+i, file_data[i]);
			}
		$("body").addClass("processing");
        $.post(ajaxurl,podata,function(dat){
           
			console.log("Log1 : "+dat);
			var resource_id = dat;
			var cus_url = ajaxurl+"?param=submit_links&action=training_lib&do=update&resourceid="+resource_id;
			$.ajax({
			   url:cus_url,
			   type: "POST",  
			   data:fd,
			   contentType: false,       
               cache: false,           
               processData:false,
			   success:function(dat){
			      console.log("Log2 : "+dat);
				  var data = $.parseJSON(dat);
				  $("body").removeClass("processing");
				  window.location.reload(); 
				  if(data.sts == 1){                
					var anchors = '';
					var haslinks = '';
					console.log("Log3 running ");  
					$(".arrow_box.submit_project").hide();
					if(dattyp == 'exercise'){ 
						console.log("Log5 running ");
						haslinks = $.trim($("#proj"+proj+" .projlnk").html());
						$("a.submitproj[data-id="+proj+"]").addClass("linksumitted").text("Submitted");
						$("#proj"+proj).addClass("submittedproj");                                      
						for(i = 0; i < links.length; i++){
							var path = links[i];
							anchors += "<a target='_blank' href='"+path+"'>"+path+"</a> <br/>";
						}
						$("#proj"+proj+" .projlnk").html(anchors);   
						console.log("My Images : "+data);
					}
					else{
						console.log("Log6 running ");
						haslinks = $.trim($("#resource_"+proj+" .projlinksdiv").html()); 
						$(".markresource[data-attr="+proj+"]").attr("data-status","marked").text("Submitted");
						$("#resource_"+proj+"").addClass("markeddiv");                                                                                   
						for(i = 0; i < links.length; i++){
							var path = links[i];
							anchors += "<a target='_blank' href='"+path+"'>"+path+"</a> <br/>";
						}
						$("#resource_"+proj+" .sublinksstudents").show();
						$("#resource_"+proj+" .projlinksdiv").html(anchors);  
                        console.log("My Images : "+data); 
					}
					 /* var htmlContent='';  
                      $.each( data, function( key, value ) {
						  if(key=="arr"){
							console.log(value);
							var temp = new Array();
							temp = value.split(",");
							
							for (a in temp ) {
								console.log(temp[a]);
								htmlContent+=temp[a]+"<br/>";
							}  
						  }
				     });
					  console.log(resource_id);*/
					  /*$('#resource_'+resource_id+'.submittedfiles').html("<h6>Submitted Files</h6><div>"+htmlContent+"</div>");
					  console.log(htmlContent);*/
					if(haslinks == '')    
						calcpercent($,'inc');


				}

				   
			   }	
			});
           /* var data = $.parseJSON(dat);
            //show_msg(data.sts,data.msg);
            if(data.sts == 1){                
                var anchors = '';
                var haslinks = '';
                $(".arrow_box.submit_project").hide();
                if(dattyp == 'exercise'){                   
                    haslinks = $.trim($("#proj"+proj+" .projlnk").html());
                    $("a.submitproj[data-id="+proj+"]").addClass("linksumitted").text("Submitted");
                    $("#proj"+proj).addClass("submittedproj");                                      
                    for(i = 0; i < links.length; i++){
                        var path = links[i];
                        anchors += "<a target='_blank' href='"+path+"'>"+path+"</a> <br/>";
                    }
                    $("#proj"+proj+" .projlnk").html(anchors);                
                }
                else{
                    haslinks = $.trim($("#resource_"+proj+" .projlinksdiv").html()); 
                    $(".markresource[data-attr="+proj+"]").attr("data-status","marked").text("Submitted");
                    $("#resource_"+proj+"").addClass("markeddiv");                                                                                   
                    for(i = 0; i < links.length; i++){
                        var path = links[i];
                        anchors += "<a target='_blank' href='"+path+"'>"+path+"</a> <br/>";
                    }
                    $("#resource_"+proj+" .sublinksstudents").show();
                    $("#resource_"+proj+" .projlinksdiv").html(anchors);  
                    
                }
                
                if(haslinks == '')    
                    calcpercent($,'inc');
                
                
            }*/
        });
        
    });
    
    $(document).on("click",".licls",function(){
        if(!$(this).hasClass('current')){
            $(".licls").removeClass("current");
            $(this).addClass('current');
            var clasother = $(this).find("a").attr("data-type");
            $(".clscomman").hide();
            $("."+clasother).fadeIn('slow');
        }
    });
    
     $(document).on("click",".sumitted_projs",function(){
        open_modal('project_summitted');
        var id =$(this).attr("data-id");
        var podata = "id=" + id + "&param=get_submissions&action=training_lib";           
            $.post(ajaxurl,podata,function(dat){                
                var data = $.parseJSON(dat);   
                var tds = '';

                if(data.arr.projects && data.arr.projects != ''){ 

                    var arlinks = data.arr.projects;

                    for(x in arlinks){
                        tds += "<tr>";
                        tds += "<td>"+arlinks[x].display_name+"</td>";
                        tds += "<td>"+arlinks[x].user_email+"</td>";                
                        var lnks = arlinks[x].links;
                        lnks = lnks.split(","); var lnkanc = '';
                        for(i =0; i < lnks.length; i++){
                            lnkanc += '<a target="_blank" href="'+lnks[i]+'" >'+lnks[i]+'</a> <br/>';
                        }

                        tds += "<td>"+lnkanc+"</td>";
                        tds += "</tr>";
                    }                        
                }
                else{

                    tds += "<tr>";
                    tds += "<td colspan='3'>No record</td>";            
                    tds += "</tr>";
                }
                $(".loadergif").hide();
                $(".tbluserdv").show();
                $(".tbluserdv tbody").html(tds);
                
            });
        
        
        
    });
    
    
    
    $(document).on("click",".reorder",function(){
        $("#reordermodal").modal();
        var id = $(this).attr("data-id");
        var type = $(this).attr("data-type");        
        var podata = "id="+id+"&type="+type+"&param=get_rows&action=training_lib";        
        $.post(ajaxurl,podata,function(dat){                       
            var data = $.parseJSON(dat);
            var tit = "Re-order Exercises [Drag & Drop Rows]";
            if(type == 'modules'){
                tit = "Re-order Modules [Drag & Drop Rows]";
            }
            else if(type == 'lessons'){
                tit = "Re-order Lessons [Drag & Drop Rows]";               
            }
            else if(type == 'courses'){
                tit = "Re-order Courses [Drag & Drop Rows]";               
            }
            $(".reordersave").attr("data-type",type);
            $(".reordersave").attr("data-id",id);
            $(".reordertitl").text(tit);
            if(data.sts == 1){                                    
              var rows = data.arr;  
              var html = '<ul class="listul" id="sortableul">';
              for(a in rows){
                  
                  html += '<li class="listli" data-id="'+rows[a].id+'" data-ord="'+rows[a].ord+'">'+rows[a].title+'</li>';
              }
              html += '</ul>';
              $("#reorderrows").html(html);
              sortul();
            }
            else{
                
                $("#reorderrows").html(data.msg);
            }
        });
        
    });
    
    
    $(document).on("click",".movelesson",function(){
        $("#movemodal").modal();
        var id = $(this).attr("data-id");
        var type = $(this).attr("data-type");        
        var podata = "id="+id+"&type="+type+"&param=get_moverows&action=training_lib";        
        $.post(ajaxurl,podata,function(dat){                       
            var data = $.parseJSON(dat);
           
            var tit = "Move Exercises";
            if(type == 'modules'){
                tit = "Move Modules";
            }
            else if(type == 'lessons'){
                tit = "Move Lessons";               
            }
            else if(type == 'courses'){
                tit = "Move Courses";               
            }
            $(".reordersave").attr("data-type",type);
            $(".reordersave").attr("data-id",id);
            $(".reordertitl").text(tit);
            if(data.sts == 1){         
              console.log(data);
              var mods = data.arr.rows_modules;
              var rows = data.arr.rows;  
              
              var select = '<div class="control-group"><label>Select Module</label><select style="margin-top: 10px;" class="form-control" name="module" id="module">';
              var module_id = $("#module_id").val();
               for(x in mods){
                   var chk = '';
                  if(module_id == mods[x].id){
                      chk = 'selected="selected"';
                  }
                  select += '<option '+chk+' value="'+mods[x].id+'" >'+mods[x].title+'</option>';
              }
              
              select += "</select></div>";
              
              
              
              var html = '<div class="control-group"><ul class="listul">';
              for(a in rows){
                  
                  html += '<li class="movelistli" data-id="'+rows[a].id+'" data-ord="'+rows[a].ord+'">\n\
                    <label> <input class="chmove" type="checkbox" name="chkrows" value="'+rows[a].id+'" />'+rows[a].title+'</label></li>';
              }
              html += '</ul></div>\n\
                <div class="row"><hr/></div>'+select;
              
              
              
              $("#moverows").html(html);
              sortul();
            }
            else{
                
                $("#moverows").html(data.msg);
            }
        });
        
    });
    
     $(document).on("click",".reordersave",function(){
        
        var id = $(this).attr("data-id");
        var type = $(this).attr("data-type");  
        
        var armult = [];
        var i = 1;
        $("#sortableul li").each(function(){           
           armult.push($(this).attr("data-id"));
           i++;
        });
        if(i == 1){
            alert("No Row Found");
            return false;
        }
        var podata = "armult="+armult+"&id="+id+"&type="+type+"&param=save_rows&action=training_lib";
        $("body").addClass("processing");
        $.post(ajaxurl,podata,function(dat){         
            $("body").removeClass("processing");
            var data = $.parseJSON(dat);
            show_msg(data.sts,data.msg);
            if(data.sts == 1){                                                  
              window.location.reload();
              
            }           
        });
        
    });
    
    
    $(document).on("click",".movesave",function(){
        
        var id = $(this).attr("data-id");
        var type = $(this).attr("data-type");  
        
        var modid = $("#module").val();
        if(modid == ''){
            alert("please select a module");
            return false;
        }
        var armult = [];        
        $(".chmove").each(function(){        
            if($(this).prop("checked") == true){
                armult.push($(this).val());
            }           
        });
        if(armult.length <= 0){
            alert("No Lesson Selecetd");
            return false;
        }
        
        
        var podata = "armult="+armult+"&modid="+modid+"&param=move_rows&action=training_lib";
        $("body").addClass("processing");
        $.post(ajaxurl,podata,function(dat){         
            $("body").removeClass("processing");
            var data = $.parseJSON(dat);
            show_msg(data.sts,data.msg);
            if(data.sts == 1){                                                  
                window.location.reload();              
            }           
        });
        
    });
    
     $(document).on("click",".uploadcourseimg",function(){
         var id = $(this).attr("data-id");
         $("#course_id").val(id);
         var row = $(".rowmod[data-id="+id+"] td.imgtd ");
         var img = row.attr("data-img");
         if(img != ''){
             $(".uploadedimg").html("<img src='"+img+"' />");
         }
         var link = row.attr("data-link");
         if(link != ''){
             $("#urlimg").val(link);
         }
         $("#image_dialog").modal();         
     });
     
     $(document).on("click",".generatereport",function(){
         var id = $("#reportcourse").val();
         if(id != ''){
            if($(".mentorhandlepage").length > 0){
                window.location.href = "admin.php?page=course_admin_mentors&course="+id;
            }
            else{
                window.location.href = "admin.php?page=course_admin&course="+id;
            }
        }
        else
            alert("Please select a course");
     });
     
    
    
     $(document).on("click",".reschedulecall",function(){
       
        var id = $(this).attr('data-id');
        window.location.href = "admin.php?page=manage_mentor_calls&call_id="+id;
        return false;
        
        var course_id = $("tr.rowmentor[data-id="+id+"] td.title").attr("data-title");
        $("#courseid").val(course_id);
        var user_id = $("tr.rowmentor[data-id="+id+"] td.name").attr("data-name");
        $("#student_user").val(user_id);
        var link = $("tr.rowmentor[data-id="+id+"] td.name").attr("data-link");            
        $("#meetinglink").val(link);
        var date = $("tr.rowmentor[data-id="+id+"] td.date").attr("data-date");
        $("#datecall").val(date);
        var isrecur = $("tr.rowmentor[data-id="+id+"] td.isrecur").attr("data-isrecur");
        
        if(isrecur == 1)
            $("#recurcall").prop('checked',true);
        else
            $("#recurcall").prop('checked',false);     
        
        if($("#mentorselect").length > 0){
            var mentor_id = $("tr.rowmentor[data-id="+id+"] td.mentor").attr("data-id");  
            if(typeof mentor_id === 'undefined'){
                $("#student_user").val(user_id);
            }
            else{
                $("#mentorselect").val(mentor_id);
                var course_id = $.trim($("#courseid").val());
                mentro_students(course_id,mentor_id,user_id);
            }            
        }
        
         
        $("#callid").val(id);
        $(".invtbtn").text("Re-Schedule Call");
        
        $('html, body').animate({scrollTop: 0}, 500,  function(){
            $("#datecall").focus();
        });
    });
    
    
    $(document).on("click",".attendeornot", function(){
        var name = $(this).attr('data-name');
        var val = $(this).val();
        var txt = name + " has not attended";
        if(val == 'yes'){
            txt = name + " has attended";
        }
        var conf = confirm("Are you sure, "+txt+" this call?");
        if(conf){
            $("body").addClass("processing");
            var id = $(this).attr('data-id');                
            var podata = "id="+id+"&val="+val+"&param=markattendence&action=training_lib";       
            $.post(ajaxurl,podata,function(dat){
                $("body").removeClass("processing");
                var data = $.parseJSON(dat);
                show_msg(data.sts,data.msg);
                if(data.sts == 1){
                    var ststxt = '<div class="divattended"><div>Call Attended</div><div class="alert alert-danger">No</div></div>';
                    if(val == 'yes'){
                        ststxt = '<div class="divattended"><div>Call Attended</div><div class="alert alert-success">Yes</div></div>';
                    }                   
                    $("tr.rowmentor[data-id="+id+"] .attdiv").html(ststxt);
                }
            });
        }
       
    });
    
    
    $(document).on("click",".assignmentor", function(e){
        $(".dddiv").empty().hide();
        $(".btndiv").show();
        var uid = $(this).attr('data-uid');        
        var mentor = $(".mentorrow[data-uid="+uid+"] td.mentortd").attr("data-mid");        
        if($(".coursereportpage").length > 0){
            mentor = $(".mentorrow[data-uid="+uid+"] .mentorspan").attr("data-mid");        
        }
        var listdd = $(".mentordd").html();
        $(".mentorrow[data-uid="+uid+"] .btndiv").hide();
        $(".mentorrow[data-uid="+uid+"] .dddiv").html(listdd).show();
        $(".dddiv #mentordropdown").val(mentor);
        e.stopPropagation();
        return false;
    });
    
     $(document).on("click","#select_all", function(e){
         if($(this).prop("checked"))
            $(".chkcommon").prop("checked",true);
        else
            $(".chkcommon").prop("checked",false);
     });
     
     $(document).on("click",".assignselected", function(e){
         var ar = [];
         $(".chkcommon").each(function(){
             if($(this).prop("checked")){
                 ar.push($(this).val());
             }
         });
         if(ar.length == 0){
             alert("Please select at least one user");
             return false;
         }
         var mentor = $(".assigntosel #mentordropdown").val();
         if(mentor == ''){
             alert("Please select mentor");
             return false;
         }
         
        $("body").addClass("processing"); 
        var course_id = $("#courseselect").val();
        var podata = "ar="+ar+"&course_id="+course_id+"&mentor="+mentor+"&param=assignmentormultiple&action=training_lib";       
        $.post(ajaxurl,podata,function(dat){
            $("body").removeClass("processing");
            var data = $.parseJSON(dat);
            show_msg(data.sts,data.msg);
            if(data.sts == 1){                
                window.location.reload();
            }
        });
         
         
     });
     
    $(document).on("change","#courseselect", function(e){
         var course = $(this).val();
         if(course != '')
            window.location.href = "admin.php?page=map_mentors&course="+course;
     });
    
    $(document).on("click",".resetcall", function(e){        
            window.location.href = "admin.php?page=manage_mentor_calls";
     });
    
    $(document).on("change","#courseid", function(e){
         var course = $(this).val();
         if(course != '')
            window.location.href = "admin.php?page=manage_mentor_calls&course="+course;
     });
     
     $(document).on("click",".dddiv #mentordropdown", function(e){
         e.stopPropagation();
         return false;
     });
    $(document).on("change",".dddiv #mentordropdown", function(e){   
        var course_id = 0;
        if($(".coursereportpage").length > 0){
            course_id = $("#reportcourse").val();
        }
        else{
            course_id = $("#courseselect").val();
        }
        
        var uid = $(this).parent().attr('data-uid');
        var id = $(this).parent().attr('data-id');
        var mentor = $.trim($(this).val());
        if(mentor == ''){
            alert("Please select a mentor");
            return false;
        }
        $("body").addClass("processing");
        
        var isdel = 0;
        if(mentor == '')
            isdel = 1;
        
        var podata = "id="+id+"&uid="+uid+"&course_id="+course_id+"&isdel="+isdel+"&mentor="+mentor+"&param=assignmentor&action=training_lib";       
        $.post(ajaxurl,podata,function(dat){
            $("body").removeClass("processing");
            var data = $.parseJSON(dat);
            show_msg(data.sts,data.msg);
            if(data.sts == 1){                
                var selectedtxt = $(".dddiv #mentordropdown option:selected").text();
                if(mentor == '')
                    selectedtxt = "Not Assigned";
                                
                if($(".coursereportpage").length > 0){
                    $(".coursereportpage .mentorrow[data-uid="+uid+"] span.mentorspan").html(selectedtxt);
                    $(".coursereportpage .mentorrow[data-uid="+uid+"] .btndiv a").text("Change Mentor");
                }
                else{
                    $(".mentorrow[data-uid="+uid+"] td.mentortd").html(selectedtxt);
                }
                if(data.arr > 0){
                    $(".mentorrow[data-uid="+uid+"] td.mentortd").attr("data-mid",data.arr);
                    if($(".coursereportpage").length > 0){
                        $(".mentorrow[data-uid="+uid+"] .mentorspan").attr("data-mid",data.arr);      
                    }
                }
                else{
                    $(".mentorrow[data-uid="+uid+"] td.mentortd").attr("data-mid","");
                    if($(".coursereportpage").length > 0){
                        $(".mentorrow[data-uid="+uid+"] .mentorspan").attr("data-mid","");      
                    }
                }
                
                $(".dddiv").empty().hide();
                $(".btndiv").show();
            }
        });
        
        e.stopPropagation();
        return false;
    });
        
    $(document).on("click",".callsch", function(e){   
        var course = $(this).attr('data-course');
        var user = $(this).attr('data-uid');
        var mentor_id = 0;
        if(typeof $("tr.mentorrow[data-uid="+user+"] span.mentorspan").attr("data-mid") !== 'undefined'){
            mentor_id = $("tr.mentorrow[data-uid="+user+"] span.mentorspan").attr("data-mid");
        }        
        window.location.href = "admin.php?page=manage_mentor_calls&mentor="+mentor_id+"&user="+user+"&course="+course;
    });
    
    $(document).on("change","#mentorselect", function(e){        
        
        var course_id = $.trim($("#courseid").val());
        var mentor = $.trim($(this).val());
        mentro_students(course_id,mentor,0);
    });
    
 
    $(document).on("click",function(){
        $(".dddiv").empty().hide();
        $(".btndiv").show();
    });
       
     $(document).on("click",".openform",function(){         
        //var id = $("#creatementorform").val();
        var isupdt = $(this).attr("data-update");
		var id=1;
        if(id > 0)
            loadform(id,isupdt);
        else
            alert("Please select mentor");
			
		/* var id='';
		 loadform(id,isupdt);*/
		 
     });
     
     
    $(document).on("click",".deletesurveyform",function(){
        var conf = confirm("This will also delete all survey results for this form. Are you sure to delete?");
        if(conf){
            var id = $(this).attr("data-id");
            var podata = "id="+id;
            $("body").addClass("processing");
            $.post(ajaxurl, podata + "&param=delete_surveyform&action=training_lib", function(dat){
                    $("body").removeClass("processing");
                    var data = $.parseJSON(dat);
                    show_msg(data.sts,data.msg);      
                    $(".rowmod[data-id="+id+"]").remove();
             });
        }
    });
    
    
    $(document).on("click",".deletesurvey",function(){
        var conf = confirm("Are you sure to delete?");
        if(conf){
            var id = $(this).attr("data-id");
            var podata = "id="+id;
            $("body").addClass("processing");
            $.post(ajaxurl, podata + "&param=delete_survey&action=training_lib", function(dat){
                    $("body").removeClass("processing");
                    var data = $.parseJSON(dat);
                    show_msg(data.sts,data.msg);      
                    $(".rowmod[data-id="+id+"]").remove();
             });
        }
    });
    
    $(document).on("click",".sendsurvey",function(){
		
		/*custom*/
		
        /* if($("#student_user").val() == "" || $("#student_user").val() == null){
            alert("Please select at least one user.");
            return false;
        }*/
		
		/*get checked type*/
		var chk_type = $('input[name=rdbSurvey]:checked').val();
		var chk_type_id='';
		if(chk_type=="Course"){ chk_type_id = $('.showCourseList').val(); }
		if(chk_type=="Mentor"){ chk_type_id = $('.showMentorList').val(); }
		 
        var conf = confirm("Are you sure to send survey?");
        if(conf){
            var formid = $("#formid").val();
			/*Custom code to select all checked users*/
			var ids=[]; 
			$('input[type=checkbox]:checked').each(function(){
				ids.push($(this).val());
			});
            //var users = $("#student_user").val();
			var users = ids;
			if(users==""){
				alert("Please select at least one user.");
				return false;
			}
			//console.log("Survey Users are : "+users+" , Type : "+chk_type+"  ,  TypeId : "+chk_type_id);
			
            var podata = "formid="+formid+"&users="+users+"&type="+chk_type+"&typeid="+chk_type_id;
            $("body").addClass("processing");
            $.post(ajaxurl, podata + "&param=survey_send&action=training_lib", function(dat){
                    $("body").removeClass("processing");
                    var data = $.parseJSON(dat);
                    show_msg(data.sts,data.msg);      
                    if(data.sts == 1){
                        window.location.reload();
                    }
             }); 
        }
    });        
     
     
     $(document).on("click",".template_update",function(){
        
        var template_id = $(this).attr('data-id');
        var sub = $.trim($("#subject_"+template_id).val());
        var content = '';
        if ($("#wp-content_"+template_id+"-wrap").hasClass("tmce-active")){

                content  = encodeURIComponent(tinyMCE.get("content_"+template_id).getContent());
        }
        else{
                content  = encodeURIComponent($("#content_"+template_id).val());
        }
        content = $.trim(content);
        if(sub == '' || content == ''){
            alert("Please fill Subject and Content field.");
            return false;
        }
                
        var podata = "template_id="+template_id+"&sub="+sub+"&content="+content;
        $("body").addClass("processing");
        $.post(ajaxurl, podata + "&param=update__template&action=training_lib", function(dat){
                $("body").removeClass("processing");
                var data = $.parseJSON(dat);
                show_msg(data.sts,data.msg);                
         });
    });
     
    
}

function mentro_students(course_id,mentor,user_id){
    
    $("body").addClass("processing");        
    var podata = "mentor="+mentor+"&course_id="+course_id+"&param=get_mentor_users&action=training_lib";       
    $.post(ajaxurl,podata,function(dat){
        $("body").removeClass("processing");
        var data = $.parseJSON(dat);            
        var options = "";
        if(data.sts == 1){   
            var dd = data.arr;
            for(a in dd){
                var sel = '';
                if(user_id == dd[a].ID)
                    sel = 'selected="selected"';
                options += "<option "+sel+" value='"+dd[a].ID+"'>"+dd[a].display_name+"</option>";
            }                
        }
        $("#student_user").html(options);            
    });
    
}

function sortul(){
    $( "#sortableul" ).sortable();
    $( "#sortableul" ).disableSelection();
}

function calcpercent($,type){
    
    var comres = $("#completed_resources").val();
    var totres = $("#total_resources").val();
    
    if(type == 'inc'){
      comres = parseInt(comres) + 1;
    }
    else{
      comres = parseInt(comres) - 1;  
    }
    if(comres < 0){
        comres = 0;
    }
    else if(comres >= totres){
        comres = totres;
    }
    
    var percent = Math.floor((comres / totres) * 100);
    
    $("#completed_resources").val(comres);
    $("#percent_bar").val(percent);
    $(".perint").text(percent);
    $(".perdiv").css("width",percent+"%");
}

// submit functions

function submitlesson(){
    jQuery("#addlesson").submit();
}



function submitres(){
    jQuery("#addresource").submit();
}


function getexceise(type,id){    
    $ = jQuery;            
    $("#addprojectexce").get(0).reset();
    var podata = "type=" + type + "&id=" + id + "&param=get_exercise&action=training_lib";
    $("body").addClass("processing");
    $.post(ajaxurl,podata,function(dat){
        $("body").removeClass("processing");
        var data = $.parseJSON(dat);   
        
        if(data.arr.info){ 
            var vals = data.arr.info;        
            if(vals.status == 1){
                $("#addprojectexce #isenabled").prop("checked",true);
            }
            $("#exid").val(vals.id);
            $("#addprojectexce #title").val(vals.title);
            $("#addprojectexce #hours").val(vals.total_hrs);
            
            tinyMCE.get('description1').setContent(vals.desc, {format : 'html'} );
            //tinyMCE.get('description1').setContent(vals.desc, {format : 'html'} );
        }
        $(".tbluserdv tbody").html("");
        $("#listusersdiv .loadergif").remove();
        $(".tbluserdv").show();
        var tds = '';
        
        if(data.arr.projects && data.arr.projects != ''){ 
                        
            var arlinks = data.arr.projects;
            
            for(x in arlinks){
                tds += "<tr>";
                tds += "<td>"+arlinks[x].display_name+"</td>";
                tds += "<td>"+arlinks[x].user_email+"</td>";                
                var lnks = arlinks[x].links;
                lnks = lnks.split(","); var lnkanc = '';
                for(i =0; i < lnks.length; i++){
                    lnkanc += '<a target="_blank" href="'+lnks[i]+'" >'+lnks[i]+'</a> <br/>';
                }
                
                tds += "<td>"+lnkanc+"</td>";
                tds += "</tr>";
            }                        
        }
        else{
            
            tds += "<tr>";
            tds += "<td colspan='3'>No record</td>";            
            tds += "</tr>";
        }
        
        $(".tbluserdv tbody").html(tds);
        open_modal('project_excercise');
    });
    
}
function funs($){
   $("#responsedoc").change(function () {   
        readdoc(this);
    });

    function readdoc(input) {
        if (input.files.length > 0)
            $("#fileList").html('');

        for (var x = 0; x < input.files.length; x++) {
            //add to list
            var li = document.createElement('li');
            var inp = "<input class='form-control' type='text' name='doctitles[]' placeholder='Enter Document Title' />";
            li.innerHTML = '<p>File ' + (x + 1) + ':  ' + input.files[x].name + '</p>\n\
                            <div>'+inp+'</div>';
            $(li).addClass('list-group-item');
            $("#fileList").append(li);
        }
    }
        
}

function uploaddocs(){
        if($("#responsedoc").val() == ''){
            alert("Please choose file(s)");
            return false;
        }
        var typematerial = $("#typematerial").val(); 
	
        var id = 0;
        if(typematerial == 'lesson')
            id = $("#lessonid").val();
        else
            id = $("#resourceid").val();
             
        var data = new FormData();
        $.each($('#responsedoc')[0].files, function (i, file) {
            data.append('file-' + i, file);
        });
	
	    if(typematerial=="community_call"){
		    var course_id = $('#course_id').val(); 
			var podata = ajaxurl+"?id=" + id + "&typematerial="+typematerial + "&param=save_doc&action=training_lib&course_id="+course_id;
		}else{
		     var podata = ajaxurl+"?id=" + id + "&typematerial="+typematerial + "&param=save_doc&action=training_lib";
		}
	
        $("body").addClass("processing");        
        
        $.ajax({
            type: "POST",
            url: podata,
            data: data,
            processData: false,
            contentType: false,
            success: function (msg)
            {
                var data = $.parseJSON(msg);
				show_msg(data.sts,data.msg);
				 if(typematerial=="community_call"){
				    //setdoctitles(data.arr.ids,data.arr.pos);
				 }else{
					setdoctitles(data.arr.ids,data.arr.pos); 
				    //window.location.reload();  
				 }
                
				window.location.reload();  
            },
            error: function (msg)
            {
                $("#fileList").empty();
                $("#responsedoc").val("");
                $("body").removeClass("processing");                
            }
        });
               
    }
    
    function setdoctitles(ids,pos){
        
        var podata = $("#adddoc").serialize()+"&ids="+ids+"&pos="+pos + "&param=save_doc_titles&action=training_lib";        
        $.post(ajaxurl, podata,function(dat){            
            var data = $.parseJSON(dat);
            $("body").removeClass("processing");                
            show_msg(data.sts,data.msg);
            $("#fileList").empty();
            $("#responsedoc").val("");
            if(data.sts == 1){
              window.location.reload();  
            }            
        });
        
    }
    
    function uploadimg(id,urlimg,typematerial){
        if($("#responseimg").val() != ''){
                        
            var data = new FormData();
            $.each($('#responseimg')[0].files, function (i, file) {
                data.append('file-' + i, file);
            });
            
            var podata = ajaxurl+"?id=" + id +"&typematerial=" + typematerial +"&urlimg=" + urlimg + "&param=save_img&action=training_lib";            
            
            $("body").addClass("processing");        

            $.ajax({
                type: "POST",
                url: podata,
                data: data,
                processData: false,
                contentType: false,
                success: function (msg)
                {
                    $("body").removeClass("processing");
                    var data = $.parseJSON(msg);
                    show_msg(data.sts,data.msg);
                    if(data.sts == 1){
                      window.location.reload(); 
                    }

                },
                error: function (msg)
                {
                    $("body").removeClass("processing");                
                }
            });
        }          
    }
    
    function saveimgurl(urlimg){
        var imageid = $("#imageid").val();
        var podata = ajaxurl+"?imageid=" + imageid +"&urlimg=" + urlimg + "&param=save_urlimg&action=training_lib";
        $("body").addClass("processing");    
        $.post(podata,function(dat){
            $("body").removeClass("processing");
            var data = $.parseJSON(dat);
            show_msg(data.sts,data.msg); 
            if(data.sts == 1){
                window.location.reload();
            }
        });
    }          
    
    function ValidURL(s) {    
          var regexp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
          return regexp.test(s);    
     }
     
     function loadform(id,isupdt){
         if($(".customformbuilder").length > 0){                          
            if(isupdt == 1){    
                $("body").addClass("processing");    
                var form_id = $("#formid").val();                
                formupdate(dataofform,form_id);
            }
            else{              
                var mentor_id = $("#creatementorform").val(); 
                var formtitle = $("#formtitle").val(); 
                if(formtitle == ""){
                    alert("Please enter form title");
                    return false;
                }

                var podata = "mentor_id=0&formtitle="+formtitle+"&param=saveform&action=training_lib";
                //var podata = "formtitle="+formtitle+"&param=saveform&action=training_lib";

                $("body").addClass("processing");    
                $.post(ajaxurl,podata,function(dat){
                    $("body").removeClass("processing");
                    var data = $.parseJSON(dat);               
                    if(data.sts == 1){

                        window.location.href = "admin.php?page=new_survey&form_id="+data.arr;                    
                    }
                });
             
             }
            
             
         }else{
		    //alert("Failed");
		 }
     }
    

    
     function formupdate(payload,form_id){               
        var formtitle = $("#formtitle").val(); 
        var podata ="form_id="+form_id+"&formtitle="+formtitle+"&form_data="+payload+"&param=updateform&action=training_lib";
         
        $.post(ajaxurl,podata,function(dat){
            $("body").removeClass("processing");
            var data = $.parseJSON(dat);            
            if(data.sts == 1){
               
            }
        });
         
     }
     
     function formbuilder(form_id,formjson){
         if($(".customformbuilder").length > 0){
            var fbbuild = new Formbuilder({
                   selector: '.customformbuilder',
                   bootstrapData: formjson
                 });

                 fbbuild.on('save', function(payload){
                       dataofform = payload;                       
                       formupdate(dataofform,form_id);
                 });
          }
     }
     
     function savesurveydata(survey_id,values){    
        //console.log(values);
        values = JSON.stringify(values);
        var podata ="survey_id="+survey_id+"&values="+values+"&param=saveformresult&action=training_lib";
        $("body").addClass("processing"); 
        $.post(ajaxurl,podata,function(dat){
            $("body").removeClass("processing");
            var data = $.parseJSON(dat);    
            //console.log(data);
            show_msg(data.sts,data.msg);
            if(data.sts == 1){
               setTimeout(function(){
                    window.location.reload(); 
               },3000);
            }
        });
         
     }
    
    function chosen_initilize(){
        if($('.chosen').length > 0){
            $('.chosen').chosen({
                width: '100%', 
                no_results_text:'Oops, nothing found!'
            });
        }
    }


	/*Custom Js Code For Survey Area - custom*/ 
	$(function(){
	   $('#rdbCourse,#rdbMentor,.show_students').hide();	 
	   $('.rdbSurvey').on("click",function(){
	       targetValue = $(this).attr("data-target");
		   if(targetValue=="rdbCourse"){ $('#rdbCourse').show();$('#rdbMentor').hide();$('.show_students').hide() ;}
		   if(targetValue=="rdbMentor"){ $('#rdbCourse').hide();$('#rdbMentor').show();$('.show_students').hide(); }
	   });
	  $('.showMentorList').on("change",function(){
	     var mentor_id = $(this).val();
		  console.log(mentor_id);
		 var mentordata = ajaxurl+ "?param=get_survey_users&action=training_lib";
         $("body").addClass("processing");  
		 $.ajax({
		    url:mentordata,
			data:{ment_id:mentor_id},
			type:"post",
			success:function(resp){
			   $("body").removeClass("processing");
			$('.show_students').show();	
				$('.head').html("<label class='slct_heading'>Select Student</label>");
			   $('.showlistbyMent').html("<ol>"+resp+"</ol>");
			}
		 });
		  
	  });
	 $('.showCourseList').on("change",function(){
	     var course_id = $(this).val();
		  console.log(course_id);
		 var coursedata = ajaxurl+ "?param=get_survey_users_by_course&action=training_lib";
         $("body").addClass("processing");  
		 $.ajax({
		    url:coursedata,
			data:{cour_id:course_id},
			type:"post",
			success:function(resp){
			   $("body").removeClass("processing");
			   $('.show_students').show();	
				$('.head').html("<label class='slct_heading'>Select Student</label>");
			    $('.showlistbyMent').html("<ol class='load-st-ol'>"+resp+"</ol>");
			  }
		 });
		 
	  });
	  $('#chkAllSt').on("click",function(){
	      if(this.checked) { 
            $('.chkSt').each(function() {
                this.checked = true;        
            });
        }
	  });
	  $('#unchkAllSt').on("click",function(){
	      if(this.checked) { 
            $('.chkSt').each(function() {
                this.checked = false;        
            });
        }
	  });
	});